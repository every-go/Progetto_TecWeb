<?php

namespace DB;

require_once "utils/imageHandler.php";

use ImageHandler;
use mysqli;
use mysqli_result;
use throwable;
use exception;

class DBAccess
{
    //XAMPP localhost mydb root ""
    //Docker db mydb root root
    //localhost mmazzare mmazzare password
    private const HOST_DB = "localhost";
    private const DATABASE_NAME = "mydb";
    private const USERNAME = "root";
    private const PASSWORD = "";

    private $connection;

    public function openDBConnection() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $this->connection = mysqli_connect(
                self::HOST_DB,
                self::USERNAME,
                self::PASSWORD,
                self::DATABASE_NAME
            );
            return true;

        } catch (\mysqli_sql_exception $e) {
            http_response_code(500);
            // Pagina di errore user-friendly
            include __DIR__ . "/500.php";
            exit();
        }
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
                "ssssssisssss",
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

            if (mysqli_stmt_execute($stmt)) {
                $nuovoId = $this->connection->insert_id;

                mysqli_stmt_close($stmt);
                return $nuovoId;
            }

            mysqli_stmt_close($stmt);
        }

        return false;
    }


    function getList()
    {
        $query = "SELECT * FROM animali WHERE adottato = 0 ORDER BY id ASC";

        ($queryResult = mysqli_query($this->connection, $query)) or
            die("Errore in dbConnection: " . mysqli_error($this->connection));

        $result = [];

        while ($row = mysqli_fetch_assoc($queryResult)) {
            array_push($result, $row);
        }
        $queryResult->free();
        return $result;
    }

    function getAllList(){
        $query = "SELECT * FROM animali ORDER BY id ASC";

        ($queryResult = mysqli_query($this->connection, $query)) or
            die("Errore in dbConnection: " . mysqli_error($this->connection));

        $result = [];

        while ($row = mysqli_fetch_assoc($queryResult)) {
            array_push($result, $row);
        }
        $queryResult->free();
        return $result;
    }

    function eliminaAnimale($id)
    {
        $risultato = false;
        $immagine = null;

        mysqli_begin_transaction($this->connection);

        try {
            $queryImm = "SELECT immagine FROM animali WHERE id = ?";
            $stmt = mysqli_prepare($this->connection, $queryImm);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $res = mysqli_stmt_get_result($stmt);
            if ($res && mysqli_num_rows($res) > 0) {
                $immagine = mysqli_fetch_assoc($res)['immagine'];
            }
            mysqli_free_result($res);
            mysqli_stmt_close($stmt);

            $queryDel = "DELETE FROM animali WHERE id = ?";
            $stmt = mysqli_prepare($this->connection, $queryDel);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) === 0) {
                throw new Exception("Nessuna riga eliminata");
            }

            mysqli_stmt_close($stmt);

            mysqli_commit($this->connection);
            $risultato = true;
        } catch (Throwable $e) {
            mysqli_rollback($this->connection);
            return false;
        }

        if (is_string($immagine) && $immagine !== "") {
            $uploadDirSystem = "images/uploads";
            $uploadDirWeb = "images/uploads/";

            $imgHandler = new ImageHandler($uploadDirSystem, $uploadDirWeb);
            $imgHandler->delete($immagine);
        }

        return $risultato;
    }


    function aggiornaAnimale(
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
                    $risultato = true;
                }
            }
            mysqli_stmt_close($stmt);
        }

        return $risultato;
    }

    function getAnimalById($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            return false;
        }

        $query = "SELECT * FROM animali WHERE id = ?";
        $animalData = false;

        if ($stmt = mysqli_prepare($this->connection, $query)) {
            mysqli_stmt_bind_param($stmt, "i", $id);

            if (mysqli_stmt_execute($stmt)) {
                $queryResult = mysqli_stmt_get_result($stmt);

                if ($queryResult) {
                    if (mysqli_num_rows($queryResult) > 0) {
                        $animalData = mysqli_fetch_assoc($queryResult);
                    }
                    mysqli_free_result($queryResult);
                }
            }
            mysqli_stmt_close($stmt);
        }
        return array_map( 'htmlspecialchars', $animalData);
    }

    function getImmagineById($id){
        if (!filter_var($id, FILTER_VALIDATE_INT) || $id <= 0) {
            return false;
        }

        $animalData = false;

        $query = "SELECT immagine, alt FROM animali WHERE id = ?";

        if ($stmt = mysqli_prepare($this->connection, $query)) {
            mysqli_stmt_bind_param($stmt, "i", $id);

            if (mysqli_stmt_execute($stmt)) {
                $queryResult = mysqli_stmt_get_result($stmt);

                if ($queryResult) {
                    if (mysqli_num_rows($queryResult) > 0) {
                        $animalData = mysqli_fetch_assoc($queryResult);
                    }
                    mysqli_free_result($queryResult);
                }
            }
            mysqli_stmt_close($stmt);
        }
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

        return $animalData;
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

        return $animalData;
    }

    public function adottaAnimale($username, $animalId)
    {
        mysqli_begin_transaction($this->connection);
        $adozioneRiuscita = false;

        try {
            $sql = "INSERT INTO adottati (username, id) VALUES (?, ?)";
            $stmt = mysqli_prepare($this->connection, $sql);
            mysqli_stmt_bind_param($stmt, "si", $username, $animalId);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) === 0) {
                throw new Exception("Insert adottati fallita");
            }
            mysqli_stmt_close($stmt);

            $updateQuery = "UPDATE animali SET adottato = 1 WHERE id = ?";
            $stmt = mysqli_prepare($this->connection, $updateQuery);
            mysqli_stmt_bind_param($stmt, "i", $animalId);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) === 0) {
                throw new Exception("Update animale fallita");
            }
            mysqli_stmt_close($stmt);

            $deleteQuery = "DELETE FROM preferiti WHERE username = ? AND id = ?";
            $stmt = mysqli_prepare($this->connection, $deleteQuery);
            mysqli_stmt_bind_param($stmt, "si", $username, $animalId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            mysqli_commit($this->connection);
            $adozioneRiuscita = true;
        } catch (Throwable $e) {
            mysqli_rollback($this->connection);
            return false;
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
        $sql = "SELECT username, password, admin FROM utenti WHERE username = ?";

        if ($stmt = mysqli_prepare($this->connection, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            $row = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            mysqli_stmt_close($stmt);

            if ($row && password_verify($passwordInserita, $row["password"])) {
                session_regenerate_id(true);

                $_SESSION["username"] = $row["username"];
                $_SESSION["is_logged_in"] = true;
                $_SESSION["role"] = $row["admin"] ? "admin" : "user";

                return true;
            }
        }
        return false;
    }

    public function registerUtente($username, $password)
    {
        $query = "INSERT INTO utenti (username, password) VALUES (?, ?)";
        $success = false;
        
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        if ($stmt = mysqli_prepare($this->connection, $query)) {
            
            mysqli_stmt_bind_param($stmt, "ss", $username, $passwordHash);

            if (!mysqli_stmt_execute($stmt)) {
                if (mysqli_errno($this->connection) == 1062) {
                    error_log("Tentativo di registrazione con username duplicato: $username");
                    $success = false;
                } else {
                    error_log(mysqli_error($this->connection));
                }
            } elseif (mysqli_stmt_affected_rows($stmt) > 0) {
                $success = true;
            }

            mysqli_stmt_close($stmt);
        } else {
            error_log(mysqli_error($this->connection));
        }

        return $success;
    }


    public function checkUsernameEsistente($username)
    {
        $query = "SELECT username FROM utenti WHERE username = ?";
        $success = false;

        if ($stmt = mysqli_prepare($this->connection, $query)) {
            mysqli_stmt_bind_param($stmt, "s", $username);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    $success = true;            
                }
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log(mysqli_error($this->connection));
        }

        return $success;
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
        $success = false;
        mysqli_begin_transaction($this->connection);

        try {
            $queryInserimento = "INSERT INTO adottati (username, id) VALUES (?, ?)";
            $stmt = mysqli_prepare($this->connection, $queryInserimento);
            mysqli_stmt_bind_param($stmt, "si", $username, $idAnimale);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) === 0) {
                throw new Exception("Insert fallita");
            }
            mysqli_stmt_close($stmt);

            $queryAggiornamento = "UPDATE animali SET adottato = TRUE WHERE id = ?";
            $stmt = mysqli_prepare($this->connection, $queryAggiornamento);
            mysqli_stmt_bind_param($stmt, "i", $idAnimale);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) === 0) {
                throw new Exception("Update fallita");
            }
            mysqli_stmt_close($stmt);

            $success = $this->rimuoviPreferito($username, $idAnimale);

            mysqli_commit($this->connection);
        } catch (Throwable $e) {
            mysqli_rollback($this->connection);
            return false;
        }

        return $success;
    }

    public function getAdottatoDa($id)
    {
        $query = "SELECT username FROM adottati WHERE id = ?";
        $username = null;

        if ($stmt = mysqli_prepare($this->connection, $query)) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            if (mysqli_stmt_execute($stmt)) {
                $queryResult = mysqli_stmt_get_result($stmt);
                if ($queryResult && ($row = mysqli_fetch_assoc($queryResult))) {
                    $username = $row['username'];
                }
                mysqli_free_result($queryResult);
            }
            mysqli_stmt_close($stmt);
        } else {
            error_log(mysqli_error($this->connection));
        }

        return $username;
    }
}
?>