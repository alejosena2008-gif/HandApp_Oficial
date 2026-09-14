package com.handapp.HandApp.service;

import com.handapp.HandApp.dto.ProgresoLeccionDTO;
import com.handapp.HandApp.dto.ReporteUsuarioDTO;
import com.lowagie.text.Document;
import com.lowagie.text.Element;
import com.lowagie.text.Font;
import com.lowagie.text.FontFactory;
import com.lowagie.text.PageSize;
import com.lowagie.text.Paragraph;
import com.lowagie.text.pdf.PdfPCell;
import com.lowagie.text.pdf.PdfPTable;
import com.lowagie.text.pdf.PdfWriter;
import org.apache.poi.ss.usermodel.Cell;
import org.apache.poi.ss.usermodel.CellStyle;
import org.apache.poi.ss.usermodel.Row;
import org.apache.poi.ss.usermodel.Sheet;
import org.apache.poi.ss.usermodel.Workbook;
import org.apache.poi.xssf.usermodel.XSSFWorkbook;
import org.apache.poi.xwpf.usermodel.ParagraphAlignment;
import org.apache.poi.xwpf.usermodel.XWPFDocument;
import org.apache.poi.xwpf.usermodel.XWPFParagraph;
import org.apache.poi.xwpf.usermodel.XWPFRun;
import org.apache.poi.xwpf.usermodel.XWPFTable;
import org.apache.poi.xwpf.usermodel.XWPFTableCell;
import org.apache.poi.xwpf.usermodel.XWPFTableRow;
import org.springframework.stereotype.Service;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.util.List;

@Service
public class ReporteExportService {

    public byte[] generarExcel(List<ReporteUsuarioDTO> filas) throws IOException {
        Workbook workbook = new XSSFWorkbook();
        ByteArrayOutputStream out = new ByteArrayOutputStream();
        try {
            Sheet sheet = workbook.createSheet("Reporte de usuarios");

            org.apache.poi.ss.usermodel.Font headerFont = workbook.createFont();
            headerFont.setBold(true);
            CellStyle headerStyle = workbook.createCellStyle();
            headerStyle.setFont(headerFont);

            String[] columnas = {"Nombre", "Correo", "Rol", "Lecciones completadas", "Promedio de puntaje", "Detalle de progreso"};
            Row filaEncabezado = sheet.createRow(0);
            for (int i = 0; i < columnas.length; i++) {
                Cell celda = filaEncabezado.createCell(i);
                celda.setCellValue(columnas[i]);
                celda.setCellStyle(headerStyle);
            }

            int numeroFila = 1;
            for (ReporteUsuarioDTO fila : filas) {
                Row filaExcel = sheet.createRow(numeroFila++);
                filaExcel.createCell(0).setCellValue(fila.getNombre());
                filaExcel.createCell(1).setCellValue(fila.getCorreo());
                filaExcel.createCell(2).setCellValue(fila.getRol() != null ? fila.getRol().name() : "");
                filaExcel.createCell(3).setCellValue(fila.getLeccionesCompletadas());
                filaExcel.createCell(4).setCellValue(fila.getPromedioPuntaje() != null ? fila.getPromedioPuntaje() : 0);

                StringBuilder detalle = new StringBuilder();
                if (fila.getProgresos() != null) {
                    for (ProgresoLeccionDTO p : fila.getProgresos()) {
                        detalle.append(p.getTituloLeccion())
                               .append(": ")
                               .append(p.isCompletado() ? (p.getPuntaje() + " pts") : "sin completar")
                               .append(" | ");
                    }
                }
                filaExcel.createCell(5).setCellValue(detalle.toString());
            }

            for (int i = 0; i < columnas.length; i++) {
                sheet.autoSizeColumn(i);
            }

            workbook.write(out);
            return out.toByteArray();
        } finally {
            workbook.close();
            out.close();
        }
    }

    public byte[] generarWord(List<ReporteUsuarioDTO> filas) throws IOException {
        XWPFDocument documento = new XWPFDocument();
        ByteArrayOutputStream out = new ByteArrayOutputStream();
        try {
            XWPFParagraph titulo = documento.createParagraph();
            titulo.setAlignment(ParagraphAlignment.CENTER);
            XWPFRun runTitulo = titulo.createRun();
            runTitulo.setText("Reporte de usuarios — HandApp");
            runTitulo.setBold(true);
            runTitulo.setFontSize(16);

            documento.createParagraph();

            int columnas = 5;
            XWPFTable tabla = documento.createTable(1, columnas);

            XWPFTableRow filaEncabezado = tabla.getRow(0);
            String[] encabezados = {"Nombre", "Correo", "Rol", "Lecciones completadas", "Promedio"};
            for (int i = 0; i < columnas; i++) {
                XWPFTableCell celda = filaEncabezado.getCell(i);
                celda.setText(encabezados[i]);
            }

            for (ReporteUsuarioDTO fila : filas) {
                XWPFTableRow filaTabla = tabla.createRow();
                filaTabla.getCell(0).setText(fila.getNombre());
                filaTabla.getCell(1).setText(fila.getCorreo());
                filaTabla.getCell(2).setText(fila.getRol() != null ? fila.getRol().name() : "");
                filaTabla.getCell(3).setText(String.valueOf(fila.getLeccionesCompletadas()));
                filaTabla.getCell(4).setText(fila.getPromedioPuntaje() != null
                        ? String.format("%.1f", fila.getPromedioPuntaje()) : "—");
            }

            documento.write(out);
            return out.toByteArray();
        } finally {
            documento.close();
            out.close();
        }
    }

    public byte[] generarPdf(List<ReporteUsuarioDTO> filas) throws Exception {
        ByteArrayOutputStream out = new ByteArrayOutputStream();
        try {
            Document documento = new Document(PageSize.A4.rotate());
            PdfWriter.getInstance(documento, out);
            documento.open();

            Font fuenteTitulo = FontFactory.getFont(FontFactory.HELVETICA_BOLD, 16);
            Paragraph titulo = new Paragraph("Reporte de usuarios — HandApp", fuenteTitulo);
            titulo.setAlignment(Element.ALIGN_CENTER);
            documento.add(titulo);
            documento.add(new Paragraph(" "));

            PdfPTable tabla = new PdfPTable(5);
            tabla.setWidthPercentage(100);

            Font fuenteEncabezado = FontFactory.getFont(FontFactory.HELVETICA_BOLD, 10);
            String[] encabezados = {"Nombre", "Correo", "Rol", "Lecciones completadas", "Promedio"};
            for (String encabezado : encabezados) {
                tabla.addCell(new PdfPCell(new Paragraph(encabezado, fuenteEncabezado)));
            }

            Font fuenteCelda = FontFactory.getFont(FontFactory.HELVETICA, 9);
            for (ReporteUsuarioDTO fila : filas) {
                tabla.addCell(new Paragraph(fila.getNombre(), fuenteCelda));
                tabla.addCell(new Paragraph(fila.getCorreo(), fuenteCelda));
                tabla.addCell(new Paragraph(fila.getRol() != null ? fila.getRol().name() : "", fuenteCelda));
                tabla.addCell(new Paragraph(String.valueOf(fila.getLeccionesCompletadas()), fuenteCelda));
                tabla.addCell(new Paragraph(fila.getPromedioPuntaje() != null
                        ? String.format("%.1f", fila.getPromedioPuntaje()) : "—", fuenteCelda));
            }

            documento.add(tabla);
            documento.close();

            return out.toByteArray();
        } finally {
            out.close();
        }
    }
}