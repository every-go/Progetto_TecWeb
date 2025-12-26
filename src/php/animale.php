<?php 
    include "menu.php";
    require_once dirname(__DIR__) .DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'connectionDB.php';
    use DB\DBAccess;
    $content = file_get_contents("../html/animale.html");
    $content = str_replace("[listaMenu]", $listaMenu, $content);
    $content = str_replace("[listaFooter]", $listaFooter, $content);

    if (isset($_SESSION['provenienza']) && $_SESSION['provenienza'] === 'Preferiti') {
        $breadcrum = '<a href="../php/preferiti.php"> Preferiti </a>';
    } else {
        $breadcrum = '<a href="../php/animali.php"> Animali </a>';
    }
    $content = str_replace("[breadcrum]", $breadcrum, $content);
    $pulsanti = '';
    $sezioneAdottami = '';
    $animalData = '';
    $adottami = '';

    if(isset($_GET['id'])){
        $db = new DBAccess();
        $connessioneOK = $db->openDBConnection();

        if($connessioneOK){
            $animalData = $db->getAnimalById($_GET['id']);
            $db->closeConnection();
            if ($animalData) {
                $content = str_replace("[NOME]", htmlspecialchars($animalData["nome"]), $content);
                $content = str_replace("[ETA]", htmlspecialchars($animalData["eta"]) . ($animalData["eta"] == 1 ? " anno" : " anni"), $content);
                $content = str_replace("[LUOGO]", htmlspecialchars($animalData["luogo"]), $content);
                $content = str_replace("[CARATTERE]", htmlspecialchars($animalData["carattere"]), $content);
                $content = str_replace("[PATH_IMMAGINE]", htmlspecialchars($animalData["immagine"]), $content);
                $content = str_replace("[ALT_IMMAGINE]", htmlspecialchars($animalData["alt"]), $content);
                $content = str_replace("[DESCRIZIONE]", htmlspecialchars($animalData["descrizione"]), $content);
                $content = str_replace("[BISOGNI]", htmlspecialchars($animalData["bisogni"]), $content);
                $content = str_replace("[STORIA]", htmlspecialchars($animalData["storia"]), $content);
            } else {
                include dirname(__DIR__) . "/html/404.html";
                http_response_code(404);
                exit();
            }
        } else {
            include dirname(__DIR__) . "/html/500.html";
            http_response_code(500);
            exit();
        }

        if (isset($_SESSION["is_logged_in"]) && $_SESSION["is_logged_in"] === true && isset($_SESSION["role"]) && $_SESSION["role"] === 'admin') {
            $pulsanti .= '<div class="admin-buttons">
                    <a href="ispeziona_animale.php?id=' . $_GET["id"] .'" class="btn-modifica">Modifica</a>
                    <a href="elimina.php?id=' . $_GET["id"] .'" class="btn-elimina"> Elimina </a>
                    </div>';
	    } else {
            $adottami = '<aside>
            <h2>Ti piaccio? Adottami!</h2>
            <p>Ogni essere, reale o straordinario, cerca un luogo in cui sentirsi al sicuro. Io non faccio eccezione.
                Che la mia natura sia comune o rara, ciò che posso offrire è semplice: compagnia, presenza e un legame
                sincero. Se qualcosa di me ha acceso la tua curiosità, forse è il primo passo verso un incontro
                speciale. Scoprimi un po’ di più… potrei essere il compagno che stavi cercando.</p>
            <button>Adotta!</button>
            </aside>';
        }


    } else{
        include dirname(__DIR__) . "/html/404.html";
        http_response_code(404);
        exit();
    }

    $content = str_replace("[pulsanti]", $pulsanti, $content);
    $content = str_replace("[adottami]", $adottami, $content);
    
    echo $content;
?>