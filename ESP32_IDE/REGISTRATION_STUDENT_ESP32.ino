#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>

// RFID and SPI pins
#define SS_PIN  4
#define RST_PIN 5
MFRC522 mfrc522(SS_PIN, RST_PIN);

// WiFi and server details
const char *ssid = "RA2414";
const char *password = "Shahidah77";
const char* server_url = "http://192.168.127.154/rfidattendance/getdata2.php";

// LED and buzzer pins
#define LED_RED 25    // GPIO pin for red LED
#define LED_GREEN 27  // GPIO pin for green LED
#define LED_BLUE 26   // GPIO pin for blue LED
#define BUZZER_PIN 14 // GPIO pin for buzzer

// Variables
String OldCardID = "";
unsigned long previousMillis2 = 0;

// Device token (unique for this device)
const char* device_token = "a3645126b1e08f54"; // Replace with your single device token

void setup() {
  Serial.begin(115200);
  SPI.begin();  
  mfrc522.PCD_Init(); 

  // Initialize LEDs and buzzer
  pinMode(LED_RED, OUTPUT);
  pinMode(LED_GREEN, OUTPUT);
  pinMode(LED_BLUE, OUTPUT); // Initialize blue LED
  pinMode(BUZZER_PIN, OUTPUT);
  
  connectToWiFi();

  Serial.println("MJII RFID EXIT ENTRY");
}

void loop() {
  // Check WiFi connection status
  if (WiFi.status() != WL_CONNECTED) {
    // Blink all LEDs continuously when disconnected
    digitalWrite(LED_RED, HIGH);
    digitalWrite(LED_GREEN, HIGH);
    digitalWrite(LED_BLUE, HIGH);
    delay(250);
    digitalWrite(LED_RED, LOW);
    digitalWrite(LED_GREEN, LOW);
    digitalWrite(LED_BLUE, LOW);
    delay(250);
    return; // Exit the loop and wait for the next cycle
  }

  // Reset the LED state if connected
  digitalWrite(LED_RED, LOW);   // Ensure red LED is off when connected
  digitalWrite(LED_GREEN, LOW); // Ensure green LED is off when connected
  digitalWrite(LED_BLUE, LOW);  // Ensure blue LED is off when connected

  if (millis() - previousMillis2 >= 15000) {
    previousMillis2 = millis();
    OldCardID = "";
  }

  // Check for new card
  if (!mfrc522.PICC_IsNewCardPresent()) {
    return;
  }

  if (!mfrc522.PICC_ReadCardSerial()) {
    return;
  }

  String CardID = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    CardID += String(mfrc522.uid.uidByte[i], HEX);
  }

  if (CardID == OldCardID) {
    return;
  } else {
    OldCardID = CardID;
  }

  // Send Card ID to server
  SendCardID(CardID);
}

void SendCardID(String Card_uid) {
  Serial.println("Sending the Card ID");
  if (WiFi.isConnected()) {
    HTTPClient http;
    String getData = "?card_uid=" + Card_uid + "&device_token=" + device_token;
    String Link = server_url + getData;
    http.begin(Link);
    
    int httpCode = http.GET();
    String payload = http.getString();

    Serial.print("HTTP Code: ");
    Serial.println(httpCode);
    Serial.print("Card ID: ");
    Serial.println(Card_uid);
    Serial.print("Response: ");
    Serial.println(payload);

    if (httpCode == 200) {
      if (payload == "successful") {
        displayMessage("New Student Card", "");
        digitalWrite(LED_BLUE, HIGH); // Blue LED for new card
        delay(2000);
        digitalWrite(LED_BLUE, LOW);

      } else if (payload == "available") {
        displayMessage("Done Register", "");
        digitalWrite(LED_GREEN, HIGH); // Green LED for registered card
        digitalWrite(BUZZER_PIN, HIGH); // Buzzer ON
        delay(1000);
        digitalWrite(LED_GREEN, LOW);
        digitalWrite(BUZZER_PIN, LOW); // Buzzer OFF
      } else {


        displayMessage("Card Detection Failed", "");
        digitalWrite(LED_RED, HIGH); // Red LED for known card
        digitalWrite(BUZZER_PIN, HIGH); // Buzzer ON

        // Wait for 2 seconds
        delay(2000);

        digitalWrite(LED_RED, LOW); // Turn off red LED
        digitalWrite(BUZZER_PIN, LOW); // Buzzer OFF
      }
    }

    http.end();
  }
}

void displayMessage(String title, String message) {
  // Print message to Serial Monitor instead of LCD
  Serial.println(title);
  if (message.length() > 0) {
    Serial.println(message);
  }
}

void connectToWiFi() {
  WiFi.mode(WIFI_OFF);
  delay(1000);
  WiFi.mode(WIFI_STA);
  Serial.print("Connecting to ");
  Serial.println(ssid);
  
  WiFi.begin(ssid, password);

  // Blink all LEDs while connecting to WiFi
  while (WiFi.status() != WL_CONNECTED) {
    digitalWrite(LED_RED, HIGH);
    digitalWrite(LED_GREEN, HIGH);
    digitalWrite(LED_BLUE, HIGH);
    delay(250);
    digitalWrite(LED_RED, LOW);
    digitalWrite(LED_GREEN, LOW);
    digitalWrite(LED_BLUE, LOW);
    delay(250);
    Serial.print(".");
  }

  // Once connected, turn off all LEDs and turn on green LED for 3 seconds
  Serial.println("\nConnected to WiFi");
  digitalWrite(LED_RED, LOW); // Ensure red LED is off
  digitalWrite(LED_GREEN, LOW);
  digitalWrite(LED_BLUE, HIGH); // Turn on green LED
  delay(3000);                   // Wait for 3 seconds
  digitalWrite(LED_BLUE, LOW);  // Turn off green LED
}
