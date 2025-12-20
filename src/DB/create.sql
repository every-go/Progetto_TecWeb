USE mydb;

DROP TABLE IF EXISTS preferiti;
DROP TABLE IF EXISTS adottati;
DROP TABLE IF EXISTS utenti;
DROP TABLE IF EXISTS animali;

CREATE TABLE animali (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nome         VARCHAR(1023) NOT NULL,
    alt          VARCHAR(1023) NOT NULL,
    immagine     VARCHAR(1023),
    sesso        ENUM('Maschio','Femmina') NOT NULL,
    tipo_animale ENUM('Reale','Fantasy') NOT NULL,
    luogo        VARCHAR(1023) NOT NULL,
    eta          INT NOT NULL,
    taglia       ENUM('Piccola','Media','Grande') NOT NULL,
    carattere    ENUM('Facile','Moderato','Difficile') NOT NULL,
    descrizione  TEXT NOT NULL,
    bisogni      TEXT NOT NULL,
    storia       TEXT,
    adottato     BOOLEAN DEFAULT FALSE
) ENGINE=InnoDB;

CREATE TABLE utenti (
    username VARCHAR(255) PRIMARY KEY,
    password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE preferiti (
    username VARCHAR(255),
    id       INT,
    PRIMARY KEY (username, id),
    FOREIGN KEY (username) REFERENCES utenti(username) ON DELETE CASCADE,
    FOREIGN KEY (id)       REFERENCES animali(id)      ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE adottati (
    username VARCHAR(255),
    id       INT,
    PRIMARY KEY (username, id),
    FOREIGN KEY (username) REFERENCES utenti(username) ON DELETE CASCADE,
    FOREIGN KEY (id)       REFERENCES animali(id)      ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO animali (nome, alt, immagine, sesso, tipo_animale, luogo, eta, taglia, carattere, descrizione, bisogni, storia, adottato)
VALUES
('Capitano', 
'Un cane di taglia media, simile a un mix di <span lang="en">Shetland Sheepdog</span> o <span lang="en">Collie</span>, 
    con un lungo e folto doppio mantello nei toni del marrone e bianco. 
    Il pelo è dritto e denso intorno al collo. 
    La testa presenta un muso stretto con una lista bianca che corre dalla fronte fino al naso.
    Gli occhi sono di un caldo colore ambra-marrone, attenti e con uno sguardo diretto.
    Le orecchie sono semi-erette, piegate in avanti, con pelo piumoso lungo i bordi.
    Il naso è nero e umido, la bocca mostra qualche
    piccolo granello sul labbro inferiore, conferendo un''espressione gentile e curiosa.',
'../images/jpg/cane_bello.jpg',
'Maschio',
'Reale',
'Padova',
4,
'Media',
'Facile',
'Capitano è un cane affettuoso e giocherellone, con un carattere leale e intelligente ereditato dalle sue origini da pastore.
    Sempre pronto a interagire con entusiasmo, ama imparare trucchi nuovi e seguire il suo proprietario in avventure quotidiane,
    mostrando una grande adattabilità e una natura curiosa che lo rende un compagno ideale per famiglie attive.
    È gentile con le persone, paziente durante le coccole e protettivo senza essere aggressivo,
    con un temperamento equilibrato che lo aiuta a integrarsi facilmente in ambienti familiari calmi e stimolanti.',
'1) Spazzolatura regolare: almeno tre volte alla settimana per evitare nodi nel pelo lungo e mantenere la cute sana.
    2) Esercizio quotidiano: passeggiate di 30-45 minuti o sessioni di gioco all''aperto per canalizzare la sua energia.
    3) Alimentazione bilanciata: crocchette premium per cani di taglia media, con integratori per le articolazioni se necessario.
    4) Ambiente ideale: famiglia affettuosa, tollera poco la solitudine (non più di 4-5 ore al giorno).
    5) Cure veterinarie: vaccini annuali e controlli oculistici',
'Capitano, un mix di <span lang="en">Collie</span> di 4 anni, è stato salvato dopo essere stato abbandonato
    durante un trasloco della sua vecchia famiglia.
    Inizialmente timido, ha riguadagnato fiducia grazie ai volontari, rivelando un carattere gioioso e affettuoso.
    Adora le passeggiate nei parchi e le coccole sul divano, e ora cerca una casa stabile dove poter offrire la sua fedeltà incondizionata.
    Con il suo spirito resiliente, Capitano è pronto a diventare il cuore di una nuova famiglia.',
FALSE),

('Cavallo Lunare',
'Un maestoso cavallo dalla corporatura possente, caratterizzato da un mantello scuro e polveroso che emula la superficie lunare,
    costellato di crateri circolari e sfumature argenteo-metalliche.
    La criniera e la coda sono lunghe, fluenti e di un bianco luminoso quasi etereo,
    che sembra risplendere di luce propria.
    La muscolatura è ben definita, conferendo all''animale un''andatura regale e soprannaturale.',
'../images/jpg/cavallo_lunare.jpg',
'Maschio',
'Fantasy',
'Parte oscura della Luna',
5, 
'Grande',
'Facile',
'Nonostante l''aspetto imponente, il Cavallo Lunare possiede un temperamento mite e riflessivo.
    È una creatura silenziosa, che ama osservare l''ambiente circostante con una calma quasi ipnotica.
    Si lega profondamente a chi dimostra pazienza e dolcezza, rivelandosi un compagno leale e protettivo.
    È particolarmente sensibile alle fasi lunari, mostrando una maggiore vivacità durante il plenilunio.',
'1) Ampi spazi aperti dove poter galoppare liberamente, preferibilmente sotto cieli stellati.
    2) Il suo manto richiede cure specifiche per mantenere la lucentezza minerale e la criniera va spazzolata regolarmente per evitare che perda il suo bagliore naturale.
    3) Alimentazione a base di erbe rare e acqua di sorgente purissima.',
'Trovato sperduto tra i picchi desertici della faccia nascosta della Luna, questo stallone è l''ultimo della sua stirpe.
    Può giungere sulla Terra seguendo la scia di una stella cadente, cercando un luogo dove la sua luce non debba temere l''oscurità eterna.
    Cerca una famiglia che sappia apprezzare la magia del silenzio e che abbia un cuore grande quanto l''universo.',
FALSE),

('Procione',
'Un procione disteso su un grosso tronco d''albero, con il muso rivolto verso il basso.
    Presenta la classica mascherina di pelo nero che avvolge gli occhi scuri e lucidi, contrastata da strisce di pelo bianco
    puro sopra e sotto gli occhi. Il naso è piccolo e nero.
    La pelliccia è folta e variegata, con una miscela di peli grigi, neri e biancastri che gli conferiscono un aspetto soffice.
    Si intravede una piccola zampa nera con dita agili appoggiata al legno.',
'../images/jpg/procione.jpg',
'Femmina',
'Reale',
'Napoli',
2,
'Piccola',
'Moderato',
'Estremamente curiosa e riflessiva, questa femmina di procione ama osservare il mondo da posizioni rialzate.
    È intelligente e dotata di una manualità eccezionale, che usa per esplorare ogni novità.',
'1) Arricchimento ambientale con strutture per l''arrampicata.
    2) Dieta onnivora bilanciata.
    3) Presenza continua di acqua.
    4) Stimoli mentali quotidiani per prevenire la noia.',
'Recuperata a Napoli mentre cercava cibo in un mercato, ha dimostrato subito una grande calma.
    Cerca una famiglia che capisca la sua natura selvatica ma sappia offrirle il calore di una casa sicura.',
FALSE),

('Medusa Lunare',
'Una medusa sospesa sopra un suolo roccioso e grigio.
    Presenta una grande cupola traslucida e bioluminescente, decorata con piccoli punti luminosi simili a stelle.
    Dalla parte inferiore del corpo si dipartono numerosi tentacoli sottili e lunghi, di colore bianco e rosa pallido,
    che fluttuano nell''aria come fili di seta.
    Al centro, sotto la cupola, si trova una struttura più densa e frastagliata,
    simile a petali di un fiore esotico, con sfumature calde che vanno dal color panna all''arancione tenue.',
'../images/jpg/medusa_lunare.jpg',
'Femmina',
'Fantasy',
'Parte visibile della Luna',
3,
'Piccola',
'Moderato',
'È una creatura estremamente pacifica e fluttuante, che si muove seguendo le correnti gravitazionali.
    Nonostante l''aspetto delicato, possiede una grande consapevolezza dell''ambiente circostante e tende
    a reagire ai cambiamenti di luce o di energia nelle vicinanze, illuminandosi più intensamente quando è felice.
    È un essere unico che non vive in acqua, ma fluttua nell''atmosfera rarefatta.
    È silenziosa e comunica attraverso variazioni di intensità luminosa.
    Non è aggressiva, ma i suoi tentacoli possono emettere una leggera scarica statica se toccati bruscamente, simile a un piccolo pizzicore.',
'1) Un ambiente con bassa gravità o correnti d''aria dolce per permetterle di fluttuare senza sforzo.
    2) Esposizione regolare alla luce lunare o a lampade a spettro completo per ricaricare la sua bioluminescenza.
    3) Un contenitore d''aria purificata e priva di polveri pesanti che potrebbero danneggiare la sua cupola sensibile.',
'Catturata accidentalmente da una sonda automatica durante una missione di esplorazione lunare, questa medusa
    si sa adattare sorprendentemente bene alla vita terrestre, purché l''ambiente sia tranquillo.
    Cerca un proprietario che ami la meditazione e che voglia aggiungere un tocco di luce celestiale alla propria casa.',
FALSE),

('Paffuto',
'Un gattino di circa tre anni con una pelliccia estremamente soffice e folta.
    Il colore dominante è un nero delicato.
    Gli occhi sono grandi, di un giallo intenso, che gli conferiscono un''espressione di perenne stupore.
    Le orecchie sono piccole e leggermente arrotondate, mentre il musetto è corto con un piccolo naso nero.',
'../images/jpg/gatto.jpg',
'Femmina',
'Reale',
'Foggia',
3,
'Piccola',
'Facile',
'Paffuto è la quintessenza della dolcezza.
    È una gattina estremamente calma per la sua età, che preferisce le lunghe dormite sulle ginocchia ai giochi troppo movimentati.
    È socievole con gli umani e tende a fare le fusa non appena viene sfiorata, dimostrando una fiducia totale nel prossimo.',
'1) Alimentazione specifica per gattini in crescita per supportare lo sviluppo osseo.
    2) Spazzolatura settimanale per mantenere il pelo morbido e privo di nodi.
    3) Molte interazioni umane e calore domestico, poiché soffre molto il freddo e la solitudine.',
'Trovata infreddolita in un vicolo di Foggia durante una mattina di pioggia, Paffuto è stata salvata da una
    signora che l''ha sentita miagolare debolmente.
    Nonostante l''inizio difficile, ha recuperato peso e salute in tempi record, rivelando un carattere d''oro.
    Ora aspetta solo una casa dove poter diventare la regina del divano.',
FALSE),

('Drago',
'Un drago maestoso. La pelle è coperta da scaglie spesse e sovrapposte di colore verde bosco e bruno, con riflessi dorati.
    Una cresta di aculei ossei corre lungo la sommità del capo e del collo.
    Gli occhi sono piccoli, di un arancione infuocato con pupilla verticale, e trasmettono un senso di antica saggezza.
    Il muso è potente, con narici ampie e una mascella robusta protetta da corna laterali.',
'../images/jpg/drago.jpg',
'Maschio',
'Fantasy',
'Terra dei Draghi',
100,
'Grande',
'Difficile',
'È una creatura di immensa dignità e pazienza, come ci si aspetta dai suoi 100 anni di età.
    È un osservatore silenzioso che predilige la solitudine delle vette montuose,
    ma dimostra un''incredibile lealtà verso chi considera degno della sua fiducia.
    Il suo temperamento è difficile solo perché richiede molto tempo per aprirsi.',
'1) Un territorio vasto e impervio dove poter volare e cacciare.
    2) Un''area calda, possibilmente vulcanica o riscaldata magicamente, per il riposo.
    3) Rispetto assoluto della sua privacy e dei suoi tempi di interazione.',
'Protettore per decenni della Terra dei Draghi, ha deciso di cercare un nuovo legame dopo che il suo antico nido è
    diventato troppo silenzioso. Nonostante l''età, la sua forza è intatta e cerca un custode
    che non tema la sua imponenza ma che sappia rispettare la sua storia millenaria.',
FALSE);

INSERT INTO utenti
VALUES ('user', 'user'),
       ('admin', 'admin');

INSERT INTO preferiti
VALUES ('user', 1),
       ('user', 2),
       ('user', 3);

INSERT INTO adottati
VALUES ('user', 1);