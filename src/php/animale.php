<?php 
    include "menu.php";
    require_once dirname(__DIR__) .DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'connectionDB.php';
    use DB\DBAccess;
    $content = file_get_contents("../html/animale.html");
    $content = str_replace("[listaMenu]", $listaMenu, $content);
    $content = str_replace("[listaFooter]", $listaFooter, $content);

    $breadcrumb = '';

    if (isset($_SESSION['provenienza']) && $_SESSION['provenienza'] === 'Preferiti') {
        // $breadcrumb = '<a href="../php/preferiti.php"> Preferiti </a>';
        if(isset($_GET['pagina']) && is_numeric($_GET['pagina'])){
            $breadcrumb .= ' > <a href="../php/preferiti.php?pagina='.$_GET['pagina'].'"> Animali </a>';
        } else {
            $breadcrumb .= ' > <a href="../php/preferiti.php"> Animali </a>';
        }
    } else {
        // $breadcrumb = '<a href="../php/animali.php"> Animali </a>';
        if(isset($_GET['pagina']) && is_numeric($_GET['pagina'])){
            $breadcrumb .= ' > <a href="../php/animali.php?pagina='.$_GET['pagina'].'"> Animali </a>';
        } else {
            $breadcrumb .= ' > <a href="../php/animali.php"> Animali </a>';
        }
    }
    $content = str_replace("[breadcrumb]", $breadcrumb, $content);
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
                $content = str_replace("[NOME]", $animalData["nome"], $content);
                $content = str_replace("[ETA]", $animalData["eta"] . ($animalData["eta"] == 1 ? " anno" : " anni"), $content);
                $content = str_replace("[LUOGO]", $animalData["luogo"], $content);
                $content = str_replace("[CARATTERE]", $animalData["carattere"], $content);
                $content = str_replace("[PATH_IMMAGINE]", $animalData["immagine"], $content);
                $content = str_replace("[ALT_IMMAGINE]", $animalData["alt"], $content);
                $content = str_replace("[DESCRIZIONE]", $animalData["descrizione"], $content);
                
                $bisogni = $animalData["bisogni"];
                // Inserisce <p> davanti a ogni nuovo punto numerato
                $bisogni = preg_replace(
                    '/(^|\s)(\d+\))\s/',
                    '<p>$2 ',
                    trim($bisogni)
                );
                // Chiude i <p>
                $bisogni = '<p>' . $bisogni . '</p>';
                $bisogni = str_replace('</p><p>', '</p><p>', $bisogni);
                // Chiusura corretta di ogni paragrafo
                $bisogni = preg_replace('/\s*(?=<p>)/', '</p>', $bisogni);
                $bisogni = rtrim($bisogni, '</p>') . '</p>';
                $content = str_replace("[BISOGNI]", $bisogni, $content);

                $content = str_replace("[STORIA]", $animalData["storia"], $content);
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
                    <a role="button" href="ispeziona_animale.php?id=' . $_GET["id"] .'" class="btn-modifica">Modifica</a>
                    <a role="button" href="elimina.php?id=' . $_GET["id"] .'" class="btn-elimina"> Elimina </a>
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