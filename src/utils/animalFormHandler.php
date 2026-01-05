<?php


//Singolo punto di modifica per gestire la corrispondenza tra 
// i nomi dei campi del form 
// i nomi usati nel DB 
// i placeholder nell'HTML 


class campiDatiAnimale{
    public static $testi = [
        'nome'        => ['form' => 'nome',          'placeholder' => 'nomeAnimale','errPlaceholder' => 'nomeErr'],
        'eta'         => ['form' => 'eta',           'placeholder' => 'etaAnimale','errPlaceholder' => 'etaErr'],
        'luogo'       => ['form' => 'luogo',         'placeholder' => 'luogoAnimale','errPlaceholder' => 'luogoErr'],
        'descrizione' => ['form' => 'descrizione',   'placeholder' => 'descrizione','errPlaceholder' => 'descrizioneErr'],
        'alt'         => ['form' => 'alt',           'placeholder' => 'altImmagine','errPlaceholder' => 'imageErr'],
        'bisogni'     => ['form' => 'bisogni',       'placeholder' => 'bisogni','errPlaceholder' => 'bisogniErr'],
        'storia'      => ['form' => 'storia',        'placeholder' => 'storia','errPlaceholder' => 'storiaErr']
    ];
    public static $radio = [
        'sesso' => [
            'form' => 'sesso', 
            'opzioni' => ['Maschio' => 'checkMaschio', 'Femmina' => 'checkFemmina'],
            'errPlaceholder'=>'sessoErr'
        ],
        'tipo_animale' => [
            'form' => 'tipo_animale', 
            'opzioni' => ['Reale' => 'checkReale', 'Fantasy' => 'checkFantasy'],
            'errPlaceholder'=>'tipoAnimaleErr'
        ],
        'taglia' => [
            'form' => 'taglia',
            'opzioni' => ['Piccola' => 'checkTagliaP', 'Media' => 'checkTagliaM', 'Grande' => 'checkTagliaG'],  
            'errPlaceholder'=>'tagliaErr'
        ],
        'carattere' => [
            'form' => 'carattere',
            'opzioni' => ['Facile' => 'checkCarF', 'Moderato' => 'checkCarM', 'Difficile' => 'checkCarD'],  
            'errPlaceholder'=>'carattereErr'
        ]
    ];
    public static $files = [
        'immagine' => ['form' => 'immagine', 'placeholder' => 'immagineCorrente', 'errPlaceholder' => 'fileErr']
    ];
}



// crea il form per aggiungere oppure il  per modificare un animale
function createForm($azione, $id = null) {
    if($azione!=="Aggiungi"&&($azione!=="Modifica" || $id===null || !is_numeric($id))){
            include dirname(__DIR__).DIRECTORY_SEPARATOR.'..' . "/html/404.html";
            http_response_code(404);
            exit();
        }
    $content = file_get_contents("html/ispeziona_animale.html");
    $content = str_replace("[Ispezione]", $azione, $content);
    $content = str_replace("[pulsanteAzione]", $azione, $content);
    $content = str_replace("[Azione]", $azione, $content);
    
    if($azione==="Aggiungi"){
        $actionForm = 'ispeziona_animale.php';
        $content = str_replace("[actionForm]", $actionForm, $content);
    }
    elseif ($azione==="Modifica"){
        $actionForm = 'ispeziona_animale.php?id=' . urlencode($id);
        $content = str_replace("[actionForm]", $actionForm, $content);
    }


    return $content;
}

function populateForm($animalData, $content) {
    // Sostituisci i placeholder con i dati dell'animale
    $placeholders = [
        'nomeAnimale' => $animalData["nome"],
        'etaAnimale' => $animalData["eta"],
        'luogoAnimale' => $animalData["luogo"],
        'descrizione' => $animalData["descrizione"],
        'altImmagine' => $animalData["alt"],
        'bisogni' => $animalData["bisogni"],
        'storia' => $animalData["storia"]
    ];
    $content = replaceTextPlaceholder($content, $placeholders);
    $content = renderRadio($content, $animalData);
    // Gestione immagine corrente
    if (!empty($animalData["immagine"])) {
        $content = str_replace("[immagineCorrente]", '<img src="' . htmlspecialchars($animalData["immagine"]) . '" alt="' . htmlspecialchars($animalData["alt"]) . '" class="current-image">', $content);
    } else {
        $content = str_replace("[immagineCorrente]", 'Nessuna immagine caricata.', $content);
    }
    return $content;
}

function cleanForm($content) {
    // Pulisce i placeholder del form
    $content = removeFormDataPlaceholders($content);
    $content = removeErrorPlaceholders($content);
    return $content;
}

function replaceTextPlaceholder($html, $placeholders) {
    foreach ($placeholders as $key => $value) {
        $html = str_replace("[$key]", $value, $html);
    }
    return $html;
}
function renderRadio($html, $datiUtente) {
    // Configurazione centralizzata dei radio button
    $config = [
        'sesso' => ['Maschio' => 'checkMaschio', 'Femmina' => 'checkFemmina'],
        'tipo_animale' => ['Reale' => 'checkReale', 'Fantasy' => 'checkFantasy'],
        'taglia' => ['Piccola' => 'checkTagliaP', 'Media' => 'checkTagliaM', 'Grande' => 'checkTagliaG'],
        'carattere' => ['Facile' => 'checkCarF', 'Moderato' => 'checkCarM', 'Difficile' => 'checkCarD']
    ];

    foreach ($config as $campo => $opzioni) {
        $selezione = $datiUtente[$campo] ?? '';
        foreach ($opzioni as $valore => $placeholder) {
            // Selezionato se corrisponde al valore utente
            $checked = ($selezione === $valore) ? 'checked' : '';
            $html = str_replace("[$placeholder]", $checked, $html);
        }
    }
    return $html;
}




function replaceErrorPlaceholders($html, $errori) {
    $chiavi=array_merge(
        array_column(campiDatiAnimale::$testi,'errPlaceholder'),
        array_column(campiDatiAnimale::$radio,'errPlaceholder'),
        array_column(campiDatiAnimale::$files,'errPlaceholder')
    );
    foreach ($chiavi as $key) {
        $html = str_replace("[$key]", $errori[$key] ?? '', $html);
    }
    return $html;
}


// Rimuove i placeholder degli errori dall'HTML
function removeErrorPlaceholders($html) {
    $chiavi=array_merge(
        array_column(campiDatiAnimale::$testi,'errPlaceholder'),
        array_column(campiDatiAnimale::$radio,'errPlaceholder'),
        array_column(campiDatiAnimale::$files,'errPlaceholder')
    );
    foreach ($chiavi as $key) {
        $html = str_replace("[$key]", '', $html);
    }
    return $html;
}

// Rimuove i dati del form dall'HTML
function removeFormDataPlaceholders($html) {
    $placeholders = [
        'nomeAnimale',
        'etaAnimale',
        'luogoAnimale',
        'descrizione',
        'altImmagine',
        'bisogni',
        'storia',
        'immagineCorrente',
        'checkMaschio',
        'checkFemmina',
        'checkReale',
        'checkFantasy',
        'checkTagliaP',
        'checkTagliaM',
        'checkTagliaG',
        'checkCarF',
        'checkCarM',
        'checkCarD'
    ];
    foreach ($placeholders as $key) {
        $html = str_replace("[$key]", '', $html);
    }
    return $html;
}



function checkSelection($input, $allowedValues, $nomeCampo) {
    if (empty($input)) {
        return "Seleziona un'opzione per $nomeCampo.";
    }
    if (!in_array($input, $allowedValues)) {
        return "Valore non valido per $nomeCampo.";
    }
    return null;
}


// function stampaErrori($errori, $content){
//     foreach ($errori as $key => $messaggio) {
//         $content = str_replace($key, $messaggio ?? '', $content);
//     }
//     return $content;
// }

// Funzione principale di validazione del form animale, ritorna array di errori
function checkForm($data) {
    $errori = [];

    // Scorciatoie per leggibilità
    $confTesti = campiDatiAnimale::$testi;
    $confRadio = campiDatiAnimale::$radio;
    $confFiles = campiDatiAnimale::$files;

    // ==================== 1. VALIDAZIONI TESTUALI ====================
    // Accedo alla configurazione per prendere la chiave corretta dell'errore (es. 'nomeErr')
    // Nome
    $errori[$confTesti['nome']['errPlaceholder']] = checkEmptyString($data['nome'], 'Nome');
    
    // Luogo
    $errori[$confTesti['luogo']['errPlaceholder']] = checkEmptyString($data['luogo'], 'Luogo');
    
    // Età (Validazione specifica per numero naturale)
    $errori[$confTesti['eta']['errPlaceholder']] = checkNotNaturalNumber($data['eta'], 'Età');
    
    // Descrizione
    $errori[$confTesti['descrizione']['errPlaceholder']] = checkEmptyString($data['descrizione'], 'Descrizione');
    
    // Bisogni
    $errori[$confTesti['bisogni']['errPlaceholder']] = checkEmptyString($data['bisogni'], 'Bisogni');
    
    // Storia
    $errori[$confTesti['storia']['errPlaceholder']] = checkEmptyString($data['storia'], 'Storia');
    
    // Alt Immagine (se presente nei testi)
    $errori[$confTesti['alt']['errPlaceholder']] = checkEmptyString($data['alt'], 'Descrizione Immagine');


    // ==================== 2. VALIDAZIONI RADIO / SELECT ====================
    // Poiché la logica è identica per tutti (checkSelection), usiamo un ciclo dinamico.
    // Questo rende il codice molto più robusto: se aggiungi un nuovo radio nella classe,
    // viene validato automaticamente qui senza toccare nulla.
    
    foreach ($confRadio as $dbKey => $config) {
        // 1. Recupero il nome del campo nel form (es. 'sesso')
        // Nota: Assicurati che $config['form'] esista nella tua classe, o usa $dbKey se coincidono
        $inputName = isset($config['form']) ? $config['form'] : $dbKey;
        
        // 2. Recupero la chiave per l'errore (es. 'sessoErr')
        $errKey = $config['errPlaceholder'];
        
        // 3. Recupero i valori ammessi (Le chiavi dell'array opzioni: Maschio, Femmina...)
        $valoriAmmessi = array_keys($config['opzioni']);
        
        // 4. Etichetta leggibile per l'errore (es. 'tipo_animale' -> 'Tipo animale')
        $label = ucfirst(str_replace('_', ' ', $dbKey)); 

        // 5. Eseguo la validazione
        $errori[$errKey] = checkSelection(
            $data[$inputName] ?? '', 
            $valoriAmmessi, 
            $label
        );
    }

    // ==================== 4. PULIZIA ====================
    // Rimuove dall'array tutte le chiavi che hanno valore null/false/vuoto.
    // In questo modo ritorni solo gli errori effettivi.
    return array_filter($errori);
}
?>