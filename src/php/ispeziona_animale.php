<?php 
    // ATTENZIONE la pagina ispeziona_animale ha due segnaposti anche nel title, controlla
    // PS cancella questo commento quando hai completato
    require_once dirname(__DIR__) .DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'connectionDB.php';
    use DB\DBAccess;

    include "menu.php";
    $content = file_get_contents("../html/ispeziona_animale.html");
    if (!isset($_SESSION["username"]) || $_SESSION["role"] !== 'admin') {
        header('Location: home.php');
        exit();
    }

    $azione = '';

    $nome = $eta = $sesso = $altImmagine = $tipologia = $luogo = $taglia = $carattere = $descrizione = $bisogni = $storia = '';
    $nomeErr = $fileErr = $imageErr = $luogoErr = $etaErr = $descrizioneErr =  $bisogniErr = $storiaErr = '';

    $actionForm = '';

    // se l'id non è settato form per l'aggiunta 
    if ( !isset($_GET["id"])) {
        $azione = 'Aggiungi';
        $actionForm = 'gestisceAnimale.php';
        $content = str_replace("[pulsanteAzione]", $azione, $content);
        $content = str_replace("[Azione]", $azione, $content);
        $content = str_replace("[actionForm]", $actionForm, $content);
        $content = str_replace('[Nome]', ' animale' , $content);

        $content = str_replace("[nomeAnimale]", $nome, $content);
        $content = str_replace("[etaAnimale]", $eta, $content);

        $content = str_replace("[checkMaschio]", $sesso, $content);
        $content = str_replace("[checkFemmina]", $sesso, $content);

        $content = str_replace("[checkReale]", $tipologia, $content);
        $content = str_replace("[checkFantasy]", $tipologia, $content);

        $content = str_replace("[luogoAnimale]", $luogo, $content);

        $content = str_replace("[checkTagliaP]", $taglia, $content);
        $content = str_replace("[checkTagliaM]", $taglia, $content);
        $content = str_replace("[checkTagliaG]", $taglia, $content);

        $content = str_replace("[checkCarF]", $carattere, $content);
        $content = str_replace("[checkCarM]", $carattere, $content);
        $content = str_replace("[checkCarD]", $carattere, $content);

        $content = str_replace("[immagineCorrente]", '', $content);
        $content = str_replace("[altImmagine]", $altImmagine, $content);

        $content = str_replace("[descrizione]", $descrizione, $content);
        $content = str_replace("[bisogni]", $bisogni, $content);
        $content = str_replace("[storia]", $storia, $content);
    } else {    // sennò è per la modifica 
        $azione = 'Modifica';
        $id = intval($_GET['id']);
        $actionForm = 'gestisceAnimale.php?id=' . urlencode($id);
        $content = str_replace("[pulsanteAzione]", $azione, $content);
        $content = str_replace("[Azione]", $azione, $content);
        $content = str_replace("[actionForm]", $actionForm, $content);

        $db = new DBAccess();
        $connessioneOk = $db->openDBConnection();
        
        if ($connessioneOk) {
            $animalData = $db->getAnimalById($id);
            $db->closeConnection();
            if ($animalData) {
                $content = str_replace("[Nome]", ' ' . $animalData["nome"], $content);
                $content = str_replace("[nomeAnimale]", $animalData["nome"], $content);
                $content = str_replace("[etaAnimale]", $animalData["eta"], $content);
                
                $immagineCorrente = '<img src="'. $animalData["immagine"] . '">'; 
                $content = str_replace("[immagineCorrente]", $immagineCorrente, $content);


                if ($animalData['sesso'] === 'Maschio') {
                    $content = str_replace("[checkMaschio]", 'checked', $content);
                    $content = str_replace("[checkFemmina]", '', $content);
                } else {
                    $content = str_replace("[checkMaschio]", '', $content);
                    $content = str_replace("[checkFemmina]", 'checked', $content);
                }
                if ($animalData['tipo_animale'] === 'Reale') {
                    $content = str_replace("[checkReale]", 'checked', $content);
                    $content = str_replace("[checkFantasy]", '', $content);
                } else {
                    $content = str_replace("[checkReale]", '', $content);
                    $content = str_replace("[checkFantasy]", 'checked', $content);
                }

                //capire come fare per l'immagine effettivamente
                $content = str_replace("[altImmagine]", $animalData["alt"], $content);

                $content = str_replace("[luogoAnimale]", $animalData["luogo"], $content);

                if ($animalData['taglia'] === 'Piccola') {
                    $content = str_replace("[checkTagliaP]", 'checked', $content);
                    $content = str_replace("[checkTagliaM]", '', $content);
                    $content = str_replace("[checkTagliaG]", '', $content);
                } else if ($animalData['taglia'] === 'Media') {
                    $content = str_replace("[checkTagliaP]", '', $content);
                    $content = str_replace("[checkTagliaM]", 'checked', $content);
                    $content = str_replace("[checkTagliaG]", '', $content);
                } else {
                    $content = str_replace("[checkTagliaP]", '', $content);
                    $content = str_replace("[checkTagliaM]", '', $content);
                    $content = str_replace("[checkTagliaG]", 'checked', $content);
                }

                if ($animalData['carattere'] === 'Facile') {
                    $content = str_replace("[checkCarF]", 'checked', $content);
                    $content = str_replace("[checkCarM]", '', $content);
                    $content = str_replace("[checkCarD]", '', $content);
                } else if ($animalData['carattere'] === 'Moderato') {
                    $content = str_replace("[checkCarF]", '', $content);
                    $content = str_replace("[checkCarM]", 'checked', $content);
                    $content = str_replace("[checkCarD]", '', $content);
                } else {
                    $content = str_replace("[checkCarF]", '', $content);
                    $content = str_replace("[checkCarM]", '', $content);
                    $content = str_replace("[checkCarD]", 'checked', $content);
                }

                $content = str_replace("[descrizione]", $animalData["descrizione"], $content);
                $content = str_replace("[bisogni]", $animalData["bisogni"], $content);
                $content = str_replace("[storia]", $animalData["storia"], $content);


            } else {
                include dirname(__DIR__) . "/html/404.html";
                http_response_code(404);
                exit();
            }
        } else {
            header('Location: 500.php');
            exit();
        }
    }

    $content = str_replace("[Ispezione]", $azione, $content);


    $content = str_replace("[nomeErr]", $nomeErr, $content);
    $content = str_replace("[etaErr]", $etaErr, $content);
    $content = str_replace("[imageErr]", $imageErr, $content);
    $content = str_replace("[fileErr]", $imageErr, $content);
    $content = str_replace("[luogoErr]", $luogoErr, $content);
    $content = str_replace("[descrizioneErr]", $descrizioneErr, $content);
    $content = str_replace("[bisogniErr]", $bisogniErr, $content);
    $content = str_replace("[storiaErr]", $storiaErr, $content);

    // menu e footer
    $content = str_replace("[listaMenu]", $listaMenu, $content);
    $content = str_replace("[listaFooter]", $listaFooter, $content);
    echo $content;
?>