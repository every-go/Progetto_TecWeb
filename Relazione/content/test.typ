= Test

I test effettuati durante lo svolgimento del progetto sono molteplici.

Il principale è la visualizzazione dell'andamento dell'interfaccia durante la scrittura codice, ispezionando anche col browser per vedere gli eventuali problemi, questo sia da computer che da smartphone per verificare il front-end mobile.

Un altro test effettuato molto spesso è l'utilizzo di NVDA alla fine della scrittura di una pagina, in modo da permetterci di capire come sta proseguendo la scrittura in termini di accessibilità tramite screen reader.

Uno dei test effettuati, menzionati nella @colore, è il calcolo dei contrasti.

Un altro test effettuato, non avendo TotalValidator ufficiale a casa, è l'utilizzo del sito #link("https://validator.w3.org")[https://validator.w3.org/] per effettuare dei test sulle pagine html e #link("https://jigsaw.w3.org/css-validator/")[https://jigsaw.w3.org/css-validator/] per i test sul CSS.

Questo è stato molto utile per trovare rapidamente errori banali e non accumulare debito tecnico nel corso del progetto.


Per tutelarci da SQL Injection e Script malevoli abbiamo fatto delle occorrenze particolari nel codice PHP.
Chiaramente quando ce n'era la possibilità TotalValidator lo abbiamo utilizzato in laboratorio per testare se sono stati riscontrati problemi non trovati dai siti appena menzionati. Infatti l'ultimo test effettuato è stato il giorno prima della consegna per essere sicuri della bontà della validazione.
Total validator ha segnalato solo un possibile errore legato a un link senza testo, il gruppo ha deciso di conformarsi alla convenzione esterna che dice che il logo è un link alla home, questo è stato nascosto a navigazione con tab e screen reader in quanto ridondante con l'immediatamente successivo link alla home del menù principale del sito.

Per la SQL Injection abbiamo inserito gli statement (?) in ogni query. Ad esempio SELECT id FROM animali WHERE id = ? e non SELECT id FROM animali WHERE id = {id}.

Per proteggerci dagli script malevoli abbiamo utilizzato htmlspecialchars e innerHTML nei punti opportuni.

Per quanto riguarda velocità di caricamento e SEO abbiamo usato lighthouse (i risultati sono consultabili in appendice @test_lh)