<?php
namespace DB;

require_once "utils/imageHandler.php";

use ImageHandler;
use mysqli;
use mysqli_result;

class DBAccess
{
    //XAMPP localhost mydb root ""
    //Docker db mydb root root
    //mmazzare mmazzare mmazzare password
    private const HOST_DB = "db";
    private const DATABASE_NAME = "mydb";
    private const USERNAME = "root";
    private const PASSWORD = "root";

    private $connection;

    public function openDBConnection()
    {
        mysqli_report(MYSQLI_REPORT_ERROR);

        $this->connection = @mysqli_connect(
            DBAccess::HOST_DB,
            DBAccess::USERNAME,
            DBAccess::PASSWORD,
            DBAccess::DATABASE_NAME
        );

        if (!$this->connection) {
            // Log the error (optional but recommended)
            // error_log("Database connection failed: " . mysqli_connect_error());

            http_response_code(500);
            include __DIR__ . "/500.php";

            // Stop script execution
            exit();
        }

        return true;
    }

    public function closeConnection()
    {
        mysqli_close($this->connection);
    }

    public function inserisciAnimale(
        $nome,
        $alt,
        $immagine,
        $sesso,
        $tipo_animale,
        $luogo,
        $eta,
        $taglia,
        $carattere,
        $descrizione,
        $bisogni,
        $storia
    ) {
        $query =
            "INSERT INTO animali (nome,alt,immagine,sesso,tipo_animale,luogo,eta,taglia,carattere,descrizione,bisogni,storia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($this->connection, $query)) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssssssssssss",
                $nome,
                $alt,
                $immagine,
                $sesso,
                $tipo_animale,
                $luogo,
                $eta,
                $taglia,
                $carattere,
                $descrizione,
                $bisogni,
                $storia
            );

            // Eseguiamo la query
            if (mysqli_stmt_execute($stmt)) {
                // --- ECCO IL TRUCCO ---
                // Recuperiamo l'ID appena generato dall'AUTO_INCREMENT
                $nuovoId = $this->connection->insert_id; // Stile a oggetti
                // Oppure: $nuovoId = mysqli_insert_id($this->connection); // Stile procedurale

                mysqli_stmt_close($stmt);

                // Ritorniamo l'ID invece di true.
                // (Nota: se l'ID è > 0 è considerato "true" nei controlli if, quindi è comodo)
                return $nuovoId;
            }

            mysqli_stmt_close($stmt);
        }

        return false; // O null, se qualcosa è andato storto
    }

    
    public function getList() {
        $query = "SELECT * FROM animali WHERE adottato = 0 ORDER BY id ASC";
        
        // Controllo errori sulla query
        ($queryResult = mysqli_query($this->connection, $query)) or
            die("Errore in dbConnection: " . mysqli_error($this->connection));
        
        $result = [];
        
        // Costruisci l'array dei risultati
        while ($row = mysqli_fetch_assoc($queryResult)) {
            array_push($result, $row);
        }
        
        $queryResult->free();
        return $result; // Ritorna sempre un array (vuoto o pieno)
    }

    public function eliminaAnimale($id)
    {   
        $queryImm = "SELECT immagine FROM animali WHERE id = ?";
        $query = "DELETE FROM animali WHERE id = ?";
        $risultato = false; // Default a false (eliminazione fallita)

        $immagine = false; // Default a false (nessun animale trovato)

        if ($stmt = mysqli_prepare($this->connection, $queryImm)) {
            mysqli_stmt_bind_param($stmt, "i", $id);

            if (mysqli_stmt_execute($stmt)) {
                $queryResult = mysqli_stmt_get_result($stmt);

                // 2. Controllo se l'oggetto risultato è valido
                if ($queryResult) {
                    // 3. Controllo ESPLICITO: abbiamo trovato almeno una riga?
                    if (mysqli_num_rows($queryResult) > 0) {
                        $immagine = mysqli_fetch_assoc($queryResult)['immagine'];
                    }

                    // 4. Liberiamo la memoria (fondamentale)
                    mysqli_free_result($queryResult);
                }
            }

            // 5. Chiudiamo lo statement
            mysqli_stmt_close($stmt);
        }

        if($immagine||$immagine!==""){

            $uploadDirSystem = "images/uploads"; // Dove salvare fisicamente
            $uploadDirWeb = "images/uploads/"; // Cosa scrivere nel DB

            // Inizializzo l'handler immagini
            $imgHandler = new ImageHandler($uploadDirSystem, $uploadDirWeb);
            $imgHandler->delete($immagine);
        }

        if ($stmt = mysqli_prepare($this->connection, $query)) {
            mysqli_stmt_bind_param($stmt, "i", $id);

            if (mysqli_stmt_execute($stmt)) {
                // Controllo se almeno una riga è stata eliminata
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $risultato = true;
                }
            }
            mysqli_stmt_close($stmt);
        }
        return $risultato; // true se eliminato, false altrimenti
    }

    public function aggiornaAnimale(
        $id,
        $nome,
        $alt,
        $immagine,
        $sesso,
        $tipo_animale,
        $luogo,
        $eta,
        $taglia,
        $carattere,
        $descrizione,
        $bisogni,
        $storia
    ) {
        $query =
            "UPDATE animali SET nome = ?, alt = ?, immagine = ?, sesso = ?, tipo_animale = ?, luogo = ?, eta = ?, taglia = ?, carattere = ?, descrizione = ?, bisogni = ?, storia = ?, adottato = 0 WHERE id = ?";

        $risultato = false;

        if ($stmt = mysqli_prepare($this->connection, $query)) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssssssisssssi",
                $nome,
                $alt,
                $immagine,
                $sesso,
                $tipo_animale,
                $luogo,
                $eta,
                $taglia,
                $carattere,
                $descrizione,
                $bisogni,
                $storia,
                $id
            );
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) >= 0) {
                    // >= 0 perché anche 0 righe modificate è OK (dati identici)
                    $risultato = true;
                }
            }
            mysqli_stmt_close($stmt);
        }

        return $risultato;
    }

    public function getAnimalById($id)
    {
        // 1. Validazione input
        if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            return false;
        }

        $query = "SELECT * FROM animali WHERE id = ?";
        $animalData = false; // Default a false (nessun animale trovato)

        if ($stmt = mysqli_prepare($this->connection, $query)) {
            mysqli_stmt_bind_param($stmt, "i", $id);

            if (mysqli_stmt_execute($stmt)) {
                $queryResult = mysqli_stmt_get_result($stmt);

                // 2. Controllo se l'oggetto risultato è valido
                if ($queryResult) {
                    // 3. Controllo ESPLICITO: abbiamo trovato almeno una riga?
                    if (mysqli_num_rows($queryResult) > 0) {
                        $animalData = mysqli_fetch_assoc($queryResult);
                    }

                    // 4. Liberiamo la memoria (fondamentale)
                    mysqli_free_result($queryResult);
                }
            }

            // 5. Chiudiamo lo statement
            mysqli_stmt_close($stmt);
        }

        // Ritorna l'array dati oppure NULL se non trovato/errore
        return $animalData;
    }

    function getPreferiti($username)
    {
        $sql = 'SELECT AN.id, AN.nome, AN.alt, AN.immagine, AN.sesso, AN.tipo_animale, AN.luogo, AN.eta,
                AN.taglia, AN.carattere, AN.descrizione, AN.bisogni, AN.storia
                FROM animali AN
                JOIN preferiti PR ON AN.id = PR.id
                JOIN utenti U ON PR.username = U.username
                WHERE U.username = ?';

        $animalData = [];

        if ($stmt = mysqli_prepare($this->connection, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            if (mysqli_stmt_execute($stmt)) {
                $queryResult = mysqli_stmt_get_result($stmt);
                if ($queryResult) {
                    while ($row = mysqli_fetch_assoc($queryResult)) {
                        $animalData[] = $row;
                    }
                    mysqli_free_result($queryResult);
                }
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log(mysqli_error($this->connection));
        }

        return $animalData; // Ritorna array di animali (può essere vuoto)
    }

    function getAdottati($username)
    {
        $sql = 'SELECT AN.id, AN.nome, AN.alt, AN.immagine, AN.sesso, AN.tipo_animale, AN.luogo, AN.eta,
                AN.taglia, AN.carattere, AN.descrizione, AN.bisogni, AN.storia
                FROM animali AN
                JOIN adottati AD ON AN.id = AD.id
                JOIN utenti U ON AD.username = U.username
                WHERE U.username = ?';

        $animalData = [];

        if ($stmt = mysqli_prepare($this->connection, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            if (mysqli_stmt_execute($stmt)) {
                $queryResult = mysqli_stmt_get_result($stmt);
                if ($queryResult) {
                    while ($row = mysqli_fetch_assoc($queryResult)) {
                        $animalData[] = $row;
                    }
                    mysqli_free_result($queryResult);
                }
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log(mysqli_error($this->connection));
        }

        return $animalData; // Ritorna array di animali (può essere vuoto)
    }

    public function adottaAnimale($username, $animalId) {
        $sql = "INSERT INTO adottati (username, id) VALUES (?, ?)";
        $adozioneRiuscita = false;
        
        if ($stmt = mysqli_prepare($this->connection, $sql)) {
            mysqli_stmt_bind_param($stmt, "si", $username, $animalId);
            
            if (mysqli_stmt_execute($stmt)) {
                // Marca l'animale come adottato
                $updateQuery = "UPDATE animali SET adottato = 1 WHERE id = ?";
                
                if ($updateStmt = mysqli_prepare($this->connection, $updateQuery)) {
                    mysqli_stmt_bind_param($updateStmt, "i", $animalId);
                    
                    if (mysqli_stmt_execute($updateStmt)) {
                        // Rimuovi dai preferiti se presente
                        $deleteQuery = "DELETE FROM preferiti WHERE username = ? AND id = ?";
                        
                        if ($deleteStmt = mysqli_prepare($this->connection, $deleteQuery)) {
                            mysqli_stmt_bind_param($deleteStmt, "si", $username, $animalId);
                            mysqli_stmt_execute($deleteStmt);
                            mysqli_stmt_close($deleteStmt);
                        }
                        $adozioneRiuscita = true;
                    }
                    mysqli_stmt_close($updateStmt);
                }
            }
            mysqli_stmt_close($stmt);
        }
        return $adozioneRiuscita;
    }

    public function searchAnimali($searchTerm)
    {
        $sql = "SELECT * FROM animali 
                WHERE nome LIKE ?
                ORDER BY nome";
        $animalData = [];

        if ($stmt = mysqli_prepare($this->connection, $sql)) {
            $searchParam = "%$searchTerm%";
            mysqli_stmt_bind_param($stmt, "s", $searchParam);
            if (mysqli_stmt_execute($stmt)) {
                $queryResult = mysqli_stmt_get_result($stmt);
                if ($queryResult) {
                    while ($row = mysqli_fetch_assoc($queryResult)) {
                        $animalData[] = $row;
                    }
                    mysqli_free_result($queryResult);
                }
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log(mysqli_error($this->connection));
        }
        return $animalData;
    }
    public function searchAnimaliAdottabili($searchTerm)
    {
        $sql = "SELECT * FROM animali 
                WHERE nome LIKE ? AND adottato = 0
                ORDER BY nome";
        $animalData = [];

        if ($stmt = mysqli_prepare($this->connection, $sql)) {
            $searchParam = "%$searchTerm%";
            mysqli_stmt_bind_param($stmt, "s", $searchParam);
            if (mysqli_stmt_execute($stmt)) {
                $queryResult = mysqli_stmt_get_result($stmt);
                if ($queryResult) {
                    while ($row = mysqli_fetch_assoc($queryResult)) {
                        $animalData[] = $row;
                    }
                    mysqli_free_result($queryResult);
                }
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log(mysqli_error($this->connection));
        }
        return $animalData;
    }

    function loginUtente($username, $passwordInserita)
    {
        // 1. Prepara la query (SOLO con username, per sicurezza)
        $sql =
            "SELECT username, password, admin FROM utenti WHERE username = ?";

        if ($stmt = mysqli_prepare($this->connection, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            // 2. Se l'utente esiste
            $row = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);
            if ($row) {
                // 3. CONFRONTO DIRETTO (visto che sono in chiaro)
                // Usa === per essere sicuro che siano identiche (case sensitive)
                if ($passwordInserita === $row["password"]) {
                    // --- PUNTO BONUS COL PROFESSORE ---
                    // Rigenera l'ID per evitare il furto di sessione
                    session_regenerate_id(true);
                    // ----------------------------------

                    // 4. Imposta le variabili di sessione
                    $_SESSION["username"] = $row["username"];
                    $_SESSION["is_logged_in"] = true; // Comodo per i controlli nelle altre pagine
                    if ($row["admin"]) {
                        $_SESSION["role"] = "admin";
                    } else {
                        $_SESSION["role"] = "user";
                    }

                    return true; // Login riuscito
                }
            }
        }
        return false; // Login fallito
    }

    public function checkPreferito($username, $idAnimale)
    {
        $query =
            "SELECT * FROM preferiti WHERE id = ? AND username = (SELECT username FROM utenti WHERE username = ?)";
        $isFavorite = false;

        if ($stmt = mysqli_prepare($this->connection, $query)) {
            mysqli_stmt_bind_param($stmt, "is", $idAnimale, $username);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if (mysqli_num_rows($result) > 0) {
                    $isFavorite = true;
                }
                mysqli_free_result($result);
            }
            mysqli_stmt_close($stmt);
        }

        return $isFavorite;
    }

    public function aggiungiPreferito($username, $idAnimale)
    {
        $query = "INSERT INTO preferiti (username, id) VALUES (?, ?)";
        $success = false;

        if ($stmt = mysqli_prepare($this->connection, $query)) {
            mysqli_stmt_bind_param($stmt, "si", $username, $idAnimale);
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $success = true;
                }
            }
            mysqli_stmt_close($stmt);
        }

        return $success;
    }

    public function rimuoviPreferito($username, $idAnimale)
    {
        $query = "DELETE FROM preferiti WHERE username = ? AND id = ?";
        $success = false;

        if ($stmt = mysqli_prepare($this->connection, $query)) {
            mysqli_stmt_bind_param($stmt, "si", $username, $idAnimale);
            if (mysqli_stmt_execute($stmt)) {
                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    $success = true;
                }
            }
            mysqli_stmt_close($stmt);
        }

        return $success;
    }

    public function aggiungiAdottato($username, $idAnimale)
    {
        $queryInserimento = "INSERT INTO adottati (username, id) VALUES (?, ?)";
        $queryAggiornamento = "UPDATE animali SET adottato = TRUE WHERE id = ?";
        $success = false;

        if ($stmt = mysqli_prepare($this->connection, $queryInserimento)) {
            mysqli_stmt_bind_param($stmt, "si", $username, $idAnimale);

            if (
                mysqli_stmt_execute($stmt) &&
                mysqli_stmt_affected_rows($stmt) > 0
            ) {
                mysqli_stmt_close($stmt); // chiuso appena finito

                if (
                    $agg = mysqli_prepare(
                        $this->connection,
                        $queryAggiornamento
                    )
                ) {
                    mysqli_stmt_bind_param($agg, "i", $idAnimale);

                    if (
                        mysqli_stmt_execute($agg) &&
                        mysqli_stmt_affected_rows($agg) > 0
                    ) {
                        $success = $this->rimuoviPreferito(
                            $username,
                            $idAnimale
                        );
                    }

                    mysqli_stmt_close($agg); // SEMPRE chiuso
                }
            } else {
                mysqli_stmt_close($stmt); // chiuso anche in caso di fallimento
            }
        }

        return $success;
    }
    



public function getListaLuoghi() {
    // 1. Modifica Query: Escludiamo i NULL e le stringhe vuote
    $query = "SELECT DISTINCT luogo 
                FROM animali 
                WHERE luogo IS NOT NULL AND luogo <> '' 
                ORDER BY luogo ASC";
    
    $luoghi = [];

    // Eseguiamo la query
    $result = mysqli_query($this->connection, $query);

    if (!$result) {

        return null; 
    }

    while ($row = mysqli_fetch_assoc($result)) {
        // 3. Pulizia dati: (Opzionale) Assicuriamoci che la prima lettera sia maiuscola
        // Utile se nel DB hai scritto "milano" invece di "Milano"
        $luogoPulito = ucfirst(strtolower($row['luogo']));
        
        // Evitiamo di reinserire duplicati se abbiamo normalizzato le maiuscole/minuscole
        if (!in_array($luogoPulito, $luoghi)) {
            $luoghi[] = $luogoPulito;
        }
    }

    mysqli_free_result($result);

    return $luoghi;
}
    

}    



?>
