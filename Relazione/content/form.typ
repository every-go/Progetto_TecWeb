= Form
<form>

Ogni form ha la sua parte di validazione lato client (JavaScript) e la validazione lato server (PHP).

Entrambe inseriscono i role=\"alert\" per permettere all'utente utilizzatore di screen reader di essere immediatamente avvisato degli errori in corso.

== Funzioni comuni

Il file "funzioni_comuni.js" include le funzioni utilizzate dai form di "login" "registrazione" e "ispeziona_animale" per validare lato client la correttezza dei campi.

L'unica cosa leggermente diversa è che la registrazione ha avuto necessità di riscrittura per le funzioni "validazioneCampo" e "caricamento" per via dell'aggiunta del campo "confirmPassword" il quale non ha una regex vera e propria ma deve essere controllato solo con il fatto che la conferma della password sia uguale alla password inserita precedentemente.

Le regex sono le seguenti e motivate in questo modo:

=== Login / Registrazione

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
  table.header([Campo],[Regex],[Motivazione]),
  [Username],[^[A-Za-z]{4,24}\$],[Abbiamo optato per un minimo di 4 caratteri per lo username per diminuire la possibilità di avere utenti che usano gli stessi username già utilizzati (ab), oltretutto non sono consentiti spazi per evitare problemi di gestione spazi iniziali e finali inseriti nel DB],
  [Password],[^(user|admin|(?=(?:.*[a-z]))(?=(?:.*[A-Z]))(?=(?:.*\d))(?=(?:.*[!@\#\$%^&\*()\_\-+=\[\]{};:'",.<>\/?\\|`~]))[A-Za-z\d!@#$%^&*()_\-+=\[\]{};:'",.<>\/?\\|`~]{8,24})\$/],[La motivazione della password è un po' ovvia, obblighiamo l'utente ad utilizzare almeno 1 numero 1 carattere speciale 1 lettera maiuscola e 1 lettera minuscola per aumentare la sicurezza. Da notare che nella regex di login.js sono consentiti "user" e "admin" altrimenti non avrebbero mai passato il lato client, mentre invece vengono esclusi nella regex di registrazione.js (qui menzionato lo stesso)]
)

=== Modifica / aggiungi

Una volta che un animale è adottato non si può ritornare in alcun modo allo stato di animale non adottato tranne con una nuova importazione o modifica diretta nel database. Questa scelta è dettata dal fatto che sarebbe scorretto che un utente adottasse un animale e poi si ritrova l'adozione persa. Comprendiamo che in un ambiente reale sarebbe da controllare se l'utente che adotta è adatto però essendo un progetto didattico abbiamo omesso quella parte.
Di conseguenza non c'è il campo "adottato" nella sezione di modifica.

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
  table.header([Campo],[Regex],[Motivazione]),
  [Nome],"/^[\p{L} ]{2,24}$/u",[Il nome di un animale può contenere solo lettere (anche accentate) e spazi.],
  [Età],"/^(0|[1-9][0-9]{0,4})$/",[L'età deve essere un numero valido da 0 a 99999 senza zeri iniziali. Abbiamo pensato che i nostri animali possono essere vecchi ma non ultra millenari.],
  [Alt],"/^[\p{L}0-9 ,:;'’ʼʻ`′.\"()]{1,1024}$/u",[Il testo alternativo di un'immagine può contenere le lettere anche accentate, spazi e i principali tipi di punteggiatura ma non permette di andare a capo, perché se fosse possibile rovinerebbe il senso dell'attributo alt.],
  [Luogo],"/^[\p{L}0-9 ,/'’ʼʻ`′\"]{1,100}$/u",[Non abbiamo reso il luogo particolarmente lungo (difficile trovare un luogo realistico con più di 100 caratteri) permettendo sempre l'utilizzo delle lettere anche accentate e dei principali tipi di punteggiatura (magari si vuole inserire l'indirizzo di casa).],
  [Descrizione, Bisogni],"/^[\p{L}0-9 ,:;'’ʼʻ`′.\"()?!\s]{1,1024}$/u",[La descrizione e i bisogni permettono di inserire tutte le lettere anche accentate, i numeri, i principali segni di punteggiatura ed è permesso di andare a capo in quanto ci si trova all'interno di una textarea.],
  [Storia],"/^[\p{L}0-9 ,:;'’ʼʻ`′.\"()?!\s]{0,1024}$/u",[La storia ha gli stessi identici "parametri" per descrizione e bisogni, l'unica cosa che cambia è che essendo un campo facoltativo è permesso l'inserimento di una stringa vuota.]
)