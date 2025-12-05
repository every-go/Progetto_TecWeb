= Analisi dei Requisiti

#text()[
  Il sito web deve offrire un'esperienza utente semplice e intuitiva, permettendo agli utenti di navigare facilmente tra le varie sezioni del sito. \
]

== Categorie utenti
#text()[
  Il sito deve supportare due categorie principali di utenti:
+ Utenti reali
+ Utenti fantasy
Entrambe le categorie hanno le stesse identiche funzionalità, quindi nessuna categoria è avvantaggiata rispetto all'altra. \
Inoltre, esiste l'utente admin, il quale ha la possibilità di aggiungere, modificare o eliminare gli animali presenti nel sito, in piena linea con il principio CRUD. \
Un animale, se adottato, scompare dalla lista degli utenti ma rimane visualizzabile dall'admin e viene segnato come adottato. \
]

== Funzionalità principali
#text()[
  Per comodità, divideremo in 3 categorie la seguente tabella per indicare le funzionalità.
+ Utente non autenticato: Utente
+ Utente autenticato: Logged-in
+ Amministratore: Admin
Da notare che tutte le funzionalità dell'utente non autenticato sono disponibili anche per l'utente autenticato e per l'amministratore. \
Le funzionalità dell'utente "Logged-in" sono disponibili anche per l'amministratore. \
]
#figure(
  caption: [Schema delle funzionalità principali del sito web 
  ]
)[
  #table(
    columns: (0.2fr, 1fr),
    [Utente], [Visualizzare gli animali disponibili per l'adozione],
    [Utente], [Visualizzare i dettagli di un animale specifico],
    [Utente], [Effettuare il login],
    [Utente], [Visualizzare le informazioni su come adottare un animale, sui suoi bisogni generali],
    [Utente], [Visualizzare la pagina sulle informazioni di chi gestisce "Casa Otium"],
    [Utente], [Ricercare un animale specifico per tipo animale o per filtri (es. regione, se reale o fantasy, ecc.)],
    [Logged-in], [Effettuare il logout],
    [Logged-in], [Aggiungere o rimuovere animali dalla lista dei preferiti],
    [Logged-in], [Visualizzare la lista dei preferiti],
    [Admin], [Aggiungere un nuovo animale],
    [Admin], [Modificare le informazioni di un animale esistente],
    [Admin], [Eliminare un animale esistente],
    [Tutti], [Design responsive e accessibile]
  )
]

== Localizzazione
#text()[
  Il sito web è studiato per essere disponibile solamente in Italia, quindi l'unica lingua supportata è l'italiano. \
  Per questo motivo, non esistono altre versioni a differenza dei siti globalizzati (es. Amazon, eBay, ecc.). \
]