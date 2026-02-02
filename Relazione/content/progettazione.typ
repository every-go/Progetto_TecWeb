= Progettazione

La progettazione è stata studiata per essere intuitiva e semplice.

Essendo comunque questo un sito web molto semplice, abbiamo optato per mantenere un'ampiezza di 4, per le pagine "Chi Siamo", "Animali", "Area Personale" (se logged-in), "Login/Logout".

La pagina "Crediti" è accessibile solo dalla mappa della navigazione presente in fondo a ogni pagina, poiché quella è solo una pagina che ringrazia i creatori delle icone utilizzate nel sito.

La massima profondità è di 4 livelli, percorribile solo dall'Admin, tramite il percorso Home>>Animali>>Animale>>Modifica.

#figure(caption: "Albero delle interazioni nel sito web")[
    #image("../images/albero.png", width: 80%)

]

Da notare che, per semplicità, non sono state rappresentate le pagine di errore (401, 404, 500) e i collegamenti dal livello 3 e 4 verso le pagine del livello 1 e 2 (accessibili tramite header e footer).

Ogni linea ha un'etichetta che rappresenta chi può effettuare quella interazione, da una pagina all'altra è possibile spostarsi tramite link o bottoni.

Le linee sono considerate bidirezionali.

Inoltre, le pagine al secondo livello di ampiezza (dopo la home) sono tutte accessibili fra di loro ma sono state omesse le linee per evitare troppa confusione nell'albero.

Ogni linea che collega specifica chi può accedere, ovvero l'Utente, il "Logged-in" e l'Admin.