= Interfaccia e Stile

== Utilizzo delle grid
Le grid non sono state molto utilizzate poiché peggiorano il rendering, ma sono state sfruttate a dovere. 

Nelle card di "animali" e "preferiti" abbiamo messo una grid nelle informazioni dell'animale perché volevamo mantenere fisso il layout 2x2 delle informazioni.

Utilizzando flex sarebbe stato un po' più complesso dato che avremmo dovuto mettere un div per ogni riga.

In "animale" abbiamo usato le grid per il layout perché nella fase di design avevamo disegnato la pagina in modo che venisse poi naturale l'utilizzo di una grid.

La stessa cosa vale per "ispeziona_animale", dato che è una variante della pagina "animale" e quindi il design originale.

== Colore e Contrasti
<colore>

Per i colori principali del sito web, sono stati scelti dei colori che richiamano la natura e gli animali.

Il verde (sia scuro che chiaro) richiama solidità, forza e crescita.\
Il giallo richiama l'energia e la libertà.\
Il beige richiama la semplicità e la calma.

== Colori utilizzati

#figure(
  caption: [Colore con la relativa codifica RGB utilizzata nel sito 
  ]
)[
#table(columns:(1fr,1fr),
[Colore],[Codifica RGB],
table.cell(fill: rgb("1B4332"))[],[1B4332],
table.cell(fill: rgb("A3B18A"))[],[A3B18A],
table.cell(fill: rgb("FFD166"))[],[FFD166],
table.cell(fill: rgb("B45000"))[],[B45000],
table.cell(fill: rgb("FAF3DD"))[],[FAF3DD],
table.cell(fill: rgb("4D2F24"))[],[4D2F24],
table.cell(fill: rgb("000000"))[],[000000]
)
]

== Contrasti principali

Per garantire una buona accessibilità del sito web, sono stati verificati i contrasti tra i colori utilizzati per gli sfondi e quelli utilizzati per i testi.

Di seguito sono riportati i risultati delle verifiche effettuate.

#figure(
  caption: [Contrasti Colori 
  ])[
#table(columns:(1fr,1fr,1fr,1fr,1fr,1fr,1fr),rows:(1.2em),
  // Header Row
[],
table.cell(fill: rgb("1B4332"))[],
table.cell(fill: rgb("A3B18A"))[],
table.cell(fill: rgb("FFD166"))[],
table.cell(fill: rgb("FAF3DD"))[],
table.cell(fill: rgb("4D2F24"))[],
table.cell(fill: rgb("000000"))[],
// Row 1
table.cell(fill: rgb("1B4332"))[],
[X],[4.86:1],[7.68:1],[9.98:1],[1.08:1],[1.89:1],
// Row 2
table.cell(fill: rgb("A3B18A"))[],
[4.86:1],[X],[1.58:1],[2.05:1],[5.27:1],[9.21:1],
// Row 3
table.cell(fill: rgb("FFD166"))[],
[7.68:1],[1.58:1],[X],[1.29:1],[8.33:1],[14.56:1],
// Row 4
table.cell(fill: rgb("FAF3DD"))[],
[9.98:1],[2.05:1],[1.29:1],[X],[10.83:1],[18.92:1],
// Row 5
table.cell(fill: rgb("4D2F24"))[],
[1.08:1],[5.27:1],[8.33:1],[10.83:1],[X],[1.74:1],
// Row 6
table.cell(fill: rgb("000000"))[],
[1.89:1],[9.21:1],[14.56:1],[18.92:1],[1.74:1],[X],
)
]


I contrasti sono stati calcolati utilizzando lo strumento online "Contrast Checker" disponibile all'indirizzo web #link("https://webaim.org/resources/contrastchecker/")[https://webaim.org/resources/contrastchecker/].

Il rapporto fra colore e testo deve essere almeno di 4.5:1 per il testo piccolo, di 3:1 per il testo grande.

I contrasti non rispettati non vengono mai utilizzati nel sito web.

I colori utilizzati per lo sfondo sono il verde scuro (1B4332), il verde chiaro (A3B18A) il beige (FAF3DD).

Sul verde scuro (1B4332) vengono utilizzati come colori del testo il beige (FAF3DD, rapporto 9.98:1) e il giallo (FFD166, rapporto 7.68:1).

Sul beige (FAF3DD) vengono utilizzati il marrone (4D2F24, rapporto 10.83:1), il nero (000000, rapporto 18.92:1) e il verde scuro (1B4332, rapporto 9.98:1).

A volte viene anche utilizzato il verde chiaro (A3B18A) come sfondo. In questo caso, il testo è di colore nero (000000, rapporto 9.21:1).


Il rapporto nei link deve essere 3:1 con il testo, 4.5:1 con lo sfondo. 

I link non visitati sono sottolineati, in modo da sistemare eventuali problemi di contrasto.

I link visitati invece sono di colore arancione (B45000) su sfondo beige (FAF3DD, rapporto 4.63:1) e vicino al testo nero (000000, rapporto 4.08:1). Vicino al marrone (4D2F24) c'è un rapporto di 2.33:1, ma non essendo affianco al testo dei link non crea problemi di accessibilità


Il colore delle icone nello sfondo verde chiaro (A3B18A) è il verde scuro (1B4332, rapporto 4.86:1).

Il logo dell'Università degli Studi di Padova, utilizzato nel disclaimer, non è in contrasto con i colori da noi utilizzati. Purtroppo, il manuale di identità visiva non fornisce alternative per il logo, costringendoci a mantenere il logo originale. Siamo comunque consapevoli del fatto che non mantiene i contrasti corretti.