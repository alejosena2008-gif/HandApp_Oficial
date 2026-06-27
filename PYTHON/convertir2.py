import joblib
from skl2onnx import convert_sklearn
from skl2onnx.common.data_types import FloatTensorType

modelo = joblib.load(r'C:\Users\ACER\Desktop\Prototipo HandApp\PYTHON\modelo_lsc.pkl')
initial_type = [('float_input', FloatTensorType([None, 42]))]
opciones = {'zipmap': False}
modelo_onnx = convert_sklearn(modelo, initial_types=initial_type, options=opciones)
with open(r'C:\Users\ACER\Desktop\Prototipo HandApp\JS\modelo_lsc.onnx', 'wb') as f:
    f.write(modelo_onnx.SerializeToString())
print('Listo')