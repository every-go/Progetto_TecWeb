= Form
<form>

Ogni form ha la sua parte di validazione lato client (JavaScript) e la validazione lato server (PHP).

Entrambe inseriscono i role=\"alert\" per permettere all'utente utilizzatore di screen reader di essere immediatamente avvisato degli errori in corso.

Oltretutto per costruzione le nostre regex impediscono l'input di chiamate javascript del tipo \<script\>alert(1)\</script\> (che è il comando javascript più corto che si possa inserire) in quanto la sua lunghezza è 24 caratteri, ma questo comando è di 25 quindi non passa né la validazione lato client né la validazione lato server nelle password.

La stessa cosa succede nel form di  aggiungi e modifica dove sono impediti i caratteri speciale \< e \> in modo da impedire l'inserimento di script malevoli. La loro omissione è comunque determinata dal fatto che in un sito di animali i caratteri di maggiore o minore sono inutili nel 99,9% dei casi e quindi non si perde contenuto importante per l'aggiunta delle informazioni sull'animale.

== Funzioni comuni

Il file "funzioni_comuni.js" include le funzioni utilizzate dai form di "login" "registrazione" e "ispeziona_animale" per validare lato client la correttezza dei campi.

L'unica cosa leggermente diversa è che la registrazione ha avuto necessità di riscrittura per le funzioni "validazioneCampo" e "caricamento" per via dell'aggiunta del campo "confirmPassword" il quale non ha una regex vera e propria ma deve essere controllato solo con il fatto che la conferma della password sia uguale alla password inserita precedentemente.

Le regex sono le seguenti e motivate in questo modo:

== Login / Registrazione

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

== Modifica / aggiungi

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
)