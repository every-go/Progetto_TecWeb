<?php 

	require_once dirname(__DIR__) .DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'connectionDB.php';
	use DB\DBAccess;

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
		return $animaleCard;	
	}

   include "menu.php";
   $content = file_get_contents("../html/animali.html");
   $content = str_replace("[listaMenu]", $listaMenu, $content);
   $content = str_replace('[listaFooter]', $listaFooter, $content);


	$stringaAnimali="";
	$listaAnimali="";
	$db=new DBAccess();
	$connessioneOK=$db->openDBConnection();

	if($connessioneOK){
		$stringaAnimali=$db->getList();
		$db->closeConnection();
		foreach($stringaAnimali as $stringaAnimale){
			$listaAnimali.= singleCardBuilder($stringaAnimale);
		}
		$listaAnimali='<ul class="animali">'.$listaAnimali.'</ul>';
	} else{
		$listaAnimali="<p>I sistemi sono momentaneamente 
		fuori servizio, ci scusiamo per il disagio.</p>";
	}

	$pulsanteAdd = '';
	if (isset($_SESSION["is_logged_in"]) && $_SESSION["is_logged_in"] === true && isset($_SESSION["role"]) && $_SESSION["role"] === 'admin') {
		$pulsanteAdd = '<a id="add-animal-btn" href="ispeziona_animale.php?">
            <span class="material-symbols-outlined" aria-hidden="true">add</span>
            Aggiungi </a>';
	}

	$content = str_replace("[addButton]", $pulsanteAdd, $content);
	$content= str_replace("[LISTA_ANIMALI]", $listaAnimali, $content);
    
   echo $content;
?>