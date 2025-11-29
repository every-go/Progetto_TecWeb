= Progettazione

#text()[
  La progettazione è stata studiata per essere intuitiva e semplice \
  Essendo comunque questo un sito web molto semplice, abbiamo optato per mantenere un'ampiezza di 4, per le pagine "Chi Siamo", "Animali", "Preferiti" (se logged-in), "Login/Logout". \
  La pagina "Crediti" è accessibile solo dalla mappa della navigazione presente in fondo a ogni pagina, poiché quella è solo una pagina che ringrazia i creatori delle icone utilizzate nel sito.\
  La massima profondità è di 4 livelli, percorribile solo dall'Admin, tramite il percorso Home>>Animali>>Animale>>Modifica.
]

== Albero gerarchico
#figure(caption: "Albero gerarchico del sito web")[
  #image("../images/albero.png", width: 80%)
]

#text()[
  Da notare che, per semplicità, non sono state rappresentate le pagine di errore (401, 404, 500).\
  Inoltre, le pagine al secondo livello di ampiezza (dopo la home) sono tutte accessibili fra di loro ma sono state omesse le linee per evitare troppa confusione nell'albero.\
  Ogni linea che collega specifica chi può accedere, ovvero l'Utente, il "Logged-in" e l'Admin.\
]