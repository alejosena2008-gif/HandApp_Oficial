"""
Motor de visión artificial LSC (MediaPipe HandLandmarker + scikit-learn) integrado en Django.
Sirve los endpoints /video (stream MJPEG) y /letra (JSON con letra y confianza).
"""
import os
import threading

CARPETA_PYTHON = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
CARPETA_MODELOS = os.path.join(CARPETA_PYTHON, 'ia_lsc', 'modelos')
MODELO_TASK = os.path.join(CARPETA_MODELOS, 'hand_landmarker.task')
MODELO_PKL = os.path.join(CARPETA_MODELOS, 'modelo_lsc.pkl')

letra_actual = {"letra": "", "confianza": 0.0}
_lock = threading.Lock()
_modelo = None
_detector = None
_inicializado = False


def inicializar_detector():
    global _modelo, _detector, _inicializado
    if _inicializado:
        return _detector is not None and _modelo is not None
    with _lock:
        if _inicializado:
            return _detector is not None and _modelo is not None
        try:
            import joblib
            import mediapipe as mp
            from mediapipe.tasks import python
            from mediapipe.tasks.python import vision
            from mediapipe.tasks.python.vision import HandLandmarker

            _modelo = joblib.load(MODELO_PKL)
            base_options = mp.tasks.BaseOptions(model_asset_path=MODELO_TASK)
            options = vision.HandLandmarkerOptions(
                base_options=base_options,
                num_hands=1,
                min_hand_detection_confidence=0.7,
                min_tracking_confidence=0.7,
            )
            _detector = HandLandmarker.create_from_options(options)
            _inicializado = True
            return True
        except Exception as exc:
            print(f"[HandApp Django IA] Aviso al cargar modelo MediaPipe/LSC: {exc}")
            _inicializado = True
            return False


def generar_frames_mjpeg():
    global letra_actual
    import cv2
    import numpy as np
    import mediapipe as mp

    ok_ia = inicializar_detector()
    cap = cv2.VideoCapture(0)
    try:
        while True:
            ret, frame = cap.read()
            if not ret:
                break

            frame = cv2.flip(frame, 1)
            h, w = frame.shape[:2]

            if ok_ia and _detector is not None and _modelo is not None:
                rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
                mp_image = mp.Image(image_format=mp.ImageFormat.SRGB, data=rgb)
                resultado = _detector.detect(mp_image)

                if resultado.hand_landmarks:
                    landmarks = resultado.hand_landmarks[0]
                    for lm in landmarks:
                        x, y = int(lm.x * w), int(lm.y * h)
                        cv2.circle(frame, (x, y), 5, (0, 255, 0), -1)

                    xs = [lm.x for lm in landmarks]
                    ys = [lm.y for lm in landmarks]
                    datos = np.array(xs + ys).reshape(1, -1)
                    letra = _modelo.predict(datos)[0]
                    confianza = _modelo.predict_proba(datos).max() * 100

                    letra_actual["letra"] = str(letra)
                    letra_actual["confianza"] = round(float(confianza), 1)
                else:
                    letra_actual["letra"] = ""
                    letra_actual["confianza"] = 0.0

            _, buffer = cv2.imencode('.jpg', frame)
            frame_bytes = buffer.tobytes()
            yield (
                b'--frame\r\n'
                b'Content-Type: image/jpeg\r\n\r\n' + frame_bytes + b'\r\n'
            )
    finally:
        cap.release()
