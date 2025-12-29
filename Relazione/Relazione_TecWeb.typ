#set page(paper:"a4")
#set text(size: 10pt,lang:"IT")


#show link:set text(blue)
#show ref:set text(blue) 

#set page(
  numbering: "i",
  footer: context [
    

    #align(center + horizon)[
    // #counter(page).display(
    //   (x,y)=>{
    //     str(x)+" di "+ str(y)
    //   },
    //   both: true,
    // )
    #counter(page).display()
        #place(left+horizon)[#image("images/gatto_logo.png",width: 3em, alt: "")]
        #place(right+horizon)[#image("images/gatto_logo.png",width: 3em, alt: "")]
  ]
  ]
)



#align(center)[
#image("images/logo_Unipd.png", width: 30%)
Università degli Studi di Padova

#strong()[Relazione Progetto di Tecnologie Web] \
A.A. 2025-2026

#strong()[Indirizzo del sito:] \
#link("tecweb.studenti.math.unipd.it/mmazzare")[tecweb.studenti.math.unipd.it/mmazzare]

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



#pagebreak()

#set heading(numbering: "1.")

#outline(title: "Indice")

#pagebreak()
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

#pagebreak()

#include "content/requisiti.typ"

#include "content/progettazione.typ"

#include "content/dati_info.typ"

#include "content/interfaccia_stile.typ"

#include "content/accessibility_usability.typ"

DA SCRIVERE TUTTA

#include "content/js.typ"

#include "content/php.typ"

#include "content/test.typ"