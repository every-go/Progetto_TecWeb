<?php 

	require_once '.'.DIRECTORY_SEPARATOR.'connectionDB.php';
	use DB\DBAccess;

	function singleCardBuilder($animale){
		$animaleCard=file_get_contents("html/animalCardTemplate.html");
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

	include "menu.php";
	$content = file_get_contents("html/animali.html");
	$content = str_replace("[listaMenu]", $listaMenu, $content);
	$content = str_replace('[listaFooter]', $listaFooter, $content);

	//setto la provenienza per la breadcrum
	$_SESSION['provenienza'] = 'Animali';

	$animaliPerPagina = 3;
	$paginaCorrente = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
	if($paginaCorrente < 1) $paginaCorrente = 1; 

	$stringaAnimali="";
	$listaAnimali="";
	$db=new DBAccess();
	$connessioneOK=$db->openDBConnection();

	if($connessioneOK){
		$stringaAnimali=$db->getList();
		$db->closeConnection();

		// calcoli per la paginazione
		$totaleAnimali = count($stringaAnimali);
		$totalePagine = ceil($totaleAnimali / $animaliPerPagina);
		if($paginaCorrente > $totalePagine && $totalePagine > 0) {
			$paginaCorrente = $totalePagine;
		}
		$offest = ($paginaCorrente - 1) * $animaliPerPagina;
		$animaliPaginaCorrente = array_slice($stringaAnimali, $offest, $animaliPerPagina);

		foreach($animaliPaginaCorrente as $stringaAnimale){
			$listaAnimali.= singleCardBuilder($stringaAnimale);
		}
		$listaAnimali='<ul class="animali">'.$listaAnimali.'</ul>';

		if ($paginaCorrente > 1 ) {
			$content = str_replace("[indietroLink]", 'href="animali.php?pagina=' . $paginaCorrente - 1 .'"', $content);
		} else {
			$content = str_replace("[indietroLink]", 'class="disabled" aria-disabled="true"', $content);
		}

		$indicatorePagina = '<span class="indicatore-pagina"> Pagina ' . $paginaCorrente . ' di ' . $totalePagine . '</span>';
		$content = str_replace('[indicatorePagina]', $indicatorePagina, $content);


		if ($paginaCorrente < $totalePagine) {
			$content = str_replace("[avantiLink]", 'href="animali.php?pagina=' . $paginaCorrente + 1 . '"', $content);
		} else {
			$content = str_replace("[avantiLink]", 'class="disabled" aria-disabled="true"', $content);

		}

	} else{
		$listaAnimali="<p>I sistemi sono momentaneamente 
		fuori servizio, ci scusiamo per il disagio.</p>";
	}

	$pulsanteAdd = '';
	if (isset($_SESSION["is_logged_in"]) && $_SESSION["is_logged_in"] === true && isset($_SESSION["role"]) && $_SESSION["role"] === 'admin') {
		$pulsanteAdd = '<a id="add-animal-btn" role="button" href="ispeziona_animale.php?">
            <span class="material-symbols-outlined" aria-hidden="true">add</span>
            Aggiungi </a>';
	}

	$content = str_replace("[addButton]", $pulsanteAdd, $content);
	$content= str_replace("[LISTA_ANIMALI]", $listaAnimali, $content);
    
   echo $content;
?>