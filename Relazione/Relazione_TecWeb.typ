#set page(paper:"a4")
#set text(size: 16pt,lang="IT")


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
= section

#set text(lang:"IT")
#let white=rgb("1b4332")
#let light=rgb("A3b18a")
#let bright=rgb("FFD166")
#let medium=rgb("faf3dd")
#let dark=rgb("62402b")
#let color6=rgb("2f2f2f")

#let colori=(
white:"1b4332",
light:"A3b18a",
bright:"FFD166",
medium:"faf3dd",
dark:"62402b",
color6:"2f2f2f"
)
#show link: body =>{
  underline()[
    #text(fill: blue,body)
  ]
} 

#let colorBox(colore:color,size:2em)={
  box(height: size,width: size,fill: color.rgb(colore))
}

= Colore
#figure(
  caption: [Contrasti Colori \ Verificati con #link("https://webaim.org/resources/contrastchecker/") 
  ])[
#table(columns:(5),
[],[Bnc],[Ner],[Arc],[Ros],
[Bnc],[1],[],[],[],
[Ner],[3],[1],[],[],
[Arc],[2],[],[1],[],
[Ros],[1],[],[],[1],


)
]

\
\

#let cose=()
#for colore in colori.keys(){
  cose.push(colorBox(colore:colori.at(colore)));

  cose.push(colore)
  cose.push(colori.at(colore))

}
#figure(caption: "Color Pallet")[
#align(center)[
  #grid(
  columns:6,
   ..cose,
   align: center,

)
]
]



#set text(fill:gradient.linear(light,bright,medium))
#box(fill:white,inset:1em,outset:0.5em,radius:0.8em)[
#lorem(500)

]
