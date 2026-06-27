from flask import Flask, Response, jsonify
import cv2
import mediapipe as mp
from mediapipe.tasks import python
from mediapipe.tasks.python import vision
from mediapipe.tasks.python.vision import HandLandmarker
import joblib
import numpy as np

app = Flask(__name__)

MODELO = r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON\hand_landmarker.task'
MODELO_LSC = r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON\modelo_lsc.pkl'

modelo = joblib.load(MODELO_LSC)

base_options = mp.tasks.BaseOptions(model_asset_path=MODELO)
options = vision.HandLandmarkerOptions(
    base_options=base_options,
    num_hands=1,
    min_hand_detection_confidence=0.7,
    min_tracking_confidence=0.7
)
detector = HandLandmarker.create_from_options(options)

letra_actual = {"letra": "", "confianza": 0}

import threading

def generar_frames():
    global letra_actual
    cap = cv2.VideoCapture(0)
    while True:
        ret, frame = cap.read()
        if not ret:
            break

        frame = cv2.flip(frame, 1)
        h, w = frame.shape[:2]

        rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        mp_image = mp.Image(image_format=mp.ImageFormat.SRGB, data=rgb)
        resultado = detector.detect(mp_image)

        if resultado.hand_landmarks:
            landmarks = resultado.hand_landmarks[0]
            for lm in landmarks:
                x, y = int(lm.x * w), int(lm.y * h)
                cv2.circle(frame, (x, y), 5, (0, 255, 0), -1)

            xs = [lm.x for lm in landmarks]
            ys = [lm.y for lm in landmarks]
            datos = np.array(xs + ys).reshape(1, -1)
            letra = modelo.predict(datos)[0]
            confianza = modelo.predict_proba(datos).max() * 100

            letra_actual["letra"] = str(letra)
            letra_actual["confianza"] = round(float(confianza), 1)
            print(f"Detectada: {letra} ({confianza:.1f}%)")
        else:
            letra_actual["letra"] = ""
            letra_actual["confianza"] = 0

        _, buffer = cv2.imencode('.jpg', frame)
        frame_bytes = buffer.tobytes()
        yield (b'--frame\r\nContent-Type: image/jpeg\r\n\r\n' + frame_bytes + b'\r\n')

    cap.release()

@app.route('/video')
def video():
    return Response(generar_frames(), mimetype='multipart/x-mixed-replace; boundary=frame')

@app.route('/letra')
def letra():
    return jsonify(letra_actual)

@app.route('/')
def index():
    return "Servidor LSC funcionando"

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=False)