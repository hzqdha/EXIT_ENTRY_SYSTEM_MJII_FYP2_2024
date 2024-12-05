#include <EEPROM.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <time.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <SPI.h>
#include <MFRC522.h>

// RFID and SPI pins
#define SS_PIN  4
#define RST_PIN 5
MFRC522 mfrc522(SS_PIN, RST_PIN);

// WiFi and server details
const char *ssid = "RA2414";
const char *password = "Shahidah77";
const char* server_url = "http://192.168.127.154/rfidattendance/getdata.php";

// Timezone settings
int timezone = 8 * 3600;   // Malaysia is UTC+8, convert hours to seconds
int time_dst = 0;

// LCD configuration
LiquidCrystal_I2C lcd(0x27, 20, 4);  // 20x4 LCD

// LED pins
#define LED_RED 25    // GPIO pin for red LED (changed to D25)
#define LED_GREEN 27  // GPIO pin for green LED (changed to D27)
#define LED_BLUE 26   // GPIO pin for blue LED (changed to D26)

// Buzzer pin
#define BUZZER_PIN 14  // GPIO pin for buzzer (changed to D14)

// Variables
String OldCardID = "";
unsigned long previousMillis1 = 0;
unsigned long previousMillis2 = 0;

// Device token (unique for this device)
const char* device_token = "a3645126b1e08f54"; // Replace with your single device token

void setup() {
  Serial.begin(115200);
  SPI.begin();  
  mfrc522.PCD_Init(); 

  // Initialize LCD
  lcd.init();
  lcd.backlight();

  // Initialize LEDs
  pinMode(LED_RED, OUTPUT);
  pinMode(LED_GREEN, OUTPUT);
  pinMode(LED_BLUE, OUTPUT);

  // Initialize buzzer
  pinMode(BUZZER_PIN, OUTPUT);

  Serial.print("Device Token: ");
  Serial.println(device_token);

  connectToWiFi();
  configTime(timezone, time_dst, "pool.ntp.org", "time.nist.gov");

  lcd.setCursor(0, 0);
  lcd.print("MJII RFID EXIT ENTRY");
  lcd.setCursor(0, 3);

  // Instructions for setting DEVICE_ID
  Serial.println("To change DEVICE_ID, update the device_token variable in the code.");
}

void loop() {
  if (millis() - previousMillis1 >= 1000) {
    previousMillis1 = millis();

    time_t now = time(nullptr);
    struct tm* p_tm = localtime(&now);
    int currentHour = p_tm->tm_hour;

    Serial.print("Current Hour: ");
    Serial.println(currentHour);

    lcd.setCursor(0, 1);
    lcd.print(centerAlign(String(p_tm->tm_hour < 10 ? "0" : "") + String(p_tm->tm_hour) + ":" +
                          String(p_tm->tm_min < 10 ? "0" : "") + String(p_tm->tm_min) + ":" +
                          String(p_tm->tm_sec < 10 ? "0" : "") + String(p_tm->tm_sec), 20));

    lcd.setCursor(0, 2);
    lcd.print(centerAlign(String(p_tm->tm_mday < 10 ? "0" : "") + String(p_tm->tm_mday) + "/" +
                          String(p_tm->tm_mon + 1 < 10 ? "0" : "") + String(p_tm->tm_mon + 1) + "/" +
                          String(p_tm->tm_year + 1900), 20));
  }

  if (millis() - previousMillis2 >= 15000) {
    previousMillis2 = millis();
    OldCardID = "";
  }

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

  SendCardID(CardID);
  delay(1000);
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("MJII RFID EXIT ENTRY");
  lcd.setCursor(0, 3);
  lcd.print(centerAlign("TOUCH CARD HERE", 20)); // Centered message
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

    time_t now = time(nullptr);
    struct tm* p_tm = localtime(&now);
    int currentHour = p_tm->tm_hour;

    if (httpCode == 200) {
      if (payload.startsWith("login")) {
        String user_name = payload.substring(5);
        displayMessage("Good Bye", user_name);

         // Sound buzzer when card is detected
        tone(BUZZER_PIN, 1000, 500);  // Buzzer sound at 1000 Hz for 500ms

        digitalWrite(LED_BLUE, HIGH); // Blue LED high for entry
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print(centerAlign("Successful Exit", 20));
        lcd.setCursor(0, 1);
        lcd.print(centerAlign("Good Bye", 20));
        delay(2000);
        digitalWrite(LED_BLUE, LOW);

      } else if (payload.startsWith("logout")) {
        String user_name = payload.substring(6);
        displayMessage("Welcome", user_name);

         // Sound buzzer when card is detected
         tone(BUZZER_PIN, 1000, 500);  // Buzzer sound at 1000 Hz for 500ms

        digitalWrite(LED_GREEN, HIGH); // Green LED high for exit
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print(centerAlign("Successful Entry", 20));
        lcd.setCursor(0, 1);
        lcd.print(centerAlign("Welcome", 20));
        delay(2000);
        digitalWrite(LED_GREEN, LOW);
      
      } else if (payload == "available") {
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print(centerAlign("Please Wait...", 20));
        lcd.setCursor(0, 1);
        lcd.print(centerAlign("System in Progress", 20));
        digitalWrite(LED_GREEN, HIGH); // Green LED high for free card read
        digitalWrite(LED_RED, HIGH); // Red LED high for free card read
        digitalWrite(LED_BLUE, HIGH); // Blue LED high for free card read
        delay(2000);
        tone(BUZZER_PIN, 1000, 1000); // Buzz for 1000ms
        digitalWrite(LED_GREEN, LOW);
        digitalWrite(LED_RED, LOW);
        digitalWrite(LED_BLUE, LOW);

      } else if (currentHour >= 22 || currentHour < 7) { // Late entry between 10 PM and 7 AM
        Serial.println("Late entry detected.");
        lcd.clear();
        lcd.setCursor(0, 0);
        lcd.print(centerAlign("It is past 10 PM", 20));
        lcd.setCursor(0, 1);
        lcd.print(centerAlign("Late entry recorded", 20));
        digitalWrite(LED_RED, HIGH); 
        
        // Blinking and buzzing for 3 seconds
        for (int i = 0; i < 6; i++) {
          tone(BUZZER_PIN, 1000);  // Buzzer sound
          delay(250);
          noTone(BUZZER_PIN);
          digitalWrite(LED_RED, LOW);
          
          delay(250);
          digitalWrite(LED_RED, HIGH);
        }

        delay(2000);
        digitalWrite(LED_RED, LOW);
        
      } else {
        displayMessage(centerAlign("No Register Card", 20), "");
        digitalWrite(LED_RED, HIGH); // Red LED high for unknown response
        
        // Buzz in "blinking-blinking" rhythm
        for (int i = 0; i < 5; i++) {
          tone(BUZZER_PIN, 200);  
          delay(200);            
          noTone(BUZZER_PIN);    
          delay(200);            
        }
        
        delay(2000);  
        digitalWrite(LED_RED, LOW);
      }

    }

    http.end();
  }
}

void displayEntryMessage(String user_name) {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(centerAlign("Successful Entry", 20));
  lcd.setCursor(0, 1);
  lcd.print(centerAlign(user_name, 20)); // Papar nama pelajar
  lcd.setCursor(0, 2);
  lcd.print(centerAlign("Welcome", 20)); // Papar "Welcome"
  delay(2000); // Tunjukkan mesej selama 2 saat
}

void displayExitMessage(String user_name) {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(centerAlign("Successful Exit", 20));
  lcd.setCursor(0, 1);
  lcd.print(centerAlign(user_name, 20)); // Papar nama pelajar
  lcd.setCursor(0, 2);
  lcd.print(centerAlign("Good Bye", 20)); // Papar "Good Bye"
  delay(2000); // Tunjukkan mesej selama 2 saat
}

void displayLateEntryMessage(String user_name) {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(centerAlign("It is past 10 PM", 20));
  lcd.setCursor(0, 1);
  lcd.print(centerAlign(user_name, 20)); // Papar nama pelajar
  lcd.setCursor(0, 2);
  lcd.print(centerAlign("Late entry recorded", 20)); // Papar mesej lewat
  delay(2000); // Tunjukkan mesej selama 2 saat
}

void displayMessage(String title, String message) {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(title);
  lcd.setCursor(0, 1);
  lcd.print(message);
}

void connectToWiFi() {
  WiFi.begin(ssid, password);
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(centerAlign("Connecting to WiFi", 20));

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connected.");
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());

  // Show success message on LCD with proper centering
  lcd.clear();
  
  // Center-align "WiFi Connected"
  lcd.setCursor(4, 0);  // 5 spaces from the left to center on a 20-char line
  lcd.print("Wifi Connect");

  // Center-align IP address
  String ipAddress = WiFi.localIP().toString();
  int padding = (20 - (ipAddress.length() + 4)) / 2; // Calculate padding for "IP: xxx.xxx.xxx.xxx"
  lcd.setCursor(padding, 1);  // Add padding to center the IP address
  lcd.print("IP:" + ipAddress);

  delay(4000); // Display for 3 seconds before clearing
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print("MJII RFID EXIT ENTRY");
}
  

String centerAlign(String str, int width) {
  int padding = (width - str.length()) / 2;
  String spaces = "";
  for (int i = 0; i < padding; i++) {
    spaces += " ";
  }
  return spaces + str;
}
