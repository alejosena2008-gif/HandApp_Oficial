import os
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import LabelEncoder
import tensorflow as tf
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import LSTM, Dense, Dropout
from tensorflow.keras.utils import to_categorical

# ---------------------------------------------------------
CARPETA_BASE = r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON'
CARPETA_DATOS = os.path.join(CARPETA_BASE, 'datos_movimientos')
MODELO_SALIDA = os.path.join(CARPETA_BASE, 'modelo_movimientos.h5')
CLASES_SALIDA = os.path.join(CARPETA_BASE, 'clases_movimientos.npy')
# ---------------------------------------------------------

X, y = [], []
palabras = sorted(os.listdir(CARPETA_DATOS))

for palabra in palabras:
    carpeta_palabra = os.path.join(CARPETA_DATOS, palabra)
    for archivo in os.listdir(carpeta_palabra):
        secuencia = np.load(os.path.join(carpeta_palabra, archivo))
        X.append(secuencia)
        y.append(palabra)

X = np.array(X)  # shape: (n_muestras, frames_por_secuencia, 42)
print(f"Datos cargados: {X.shape}")

encoder = LabelEncoder()
y_enc = encoder.fit_transform(y)
y_cat = to_categorical(y_enc)

X_train, X_test, y_train, y_test = train_test_split(
    X, y_cat, test_size=0.2, random_state=42, stratify=y_enc
)

n_frames = X.shape[1]
n_features = X.shape[2]
n_clases = y_cat.shape[1]

modelo = Sequential([
    LSTM(64, return_sequences=True, activation='tanh', input_shape=(n_frames, n_features)),
    Dropout(0.3),
    LSTM(128, return_sequences=False, activation='tanh'),
    Dropout(0.3),
    Dense(64, activation='relu'),
    Dense(n_clases, activation='softmax')
])

modelo.compile(optimizer='adam', loss='categorical_crossentropy', metrics=['accuracy'])
modelo.summary()

print("Entrenando modelo...")
modelo.fit(X_train, y_train, epochs=100, batch_size=8,
        validation_data=(X_test, y_test))

perdida, precision = modelo.evaluate(X_test, y_test)
print(f"Precisión en test: {precision * 100:.1f}%")

modelo.save(MODELO_SALIDA)
np.save(CLASES_SALIDA, encoder.classes_)
print(f"Modelo guardado en: {MODELO_SALIDA}")
print(f"Clases guardadas en: {CLASES_SALIDA} -> {list(encoder.classes_)}")