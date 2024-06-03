import requests
import random
import time

url = 'https://controltemperaturas.online/files/servicio.php'

def generar_temperatura():
    return round(random.uniform(20, 22), 2)

heladera_id = 1

# envio de datos
while True:
    
    temperatura = generar_temperatura()
    
    payload = {'temperatureC': temperatura, 'heladera_id': heladera_id}
    
    try:
        # Realizar la solicitud POST
        response = requests.post(url, data=payload)
        
        if response.status_code == 200:
            print(f'Solicitud POST exitosa: Temperatura = {temperatura} °C, ID de la heladera = {heladera_id}')
        else:
            print(f'Error en la solicitud POST: Código de respuesta {response.status_code}')
    
    except Exception as e:
        print(f'Error en la solicitud POST: {e}')
    
    time.sleep(60)
