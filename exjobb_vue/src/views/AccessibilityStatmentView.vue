<script setup lang="ts"></script>

<!--Vy med tillgänglighetsredogörelse"-->
<template>
  <section class="max-w-5xl mx-auto p-2">
    <h1 class="text-3xl text-center mt-4 mb-4">Tillgänglighetsredogörelse</h1>
    <p>
      Din digitala bovärd står bakom den här webbplatsen. Vi vill att så många som möjligt ska kunna
      använda den. Det här dokumentet beskriver hur webbplats uppfyller lagen om tillgänglighet till
      digital offentlig service, eventuella kända tillgänglighetsproblem och hur du kan rapportera
      brister till oss så att vi kan åtgärda dem.
    </p>
    <h2 class="text-2xl mt-4 mb-1">Hur tillgänglig är webbplatsen?</h2>
    <p>
      Webbplatsen är i höggrad tillgänglig det finns vissa avseenden där tillgängligheten kan
      förbättras. Se avsnittet om innehåll som inte är tillgängligt nedan för mer information.
    </p>
    <h2 class="text-2xl mt-4 mb-1">Vad kan du göra om du inte kan använda delar av webbplatsen?</h2>
    <p>
      Vi uppskattar feedback där du beskriver vad som inte är tillgängligt för dig och du kan
      kontakta oss för att beskriva webbplatsens problem. Om vi kan utan särskiljt betungande arbete
      åtgärda problemet så sker det inom en rimlig tidsram. Du kan kontakta oss via e-postlänken
      längre ner på sidan och svarstiden är normalt inom 24 timmar.
    </p>
    <h2 class="text-2xl mt-4 mb-1">Rapportera brister i webbplatsens tillgänglighet</h2>
    <p>
      Vi strävar hela tiden efter att förbättra webbplatsens tillgänglighet. Om du upptäcker problem
      som inte är beskrivna på den här sidan, eller om du anser att vi inte uppfyller lagens krav,
      kontakta oss via e-postlänken längre ner på sidan så att vi får veta att problemet finns.
    </p>
    <h2 class="text-2xl mt-4 mb-1">Tillsyn</h2>
    <p>
      Myndigheten för digital förvaltning, Digg, har ansvaret för tillsyn över lagen om
      tillgänglighet till digital offentlig service. Du kan anmäla till Digg om du tycker att vår
      digitala service har brister i tillgänglighet. Du kan också anmäla till Digg om du tycker att
      vår bedömning av vad som är oskäligt betungande ska granskas, om du tycker att vår
      tillgänglighetsredogörelse har brister eller om du tycker att vi inte har hanterat din begäran
      om tillgängliggörande korrekt.
    </p>
    <h2 class="text-2xl mt-4 mb-1">Teknisk information om webbplatsens tillgänglighet</h2>
    <p>
      Den här webbplatsen omfattas inte om lagen om tillgänglighet till digital offentlig service.
      Däremot är är webbplatsen till stor del anpassad efter lagen.
    </p>
    <h2 class="text-2xl mt-4 mb-1">Innehåll som inte är tillgängligt</h2>
    <p>
      Hela webbplatsen följer HTML-standard för WCAG 2.1 på AA-nivå. Hela webbplatsen är anpassad
      för att kunna styras från enbart tangentbordet. Däremot hade navigering på sidor underlättats
      om stycken varit mer lättare att navigera med tangentbord. Även fler och mer beskrivande
      ARIA-attribut i HTML-koden hade givet en bättre upplevelse. Antal tecken per textrad längre än
      optimalt för läsbarhet när webbplatsen besöks från stora skärmar och kortare än optimalt när
      webbplatsen besökt från mobiltelefoner.
    </p>
    <h2 class="text-2xl mt-4 mb-1">Hur vi testat webbplatsen</h2>
    <p>
      Vi har gjort en intern testning av webbplatsen Din digitala bovärd. Senaste bedömningen
      gjordes den 16-05-2025. Webbplatsen publicerades den 16-05-2025. Redogörelsen uppdaterades
      senast den 16-05-2025. Hela webbplatsen är testade så att den går att navigera till och styra
      med enbart tangentbord. Kontraster av text och knappar är kontrollerad med testverktyget WAVE.
      Webbplatsen är testad och anpassad för tillgänglighet med hjälp av verktyget ”Ace it” från
      ”Use it”.
    </p>

    <a
      href="/accessibility_statement.pdf"
      download
      title="Ladda ner tillgänglighetsredogörelse"
      class="hover:shadow-md hover:rounded-md p-2 w-fit mt-6 mb-6 mx-auto block text-blue-800 hover:font-semibold"
      aria-label="Ladda ner tillgänglighetsredogörelse"
    >
      Ladda ner tillgänglighetsredogörelse
      <img
        alt="menu close logo"
        class="logo p-2 inline"
        src="@/assets/file-download.svg"
        width="55"
        height="55"
      />
    </a>
    <a
      href="mailto:din.digitala.bovard@markuswebb.se"
      title="Kontakta oss med e-post"
      class="hover:shadow-md hover:rounded-md p-2 w-fit mt-6 mb-6 mx-auto block text-blue-800 hover:font-semibold"
      aria-label="Kontakta oss med e-post"
    >
      Kontaka oss
      <img
        alt="menu close logo"
        class="logo p-2 inline"
        src="@/assets/email.svg"
        width="55"
        height="55"
      />
    </a>
  </section>
</template>
