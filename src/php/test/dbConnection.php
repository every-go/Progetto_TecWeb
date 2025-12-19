<?php 

require_once "..". DIRECTORY_SEPARATOR ."connectionDB.php";
use DB\DBAccess;

$db=new DBAccess();
		$stringaAnimali="";
		$connessioneOK=$db->openDBConnection();
        $stringaAnimali=$db->getList();
        // print($stringaAnimali);
        // print($stringaAnimali[0]["id"]);

if($connessioneOK){
            echo "connessioneok";
        $db->closeConnection();    
        }
else{
            echo "moaeengjoewnrocgphe07thilesu5";
        }   
        
?>