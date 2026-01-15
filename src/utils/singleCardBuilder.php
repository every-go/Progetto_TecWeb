<?php
function singleCardBuilder($animale)
{
    $animaleCard = file_get_contents("html/animalCardTemplate.html");
    $animaleCard = str_replace("[ID_ANIMALE]", $animale["id"], $animaleCard);
    $animaleCard = str_replace(
        "[NOME_ANIMALE]",
        $animale["nome"],
        $animaleCard
    );
    $animaleCard = str_replace(
        "[SESSO_ANIMALE]",
        "<img src='./images/png/".strtolower($animale["sesso"]).".png' class = 'sesso' style='' alt='Sesso: ".$animale["sesso"]."'>",
        $animaleCard );
    $animaleCard = str_replace("[ALT_IMMAGINE]", $animale["alt"], $animaleCard);
    $animaleCard = str_replace(
        "[PATH_IMMAGINE]",
        $animale["immagine"] ? $animale["immagine"] : "./images/png/notfound.png",
        $animaleCard
    );
    $animaleCard = str_replace(
        "[TIPO]",
        $animale["tipo_animale"],
        $animaleCard
    );
    $animaleCard = str_replace("[LUOGO]", $animale["luogo"], $animaleCard);

    if ($animale["eta"] == 1) {
        $animale["eta"] = "1 anno";
    } else {
        $animale["eta"] = $animale["eta"] . " anni";
    }
    $animaleCard = str_replace("[ETA_ANIMALE]", $animale["eta"], $animaleCard);
    $animaleCard = str_replace("[TAGLIA]", $animale["taglia"], $animaleCard);
    $animaleCard = str_replace(
        "[CARATTERE]",
        $animale["carattere"],
        $animaleCard
    );

    $paramQueryProvenienza = "";
    if (isset($_GET["pagina"]) && is_numeric($_GET["pagina"])) {
        $paramQueryProvenienza .= "&pagina=" . $_GET["pagina"];
    }

    if (isset($_GET["search"]) && !empty(trim($_GET["search"]))) {
        $paramQueryProvenienza .= "&search=" . $_GET["search"];
    }

    $animaleCard = str_replace(
        "[PAGINA_PROVENIENZA]",
        $paramQueryProvenienza,
        $animaleCard
    );

    return $animaleCard;
}

?>