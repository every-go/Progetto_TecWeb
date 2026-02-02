= Analisi dei Requisiti

Il sito web deve offrire un'esperienza utente semplice e intuitiva, permettendo agli utenti di navigare facilmente tra le varie sezioni del sito.
Si rivolge a un pubblico eterogeneo.

== Funzionalità principali
Per comodità, divideremo in 3 categorie la seguente tabella per indicare le funzionalità.

+ Utente non autenticato: Utente
+ Utente autenticato: Logged-in
+ Amministratore: Admin

Da notare che tutte le funzionalità dell'utente non autenticato sono disponibili anche per l'utente autenticato e per l'amministratore.

#set figure(placement: auto) // Opzionale: permette il posizionamento automatico

#table(
  columns: (0.3fr, 1fr),
  inset: 10pt,
  align: (center, left),
  fill: (x, y) => if y == 0 { gray.lighten(60%) }, // Header colorato (opzionale)
  
  // Intestazione che si ripete su ogni pagina
  table.header(
    [*Ruolo*], [*Funzionalità*],
  ),

  [Utente], [Design responsive e accessibile],
  [Utente], [Visualizzare gli animali disponibili per l'adozione],
  [Admin], [Visualizzare tutti gli animali, adottati e non.],
  [Utente], [Visualizzare i dettagli di un animale specifico],
  [Utente], [Effettuare il login],
  [Utente], [Visualizzare le informazioni su come adottare un animale, sui suoi bisogni generali],
  [Utente], [Visualizzare la pagina sulle informazioni di chi gestisce "Casa Otium"],
  [Utente], [Ricercare un animale specifico per nome oppure filtrare per tipo (reale oppure fantasy)],
  [Logged-in, Admin], [Effettuare il logout],
  [Logged-in], [Aggiungere o rimuovere animali dalla lista dei preferiti (è possibile farlo solo nella schermata di dettaglio animale, questo serve a non incoraggiare l'aggiunta ai preferiti se non vi è un reale interesse)],
  [Logged-in], [Visualizzare la lista dei preferiti],
  [Logged-in], ["Adottare" un animale],
  [Admin], [Aggiungere un nuovo animale],
  [Admin], [Modificare le informazioni di un animale esistente],
  [Admin], [Eliminare un animale esistente]
)
#v(8pt)
#align(center)[
  #block(width: 80%)[
    #text(style: "italic")[Schema delle funzionalità principali del sito web]
  ]
]

== Localizzazione
Il sito web è studiato per essere disponibile solamente in Italia, quindi l'unica lingua supportata è l'italiano.

== Hardware minimo
Per il nostro sito non è richiesto un hardware minimo necessario per la visualizzazione delle pagine.
L'architettura del sito segue il principio del *Progressive Enhancement*:
- *Client-side:* L'uso di JavaScript è limitato e non critico; ciò garantisce che il sito rimanga fruibile anche su dispositivi datati o con browser con risorse limitate.
- *Server-side:* Poiché la logica di business è gestita interamente lato server tramite PHP, l'utente riceve codice HTML standard, garantendo la massima compatibilità cross-browser. 
- *Requisiti:* È richiesto un browser moderno con supporto a CSS3 (per il layout responsive) e una connessione internet stabile per il caricamento dei contenuti multimediali.
