import cv2
import mediapipe as mp
from mediapipe.tasks import python
from mediapipe.tasks.python import vision
from mediapipe.tasks.python.vision import HandLandmarker
import csv
import os

MODELO = r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON\hand_landmarker.task'
ARCHIVO_DATOS = r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON\datos_lsc.csv'

# Abecedario LSC
LETRAS = list('ABCDEFGHIJKLMNOPQRSTUVWXYZ')
MUESTRAS_POR_LETRA = 50  # cuántas fotos por letra

base_options = mp.tasks.BaseOptions(model_asset_path=MODELO)
options = vision.HandLandmarkerOptions(
    base_options=base_options,
    num_hands=1,
    min_hand_detection_confidence=0.7,
    min_tracking_confidence=0.7
)
detector = HandLandmarker.create_from_options(options)

# Crear archivo CSV si no existe
if not os.path.exists(ARCHIVO_DATOS):
    with open(ARCHIVO_DATOS, 'w', newline='') as f:
        writer = csv.writer(f)
        encabezado = ['letra'] + [f'x{i}' for i in range(21)] + [f'y{i}' for i in range(21)]
        writer.writerow(encabezado)

cap = cv2.VideoCapture(0)
letra_actual = 0
conteo = 0
recolectando = False

print(f"Presiona ESPACIO para empezar a recolectar la letra: {LETRAS[letra_actual]}")
print("Presiona Q para salir")

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

        if recolectando:
            # Guardar datos
            xs = [lm.x for lm in landmarks]
            ys = [lm.y for lm in landmarks]
            fila = [LETRAS[letra_actual]] + xs + ys

            with open(ARCHIVO_DATOS, 'a', newline='') as f:
                writer = csv.writer(f)
                writer.writerow(fila)

            conteo += 1

            if conteo >= MUESTRAS_POR_LETRA:
                recolectando = False
                conteo = 0
                letra_actual += 1

                if letra_actual >= len(LETRAS):
                    cv2.putText(frame, "¡Datos completos!", (30, 50),
                                cv2.FONT_HERSHEY_SIMPLEX, 1.2, (0, 255, 0), 3)
                    cv2.imshow("Recolector LSC", frame)
                    cv2.waitKey(2000)
                    break

                print(f"Presiona ESPACIO para la letra: {LETRAS[letra_actual]}")

    # Mostrar info en pantalla
    letra = LETRAS[letra_actual] if letra_actual < len(LETRAS) else "Listo"
    estado = f"Recolectando: {conteo}/{MUESTRAS_POR_LETRA}" if recolectando else "Presiona ESPACIO"
    cv2.putText(frame, f"Letra: {letra}", (30, 50),
                cv2.FONT_HERSHEY_SIMPLEX, 1.2, (0, 255, 0), 3)
    cv2.putText(frame, estado, (30, 100),
                cv2.FONT_HERSHEY_SIMPLEX, 0.8, (255, 255, 0), 2)

    cv2.imshow("Recolector LSC", frame)

    key = cv2.waitKey(1) & 0xFF
    if key == ord(' '):
        recolectando = True
        print(f"Recolectando letra {LETRAS[letra_actual]}...")
    if key == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()