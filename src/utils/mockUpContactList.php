<?php
require_once dirname(__DIR__).DIRECTORY_SEPARATOR.'connectionDB.php';
use DB\DBAccess;




function mockUpContactList(){
    $lista=null;
    $dbConnection=new DBAccess();
    if($dbConnection->openDBConnection()){
        
        if($lista=$dbConnection->getListaLuoghi()){
            $dbConnection->closeConnection();
            $lista=array_map(function($luogo){
                return [
                    "nome"=>$luogo,
                    "email"=>"mail fittizia ".$luogo.'@casaOtium.it',
                    "numero di telefono"=>"+XX 123 YYY ZZZZZZZZ"
                ];
            },$lista);
            }
        }
        

    return $lista;
    }
    
function contactTableRowBuilder(){
    $contactList=mockUpContactList();
    $tableRows="";
    foreach($contactList as $contact){
        $tableRows.="<tr>";
        $tableRows.="<td>".$contact['nome']."</td>";
        $tableRows.="<td>".$contact['email']."</td>";
        $tableRows.="<td>".$contact['numero di telefono']."</td>";
        $tableRows.="</tr>";
    }
    return $tableRows;
}


?>