DROP TABLE IF EXISTS preferiti;
DROP TABLE IF EXISTS utenti;
DROP TABLE IF EXISTS animali;

DROP TYPE IF EXISTS genere;
DROP TYPE IF EXISTS tipo;
DROP TYPE IF EXISTS taglia;
DROP TYPE IF EXISTS adozione;

CREATE TYPE genere AS ENUM ('Maschio', 'Femmina');
CREATE TYPE tipo AS ENUM ('Reale', 'Fantasy');
CREATE TYPE taglia AS ENUM ('Piccola', 'Media', 'Grande');
CREATE TYPE adozione AS ENUM ('Bassa', 'Media', 'Elevata');

CREATE TABLE animali (
    id          SERIAL PRIMARY KEY,
    nome        TEXT NOT NULL,
    immagine    TEXT NOT NULL,
    sesso       genere NOT NULL,
    tipo        tipo NOT NULL,
    luogo       TEXT NOT NULL,
    eta         INTEGER,
    taglia      taglia NOT NULL,
    difficolta  adozione NOT NULL,
    descrizione TEXT,
    adottato    BOOLEAN DEFAULT FALSE
);

CREATE TABLE utenti (
    username TEXT PRIMARY KEY,
    password TEXT NOT NULL
);

CREATE TABLE preferiti (
    username TEXT,
    id       INTEGER,
    PRIMARY KEY (username, id),
    FOREIGN KEY (username) REFERENCES utenti(username) ON DELETE CASCADE,
    FOREIGN KEY (id)       REFERENCES animali(id)    ON DELETE CASCADE
);

CREATE TABLE adottati (
    username TEXT,
    id       INTEGER,
    PRIMARY KEY (username, id),
    FOREIGN KEY (username) REFERENCES utenti(username) ON DELETE CASCADE,
    FOREIGN KEY (id)       REFERENCES animali(id)    ON DELETE CASCADE
);
