= PhP

== Header e footer
Tutti gli header e footer, tranne quelli per la pagina "Preferiti" e "Ispeziona" (poiché accessibili solo dall'admin, e quindi già autenticato obbligatoriarmente), hanno un header e un footer dinamico.

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
+ Preferiti
+ Login

Una cosa molto simile succede al footer, "introducendo" il link alla pagina dei preferiti solo dopo aver effettuato il login.

L'unica differenza è che nel footer è presente il link anche alla pagina dei crediti.

== Animali ed Animale
Anche qui, è possibile visualizzare completamente qualsiasi animale nel dettaglio oppure l'elenco degli animali senza aver effettuato il login.

Ma abbiamo deciso, per scelta stilistica e di coerenza, di non permettere la visualizzazione del tasto per aggiungere un animale ai preferiti agli utenti che non hanno effettuato il login.

Ovviamente questo tasto comparirà quando un utente effettua il login.