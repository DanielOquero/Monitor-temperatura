#include <WiFi.h>
#include <HTTPClient.h>
#include <OneWire.h>
#include <DallasTemperature.h>

#define ONE_WIRE_BUS 4 // Pin donde está conectado el sensor DS18B20


const char* ssid = "MIRANDA";
const char* password = "96011193";

const int heladera_id = 1;
const char* serverUrl = "https://controltemperaturas.online/files/servicio.php"; // URL Servicio


OneWire oneWire(ONE_WIRE_BUS);
DallasTemperature sensors(&oneWire);

unsigned long lastHttpRequestTime = 0;
const unsigned long httpRequestInterval = 60000;  // 60 segundos
const int maxHttpRequestAttempts = 10;             // intentos de solicitud HTTP

void setup() {
  Serial.begin(115200);

  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Conectando a WiFi...");
  }
  Serial.println("Conectado a la red WiFi");

  sensors.begin();
}

void loop() {
  // Solicitar temperatura del sensor
  sensors.requestTemperatures();
  float temperatureC = sensors.getTempCByIndex(0);

  // Imprimir la temperatura en el puerto serie cada segundo
  Serial.print("Temperatura: ");
  Serial.print(temperatureC);
  Serial.println(" °C");

  // Enviar la temperatura al servidor
  int attemptCount = 0;
  while (attemptCount < maxHttpRequestAttempts) {
    // Verificar la conexión Wi-Fi
    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http;

      // Iniciar conexión HTTP
      http.begin(serverUrl);
      http.addHeader("Content-Type", "application/x-www-form-urlencoded");

      String httpRequestData = "temperatureC=" + String(temperatureC) + "&heladera_id=" + String(heladera_id);

      // Enviar solicitud POST
      int httpResponseCode = http.POST(httpRequestData);

      // Verificar el código de respuesta del servidor
      if (httpResponseCode > 0) {
        String response = http.getString();
        Serial.println(httpResponseCode);
        Serial.println(response);
        break; // Salir del bucle de intentos si la solicitud fue exitosa
      } else {
        Serial.print("Error en la solicitud POST (Intento ");
        Serial.print(attemptCount + 1);
        Serial.print("): ");
        Serial.println(httpResponseCode);
      }

      // Finalizar la conexión HTTP
      http.end();
    } else {
      Serial.println("No conectado a la red WiFi");
    }

    // Incrementar el contador de intentos y esperar antes de volver a intentarlo
    attemptCount++;
    delay(1000); // Esperar 1 segundo antes de realizar otro intento
  }

  // Esperar hasta el próximo intervalo de solicitud
  unsigned long currentMillis = millis();
  while (currentMillis - lastHttpRequestTime < httpRequestInterval) {
    delay(1000); // Esperar 1 segundo
    currentMillis = millis();
  }
  lastHttpRequestTime = currentMillis;
}
