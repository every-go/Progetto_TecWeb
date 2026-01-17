<?php

//helper per la creazione delle query dei parametri http
function createHttpQuery($source, $allowedKeys, $htmlChars = true)
{
    // Converto la lista di chiavi ['chiave', 'valore'] in un array associativo ['id'=>0, 'nome'=>1]
    // Questo serve per poter fare l'intersezione basata sulle chiavi.
    $whitelist = array_flip($allowedKeys);

    // Prendo da $source SOLO le chiavi che esistono in $whitelist.
    $filtered = array_intersect_key($source, $whitelist);

    // Costruisco la query, considerando se devo fare l'escape dei caratteri HTML.
    return $htmlChars ? 
        htmlspecialchars(http_build_query($filtered)) : 
        http_build_query($filtered);
}






class campiDatiAnimale
{
    public static $testi = [
        "nome" => [
            "form" => "nome",
            "placeholder" => "nomeAnimale",
            "errPlaceholder" => "nomeErr",
            "ariaInvalidPlaceholder" => "ariaInvalidNome",
        ],
        "eta" => [
            "form" => "eta",
            "placeholder" => "etaAnimale",
            "errPlaceholder" => "etaErr",
            "ariaInvalidPlaceholder" => "ariaInvalidEta",
        ],
        "luogo" => [
            "form" => "luogo",
            "placeholder" => "luogoAnimale",
            "errPlaceholder" => "luogoErr",
            "ariaInvalidPlaceholder" => "ariaInvalidLuogo",
        ],
        "descrizione" => [
            "form" => "descrizione",
            "placeholder" => "descrizione",
            "errPlaceholder" => "descrizioneErr",
            "ariaInvalidPlaceholder" => "ariaInvalidDesc",
        ],
        "alt" => [
            "form" => "alt",
            "placeholder" => "altImmagine",
            "errPlaceholder" => "imageErr",
            "ariaInvalidPlaceholder" => "ariaInvalidAlt",
        ],
        "bisogni" => [
            "form" => "bisogni",
            "placeholder" => "bisogni",
            "errPlaceholder" => "bisogniErr",
            "ariaInvalidPlaceholder" => "ariaInvalidBisogni",
        ],
        "storia" => [
            "form" => "storia",
            "placeholder" => "storia",
            "errPlaceholder" => "storiaErr",
            "ariaInvalidPlaceholder" => "ariaInvalidStoria",
        ],
    ];
    public static $radio = [
        "sesso" => [
            "form" => "sesso",
            "opzioni" => [
                "Maschio" => "checkMaschio",
                "Femmina" => "checkFemmina",
            ],
            "errPlaceholder" => "sessoErr",
            "ariaInvalidPlaceholder" => "ariaInvalidSesso",
        ],
        "tipo_animale" => [
            "form" => "tipo_animale",
            "opzioni" => ["Reale" => "checkReale", "Fantastico" => "checkFantastico"],
            "errPlaceholder" => "tipoAnimaleErr",
            "ariaInvalidPlaceholder" => "ariaInvalidTipoAnimale",
        ],
        "taglia" => [
            "form" => "taglia",
            "opzioni" => [
                "Piccola" => "checkTagliaP",
                "Media" => "checkTagliaM",
                "Grande" => "checkTagliaG",
            ],
            "errPlaceholder" => "tagliaErr",
            "ariaInvalidPlaceholder" => "ariaInvalidTaglia",
        ],
        "carattere" => [
            "form" => "carattere",
            "opzioni" => [
                "Facile" => "checkCarF",
                "Moderato" => "checkCarM",
                "Difficile" => "checkCarD",
            ],
            "errPlaceholder" => "carattereErr",
            "ariaInvalidPlaceholder" => "ariaInvalidCarattere",
        ],
    ];
    public static $files = [
        "immagine" => [
            "form" => "immagine",
            "placeholder" => "immagineCorrente",
            "errPlaceholder" => "fileErr",
            "ariaInvalidPlaceholder" => "ariaInvalidImmagine",
        ],
    ];
}

function createForm($azione, $id = null)
{
    if (
        $azione !== "Aggiungi" &&
        ($azione !== "Modifica" || $id === null || !is_numeric($id))
    ) {
        include __DIR__ . "/../../404.php";
        http_response_code(404);
        exit();
    }
    $content = file_get_contents("html/ispeziona_animale.html");
    $content = str_replace("[Ispezione]", $azione, $content);
    $content = str_replace("[pulsanteAzione]", $azione, $content);
    $content = str_replace("[Azione]", $azione, $content);

    if ($azione === "Aggiungi") {

        $actionForm = "ispeziona_animale.php?".createHttpQuery($_GET, ['pagina', 'search']);
        $content = str_replace("[actionForm]", $actionForm, $content);
    } elseif ($azione === "Modifica") {
        $actionForm = "ispeziona_animale.php?" . createHttpQuery($_GET, ['pagina', 'search', 'id']);
        $content = str_replace("[actionForm]", $actionForm, $content);
    }

    return $content;
}

function populateForm($animalData, $content)
{
    $placeholders = [
        "nomeAnimale" => $animalData["nome"],
        "etaAnimale" =>
        $animalData["eta"] !== "" ? $animalData["eta"] : "[etaAnimale]",
        "luogoAnimale" => $animalData["luogo"],
        "descrizione" => $animalData["descrizione"],
        "altImmagine" => $animalData["alt"],
        "bisogni" => $animalData["bisogni"],
        "storia" => $animalData["storia"],
    ];
    $content = replaceTextPlaceholder($content, $placeholders);
    $content = renderRadio($content, $animalData);
    if (!empty($animalData["immagine"])) {
        $content = str_replace(
            "[immagineCorrente]",
            '<img src="' .
                htmlspecialchars($animalData["immagine"]) .
                '" alt="">',
            $content
        );
    } else {
        $content = str_replace(
            "[immagineCorrente]",
            '<img id=immagineCorrente alt="">',
            $content
        );
    }
    return $content;
}

function cleanForm($content)
{
    $content = removeFormDataPlaceholders($content);
    $content = removeErrorPlaceholders($content);
    $content = removeAriaInvalidPlaceholders($content);
    return $content;
}

function replaceTextPlaceholder($html, $placeholders)
{
    foreach ($placeholders as $key => $value) {
        $html = str_replace("[$key]", $value, $html);
    }
    return $html;
}

function renderRadio($html, $datiUtente)
{
    $config = [
        "sesso" => ["Maschio" => "checkMaschio", "Femmina" => "checkFemmina"],
        "tipo_animale" => [
            "Reale" => "checkReale",
            "Fantastico" => "checkFantastico",
        ],
        "taglia" => [
            "Piccola" => "checkTagliaP",
            "Media" => "checkTagliaM",
            "Grande" => "checkTagliaG",
        ],
        "carattere" => [
            "Facile" => "checkCarF",
            "Moderato" => "checkCarM",
            "Difficile" => "checkCarD",
        ],
    ];

    foreach ($config as $campo => $opzioni) {
        $selezione = $datiUtente[$campo] ?? "";
        foreach ($opzioni as $valore => $placeholder) {
            $checked = $selezione === $valore ? "checked" : "";
            $html = str_replace("[$placeholder]", $checked, $html);
        }
    }
    return $html;
}

function replaceErrorPlaceholders($html, $errori)
{
    $chiavi = array_merge(
        array_column(campiDatiAnimale::$testi, "errPlaceholder"),
        array_column(campiDatiAnimale::$radio, "errPlaceholder"),
        array_column(campiDatiAnimale::$files, "errPlaceholder")
    );
    foreach ($chiavi as $key) {
        $html = str_replace("[$key]", $errori[$key] ?? "", $html);
    }
    return $html;
}

function removeErrorPlaceholders($html)
{
    $chiavi = array_merge(
        array_column(campiDatiAnimale::$testi, "errPlaceholder"),
        array_column(campiDatiAnimale::$radio, "errPlaceholder"),
        array_column(campiDatiAnimale::$files, "errPlaceholder")
    );
    foreach ($chiavi as $key) {
        $html = str_replace("[$key]", "", $html);
    }
    return $html;
}

function addAriaInvalid($html, $errori)
{
    $allAriaPlaceholders = array_merge(
        array_column(
            campiDatiAnimale::$testi,
            "ariaInvalidPlaceholder",
            "errPlaceholder"
        ),
        array_column(
            campiDatiAnimale::$radio,
            "ariaInvalidPlaceholder",
            "errPlaceholder"
        ),
        array_column(
            campiDatiAnimale::$files,
            "ariaInvalidPlaceholder",
            "errPlaceholder"
        )
    );

    foreach ($allAriaPlaceholders as $errKey => $ariaPlaceholder) {
        $valore =
            isset($errori[$errKey]) && !empty($errori[$errKey])
            ? ' aria-invalid="true"'
            : "";
        $html = str_replace("[$ariaPlaceholder]", $valore, $html);
    }

    return $html;
}

function removeAriaInvalidPlaceholders($html)
{
    $chiavi = array_merge(
        array_column(campiDatiAnimale::$testi, "ariaInvalidPlaceholder"),
        array_column(campiDatiAnimale::$radio, "ariaInvalidPlaceholder"),
        array_column(campiDatiAnimale::$files, "ariaInvalidPlaceholder")
    );
    foreach ($chiavi as $key) {
        $html = str_replace("[$key]", "", $html);
    }
    return $html;
}

function removeFormDataPlaceholders($html)
{
    $placeholders = [
        "nomeAnimale",
        "etaAnimale",
        "luogoAnimale",
        "descrizione",
        "altImmagine",
        "bisogni",
        "storia",
        "immagineCorrente",
        "checkMaschio",
        "checkFemmina",
        "checkReale",
        "checkFantastico",
        "checkTagliaP",
        "checkTagliaM",
        "checkTagliaG",
        "checkCarF",
        "checkCarM",
        "checkCarD",
    ];
    foreach ($placeholders as $key) {
        if ($key === "etaAnimale") {
            $html = str_replace("[$key]", "0", $html);
        } else {
            $html = str_replace("[$key]", "", $html);
        }
    }
    return $html;
}

function checkSelection($input, $allowedValues, $nomeCampo)
{
    if (empty($input)) {
        return "Seleziona un'opzione per $nomeCampo.";
    }
    if (!in_array($input, $allowedValues)) {
        return "Valore non valido per $nomeCampo.";
    }
    return null;
}

function checkForm($data)
{
    $errori = [];

    $confTesti = campiDatiAnimale::$testi;
    $confRadio = campiDatiAnimale::$radio;
    $confFiles = campiDatiAnimale::$files;

    $errori[$confTesti["nome"]["errPlaceholder"]] = checkEmptyString(
        $data["nome"],
        "Nome"
    );

    $errori[$confTesti["luogo"]["errPlaceholder"]] = checkEmptyString(
        $data["luogo"],
        "Luogo"
    );

    $errori[$confTesti["eta"]["errPlaceholder"]] = checkNotNaturalNumber(
        $data["eta"],
        "Età"
    );

    $errori[$confTesti["descrizione"]["errPlaceholder"]] = checkEmptyString(
        $data["descrizione"],
        "Descrizione"
    );

    $errori[$confTesti["bisogni"]["errPlaceholder"]] = checkListaBisogni(
        $data["bisogni"],
        "Bisogni"
    );

    $errori[$confTesti["storia"]["errPlaceholder"]] = checkEmptyString(
        $data["storia"],
        "Storia"
    );

    $errori[$confTesti["alt"]["errPlaceholder"]] = checkEmptyString(
        $data["alt"],
        "Descrizione Immagine"
    );

    foreach ($confRadio as $dbKey => $config) {
        $inputName = isset($config["form"]) ? $config["form"] : $dbKey;

        $errKey = $config["errPlaceholder"];

        $valoriAmmessi = array_keys($config["opzioni"]);

        $label = ucfirst(str_replace("_", " ", $dbKey));

        $errori[$errKey] = checkSelection(
            $data[$inputName] ?? "",
            $valoriAmmessi,
            $label
        );
    }

    return array_filter($errori);
}
