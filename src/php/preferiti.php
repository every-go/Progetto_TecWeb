<?php 

	function singleCardBuilder($animale){
		$animaleCard=file_get_contents("../html/animalCardTemplate.html");
		$animaleCard=str_replace("[ID_ANIMALE]",$animale["id"],$animaleCard);
		$animaleCard=str_replace("[NOME_ANIMALE]",$animale["nome"],$animaleCard);
		$animaleCard=str_replace("[ALT_IMMAGINE]",$animale["alt"],$animaleCard);
		$animaleCard=str_replace("[PATH_IMMAGINE]",$animale["immagine"],$animaleCard);
		$animaleCard=str_replace("[TIPO]",$animale["tipo_animale"],$animaleCard);
		$animaleCard=str_replace("[LUOGO]",$animale["luogo"],$animaleCard);
		if($animale["eta"]==1){
			$animale["eta"]="1 anno";
		}
		else{
			$animale["eta"]=$animale["eta"]." anni";
		}
		$animaleCard=str_replace("[ETA_ANIMALE]",$animale["eta"],$animaleCard);
		$animaleCard=str_replace("[TAGLIA]",$animale["taglia"],$animaleCard);
		$animaleCard=str_replace("[CARATTERE]",$animale["carattere"],$animaleCard);	

		//gestione provenienza per tornare alla pagina corretta dopo la visualizzazione del singolo animale
		if(isset($_GET['pagina'])&&is_numeric($_GET['pagina'])){
			$animaleCard=str_replace("[PAGINA_PROVENIENZA]",'&pagina='.$_GET['pagina'],$animaleCard);
		}
		else{
			$animaleCard=str_replace("[PAGINA_PROVENIENZA]",'',$animaleCard);
		}



		return $animaleCard;	
	}

    require_once dirname(__DIR__) .DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'connectionDB.php';
    use DB\DBAccess;

    include "menu.php";
    $content = file_get_contents("../html/preferiti.html");

    // Controllo accesso admin
    if (!isset($_SESSION["username"]) || $_SESSION["role"] !== 'user') {
        header('Location: home.php');
        exit();
    }

    $_SESSION['provenienza'] = 'Preferiti';
    $stringaPreferiti="";
    $h2Preferiti="";
    $listaPreferiti="";
    $h2Adottati="";
    $listaAdottati="";
    $db = new DBAccess();
    $connessioneOK = $db->openDBConnection();

    if($connessioneOK){
        $stringaPreferiti = $db->getPreferiti($_SESSION['username']);
        $stringaAdottati = $db->getAdottati($_SESSION['username']);
        $db->closeConnection();

        // Preferiti
        if(!empty($stringaPreferiti)){
            $h2Preferiti="<h2 id = 'preferiti-content'>I tuoi animali preferiti </h2>";
            $listaPreferiti = '<ul class="animali">';
            foreach ($stringaPreferiti as $stringaAnimale) {
                $listaPreferiti .= singleCardBuilder($stringaAnimale);
            }
            $listaPreferiti .= '</ul>';
        }

        // Adottati
        if (!empty($stringaAdottati)) {
            $h2Adottati = "<h2 id='adottati-content'>I tuoi animali adottati</h2>";
            $listaAdottati = '<ul class="animali">';
            foreach ($stringaAdottati as $stringaAnimale) {
                $listaAdottati .= singleCardBuilder($stringaAnimale);
            }
            $listaAdottati .= '</ul>';
        }
    }
    else{
        $listaPreferiti="<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>";
    }


    
    $content= str_replace("[H2_PREFERITI]", $h2Preferiti, $content);
    $content= str_replace("[LISTA_PREFERITI]", $listaPreferiti, $content);
    $content= str_replace("[H2_ADOTTATI]", $h2Adottati, $content);
    $content= str_replace("[LISTA_ADOTTATI]", $listaAdottati, $content);
    $content = str_replace("[listaMenu]", $listaMenu, $content);
    $content = str_replace("[listaFooter]", $listaFooter, $content);
    echo $content;
?>