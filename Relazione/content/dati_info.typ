= Dati e Informazioni

== Database
Il database utilizzato per il progetto è rappresentato dal seguente diagramma Entity-Relationship:\
#figure(caption: "E-R Database")[
  #image("../images/db_er.png", width: 80%)
]
Ci sono due tabelle principali che sono "Utenti" e "Animali", fra di loro ci sono due collegamenti N:N che creano due nuove tabelle: "Preferiti" e "Adottati".\
Abbiamo deciso di identificare in maniera univoca gli Utenti con lo username, mentre per gli Animali abbiamo deciso di usare come chiave primaria l'id.
Gli animali hanno un sacco di attributi, dei quali alcuni sono enum.\
Questi sono:
+ Taglia {"Piccola","Media","Grande"}
+ Difficoltà {"Bassa","Media","Elevata"}
+ Sesso {"Maschio","Femmina"}
+ Tipo {"Reale","Fantasy"}
Inoltre l'attributo "Adottato" è un booleano.\
Tutti gli altri attributi sono integer oppure text.\
Gli unici elementi che permettono il valore NULL sono "Descrizione" ed "Immagine", tutti gli altri elementi sono obbligatori all'inserimento di un nuovo utente/animale.\
Per quest'ultimo abbiamo preso questa scelta perché non vogliamo la possibilità dell'admin di inserire animali senza informazioni dettagliate per l'adozione da parte di qualche utente.
