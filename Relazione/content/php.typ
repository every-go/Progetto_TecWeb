= PhP
<php>

== Header e footer
Le pagine hanno un header e un footer dinamico che cambia in base all'accesso "logged-in", admin oppure utente e in base alla pagina corrente (per evitare link circolari).

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
+ Area Personale (non visualizzabile dall'admin, in quanto lui non ha bisogno idealmente di aggiungere animali ai preferiti o di adottarne)
+ Logout

Una cosa molto simile succede al footer, "introducendo" il link alla pagina dell'Area Personale solo dopo aver effettuato il login.

L'unica differenza è che nel footer è presente il link anche alla pagina dei crediti.

=== Area Personale
Da notare però che la pagina Area Personale è l'unica a non avere header e footer dinamici, in quanto per accederci bisogna essere un utente "logged-in", e in quanto tale si ha a disposizione la navbar menzionata precedentemente per gli utenti che hanno effettuato il login.

=== Pagine di errore
Queste pagine per scelta non hanno la breadcrumb.
Dipsongono comunque di header, footer e link/pulsanti che rimandano a pagine rilevanti (ad esempio login nella pagina di errore 401)

== Animali ed Animale
Anche qui, è possibile visualizzare completamente qualsiasi animale nel dettaglio oppure l'elenco degli animali senza aver effettuato il login.

Il tasto aggiungi hai preferiti è visibile ma non utilizzabile, per non nascondere una funzionalità a un utente che sta esplorando.

Non è previsto che un account admin possa avere una lista dei preferiti, quindi anche a agli account con role admin viene nascosto il tasto dei preferiti.

== Pagina animali
Mostra la lista degli animali adottabili.

È possibile eseguire una ricerca per nome, ritornando una corrispondenza positiva se trova una sottostringa uguale ai termini di ricerca.
Ad esempio se si cerca "Lunare" trova sia "Cavallo Lunare" che "Medusa Lunare" sono tra i risultati di ricerca.

== Pagina animale singolo
Nell'eventualità che un animale sia stato già adottato da un altro utente nella pagina animale compare un'avviso che segnala tale fatto all'utente.

L'utente con disabilità visive lo nota subito al primo tab con un aiuto speciale che specifica che l'animale è stato già adottato e rimanda alla sezione che descrive l'adozione già completa. 

L'utente che non usa gli aiuti alla navigazione lo nota leggendo la sezione dedicata all'adozione.

Negli aiuti alla navigazione viene inserito come prima voce un link interno all'avviso. Essendo la prima voce un utente che si avvale di screen reader saprà fin da subito che l'animale è stato adottato.

Questa situazione si verifica solo quando un animale è stato inserito tra i preferiti di un utente, poiché nella lista degli animali non c'è possibilità di vedere animali già adottati.

L'eliminazione automatica dalla lista dei preferiti degli utenti che non hanno adottato l'animale potrebbe creare confusione all'utente, per questo abbiamo deciso di modificare solo la stringa di adozione.

Rimane comunque la possibilità dell'utente di togliere l'animale fra i preferiti per la pulizia della propria Area Personale.

=== Breadcrumb nella pagina animale
Per garantire una qualità di navigazione accettabile le breadcrumb della pagina animale.php e area_personale.php rimandano alla medesima pagina da cui si è arrivati.

Ad esempio se l'utente stava svolgendo una ricerca e dopo aver visualizzato un animale vuole proseguire con l'esplorazione i termini di ricerca e la pagina corrente sono mantenuti.

Se si era arrivati dalla pagina Area Personale la breadcrumb mostra la pagina Area Personale.

=== Impaginazione
Per limitare la quantità di dati inviati all'apertura della lista degli animali è stata implementata una visualizzazione per pagine.

La lista degli animali generale e anche quella risultato di una ricerca sono divise in pagine e il server invia all'utente solo una sezione della lista.

La pagina Area Personale non è soggetta a paginazione, si assume che l'utente abbia un numero ragionevole di animali salvati tra i preferiti

Al termine della lista vi è un menù di navigazione implementato con un nav contenente una ul di link, è strutturato nel seguente modo: \
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

*Gestione di link non validi e circolari* 

La freccia di pagina successiva sparisce all'ultima pagina e 
la freccia di pagina precedente sparisce alla prima pagina.

I link di pagina numerata non sono doppi, a esempio alla prima pagina vengono visualizzate solo le pagine 1, 2 e 3

Come l'utente si aspetta, il passaggio tra le pagine non cancella i termini di ricerca impostati

La Pagina corrente non è un link e contiene una label che la segnala come pagina corrente.

== Redirect
<redirect>
Abbiamo deciso di adottare una nostra convenzione interna non presente su tutti i siti, ovvero che, ogni volta che si esegue il login o il logout da una pagina, si viene reindirizzati alla pagina da cui si è arrivati.

Ad esempio se sono nella pagina "Chi siamo" ed eseguo il login comparirà la notifica "Login avvenuto con successo" però mi ritrovo nella pagina "Chi siamo" senza essere reindirizzato alla home.