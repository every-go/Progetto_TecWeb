<?php
namespace DB;

use mysqli;
use mysqli_result;

class DBAccess {

	private const HOST_DB = "mysql_db";
	private const DATABASE_NAME = "mydb";
	private const USERNAME = "root";
	private const PASSWORD = "root";

	private $connection;

	public function openDBConnection() {
		mysqli_report(MYSQLI_REPORT_ERROR);

		$this->connection = @mysqli_connect(DBAccess::HOST_DB, DBAccess::USERNAME, DBAccess::PASSWORD, DBAccess::DATABASE_NAME);

	if (!$this->connection) {
    // Log the error (optional but recommended)
    // error_log("Database connection failed: " . mysqli_connect_error());
    
    	http_response_code(500);
		include dirname(__DIR__) . "/html/500.html";
    
    // Stop script execution
    	exit();
	}

		return true;

	}

	public function closeConnection() {
		mysqli_close($this->connection);
	}


public function inserisciAnimale($nome, $alt, $immagine, $sesso, $tipo_animale, $luogo, $eta, $taglia, $carattere, $descrizione, $bisogni, $storia) {
    
    $query = "INSERT INTO animali (nome,alt,immagine,sesso,tipo_animale,luogo,eta,taglia,carattere,descrizione,bisogni,storia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($this->connection, $query)) {
        mysqli_stmt_bind_param($stmt, "ssssssssssss", $nome, $alt, $immagine, $sesso, $tipo_animale, $luogo, $eta, $taglia, $carattere, $descrizione, $bisogni, $storia);
        
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
		//controllo errori sulla query
		$queryResult = mysqli_query($this->connection, $query) or die("Errore in dbConnection: " . mysqli_error($this->connection));
		//controllo presenza di righe
		if (mysqli_num_rows($queryResult) != 0) {
			$result = array();
			while ( $row = mysqli_fetch_assoc($queryResult) ) {
				array_push($result, $row); //array di array associativi 
			}
			$queryResult->free();
			return $result;
		} else {
			return false;
		}
	}
	
	public function getAnimalById($id) {
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

   
function loginUtente($username, $passwordInserita) {
    
    // 1. Prepara la query (SOLO con username, per sicurezza)
    $sql = "SELECT username, password FROM utenti WHERE username = ?";
    
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
            if ($passwordInserita === $row['password']) {
                
                // --- PUNTO BONUS COL PROFESSORE ---
                // Rigenera l'ID per evitare il furto di sessione
                session_regenerate_id(true); 
                // ----------------------------------

                // 4. Imposta le variabili di sessione
                $_SESSION['username'] = $row['username'];
                $_SESSION['is_logged_in'] = true; // Comodo per i controlli nelle altre pagine

                return true; // Login riuscito
            }
        }

    }
    return false; // Login fallito
}

}



?>