USE mydb;

DROP TABLE IF EXISTS preferiti;
DROP TABLE IF EXISTS adottati;
DROP TABLE IF EXISTS utenti;
DROP TABLE IF EXISTS animali;

CREATE TABLE animali (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nome         VARCHAR(1023) NOT NULL,
    alt          VARCHAR(1023) NOT NULL,
    immagine     VARCHAR(1023) NOT NULL,
    sesso        ENUM('Maschio','Femmina') NOT NULL,
    tipo_animale ENUM('Reale','Fantastico') NOT NULL,
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
    password VARCHAR(255) NOT NULL,
    admin BOOLEAN DEFAULT FALSE
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

DELIMITER $$

CREATE TRIGGER check_non_admin_preferiti
BEFORE INSERT ON preferiti
FOR EACH ROW
BEGIN
    DECLARE isAdmin BOOLEAN;
    SELECT admin INTO isAdmin FROM utenti WHERE username = NEW.username;
    IF isAdmin THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Gli admin non possono essere inseriti in preferiti';
    END IF;
END$$

CREATE TRIGGER check_non_admin_adottati
BEFORE INSERT ON adottati
FOR EACH ROW
BEGIN
    DECLARE isAdmin BOOLEAN;
    SELECT admin INTO isAdmin FROM utenti WHERE username = NEW.username;
    IF isAdmin THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Gli admin non possono essere inseriti in adottati';
    END IF;
END$$

DELIMITER ;


INSERT INTO animali (nome, alt, immagine, sesso, tipo_animale, luogo, eta, taglia, carattere, descrizione, bisogni, storia, adottato)
VALUES
('Capitano', 
'Un cane di taglia media, simile a un mix di Shetland Sheepdog o Collie, 
    con un lungo e folto doppio mantello nei toni del marrone e bianco. 
    Il pelo è dritto e denso intorno al collo. 
    La testa presenta un muso stretto con una lista bianca che corre dalla fronte fino al naso.
    Gli occhi sono di un caldo colore ambra marrone, attenti e con uno sguardo diretto.
    Le orecchie sono semi erette, piegate in avanti, con pelo piumoso lungo i bordi.
    Il naso è nero e umido, la bocca mostra qualche
    piccolo granello sul labbro inferiore, conferendo un''espressione gentile e curiosa.',
'./images/jpg/animali/capitano.jpg',
'Maschio',
'Reale',
'Veneto',
4,
'Media',
'Facile',
'Capitano è un cane affettuoso e giocherellone, con un carattere leale e intelligente ereditato dalle sue origini da pastore.
    Sempre pronto a interagire con entusiasmo, ama imparare trucchi nuovi e seguire il suo proprietario in avventure quotidiane,
    mostrando una grande adattabilità e una natura curiosa che lo rende un compagno ideale per famiglie attive.
    È gentile con le persone, paziente durante le coccole e protettivo senza essere aggressivo,
    con un temperamento equilibrato che lo aiuta a integrarsi facilmente in ambienti familiari calmi e stimolanti.',
'Spazzolatura regolare: almeno tre volte alla settimana per evitare nodi nel pelo lungo e mantenere la cute sana;
    Esercizio quotidiano: passeggiate di 30 o 45 minuti o sessioni di gioco all''aperto per canalizzare la sua energia;
    Alimentazione bilanciata: crocchette per cani di taglia media, con integratori per le articolazioni se necessario;
    Ambiente ideale: famiglia affettuosa, tollera poco la solitudine (non più di 4 ore al giorno);
    Cure veterinarie: vaccini annuali e controlli oculistici;',
'Capitano, è stato salvato dopo essere stato abbandonato
    durante un trasloco della sua vecchia famiglia.
    Inizialmente timido, ha riguadagnato fiducia grazie ai volontari, rivelando un carattere gioioso e affettuoso.
    Adora le passeggiate nei parchi e le coccole sul divano, e ora cerca una casa stabile dove poter offrire la sua fedeltà incondizionata.
    Con il suo spirito resiliente, Capitano è pronto a diventare il cuore di una nuova famiglia.',
FALSE),

('Cavallo Lunare',
'Un maestoso cavallo dalla corporatura possente, caratterizzato da un mantello scuro e polveroso che emula la superficie lunare,
    costellato di crateri circolari e sfumature argenteo metalliche.
    La criniera e la coda sono lunghe, fluenti e di un bianco luminoso quasi etereo,
    che sembra risplendere di luce propria.
    La muscolatura è ben definita, conferendo all''animale un''andatura regale e soprannaturale.',
'./images/jpg/animali/cavallo_lunare.jpg',
'Maschio',
'Fantastico',
'Crateri lunari',
5, 
'Grande',
'Facile',
'Nonostante l''aspetto imponente, il Cavallo Lunare possiede un temperamento mite e riflessivo.
    È una creatura silenziosa, che ama osservare l''ambiente circostante con una calma quasi ipnotica.
    Si lega profondamente a chi dimostra pazienza e dolcezza, rivelandosi un compagno leale e protettivo.
    È particolarmente sensibile alle fasi lunari, mostrando una maggiore vivacità durante il plenilunio.',
'Ampi spazi aperti dove poter galoppare liberamente, preferibilmente sotto cieli stellati;
    Il suo manto richiede cure specifiche per mantenere la lucentezza minerale e la criniera va spazzolata regolarmente per evitare che perda il suo bagliore naturale;
    Alimentazione a base di erbe rare e acqua di sorgente purissima;',
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
'./images/jpg/animali/procione.jpg',
'Femmina',
'Reale',
'Campania',
2,
'Piccola',
'Moderato',
'Estremamente curiosa e riflessiva, questa femmina di procione ama osservare il mondo da posizioni rialzate.
    È intelligente e dotata di una manualità eccezionale, che usa per esplorare ogni novità.',
'Arricchimento ambientale con strutture per l''arrampicata;
    Dieta onnivora bilanciata;
    Presenza continua di acqua;
    Stimoli mentali quotidiani per prevenire la noia;',
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
'./images/jpg/animali/medusa_lunare.jpg',
'Femmina',
'Fantastico',
'Crateri lunari',
3,
'Piccola',
'Moderato',
'È una creatura estremamente pacifica e fluttuante, che si muove seguendo le correnti gravitazionali.
    Nonostante l''aspetto delicato, possiede una grande consapevolezza dell''ambiente circostante e tende
    a reagire ai cambiamenti di luce o di energia nelle vicinanze, illuminandosi più intensamente quando è felice.
    È un essere unico che non vive in acqua, ma fluttua nell''atmosfera rarefatta.
    È silenziosa e comunica attraverso variazioni di intensità luminosa.
    Non è aggressiva, ma i suoi tentacoli possono emettere una leggera scarica statica se toccati bruscamente, simile a un piccolo pizzicore.',
'Un ambiente con bassa gravità o correnti d''aria dolce per permetterle di fluttuare senza sforzo;
    Esposizione regolare alla luce lunare o a lampade a spettro completo per ricaricare la sua bioluminescenza;
    Un contenitore d''aria purificata e priva di polveri pesanti che potrebbero danneggiare la sua cupola sensibile;',
'Catturata accidentalmente da una sonda automatica durante una missione di esplorazione lunare, questa medusa
    si sa adattare sorprendentemente bene alla vita terrestre, purché l''ambiente sia tranquillo.
    Cerca un proprietario che ami la meditazione e che voglia aggiungere un tocco di luce celestiale alla propria casa.',
FALSE),

('Paffuta',
'Una gattina di circa tre anni con una pelliccia estremamente soffice e folta.
    Il colore dominante è un nero delicato.
    Gli occhi sono grandi, di un giallo intenso, che gli conferiscono un''espressione di perenne stupore.
    Le orecchie sono piccole e leggermente arrotondate, mentre il musetto è corto con un piccolo naso nero.',
'./images/jpg/animali/paffuta.jpg',
'Femmina',
'Reale',
'Puglia',
3,
'Piccola',
'Facile',
'Paffuta è la quintessenza della dolcezza.
    È una gattina estremamente calma per la sua età, che preferisce le lunghe dormite sulle ginocchia ai giochi troppo movimentati.
    È socievole con gli umani e tende a fare le fusa non appena viene sfiorata, dimostrando una fiducia totale nel prossimo.',
'Alimentazione specifica per gattini in crescita per supportare lo sviluppo osseo;
    Spazzolatura settimanale per mantenere il pelo morbido e privo di nodi;
    Molte interazioni umane e calore domestico, poiché soffre molto il freddo e la solitudine;',
'Trovata infreddolita in un vicolo di Foggia durante una mattina di pioggia, Paffuta è stata salvata da una
    signora che l''ha sentita miagolare debolmente.
    Nonostante l''inizio difficile, ha recuperato peso e salute in tempi record, rivelando un carattere d''oro.
    Ora aspetta solo una casa dove poter diventare la regina del divano.',
FALSE),

('Drago',
'Un drago maestoso. La pelle è coperta da scaglie spesse e sovrapposte di colore verde bosco e bruno, con riflessi dorati.
    Una cresta di aculei ossei corre lungo la sommità del capo e del collo.
    Gli occhi sono piccoli, di un arancione infuocato con pupilla verticale, e trasmettono un senso di antica saggezza.
    Il muso è potente, con narici ampie e una mascella robusta protetta da corna laterali.',
'./images/jpg/animali/drago.jpg',
'Maschio',
'Fantastico',
'Terra dei Draghi',
100,
'Grande',
'Difficile',
'È una creatura di immensa dignità e pazienza, come ci si aspetta dai suoi 100 anni di età.
    È un osservatore silenzioso che predilige la solitudine delle vette montuose,
    ma dimostra un''incredibile lealtà verso chi considera degno della sua fiducia.
    Il suo temperamento è difficile solo perché richiede molto tempo per aprirsi.',
'Un territorio vasto e impervio dove poter volare e cacciare;
    Un''area calda, possibilmente vulcanica o riscaldata magicamente, per il riposo;
    Rispetto assoluto della sua privacy e dei suoi tempi di interazione;',
'Protettore per decenni della Terra dei Draghi, ha deciso di cercare un nuovo legame dopo che il suo antico nido è
    diventato troppo silenzioso. Nonostante l''età, la sua forza è intatta e cerca un custode
    che non tema la sua imponenza ma che sappia rispettare la sua storia millenaria.',
TRUE),
('Rodrigo', 
'Un gabbiano dall''aria spavalda. 
    Ha il piumaggio bianco e grigio, il becco giallo con la classica macchia rossa e uno sguardo che punta dritto al tuo panino.',
'./images/jpg/animali/rodrigo.jpg',
'Maschio',
'Reale',
'Liguria',
6,
'Piccola',
'Difficile',
'Rodrigo non è un semplice uccello, è il capitano del lungomare. 
    Estremamente intelligente e opportunista, ha fatto del furto di focaccia un''arte. 
    Non teme l''uomo, anzi, lo vede come un distributore automatico di cibo con le gambe.',
'Accesso a scogliere o tetti alti; 
    Pesce fresco (o avanzi di frittura mista); 
    Non lasciare mai cibo incustodito in sua presenza; 
    Tappi per le orecchie per le sue urla mattutine.',
'Allontanato dal porto vecchio per "condotta molesta" ai danni dei turisti, Rodrigo cerca una nuova zona di caccia o un umano paziente che apprezzi la sua audacia.',
FALSE),
('Flavio', 
'Un piccione di città dal piumaggio grigio iridescente sul collo, indossa delle scarpe con nere con la punta bianca. 
    Gli manca qualche piuma sulla coda e cammina con un''andatura ciondolante. 
    Sembra perennemente confuso.',
'./images/jpg/animali/flavio.jpg',
'Maschio',
'Reale',
'Lazio',
3,
'Piccola',
'Facile',
'Flavio è un cittadino modello. Pacifico, un po'' tonto e amante della vita di piazza. 
    Passa le giornate a tubare e a fissare le statue. 
    Non ha grandi pretese, gli basta un marciapiede e qualche briciola.',
'Compagnia di altri volatili (soffre la solitudine); 
    Semi misti e granaglie; 
    Un cornicione riparato dalla pioggia; 
    Pazienza per il suo tubare incessante.',
'Salvato dopo essere rimasto incastrato nell''insegna di una pizzeria a Roma. Ha deciso di ritirarsi dalla vita frenetica della capitale.',
FALSE),

('Magmion', 
'Una piccola creatura tozza e muscolosa fatta di roccia e magma parzialmente solidificato. 
    Ha occhi gialli luminosi.',
'./images/jpg/animali/magmion.jpg',
'Maschio',
'Fantastico',
'Toscana',
900,
'Piccola',
'Moderato',
'Il potente signore della terra in miniatura. 
    Parla una lingua antica fatta di grugniti e crede di dover salvare l''isola di Gallipoli, che però confonde con il giardino di casa. 
    È molto serio e drammatico in tutto ciò che fa.',
'Un terrario con sassi e vulcani finti; 
    Non bagnare mai con acqua fredda (si solidifica); 
    Vasetto di riposo per la notte; 
    Bisogna chiamarlo "Sommo Luminescente" per farlo mangiare.',
'È fuggito dal vasetto di un''edicola nel 2008 e ha vagato per anni sotto un divano. Ora cerca nuovi alleati per la battaglia finale.',
FALSE),

('Genoveffo', 
'Non il solito unicorno etereo. Genoveffo è un po'' in sovrappeso, col manto rosa shocking e una criniera piena di glitter naturali. 
    Il suo corno è leggermente storto e mastica sempre gomme da masticare magiche.',
'./images/jpg/animali/genoveffo.jpg',
'Maschio',
'Fantastico',
'Lombardia',
88,
'Grande',
'Difficile',
'Genoveffo è un divo. Si rifiuta di camminare nel fango e pretende di essere ammirato. 
    Nonostante l''atteggiamento altezzoso, ha un cuore d''oro e usa la sua magia per raddrizzare i quadri storti nelle case. 
    È molto rumoroso quando cammina a causa degli zoccoli di diamante.',
'Stalla riscaldata con specchi; 
    Zucchero filato e arcobaleni da mangiare; 
    Manicure agli zoccoli settimanale; 
    Musica pop in sottofondo.',
'Espulso dalla Foresta Incantata perché cantava troppo forte durante l''ora del riposino delle fate. Cerca un ambiente mondano.',
FALSE),

('Eusbebo', 
'Un topolino dal pelo verde e scarmigliato. 
    Gli manca un orecchio e si vede una costola, cammina trascinando una zampetta.',
'./images/jpg/animali/eusbebo.jpg',
'Maschio',
'Fantastico',
'Piemonte',
102,
'Piccola',
'Facile',
'Nonostante sia uno zombi, Eusbebo è innocuo. 
    Non cerca cervelli, ma formaggio stagionato, molto stagionato, quasi ammuffito. 
    È molto lento e non scappa via, il che lo rende un animale domestico molto tranquillo.',
'Gabbia a prova di fuga; 
    Tanto formaggio gorgonzola ; 
    Ambiente fresco; 
    Non necessita di dormire, vaga la notte.',
'Risvegliato da un esperimento culinario andato male in una cantina di vini. Cerca qualcuno che lo ami finché morte non li separi (di nuovo).',
FALSE),

('Luna', 
'Una gatta europea sonnolenta dal manto tigrato grigio e nero, con zampe bianche come se indossasse dei calzini. 
    Gli occhi sono grandi, di un verde smeraldo intenso.',
'./images/jpg/animali/luna.jpg',
'Femmina',
'Reale',
'Lombardia',
2,
'Piccola',
'Facile',
'Luna è una gatta estremamente indipendente ma che sa regalare momenti di pura dolcezza. 
    Ama osservare il mondo dalla finestra e cacciare insetti immaginari. 
    È silenziosa e pulita, ideale per chi lavora da casa e cerca una compagnia discreta ma presente.',
'Lettiera sempre pulita; 
    Tiragraffi verticale alto almeno 1 metro; 
    Cibo umido di qualità alternato a crocchette; 
    Non necessita di uscire all''aperto.',
'Trovata in uno scatolone davanti a un supermercato durante un temporale, Luna è stata svezzata da una volontaria e ora è pronta per una casa definitiva.',
FALSE),

('Ignis', 
'Un piccolo drago dalle scaglie rosso rubino che brillano alla luce. 
    Ha piccole ali membranose ripiegate sulla schiena e una coda che termina con una punta a forma di freccia. 
    Dalle narici esce un sottile filo di fumo grigio.',
'./images/jpg/animali/ignis.jpg',
'Maschio',
'Fantastico',
'Campania',
15,
'Grande',
'Difficile',
'Nonostante l''aspetto intimidatorio, Ignis è un cucciolo (per gli standard dei draghi). 
    È molto intelligente e protettivo verso i suoi custodi, ma tende ad accumulare oggetti luccicanti sotto la sua cuccia. 
    Richiede un proprietario con polso fermo e pazienza.',
'Spazio aperto ignifugo (preferibilmente grotta o garage in pietra); 
    Alimentazione a base di carbone e carne arrostita; 
    Lucidatura scaglie mensile con olio apposito; 
    Attenzione: starnutisce fiamme quando è emozionato.',
'Ignis è stato recuperato dalle pendici del Vesuvio dove i turisti lo scambiavano per un''attrazione locale. Cerca un castello o una villa isolata.',
FALSE),

('Pompon', 
'Un coniglio ariete nano dal pelo bianco candido e sofficissimo. 
    Le sue lunghe orecchie pendono ai lati del muso, coprendogli quasi gli occhi neri e rotondi. 
    Sta sgranocchiando una carota con espressione soddisfatta.',
'./images/jpg/animali/pompon.jpg',
'Maschio',
'Reale',
'Toscana',
1,
'Piccola',
'Moderato',
'Pompon è un vortice di energia. Adora saltellare per casa e fare salti di gioia. 
    È molto socievole ma non ama essere sollevato da terra. 
    Ha bisogno di stimoli mentali costanti per non rosicchiare i mobili.',
'Fieno fresco illimitato (fondamentale); 
    Verdure fresche quotidiane (sedano, finocchio); 
    Recinto spazioso, mai gabbie piccole; 
    Controllo dentatura veterinario ogni 6 mesi.',
'Nato in un allevamento sovraffollato, è stato salvato da un''associazione. Ha imparato a usare la lettiera perfettamente.',
FALSE),

('Aura', 
'Una creatura maestosa simile a un cavallo, con un manto perlaceo che riflette i colori dell''arcobaleno. 
    Sulla fronte svetta un corno a spirale cristallino. 
    La criniera è lunga, setosa e sembra muoversi anche in assenza di vento.',
'./images/jpg/animali/aura.jpg',
'Femmina',
'Fantastico',
'Valle d''Aosta',
120,
'Media',
'Difficile',
'Aura è una creatura antica e saggia. Non si lega facilmente agli umani, ma quando lo fa, il legame è eterno. 
    È estremamente sensibile alle emozioni negative e non tollera la violenza o le urla. 
    La sua presenza porta serenità all''ambiente circostante.',
'Acqua di fonte purissima (non beve acqua del rubinetto); 
    Mele d''oro o frutta biologica di alta qualità; 
    Ampie radure boschive per correre; 
    Spazzolatura della criniera con pettini d''argento.',
'Aura è apparsa misteriosamente nei boschi alpini, fuggita da un regno in cui la magia stava svanendo. Cerca un rifugio dove la natura è rispettata.',
FALSE);


INSERT INTO utenti (username, password, admin) VALUES
('user',  '$2y$12$K2R1ELDMEzDLHGrJc3Naae2ETOBZ1TLwbpON4yBqohfsyKl8AdLDe', 0),
('admin', '$2y$10$VOI/xswYwipUU0kwauXl4uxygOZbhl9L4QeJYptmEipkHnkkRP0Ua', 1),
('userrrrr', '$2y$10$NfqEqEV5ktu6/D6rsxD4YuFVfeOjoXDTZF9fYry7gXme9IU2uMqRK', 0);

INSERT INTO preferiti (username, id)
VALUES ('user', 1),
       ('user', 2),
       ('user', 3),
       ('userrrrr', 6);

INSERT INTO adottati (username, id)
VALUES ('user', 6);