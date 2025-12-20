<?php 
    include "menu.php";
    require_once dirname(__DIR__) .DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'connectionDB.php';
    use DB\DBAccess;
    $content = file_get_contents("../html/animale.html");
    $content = str_replace("[listaMenu]", $listaMenu, $content);
    $content = str_replace("[listaFooter]", $listaFooter, $content);

    if(isset($_GET['id'])){
        $db = new DBAccess();
        $connessioneOK = $db->openDBConnection();

        if($connessioneOK){
            $animalData = $db->getAnimalById($_GET['id']);
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
            $db->closeConnection();
        } else {
            include dirname(__DIR__) . "/html/500.html";
            http_response_code(500);
            exit();
        }
    } else {
            include dirname(__DIR__) . "/html/404.html";
        http_response_code(404);
        exit();
    }



    
    echo $content;
?>