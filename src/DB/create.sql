DROP TABLE IF EXISTS preferiti;
DROP TABLE IF EXISTS adottati;
DROP TABLE IF EXISTS utenti;
DROP TABLE IF EXISTS animali;

CREATE TABLE animali (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nome         VARCHAR(255) NOT NULL,
    alt          VARCHAR(255) NOT NULL,
    immagine     VARCHAR(255),
    sesso        ENUM('Maschio','Femmina') NOT NULL,
    tipo_animale ENUM('Reale','Fantasy') NOT NULL,
    luogo        VARCHAR(255) NOT NULL,
    eta          INT NOT NULL,
    taglia       ENUM('Piccola','Media','Grande') NOT NULL,
    carattere    ENUM('Facile','Moderato','Difficile') NOT NULL,
    descrizione  TEXT NOT NULL,
    bisogni      TEXT,
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

INSERT INTO animali (nome, alt, immagine, sesso, tipo_animale, luogo, eta, taglia, carattere, descrizione, storia, adottato)
VALUES
("Capitano", "Un cane giocherellone e affettuoso", "../images/jpg/cane_bello.jpg", "Maschio", "Reale", "Parco Centrale", 4, "Media", "Facile", "Un cane giocherellone e affettuoso, perfetto compagno di passeggiate.", "Ha imparato qualche trucco divertente per conquistare il cuore di tutti.", FALSE),
("Cavallo Lunare", "Un cavallo dal manto scintillante sotto la luna", "../images/jpg/cavallo_lunare.jpg", "Maschio", "Fantasy", "Prateria Magica", 5, "Grande", "Facile", "Un cavallo dal manto scintillante, sempre pronto a fare una corsa sotto la luna.", "Si dice che porti fortuna a chi lo cavalca durante le notti di luna piena.", FALSE),
("Procione", "Un procione curioso che esplora la foresta", "../images/jpg/procione.jpg", "Femmina", "Reale", "Foresta Urbana", 2, "Piccola", "Moderato", "Furbo e curioso, ama esplorare ogni angolo e rubare qualche snack.", "Ha una passione per i bottini notturni, ma è molto amichevole.", FALSE),
("Medusa Lunare", "Una medusa luminosa nella laguna", "../images/jpg/medusa_lunare.jpg", "Femmina", "Fantasy", "Laguna Incantata", 3, "Media", "Moderato", "Una creatura elegante e misteriosa con tentacoli luminosi, ama fare giochi d'acqua.", "Nessuno sa esattamente da dove venga, ma tutti rimangono affascinati dai suoi movimenti.", FALSE),
("Gatto Paffuto", "Un gatto paffuto che fa le fusa", "../images/jpg/gatto.jpg", "Femmina", "Reale", "Appartamento Accogliente", 3, "Piccola", "Moderato", "Un gatto curioso e coccolone, ama i sonnellini e le coccole.", "Spesso si intrufola tra le gambe dei visitatori e fa le fusa incessantemente.", FALSE);
("Drago", "Un drago maestoso con ali spiegate", "../images/jpg/drago.jpg", "Maschio", "Fantasy", "Montagne Fiammeggianti", 100, "Grande", "Difficile", "Un drago imponente ma dal cuore gentile, adora le storie intorno al fuoco.", "Si dice che custodisca tesori e racconti antichi da centinaia di anni.", FALSE),

INSERT INTO utenti
VALUES ("user", "user"),
       ("admin", "admin");

INSERT INTO preferiti
VALUES ("user", 1),
       ("user", 2),
       ("user", 3);

INSERT INTO adottati
VALUES ("user", 1);