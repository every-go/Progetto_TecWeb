<?php

require_once dirname(__DIR__) .DIRECTORY_SEPARATOR.'php'.DIRECTORY_SEPARATOR.'templatedConnection.php';
use DB\DBAccess;





$tagPermessi ='<em><strong><ul><li>';

function pulisciInput($value){
 	// elimina gli spazi
 	$value = trim($value);
 	// rimuove tag html (non sempre è una buona idea!) 
  	$value = strip_tags($value);
  	// converte i caratteri speciali in entità html (ex. &lt;)
	$value = htmlentities($value);
  	return $value;
}

function pulisciNote($value){
	global $tagPermessi;

	// elimina gli spazi
 	$value = trim($value);
 	// rimuove tag html (non sempre è una buona idea!) 
  	$value = strip_tags($value,$tagPermessi);
  	return $value;
}




function singleCardBuilder($animale){
	$animaleCard=file_get_contents("animalCardTemplate.html");
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
	$animaleCard=str_replace("[DESCRIZIONE_ANIMALE]",$animale["descrizione"],$animaleCard);
	$animaleCard=str_replace("[STORIA]",$animale["storia"],$animaleCard);
	$animaleCard=str_replace("[ADOTTATO]",$animale["adottato"],$animaleCard);			
	return $animaleCard;	
}




	$paginaHTML=file_get_contents("animalListTemplate.html");
	
		$db=new DBAccess();
		$stringaAnimali="";
		$listaAnimali="";
		$connessioneOK=$db->openDBConnection();

		if($connessioneOK){
			$stringaAnimali=$db->getList();
			$db->closeConnection();

			foreach($stringaAnimali as $stringaAnimale){
				$listaAnimali.= singleCardBuilder($stringaAnimale);
			}

		}
		else{
			$listaAnimali="<p>I sistemi sono momentaneamente 
			fuori servizio, ci scusiamo per il disagio.</p>";
		}


	$paginaHTML= str_replace("[LISTA_ANIMALI]", $listaAnimali, $paginaHTML);

		echo  $paginaHTML;
	








?>