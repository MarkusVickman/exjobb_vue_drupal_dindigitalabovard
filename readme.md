# Din Digitala Bovärd
## Självständigt examensarbete
### Högskolaexamen i datateknik med inriktning Webbutveckling

## Sammanfattning
Målet med projektet har varit att skapa en webbapplikation för bovärdar. Först planerades projektet efter MOSCOW-metoden för att skapa en minsta produkt som skapar värde. Sedan skapades designskisser och ER-diagram för produkten som fick namnet ”Din Digitala Bovärd”. Applikationen använder en uppdelad lösning där backend skapades i PHP-ramverket Drupal. Backend innehåller en blandning av Drupals inbyggda funktioner, tilläggsmoduler samt egenskapat PHP-kod. Den grafiska gränssnittet skapades i Vue och programmerades med TypeScript samt stylades med Tailwind. Vue-applikationen använder en REST API-lösning för att ansluta till Drupal. Metoden för säkerhet är OAuth2. Resultatet blev en webbapplikation där bovärdar kan registrera konton, lägga till egna fastigheter samt bostäder, manuell och automatisk fakturering via e-post med PDF-fakturor och ett system för att hantera felanmälningar från boende. Boende kan även logga in för att se aktuell information från bovärden samt skicka in en felanmälan. Integritet är viktigt och därför lägger applikationen stor vikt vid att personuppgifter hanteras varsamt och följer GDPR. Den är även tillgänglighetsanpassad för att fler ska få åtkomst till tjänsten. Applikationen innehåller en tillgänglighetsredogörelse och följer i hög grad DOS-lagens riktlinjer.

Nyckelord: PHP, Drupal, Vue, Tailwind, WCAG, Tillgänglighet, GDPR, REST API.






## Reständpunkter

|Metod  |Ändpunkt           |Parametrar                         |Svar                                  |
|-------|-------------------|----------------------------------------------------------------------------|
|POST   |/oauth/token       |client_id: specifikt id, grant_type: password, client_secret: specifik hemlighet, användarnamn, lösenord             |Bearer-token                                                  |
|POST    |/user/register?_format=json'          |Användarnamn, lösenord                                                         |Status
|GET   |/api/realestate/ok      |        |Fastighetsobjekt
|POST    |/api/realestate   |Fastighetsobjekt |Status |
|PATCH |/api/realestate/${realestate.id}  |Fastighetsobjekt |Fastighetsobjekt                                             |
|DELETE |/api/realestate/${realestate.id}  | |Status                                             |