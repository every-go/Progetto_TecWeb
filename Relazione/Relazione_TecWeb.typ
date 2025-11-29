#set page(paper:"a4")
#set text(size: 16pt,lang:"IT")


#show link:set text(blue)

#align(center)[
#image("images/logo_Unipd.png", width: 30%)
Università degli Studi di Padova

#strong()[Relazione Progetto di Tecnologie Web] \
A.A. 2025-2026

#strong()[Indirizzo del sito:] \
#link("https")[link del sito]

#strong()[Mail referente:] \
#link("mailto:matteo.mazzaretto@studenti.unipd.it")[matteo.mazzaretto\@studenti.unipd.it]

#strong()[Account Accesso al sito:]
#set table(
  fill: (x, y) =>
    if y == 0 {
      gray.lighten(40%)
    },
  align: left,
)

#show table.cell.where(y: 0): strong

#table(
  columns: (1fr, 1fr, 1fr),
table.header([Ruolo],[Username],[Password]),
[Utente],[user],[user],
[Amministratore],[admin],[admin],

)
#strong()[Autori:]
#set table(
  align: center,
)
#table(
  columns: (1fr,  1fr),
table.header([Nome],[Matricola]),
[Matteo Mazzaretto],[2111005],
[Marco Brunello],[2110997],
[Stefano Maso],[2110983],
[Davide Lorenzon],[2101075]

)


]

// #set heading(numbering: none)


#pagebreak()

#outline(title: "Indice")

#pagebreak()
#context counter(page).update(1)
= Analisi dei Requisiti



#pagebreak()

= Colore e Contrasti

#text()[Per i colori del sito web, sono stati scelti dei colori che richiamano la natura e gli animali. Ovvero verde (sia scuro che chiaro) che richiama solidità, forza e crescita, in questo caso dell'animale. Il giallo richiama l'energia e la libertà. Il beige richiama la semplicità e la calma. Il marrone richiama la terra. Infine il grigio scuro richiama l'equilibrio e lo stile.]

#figure(
  caption: [Colore con la relativa codifica RGB utilizzata nel sito 
  ]
)[
#table(columns:(1fr,1fr),
[Colore],[Codifica RGB],
table.cell(fill: rgb("1b4332"))[],[1b4332],
table.cell(fill: rgb("A3b18a"))[],[A3b18a],
table.cell(fill: rgb("FFD166"))[],[FFD166],
table.cell(fill: rgb("faf3dd"))[],[faf3dd],
table.cell(fill: rgb("62402b"))[],[62402b],
table.cell(fill: rgb("2f2f2f"))[],[2f2f2f]
)
]

#text()[Per garantire una buona accessibilità del sito web, sono stati verificati i contrasti tra i colori utilizzati per gli sfondi e quelli utilizzati per i testi. Di seguito sono riportati i risultati delle verifiche effettuate.]

#figure(
  caption: [Contrasti Colori \ Verificati con #link("https://webaim.org/resources/contrastchecker/") 
  ])[
#table(columns:(1fr,1fr,1fr,1fr,1fr,1fr,1fr),
[],table.cell(fill: rgb("1b4332"))[],
table.cell(fill: rgb("A3b18a"))[],
table.cell(fill: rgb("FFD166"))[],
table.cell(fill: rgb("faf3dd"))[],
table.cell(fill: rgb("62402b"))[],
table.cell(fill: rgb("2f2f2f"))[],
// Row 1
table.cell(fill: rgb("1b4332"))[],
[],[],[],[],[],[],
// Row 2
table.cell(fill: rgb("A3b18a"))[],
[],[],[],[],[],[],
// Row 3
table.cell(fill: rgb("FFD166"))[],
[],[],[],[],[],[],
// Row 4
table.cell(fill: rgb("faf3dd"))[],
[],[],[],[],[],[],
// Row 5
table.cell(fill: rgb("62402b"))[],
[],[],[],[],[],[],
// Row 6
table.cell(fill: rgb("2f2f2f"))[],
[],[],[],[],[],[],
)
]
