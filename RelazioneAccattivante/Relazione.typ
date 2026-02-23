#set page(paper:"a4")
#set text(size: 10pt,lang:"IT")

#show figure: set block(breakable: true)

#show link:set text(blue)
#show ref:set text(blue) 

#set page(
  numbering: "i",
  footer: context [
    

    #align(center + horizon)[
    #counter(page).display()
        #place(left+horizon)[#image("images/gatto_logo.png",width: 3em, alt: "")]
        #place(right+horizon)[#image("images/gatto_logo.png",width: 3em, alt: "")]
  ]
  ]
)

#align(center)[
#image("images/logo_Unipd.png", width: 30%)
Università degli Studi di Padova

#strong()[Relazione per il concorso accattivante e accessibile $5^"a"$ edizione] \
A.A. 2025-2026

#strong()[Indirizzo del sito:] \
#link("https://caa.studenti.math.unipd.it/mmazzare/")[caa.studenti.math.unipd.it/mmazzare/]

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
[Utente],[username],[User_Saf3]
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



#pagebreak()

#set heading(numbering: "1.")

#context counter(page).update(1)

#set page(
  numbering: "1",
  footer: context [
    

    #align(center + horizon)[
    #counter(page).display(
      (x,y)=>{
        str(x)+" di "+ str(y)
      },
      both: true,
    )

        #place(left+horizon)[#image("images/gatto_logo.png",width: 3em, alt: "")]
        #place(right+horizon)[#image("images/gatto_logo.png",width: 3em, alt: "")]
  ]
  ]
)

#include "content/intro.typ"

#include "content/test.typ"

#include "content/appendice.typ"