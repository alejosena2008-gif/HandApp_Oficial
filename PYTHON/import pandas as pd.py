import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score
import joblib

ARCHIVO_DATOS = r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON\datos_lsc.csv'
MODELO_SALIDA = r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON\modelo_lsc.pkl'

df = pd.read_csv(ARCHIVO_DATOS)
X = df.drop('letra', axis=1)
y = df['letra']

X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

print("Entrenando modelo...")
modelo = RandomForestClassifier(n_estimators=100, random_state=42)
modelo.fit(X_train, y_train)

precision = accuracy_score(y_test, modelo.predict(X_test))
print(f"Precisión del modelo: {precision * 100:.1f}%")

joblib.dump(modelo, MODELO_SALIDA)
print(f"Modelo guardado en: {MODELO_SALIDA}")