DROP TABLE IF EXISTS preferiti;
DROP TABLE IF EXISTS adottati;
DROP TABLE IF EXISTS utenti;
DROP TABLE IF EXISTS animali;

CREATE TABLE animali (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    nome         VARCHAR(255) NOT NULL,
    immagine     VARCHAR(255) NOT NULL,
    sesso        ENUM('Maschio','Femmina') NOT NULL,
    tipo_animale ENUM('Reale','Fantasy') NOT NULL,
    luogo        VARCHAR(255) NOT NULL,
    eta          INT,
    taglia       ENUM('Piccola','Media','Grande') NOT NULL,
    difficolta   ENUM('Bassa','Media','Elevata') NOT NULL,
    descrizione  TEXT,
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
