import cv2
import mediapipe as mp
from mediapipe.tasks import python
from mediapipe.tasks.python import vision
from mediapipe.tasks.python.vision import HandLandmarker
import joblib
import numpy as np

MODELO = r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON\hand_landmarker.task'
MODELO_LSC = r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON\modelo_lsc.pkl'

# Cargar modelo entrenado
modelo = joblib.load(MODELO_LSC)

base_options = mp.tasks.BaseOptions(model_asset_path=MODELO)
options = vision.HandLandmarkerOptions(
    base_options=base_options,
    num_hands=1,
    min_hand_detection_confidence=0.7,
    min_tracking_confidence=0.7
)
detector = HandLandmarker.create_from_options(options)

cap = cv2.VideoCapture(0)
print("Detección en tiempo real iniciada. Presiona Q para salir.")

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

        # Dibujar puntos
        for lm in landmarks:
            x, y = int(lm.x * w), int(lm.y * h)
            cv2.circle(frame, (x, y), 5, (0, 255, 0), -1)

        # Predecir letra
        xs = [lm.x for lm in landmarks]
        ys = [lm.y for lm in landmarks]
        datos = np.array(xs + ys).reshape(1, -1)
        letra = modelo.predict(datos)[0]
        confianza = modelo.predict_proba(datos).max() * 100

        # Mostrar en pantalla
        cv2.putText(frame, f"Letra: {letra}", (30, 60),
                    cv2.FONT_HERSHEY_SIMPLEX, 2, (0, 255, 0), 4)
        cv2.putText(frame, f"Confianza: {confianza:.0f}%", (30, 120),
                    cv2.FONT_HERSHEY_SIMPLEX, 0.9, (255, 255, 0), 2)
    else:
        cv2.putText(frame, "Muestra tu mano", (30, 60),
                    cv2.FONT_HERSHEY_SIMPLEX, 1, (0, 100, 255), 2)

    cv2.imshow("Detector LSC", frame)

    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows() 