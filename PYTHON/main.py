import cv2
import mediapipe as mp
from mediapipe.tasks import python
from mediapipe.tasks.python import vision
from mediapipe.tasks.python.vision import HandLandmarker

MODELO = r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON\hand_landmarker.task'

base_options = mp.tasks.BaseOptions(model_asset_path=MODELO)
options = vision.HandLandmarkerOptions(
    base_options=base_options,
    num_hands=1,
    min_hand_detection_confidence=0.7,
    min_tracking_confidence=0.7
)
detector = HandLandmarker.create_from_options(options)

CONEXIONES = [
    (0,1),(1,2),(2,3),(3,4),
    (0,5),(5,6),(6,7),(7,8),
    (0,9),(9,10),(10,11),(11,12),
    (0,13),(13,14),(14,15),(15,16),
    (0,17),(17,18),(18,19),(19,20)
]

def dibujar_mano(frame, landmarks, w, h):
    puntos = []
    for lm in landmarks:
        x, y = int(lm.x * w), int(lm.y * h)
        puntos.append((x, y))
        cv2.circle(frame, (x, y), 5, (0, 255, 0), -1)
    for a, b in CONEXIONES:
        cv2.line(frame, puntos[a], puntos[b], (255, 255, 255), 2)

def detectar_seña(landmarks):
    return "Mano detectada"

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

    seña = "Sin seña detectada"

    if resultado.hand_landmarks:
        landmarks = resultado.hand_landmarks[0]
        dibujar_mano(frame, landmarks, w, h)
        seña = detectar_seña(landmarks)

    cv2.putText(frame, seña, (30, 50),
                cv2.FONT_HERSHEY_SIMPLEX, 1.2, (0, 255, 0), 3)

    cv2.imshow("HandApp - Traductor LSC", frame)

    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()