= PhP
<php>

== Header e footer
Le pagine che ne hanno bisogno hanno un header e un footer dinamico che cambiano in base all'accesso "logged-in", admin oppure utente.

Infatti, nel caso della navbar nell'header questa ha come elementi 
+ Home
+ Animali
+ Chi siamo
+ Login
quando non hai effettuato il login.

Questa cambia quando effettui il login, diventando
+ Home
+ Animali
+ Chi siamo
+ Preferiti (non visualizzabile dall'admin, in quanto lui non ha bisogno idealmente di aggiungere animali ai preferiti)
+ Logout

Una cosa molto simile succede al footer, "introducendo" il link alla pagina dei preferiti solo dopo aver effettuato il login.

L'unica differenza è che nel footer è presente il link anche alla pagina dei crediti.

=== Preferiti
Da notare però che la pagina Preferiti è l'unica a non avere header e footer dinamici, in quanto per accederci bisogna essere un utente "logged-in", e in quanto tale si ha a disposizione la navbar menzionata precedentemente per gli utenti che hanno effettuato il login.

=== Pagine di errore
Queste pagine per scelta non hanno la navbar ma dispongono del footer, il quale è un elemento che deve rimanere in qualunque parte del sito web.

== Animali ed Animale
Anche qui, è possibile visualizzare completamente qualsiasi animale nel dettaglio oppure l'elenco degli animali senza aver effettuato il login.

Ma abbiamo deciso, per scelta stilistica e di coerenza, di non permettere la visualizzazione del tasto per aggiungere un animale ai preferiti agli utenti che non hanno effettuato il login.

Ovviamente questo tasto comparirà quando un utente effettua il login.

Non è previsto che un account admin possa avere una lista dei preferiti, quindi anche a agli account con role admin viene nascosto il tasto dei preferiti.
== Pagina animali
Mostra la lista degli animali adottabili.

#upper("è") possibile eseguire una ricerca per nome.
La query di ricerca ritorna una corrispondenza positiva se trova una sottostringa uguale ai termini di ricerca
Ad esempio se si cerca "gatto" sia "Signor Gatto" che "Gattone" sono tra i risultati di ricerca.

Uno script javascript si occupa di fare un refresh automatico della pagina una volta inseriti almeno 2 caratteri validi nella box di ricerca.

Il refresh automatico inserisce in automatico i parametri di ricerca nella query dei parametri dell'URL, permettendo al file php di restituire la pagina contenente i risultati della ricerca 

== Pagina animale
  Nell'eventualità che un animale sia stato già adottato da un altro utente nella pagina animale compare un'avviso che segnala tale fatto all'utente.

  L'avviso va a rimpiazzare la sezione dedicata all'adozione

  Negli aiuti alla navigazione viene inserito come prima voce un link interno all'avviso. Essendo la prima voce un utente che si avvale di screen reader saprà fin da subito che l'animale è stato adottato.

  Questa situazione si verifica solo quando un animale è stato inserito tra i preferiti di un utente.
  L'eliminazione automatica dalla lista dei preferiti degli utenti che non hanno adottato l'animale potrebbe creare confusione all'utente. 

=== Breadcrumb nella pagina animale
Per garantire una qualità di navigazione accettabile le breadcrumb della pagina animale.php e preferiti.php rimandano alla medesima pagina da cui si è arrivati.

Ad esempio se l'utente stava svolgendo una ricerca e dopo aver visualizzato un animale vuole proseguire con l'esplorazione i termini di ricerca e la pagina corrente sono mantenuti.

Se si era arrivati dalla pagina preferiti.php la breadcrumb mostra la pagina preferiti.



=== Impaginazione
Per limitare la quantità di dati inviati all'apertura della lista degli animali è stata implementata una visualizzazione per pagine.

La lista degli animali generale e anche quella risultato di una ricerca sono divise in pagine e il server invia all'utente solo una sezione della lista.

La pagina preferiti non è soggetta a paginazione, si assume che l'utente abbia un numero ragionevole di animali salvati tra i preferiti

Al termine della lista vi è un menù di navigazione implementato con un nav contente una ul di link, è strutturato nel seguente modo: \
#align(center)[
#set par(justify: true)
// #box(stroke:(1pt),width:100%, height: 3em,inset:1em)[
  #set box(stroke:(thickness: 1pt,),radius:3pt, inset:0.5em) 
  
  #box()[ #sym.arrow.l ] 
  #box()[ 1 ] 
  #box()[ PC-2 ] 
  #box()[ PC-1 ] 
  #box()[ PC ] 
  #box()[ PC+1 ] 
  #box()[ PC+2 ] 
  #box()[ UP ] 
  #box()[ #sym.arrow  ] 
// ]
- PC: Pagina Corrente
- UP: Ultima pagina
- #sym.arrow.l Pagina precedente
- #sym.arrow Pagina Successiva
]
*Gestione di link non validi e circolari* \
La freccia di pagina successiva sparisce all'ultima pagina e 
la freccia di pagina precedente sparisce alla prima pagina.

I link di pagina numerata non sono doppi, a esempio alla prima pagina vengono visualizzate solo le pagine 1, 2 e 3

Come l'utente si aspetta, il passaggio tra le pagine non cancella i termini di ricerca impostati

La Pagina corrente non è un link e contiene una label che la segnala come pagina corrente.

== Logout
Il logout è gestito con un file semplice "logout.php", però ad ogni onclick() del tasto logout con una funzione JavaScript si avvisa l'utente che verrà reindirizzato alla home, per permettere di conoscere ciò che avviene dopo il logout ed impedire  il disorientamento dell'utente.