<?php
namespace DB;

use mysqli;
use mysqli_result;

class DBAccess {

	private const HOST_DB = "mysql_db";
	private const DATABASE_NAME = "mydb";
	private const USERNAME = "rot";
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

// 	public function insertNewElement($nome, $capitano, $dataNascita, $luogo, $squadra, $ruolo, $altezza, $maglia, $magliaNazionale, $punti, $riconoscimenti, $note, $genere) {

// 		$queryInsert = 'INSERT INTO atleti (nome, capitano, dataNascita, luogo, squadra, ruolo, altezza, maglia, magliaNazionale, punti, riconoscimenti, note, genere) VALUES ("$nome", "$capitano", "$dataNascita", "$luogo", "$squadra", "$ruolo", $altezza, $maglia, $magliaNazionale, $punti, "$riconoscimenti", "$note", $genere)';

// 		$queryResult = mysqli_query($this->connection, $queryInsert) or die("Errore in dbConnection: " . mysqli_error($this->connection));
// 		//controllo presenza di righe
// 		if (mysqli_affected_rows($this->connection) > 0) {
// 			return true;
// 		} else {
// 			return false;
// 		}
// 	}

	
// 


// 
}
?>