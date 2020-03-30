#include <SPI.h>
#include <Ethernet.h>
#include "DHT.h"

#define DHTPIN 9 //Sensor pin
#define DHTTYPE DHT22 //Sensor type
DHT dht(DHTPIN, DHTTYPE);

byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
char server[] = "gruppe1.7332.no";

IPAddress ip(10, 0, 0, 18);
IPAddress myDns(10, 0, 0, 64);

EthernetClient client;

// Variables to measure the speed
unsigned long beginMicros, endMicros;
unsigned long byteCount = 0;
bool printWebData = true;  // set to false for better speed measurement

unsigned long timeBegin = 0;
unsigned long milliPause = 10000;

void setup() {
  // You can use Ethernet.init(pin) to configure the CS pin
  //Ethernet.init(10);  // Most Arduino shields
  //Ethernet.init(5);   // MKR ETH shield
  //Ethernet.init(0);   // Teensy 2.0
  //Ethernet.init(20);  // Teensy++ 2.0
  //Ethernet.init(15);  // ESP8266 with Adafruit Featherwing Ethernet
  //Ethernet.init(33);  // ESP32 with Adafruit Featherwing Ethernet

  // Open serial communications and wait for port to open:
  Serial.begin(9600);
  while (!Serial) {
    ; // wait for serial port to connect. Needed for native USB port only
  }

  // start the Ethernet connection:
  Serial.println("Initialize Ethernet with DHCP:");
  if (Ethernet.begin(mac) == 0) {
    Serial.println("Failed to configure Ethernet using DHCP");
    // Check for Ethernet hardware present
    if (Ethernet.hardwareStatus() == EthernetNoHardware) {
      Serial.println("Ethernet shield was not found.  Sorry, can't run without hardware. :(");
      while (true) {
        delay(1); // do nothing, no point running without Ethernet hardware
      }
    }
    if (Ethernet.linkStatus() == LinkOFF) {
      Serial.println("Ethernet cable is not connected.");
    }
    // try to congifure using IP address instead of DHCP:
    Ethernet.begin(mac, ip, myDns);
  } else {
    Serial.print("  DHCP assigned IP ");
    Serial.println(Ethernet.localIP());
  }
  // give the Ethernet shield a second to initialize:
  delay(1000);

  dht.begin();
  timeBegin = 0;
}

void loop() {

  Serial.println("Timer: " + String(timeBegin) + ", Current: " + String(millis()));
  if(timeBegin < millis()){

    float hum = dht.readHumidity(); //Læs av luftfuktighet
    float temp = dht.readTemperature(); //Læs av tæmpraturen i celsius
    float heat_index = dht.computeHeatIndex(temp, hum); //Læs av heat index (humidity og varme blanda t en) i celsius

    Serial.println("Humidity: " + String(hum));
    Serial.println("Temperature: " + String(temp));
    Serial.println("Heat Index: " + String(heat_index));
  
    sendClient(temp, hum, heat_index);
    
    timeBegin = millis() + milliPause;
  }
  readClient();

  delay(1000);
}

void sendClient(float temp, float hum, float heat_index){
  Serial.print("connecting to ");
  Serial.print(server);
  Serial.println("...");

  // if you get a connection, report back via serial:
  if (client.connect(server, 80)) {
    Serial.print("connected to ");
    Serial.println(client.remoteIP());
    // Make a HTTP request:
    client.print("GET /status/add/index.php");
    client.print("?temperature=" + String(temp));
    client.print("&humidity=" + String(hum));
    client.print("&heat_index=" + String(heat_index));
    
    client.println(" HTTP/1.1");
    client.println("Host: gruppe1.7332.no");
    client.println("Connection: close");
    client.println();
  } else {
    // if you didn't get a connection to the server:
    Serial.println("connection failed");
  }
  beginMicros = micros();
}

void readClient(){
  int len = client.available();
  if (len > 0) {
    byte buffer[80];
    if (len > 80) len = 80;
    client.read(buffer, len);
    if (printWebData) {
      Serial.write(buffer, len); // show in the serial monitor (slows some boards)
    }
    byteCount = byteCount + len;
  }

  // if the server's disconnected, stop the client:
  if (!client.connected()) {
    endMicros = micros();
    //Serial.println("disconnecting.");
    client.stop();
  }
}
