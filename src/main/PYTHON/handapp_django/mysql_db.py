"""
Conector MySQL/MariaDB nativo por protocolo TCP (127.0.0.1:3306) para HandApp en Django.
Conecta directamente a la base de datos MySQL `handapp` (XAMPP) sin requerir compilación C,
guardando y consultando en tiempo real en las tablas `usuarios`, `lecciones` y `progreso_estudiante`.
"""
import hashlib
import socket
import struct


class MySQLError(Exception):
    def __init__(self, code: int, message: str):
        super().__init__(f"MySQL Error {code}: {message}")
        self.code = code
        self.message = message


class MySQLIntegrityError(MySQLError):
    pass


def _encode_mysql_native_password(password: str, scramble: bytes) -> bytes:
    if not password:
        return b""
    p1 = hashlib.sha1(password.encode("utf-8")).digest()
    p2 = hashlib.sha1(p1).digest()
    p3 = hashlib.sha1(scramble[:20] + p2).digest()
    return bytes(a ^ b for a, b in zip(p1, p3))


def _read_lenenc_int(buf: bytes, pos: int) -> tuple[int | None, int]:
    first = buf[pos]
    pos += 1
    if first < 0xFB:
        return first, pos
    if first == 0xFB:
        return None, pos
    if first == 0xFC:
        return struct.unpack_from("<H", buf, pos)[0], pos + 2
    if first == 0xFD:
        b0, b1, b2 = buf[pos], buf[pos + 1], buf[pos + 2]
        return b0 | (b1 << 8) | (b2 << 16), pos + 3
    if first == 0xFE:
        return struct.unpack_from("<Q", buf, pos)[0], pos + 8
    return 0, pos


def _read_lenenc_bytes(buf: bytes, pos: int) -> tuple[bytes | None, int]:
    length, pos = _read_lenenc_int(buf, pos)
    if length is None:
        return None, pos
    return buf[pos : pos + length], pos + length


class MySQLResult:
    def __init__(self, rows: list[dict], lastrowid: int = 0, rowcount: int = 0):
        self._rows = rows
        self.lastrowid = lastrowid
        self.rowcount = rowcount

    def fetchall(self) -> list[dict]:
        return self._rows

    def fetchone(self) -> dict | None:
        return self._rows[0] if self._rows else None


class MySQLConnection:
    CLIENT_LONG_PASSWORD = 1
    CLIENT_FOUND_ROWS = 2
    CLIENT_LONG_FLAG = 4
    CLIENT_CONNECT_WITH_DB = 8
    CLIENT_PROTOCOL_41 = 512
    CLIENT_TRANSACTIONS = 8192
    CLIENT_SECURE_CONNECTION = 32768
    CLIENT_MULTI_RESULTS = 131072
    CLIENT_PLUGIN_AUTH = 524288

    def __init__(
        self,
        host: str = "127.0.0.1",
        port: int = 3306,
        user: str = "root",
        password: str = "",
        database: str = "handapp",
        timeout: float = 10.0,
    ):
        self.host = host
        self.port = port
        self.user = user
        self.password = password
        self.database = database
        self.timeout = timeout
        self.sock: socket.socket | None = None
        self._connect()

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_val, exc_tb):
        self.close()

    def _recv_exact(self, n: int) -> bytes:
        chunks = []
        received = 0
        while received < n:
            chunk = self.sock.recv(n - received)
            if not chunk:
                raise ConnectionError("Conexión cerrada por el servidor MySQL")
            chunks.append(chunk)
            received += len(chunk)
        return b"".join(chunks)

    def _read_packet(self) -> tuple[int, bytes]:
        header = self._recv_exact(4)
        length = header[0] | (header[1] << 8) | (header[2] << 16)
        seq_id = header[3]
        payload = self._recv_exact(length) if length > 0 else b""
        return seq_id, payload

    def _write_packet(self, seq_id: int, payload: bytes) -> None:
        length = len(payload)
        header = bytes([length & 0xFF, (length >> 8) & 0xFF, (length >> 16) & 0xFF, seq_id & 0xFF])
        self.sock.sendall(header + payload)

    def _connect(self) -> None:
        self.sock = socket.create_connection((self.host, self.port), timeout=self.timeout)
        seq_id, greeting = self._read_packet()

        pos = 1  # protocol version (10)
        end_ver = greeting.index(b"\x00", pos)
        pos = end_ver + 1
        pos += 4  # connection id
        auth_part1 = greeting[pos : pos + 8]
        pos += 9  # 8 bytes scramble + 1 filler
        cap_low = struct.unpack_from("<H", greeting, pos)[0]
        pos += 2
        if pos < len(greeting):
            pos += 1  # charset
            pos += 2  # status flags
            cap_high = struct.unpack_from("<H", greeting, pos)[0]
            pos += 2
            auth_len = greeting[pos]
            pos += 1
            pos += 10  # reserved
            rest_len = max(13, auth_len - 8) if auth_len else 13
            auth_part2 = greeting[pos : pos + rest_len].rstrip(b"\x00")
        else:
            auth_part2 = b""

        scramble = auth_part1 + auth_part2[:12]

        client_flags = (
            self.CLIENT_LONG_PASSWORD
            | self.CLIENT_LONG_FLAG
            | self.CLIENT_CONNECT_WITH_DB
            | self.CLIENT_PROTOCOL_41
            | self.CLIENT_TRANSACTIONS
            | self.CLIENT_SECURE_CONNECTION
            | self.CLIENT_PLUGIN_AUTH
        )

        auth_resp = _encode_mysql_native_password(self.password, scramble)
        charset_utf8mb4 = 45

        resp = bytearray()
        resp += struct.pack("<IIb", client_flags, 0x01000000, charset_utf8mb4)
        resp += b"\x00" * 23
        resp += self.user.encode("utf-8") + b"\x00"
        resp += bytes([len(auth_resp)]) + auth_resp
        resp += self.database.encode("utf-8") + b"\x00"
        resp += b"mysql_native_password\x00"

        self._write_packet(seq_id + 1, bytes(resp))
        _, auth_result = self._read_packet()
        if auth_result[0] == 0xFF:
            self._raise_error(auth_result)
        self.execute("SET NAMES utf8mb4")

    def _raise_error(self, payload: bytes) -> None:
        code = struct.unpack_from("<H", payload, 1)[0]
        msg = payload[9:].decode("utf-8", errors="replace") if len(payload) > 9 else payload[3:].decode("utf-8", errors="replace")
        if code == 1062:
            raise MySQLIntegrityError(code, msg)
        raise MySQLError(code, msg)

    @staticmethod
    def _escape_value(val) -> str:
        if val is None:
            return "NULL"
        if isinstance(val, bool):
            return "1" if val else "0"
        if isinstance(val, (int, float)):
            return str(val)
        if isinstance(val, (bytes, bytearray)):
            return "0x" + bytes(val).hex()
        s = str(val)
        s = (
            s.replace("\\", "\\\\")
            .replace("'", "\\'")
            .replace('"', '\\"')
            .replace("\x00", "\\0")
            .replace("\n", "\\n")
            .replace("\r", "\\r")
        )
        return f"'{s}'"

    def _format_sql(self, sql: str, params: tuple | list | None) -> str:
        if not params:
            return sql
        parts = sql.split("?")
        if len(parts) - 1 != len(params):
            raise ValueError(f"Número de parámetros no coincide en SQL: {sql}")
        out = [parts[0]]
        for p, next_part in zip(params, parts[1:]):
            out.append(self._escape_value(p))
            out.append(next_part)
        return "".join(out)

    def execute(self, sql: str, params: tuple | list | None = None) -> MySQLResult:
        formatted = self._format_sql(sql, params)
        cmd_payload = b"\x03" + formatted.encode("utf-8")
        self._write_packet(0, cmd_payload)

        _, first_pkt = self._read_packet()
        header_byte = first_pkt[0]

        if header_byte == 0xFF:
            self._raise_error(first_pkt)

        if header_byte == 0x00:
            # OK Packet
            pos = 1
            affected_rows, pos = _read_lenenc_int(first_pkt, pos)
            last_insert_id, pos = _read_lenenc_int(first_pkt, pos)
            return MySQLResult([], lastrowid=last_insert_id or 0, rowcount=affected_rows or 0)

        # Resultset
        col_count, _ = _read_lenenc_int(first_pkt, 0)
        columns = []
        for _ in range(col_count):
            _, col_pkt = self._read_packet()
            pos = 0
            _, pos = _read_lenenc_bytes(col_pkt, pos)  # catalog
            _, pos = _read_lenenc_bytes(col_pkt, pos)  # schema
            _, pos = _read_lenenc_bytes(col_pkt, pos)  # table
            _, pos = _read_lenenc_bytes(col_pkt, pos)  # org_table
            name_bytes, pos = _read_lenenc_bytes(col_pkt, pos)  # name
            _, pos = _read_lenenc_bytes(col_pkt, pos)  # org_name
            pos += 1  # filler 0x0c
            charset, col_len, col_type = struct.unpack_from("<HIB", col_pkt, pos)
            col_name = name_bytes.decode("utf-8", errors="replace") if name_bytes else ""
            columns.append((col_name, col_type))

        # EOF packet after columns
        _, eof_pkt = self._read_packet()

        rows = []
        while True:
            _, row_pkt = self._read_packet()
            if row_pkt[0] == 0xFE and len(row_pkt) < 9:
                break
            if row_pkt[0] == 0xFF:
                self._raise_error(row_pkt)

            pos = 0
            row_dict = {}
            for col_name, col_type in columns:
                raw_val, pos = _read_lenenc_bytes(row_pkt, pos)
                if raw_val is None:
                    row_dict[col_name] = None
                elif col_type == 16:  # MYSQL_TYPE_BIT
                    row_dict[col_name] = 1 if (len(raw_val) > 0 and raw_val[0] not in (0, 48)) else 0
                elif col_type in (1, 2, 3, 8, 9, 13):  # TINY, SHORT, LONG, LONGLONG, INT24, YEAR
                    try:
                        row_dict[col_name] = int(raw_val.decode("ascii"))
                    except ValueError:
                        row_dict[col_name] = raw_val.decode("utf-8", errors="replace")
                elif col_type in (0, 4, 5, 246):  # DECIMAL, FLOAT, DOUBLE, NEWDECIMAL
                    try:
                        row_dict[col_name] = float(raw_val.decode("ascii"))
                    except ValueError:
                        row_dict[col_name] = raw_val.decode("utf-8", errors="replace")
                else:
                    row_dict[col_name] = raw_val.decode("utf-8", errors="replace")
            rows.append(row_dict)

        return MySQLResult(rows, rowcount=len(rows))

    def commit(self) -> None:
        # Autocommit está activo por defecto en MySQL/MariaDB
        pass

    def close(self) -> None:
        if self.sock:
            try:
                self._write_packet(0, b"\x01")  # COM_QUIT
            except Exception:
                pass
            try:
                self.sock.close()
            except Exception:
                pass
            self.sock = None
