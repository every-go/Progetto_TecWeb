= Dati e Informazioni

== Database
Il database utilizzato per il progetto è rappresentato dal seguente diagramma Entity-Relationship:
#figure(caption: "E-R Database")[
  #image("../images/db_er.png", width: 80%)
]
Ci sono due tabelle principali che sono "Utenti" e "Animali", fra di loro ci sono due collegamenti N:N che, con la ristrutturazione, portano alla creazione di due nuove tabelle "Preferiti" e "Adottati".

Abbiamo deciso di identificare in maniera univoca gli Utenti con lo username, mentre per gli Animali abbiamo deciso di usare come chiave primaria l'id.
Gli animali hanno un sacco di attributi, dei quali alcuni sono enum.

Questi sono:
+ Taglia {"Piccola","Media","Grande"}
+ Carattere {"Facile","Moderato","Difficile"}
+ Sesso {"Maschio","Femmina"}
+ Tipo {"Reale","Fantasy"}

Inoltre gli attributi "Adottato" e "Admin" per la tabella Utenti sono boolean.

Questo indica che, se un animale ha il bool "Adottato" a true, allora questo non sarà più visualizzabile da altri utenti nella pagina degli animali, ma sarà visualizzabile solo dall'utente che l'ha effettivamente adottato nella pagina dedicata all'Area Personale.

Questo è consentito dal controllo di PhP, descritto meglio nella sezione PhP, che visualizza solo gli animali non ancora adottati.

Tutti gli altri attributi sono int, text oppure varchar.

Gli unici elementi che permettono il valore NULL sono "Storia" ed "Immagine", tutti gli altri elementi sono obbligatori all'inserimento di un nuovo animale.

Per quest'ultimo abbiamo preso questa scelta poiché non vogliamo permettere all'admin di inserire animali senza informazioni dettagliate per l'adozione.

== Titoli
I titoli sono stati costruiti con la regola generale che dice esplicitamente "Dal particolare al generale".

Per questo tutte le pagine sono della forma "Nome Pagina - Casa Otium" per permettere all'utente un'ottima navigazione.

L'unica cosa che necessita una menzione particolare è la navigazione nelle pagine del singolo animale, il cui titolo è gestito con PHP scrivendo "NomeAnimale - Casa Otium". 

Anche questo è stato necessario per permettere il miglioramento dell'orientamento dell'utente.

== Keywords e description nelle pagine
Tutte le pagine contengono diverse parole chiave, tranne 3 keyword sempre presenti, ovvero Casa Otium, animali ed adozione.

Queste keyword sono sempre presenti poiché Casa Otium è principalmente un sito per cercare animali ed adottarli, il che porta immediatamente a pensare alle 2 keyword menzionate.

Inoltre Casa Otium è fondamentale mantenerla poiché, siccome è il nome del sito, non si può non pensare che un utente possa cercare il nostro sito con il nome del sito.

Le altre keyword utilizzate con la relativa spiegazione sono descritte nella seguente tabella:

#table(
  columns: (0.15fr, 0.25fr, 0.6fr),
  inset: 10pt,
  align: (center, left),
  fill: (x, y) => if y == 0 { gray.lighten(40%) },
  
  // Intestazione che si ripete su ogni pagina
  table.header(
    [*Pagina*],[*Keywords*], [*Descrizione*],
  ),
  [Home],[Rifugio, Amici, Felice],[Rifugio e Felice sono compresi nelle intestazioni. Siccome Rifugio è anche una parola che può indicare un posto in cui gli animali si nascondono, abbiamo deciso di tenerlo come metafora per il nostro sito. Amici è necessario per la description poiché si sa che gli animali sono anche amici dell'uomo (o dei maghi in questo caso) ed era coerente con il significato della pagina.],
  [Login],[Login, Accesso],[Qui le due keyword aggiuntive erano implicite dal significato della pagina, poiché un utente può ricercare la pagina di login per accedere ai servizi di Casa Otium],
  [Chi Siamo],[Volontari, Intervento, contatti],[Questa pagina descrive come i volontari di casa Otium intervongono con la loro dedizione, di conseguenza le parole volontari ed intervento erano naturali e abbiamo pensato che potessero essere ricercate per capire più informazioni sull'operato dei volontari. 
  
  Contiene anche la tabella dei contatti, in questo caso vi è una tabella contente dati fittizzi sugli admin da contattare.
  La tabella utilizza la tecnica della linearizzazione di Aaron Gustafson per trasformare la tabella al ridursi della dimensione dello schermo.
  ],
  [401],[Errore 401, Non autorizzato], table.cell(rowspan:3)[Non essendo le pagine di errore effettivamente ricercabili, abbiamo deciso solo di inserire il nome e la breve descrizione del tipo di errore.],
  [404],[Errore 404, Non trovato],
  [500],[Errore 500, Server],
  [Animale],[Bisogni, Descrizione, Storia],[Questa pagina difficilmente è ricercata direttamente da un utente per la prima volta, poiché sarebbe necessario sapere già quale animale si va a ricercare. Però, per migliorare l'utilizzo delle keyword, abbiamo ritenuto naturale l'utilizzo delle parole chiave menzionate, poiché ogni animale ha dei propri bisogni, una propria descrizione e una propria storia, tutto introdotto dalle intestazioni ],
  [Animali],[Amico],[In questa pagina le keyword "universali" erano molto sufficienti, l'unica parole chiave non ancora utilizzata utile era amico, per lo stesso discorso fatto nella descrizione delle keywords della "Home"],
  [Crediti],[Crediti, Ringraziamenti],[Anche qui le nuove parole chiave vengono naturali, un utente può essere incuriosito chi Casa Otium ringrazia, sia per l'utilizzo nel sito web (come nel nostro caso) oppure di altre persone (non nel nostro caso), ma in entrambi i casi la ricerca dei ringraziamenti e dei crediti può portare a questa pagina senza clickbait],
  [Area Personale],[Preferiti, Adottati],[Anche questa è una pagina come gli errori, ovvero non dovrebbe essere direttamente raggiungibile da una semplice ricerca alla prima volta, poiché è un'area personale che si crea almeno dopo la prima volta in cui si entra. Le uniche parole chiave adatte al caso sono proprio quelle che rappresentano il contenuto della pagina, ovvero gli animali preferiti e adottati fino a quel momento grazie a Casa Otium],
  [Ispeziona Animale],[Aggiungi, Modifica],[Questa è stata una pagina in cui abbiamo fatto un po' fatica a trovare altre keyword, poiché dovrebbe essere una pagina accessibile solo da un admin e non da un utente comune. Comunque abbiamo optato per quelle parole chiave affinché richiamino l'effettivo scopo della pagina, ovvero aggiungere oppure modificare un animale],
)
#v(8pt)
#align(center)[
  #block(width: 80%)[
    #text(style: "italic")[Schema delle funzionalità principali del sito web]
  ]
]

== Generazione ed alternativa testuale delle immagini
Per la generazione delle immagini degli animali fantasy, ci siamo affidati alla AI specializzate per la generazione animale come Grok.

Per l'alternativa testuale delle immagini, abbiamo inserito nel DB l'attributo alt che va a inserire l'alternativa testuale per ogni immagine.

Questa si differenzia dalla descrizione dell'animale, perché l'idea è che l'alternativa testuale descriva fisicamente l'animale, per permettere a chiunque di comprendere com'è fatto l'animale, mentre la descrizione è di tipo caratteriale.