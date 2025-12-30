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

== Logout
Il logout è gestito con un file semplice "logout.php", però ad ogni onclick() del tasto logout con una funzione JavaScript si avvisa l'utente che verrà reindirizzato alla home, per permettere di conoscere ciò che avviene dopo il logout ed impedire  il disorientamento dell'utente.