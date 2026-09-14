import joblib
import numpy as np
from skl2onnx import convert_sklearn
from skl2onnx.common.data_types import FloatTensorType

MODELO_PKL = r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON\modelo_lsc.pkl'
MODELO_ONNX = r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON\modelo_lsc.onnx'

print("Cargando modelo...")
modelo = joblib.load(MODELO_PKL)

print("Convirtiendo...")
initial_type = [('float_input', FloatTensorType([None, 42]))]
modelo_onnx = convert_sklearn(modelo, initial_types=initial_type)

with open(MODELO_ONNX, 'wb') as f:
    f.write(modelo_onnx.SerializeToString())

print(f"Modelo convertido y guardado en: {MODELO_ONNX}")