#set page(paper:"a4")
#set text(size: 16pt)


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
[Utente reale],[user],[user],
[Utente fantasy],[fantasy],[fantasy],
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

== subsection

=== subsubsection

====

=====

======

=======