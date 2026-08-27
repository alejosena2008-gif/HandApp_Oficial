import cv2
import mediapipe as mp
from mediapipe.tasks.python import vision
from mediapipe.tasks.python.vision import HandLandmarker
import numpy as np
import os

# ---------------------------------------------------------
# CONFIGURA AQUÍ TUS RUTAS Y TUS PALABRAS/SEÑAS CON MOVIMIENTO
# ---------------------------------------------------------
CARPETA_BASE = r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON'
MODELO = os.path.join(CARPETA_BASE, 'hand_landmarker.task')
CARPETA_DATOS = os.path.join(CARPETA_BASE, 'datos_movimientos')

PALABRAS = ['HOLA', 'GRACIAS', 'POR_FAVOR']  # <-- agrega aquí tus señas dinámicas
SECUENCIAS_POR_PALABRA = 30      # cuántos "videos" (repeticiones) por palabra
FRAMES_POR_SECUENCIA = 30        # cuántos frames dura cada repetición (~1 seg a 30fps)

# ---------------------------------------------------------

base_options = mp.tasks.BaseOptions(model_asset_path=MODELO)
options = vision.HandLandmarkerOptions(
    base_options=base_options,
    num_hands=1,
    min_hand_detection_confidence=0.7,
    min_tracking_confidence=0.7
)
detector = HandLandmarker.create_from_options(options)

for palabra in PALABRAS:
    os.makedirs(os.path.join(CARPETA_DATOS, palabra), exist_ok=True)


def normalizar(landmarks):
    """Normaliza landmarks respecto a la muñeca (punto 0) para que el
    modelo no dependa de la posición de la mano en la pantalla."""
    xs = np.array([lm.x for lm in landmarks])
    ys = np.array([lm.y for lm in landmarks])
    xs = xs - xs[0]
    ys = ys - ys[0]
    escala = max(np.max(np.abs(xs)), np.max(np.abs(ys)), 1e-6)
    xs = xs / escala
    ys = ys / escala
    return np.concatenate([xs, ys])  # shape (42,)


cap = cv2.VideoCapture(0)

for palabra in PALABRAS:
    existentes = len(os.listdir(os.path.join(CARPETA_DATOS, palabra)))
    print(f"\n=== Palabra: {palabra} ===")
    print("Presiona ESPACIO para grabar cada secuencia. Q para salir.")

    secuencia_num = existentes
    while secuencia_num < existentes + SECUENCIAS_POR_PALABRA:
        # Esperar a que el usuario presione espacio
        esperando = True
        while esperando:
            ret, frame = cap.read()
            if not ret:
                break
            frame = cv2.flip(frame, 1)
            cv2.putText(frame, f"Palabra: {palabra} ({secuencia_num - existentes + 1}/{SECUENCIAS_POR_PALABRA})",
                        (30, 50), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 255, 0), 3)
            cv2.putText(frame, "ESPACIO para grabar, Q para salir",
                        (30, 100), cv2.FONT_HERSHEY_SIMPLEX, 0.8, (255, 255, 0), 2)
            cv2.imshow("Recolector de movimientos", frame)
            key = cv2.waitKey(1) & 0xFF
            if key == ord(' '):
                esperando = False
            if key == ord('q'):
                cap.release()
                cv2.destroyAllWindows()
                exit()

        # Grabar la secuencia de frames
        buffer = []
        while len(buffer) < FRAMES_POR_SECUENCIA:
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
                # Si se pierde la mano, repetimos el último frame válido
                if buffer:
                    buffer.append(buffer[-1])

            cv2.putText(frame, f"Grabando {len(buffer)}/{FRAMES_POR_SECUENCIA}",
                        (30, 50), cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 0, 255), 3)
            cv2.imshow("Recolector de movimientos", frame)
            cv2.waitKey(1)

        if len(buffer) == FRAMES_POR_SECUENCIA:
            ruta = os.path.join(CARPETA_DATOS, palabra, f"seq_{secuencia_num}.npy")
            np.save(ruta, np.array(buffer))
            print(f"Guardada: {ruta}")
            secuencia_num += 1

cap.release()
cv2.destroyAllWindows()
print("¡Recolección completa!")