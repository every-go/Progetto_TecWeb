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
            "errPlaceholder" => "errNome"
        ],
        "eta" => [
            "form" => "eta",
            "placeholder" => "etaAnimale",
            "errPlaceholder" => "errEta"
        ],
        "alt" => [
            "form" => "alt",
            "placeholder" => "altImmagine",
            "errPlaceholder" => "errAlt"
        ],
        "luogo" => [
            "form" => "luogo",
            "placeholder" => "luogoAnimale",
            "errPlaceholder" => "errLuogo"
        ],
        "descrizione" => [
            "form" => "descrizione",
            "placeholder" => "descrizione",
            "errPlaceholder" => "errDesc"
        ],
        "bisogni" => [
            "form" => "bisogni",
            "placeholder" => "bisogni",
            "errPlaceholder" => "errBisogni"
        ],
        "storia" => [
            "form" => "storia",
            "placeholder" => "storia",
            "errPlaceholder" => "errStoria"
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
        ],
        "tipo_animale" => [
            "form" => "tipo_animale",
            "opzioni" => ["Reale" => "checkReale", "Fantastico" => "checkFantastico"],
            "errPlaceholder" => "tipoAnimaleErr",
        ],
        "taglia" => [
            "form" => "taglia",
            "opzioni" => [
                "Piccola" => "checkTagliaP",
                "Media" => "checkTagliaM",
                "Grande" => "checkTagliaG",
            ],
            "errPlaceholder" => "tagliaErr",
        ],
        "carattere" => [
            "form" => "carattere",
            "opzioni" => [
                "Facile" => "checkCarF",
                "Moderato" => "checkCarM",
                "Difficile" => "checkCarD",
            ],
            "errPlaceholder" => "carattereErr",
        ],
    ];
    public static $files = [
        "immagine" => [
            "form" => "immagine",
            "placeholder" => "immagineCorrente",
            "errPlaceholder" => "fileErr",
        ],
    ];

    const ARIA_MAP = [
        "errNome"         => "errNome",
        "errEta"          => "errEta",
        "errSesso"        => "errSesso",
        "errTipoAnimale"  => "errTipoAnimale",
        "errImmagine"     => "errImmagine",
        "errAlt"          => "errAlt",
        "errLuogo"        => "errLuogo",
        "errTaglia"       => "errTaglia",
        "errCarattere"    => "errCarattere",
        "errDesc"         => "errDesc",
        "errBisogni"      => "errBisogni",
        "errStoria"       => "errStoria",
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

        // $actionForm = "ispeziona_animale.php?".createHttpQuery($_GET, ['pagina', 'search']);
        $actionForm = "./aggiungiAnimale.php?" . createHttpQuery($_GET, ['pagina', 'search']);
        $content = str_replace("[actionForm]", $actionForm, $content);
    } elseif ($azione === "Modifica") {
        $actionForm = "modificaAnimale.php?" . createHttpQuery($_GET, ['pagina', 'search', 'id']);
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
            '<img id="immagineCorrente" src="' . htmlspecialchars($animalData["immagine"], ENT_QUOTES) . '" alt="">',
            $content
        );
    } else {
        $content = str_replace(
            "[immagineCorrente]",
            '<img id="immagineCorrente" src="" alt="">',
            $content
        );
    }

    return $content;
}

function buildAriaAlert(string $id, string $msg): string
{
    return '<div role="alert" id="' . htmlspecialchars($id, ENT_QUOTES) . '">' .
           htmlspecialchars($msg, ENT_QUOTES) .
           '</div>';
}

function renderErrorsWithAria(string $html, array $errors): string
{
    foreach (CampiDatiAnimale::ARIA_MAP as $placeholder => $ariaId) {
        if (!empty($errors[$placeholder])) {
            $replacement = buildAriaAlert($ariaId, $errors[$placeholder]);
        } else {
            $replacement = "";
        }
        $html = str_replace("[$placeholder]", $replacement, $html);
    }
    return $html;
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
    $errori = [
        "errNome" => "",
        "errEta" => "",
        "errSesso" => "",
        "errTipoAnimale" => "",
        "errImmagine" => "",
        "errAlt" => "",
        "errLuogo" => "",
        "errTaglia" => "",
        "errCarattere" => "",
        "errDesc" => "",
        "errBisogni" => "",
        "errStoria" => "",
    ];

    $confTesti = campiDatiAnimale::$testi;
    $confRadio = campiDatiAnimale::$radio;
    $confFiles = campiDatiAnimale::$files;

    $errori[$confTesti["nome"]["errPlaceholder"]] =
        validateTextField($data["nome"] ?? "", REGEX["nome"])
            ? null
            : "Nome non valido";

    $errori[$confTesti["luogo"]["errPlaceholder"]] =
        validateTextField($data["luogo"] ?? "", REGEX["luogo"])
            ? null
            : "Luogo non valido";

    $errori[$confTesti["eta"]["errPlaceholder"]] =
        validateNumberField($data["eta"] ?? "", REGEX["eta"])
            ? null
            : "Età non valida";

    $errori[$confTesti["descrizione"]["errPlaceholder"]] =
        validateTextareaField($data["descrizione"] ?? "", REGEX["descrizione"])
            ? null
            : "Descrizione non valida";

    $errori[$confTesti["bisogni"]["errPlaceholder"]] =
        validateTextareaField($data["bisogni"] ?? "", REGEX["bisogni"])
            ? null
            : "Bisogni non validi";

    $errori[$confTesti["alt"]["errPlaceholder"]] =
        validateTextareaField($data["alt"] ?? "", REGEX["alt"])
            ? null
            : "Descrizione immagine non valida";

    $errori[$confTesti["storia"]["errPlaceholder"]] =
        validateTextareaField($data["storia"] ?? "", REGEX["storia"])
            ? null
            : "Storia non valida";

    foreach ($confRadio as $dbKey => $config) {
        $inputName = $config["form"] ?? $dbKey;
        $errKey = $config["errPlaceholder"];
        $valoriAmmessi = array_keys($config["opzioni"]);
        $label = ucfirst(str_replace("_", " ", $dbKey));

        $errori[$errKey] =
            validateRadioField($data[$inputName] ?? null, $valoriAmmessi)
                ? null
                : "$label non valido";
    }

    $errori["errImmagine"] =
        validateFileField(
            $_FILES["immagine"] ?? [],
            2 * 1024 * 1024,
            ["image/jpeg", "image/jpg", "image/png"]
        )
            ? null
            : "Immagine non valida";

    return array_filter($errori);

}

?>