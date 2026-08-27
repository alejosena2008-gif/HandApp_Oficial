import cv2
import mediapipe as mp
from mediapipe.tasks.python import vision
from mediapipe.tasks.python.vision import HandLandmarker
import numpy as np
from collections import deque
import tensorflow as tf
import os

# ---------------------------------------------------------
CARPETA_BASE = r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON'
MODELO_MANO = os.path.join(CARPETA_BASE, 'hand_landmarker.task')
MODELO_MOVIMIENTOS = os.path.join(CARPETA_BASE, 'modelo_movimientos.h5')
CLASES = os.path.join(CARPETA_BASE, 'clases_movimientos.npy')

FRAMES_POR_SECUENCIA = 30
UMBRAL_CONFIANZA = 0.8
# ---------------------------------------------------------

modelo = tf.keras.models.load_model(MODELO_MOVIMIENTOS)
clases = np.load(CLASES)

base_options = mp.tasks.BaseOptions(model_asset_path=MODELO_MANO)
options = vision.HandLandmarkerOptions(
    base_options=base_options,
    num_hands=1,
    min_hand_detection_confidence=0.7,
    min_tracking_confidence=0.7
)
detector = HandLandmarker.create_from_options(options)


def normalizar(landmarks):
    xs = np.array([lm.x for lm in landmarks])
    ys = np.array([lm.y for lm in landmarks])
    xs = xs - xs[0]
    ys = ys - ys[0]
    escala = max(np.max(np.abs(xs)), np.max(np.abs(ys)), 1e-6)
    xs = xs / escala
    ys = ys / escala
    return np.concatenate([xs, ys])


buffer = deque(maxlen=FRAMES_POR_SECUENCIA)
cap = cv2.VideoCapture(0)
texto_actual = "Esperando..."
cooldown = 0  # evita repetir la misma predicción frame a frame

print("Detección de movimientos iniciada. Presiona Q para salir.")

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
        buffer.append(normalizar(landmarks))
    else:
        buffer.clear()
        texto_actual = "Muestra tu mano"

    if len(buffer) == FRAMES_POR_SECUENCIA and cooldown == 0:
        entrada = np.expand_dims(np.array(buffer), axis=0)  # (1, 30, 42)
        pred = modelo.predict(entrada, verbose=0)[0]
        idx = np.argmax(pred)
        confianza = pred[idx]

        if confianza >= UMBRAL_CONFIANZA:
            texto_actual = f"{clases[idx]} ({confianza*100:.0f}%)"
            cooldown = 20        # espera unos frames antes de volver a predecir
            buffer.clear()       # reinicia la ventana para la siguiente seña
        else:
            texto_actual = "..."

    if cooldown > 0:
        cooldown -= 1

    cv2.putText(frame, texto_actual, (30, 60),
                cv2.FONT_HERSHEY_SIMPLEX, 1.3, (0, 255, 0), 3)
    cv2.imshow("Detector de movimientos LSC", frame)

    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()