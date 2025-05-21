# Din Digitala Bovärd
## Självständigt examensarbete
### Högskolaexamen i datateknik med inriktning Webbutveckling

## Sammanfattning
Målet med projektet har varit att skapa en webbapplikation för bovärdar. Först planerades projektet efter MOSCOW-metoden för att skapa en minsta produkt som skapar värde. Sedan skapades designskisser och ER-diagram för produkten som fick namnet ”Din Digitala Bovärd”. Applikationen använder en uppdelad lösning där backend skapades i PHP-ramverket Drupal. Backend innehåller en blandning av Drupals inbyggda funktioner, tilläggsmoduler samt egenskapat PHP-kod. Den grafiska gränssnittet skapades i Vue och programmerades med TypeScript samt stylades med Tailwind. Vue-applikationen använder en REST API-lösning för att ansluta till Drupal. Metoden för säkerhet är OAuth2. Resultatet blev en webbapplikation där bovärdar kan registrera konton, lägga till egna fastigheter samt bostäder, manuell och automatisk fakturering via e-post med PDF-fakturor och ett system för att hantera felanmälningar från boende. Boende kan även logga in för att se aktuell information från bovärden samt skicka in en felanmälan. Integritet är viktigt och därför lägger applikationen stor vikt vid att personuppgifter hanteras varsamt och följer GDPR. Den är även tillgänglighetsanpassad för att fler ska få åtkomst till tjänsten. Applikationen innehåller en tillgänglighetsredogörelse och följer i hög grad DOS-lagens riktlinjer.

Nyckelord: PHP, Drupal, Vue, Tailwind, WCAG, Tillgänglighet, GDPR, REST API.






## Reständpunkter

### Användare
|Metod  |Ändpunkt                         |Syfte                 |Parametrar                                                                                               |Svar                                  |
|-------|---------------------------------|----------------------|---------------------------------------------------------------------------------------------------------|--------------------------------------|
|POST   |/oauth/token                     |Loggar in             |client_id: specifikt id, grant_type: password, client_secret: specifik hemlighet, användarnamn, lösenord |Bearer-token                          |
|POST   |/user/register?_format=json'     |Registrerar konto     |Användarnamn, lösenord                                                                                   |Status                                |

### Fastigheter
|Metod  |Ändpunkt                       |Syfte                    |Parametrar                                                                                     |Svar                                  |
|-------|-------------------------------|-------------------------|-----------------------------------------------------------------------------------------------|--------------------------------------|
|GET    |/api/realestate/ok             |Hämtar alla fastigheter  |                                                                                               |Fastighetsobjekt                      |
|POST   |/api/realestate                |Skapar en fastighet      |Fastighetsobjekt                                                                               |Status                                |
|PATCH  |/api/realestate/{id}           |Uppdaterar en fastighet  |Fastighetsobjekt                                                                               |Fastighetsobjekt                      |
|DELETE |/api/realestate/{id}           |Tar bort en fastighet    |                                                                                               |Status                                |

### Bostäder
|Metod  |Ändpunkt                       |Syfte                    |Parametrar                                                                             |Svar                                  |
|-------|-------------------------------|-------------------------|---------------------------------------------------------------------------------------|--------------------------------------|
|GET    |/api/accommodation/ok          |Hämtar alla bostäder     |                                                                                       |Bostadsobjekt                         |
|POST   |/api/accommodation             |Skapar en bostad         |Bostadsobjekt                                                                          |Status                                |
|PATCH  |/api/accommodation/{id}        |Uppdaterar en bostad     |Bostadsobjekt                                                                          |Bostadssobjekt                        |
|DELETE |/api/accommodation/{id}        |Tar bort en bostad       |                                                                                       |Status                                |

### Felanmälan
|Metod  |Ändpunkt                       |Syfte                               |Parametrar                                                                  |Svar                                  |
|-------|-------------------------------|------------------------------------|----------------------------------------------------------------------------|--------------------------------------|
|GET    |/api/error_report/id           |Hämtar alla felanmälningar          |                                                                            |Felanmälansobjekt                     |
|PATCH  |/api/error_report/{id}         |Uppdaterar status för felanmälan    |Status för felanmälan                                                       |Felanmälansobjekt                     |
|DELETE |/api/error_report/{id}         |Tar bort en felanmälan              |                                                                            |Status                                |

### Information
|Metod  |Ändpunkt                       |Syfte                               |Parametrar                                                                  |Svar                                  |
|-------|-------------------------------|------------------------------------|----------------------------------------------------------------------------|--------------------------------------|
|GET    |/api/information/ok            |Hämtar fastighetsinformation        |                                                                            |Informationsobjekt                    |
|POST   |/api/information               |Skapar en fastighetsinformation     |Informationsobjekt                                                          |Status                                |
|PATCH  |/api/information/{id}          |Uppdaterar fastighetsinformation    |Informationsobjekt                                                          |Informationsobjekt                    |
|DELETE |/api/information/{id}          |Tar bort fastighetsinformation      |                                                                            |Status                                |

### Fakturor
|Metod  |Ändpunkt                         |Syfte                               |Parametrar                                                                  |Svar                                  |
|-------|---------------------------------|------------------------------------|----------------------------------------------------------------------------|--------------------------------------|
|GET    |/api/invoice/ok                  |Hämtar alla fakturor                |                                                                            |Informationsobjekt                    |
|POST   |/api/invoice/${accommodation.id} |Skapar och mejlar en faktura        |                                                                            |Status                                |
|POST   |/api/resend_invoice/{invoice_id} |Mejlar en faktura påminnelse       |                                                                            |Informationsobjekt                    |
|PATCH  |/api/invoice/{id}                |Uppdaterar fakturastatus            |Status för fakturan                                                         |Status                                |
|DELETE |/api/invoice/{id}                |Ta bort en faktura                  |                                                                            |Status                                |

### Publika ändpunkter för boende
|Metod  |Ändpunkt                       |Syfte                               |Parametrar                                                                  |Svar                                  |
|-------|-------------------------------|------------------------------------|----------------------------------------------------------------------------|--------------------------------------|
|GET    |/api/tenant                    |Hämtar fastighetsinformation        |Fastighetsid, e-postadress                                                  |Status                                |
|POST   |/api/error_report              |Skapar felrapport                   |Felanmälansobjekt                                                           |Status                                |
|GET    |/api/realestate_list           |Hämtar en lista med fastighetsnamn  |                                                                            |Status                                |
|GET    |/api/tenant/{email}            |Bostäder där email är registrerad   |                                                                            |Status                                |
