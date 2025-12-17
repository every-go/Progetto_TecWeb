<?php

require_once "dbConnection.php";
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

}



class animalCardsBuilder{
	private $paginaHTML=file_get_contents(".." . DIRECTORY_SEPARATOR ."html".DIRECTORY_SEPARATOR."animali.html");
	
	public function build(){
		$db=new DBAccess();
		$stringaAnimali="";
		$connessioneOK=$db->openDBConnection();

		if($connessioneOK){
			$stringaAnimali=$db->getList();
			$db->closeConnection();

			foreach($stringaAnimali as $animale){
				singleCardBuilder($animale);
			}

		}
		else{
			$stringaAnimali="<p>I sistemi sono momentaneamente 
			fuori servizio, ci scusiamo per il disagio.</p>";
		}




		return $stringaAnimali;
	}
}





?>