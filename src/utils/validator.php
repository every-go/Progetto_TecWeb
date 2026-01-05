<?php

// helper: Funzioni pure che fanno un solo controllo e ritornano true/false
// migliorano la leggibilità
function checkEmptyString($input, $nomeCampo = null) {
    // Controllo se è vuoto (trim per evitare spazi bianchi ingannevoli)
    if (empty(trim($input))) {
        
        // LOGICA DI COSTRUZIONE MESSAGGIO
        if ($nomeCampo !== null) {
            // Caso con nome: "Il campo 'Nome' è obbligatorio."
            return "Il campo $nomeCampo è obbligatorio.";
        } else {
            // Caso senza nome: "Questo campo è obbligatorio."
            return "Questo campo è obbligatorio.";
        }
    }
    
    return null;
}

function checkNotNaturalNumber($input, $nomeCampo = null) {
    if (filter_var($input, FILTER_VALIDATE_INT) !== false && intval($input) >= 0) {
        return null;
    } else {
        if ($nomeCampo !== null) {
            return "Il campo '$nomeCampo' deve essere un numero naturale.";
        } else {
            return "Questo campo deve essere un numero naturale.";
        }
    }
}


?>