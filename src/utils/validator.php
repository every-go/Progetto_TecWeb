<?php

function checkEmptyString($input, $nomeCampo = null)
{
    if (empty(trim($input))) {
        if ($nomeCampo !== null) {
            return "Il campo $nomeCampo è obbligatorio.";
        } else {
            return "Questo campo è obbligatorio.";
        }
    }

    return null;
}

function checkNotNaturalNumber($input, $nomeCampo = null)
{
    if (
        filter_var($input, FILTER_VALIDATE_INT) !== false &&
        intval($input) >= 0
    ) {
        return null;
    } else {
        if ($nomeCampo !== null) {
            return "Il campo '$nomeCampo' deve essere un numero naturale.";
        } else {
            return "Questo campo deve essere un numero naturale.";
        }
    }
}

function checkListaBisogni($input, $nomeCampo = null)
{
    $errore = checkEmptyString($input, $nomeCampo);
    if ($errore === null) {
        $arrayBisogni = array_filter(
            array_map('trim', explode(';', $input)),
            fn($value) => $value !== ''
        );

        if (count($arrayBisogni) === 0) {
            if ($nomeCampo !== null) {
                return "Il campo '$nomeCampo' deve contenere almeno un bisogno.";
            } else {
                return "Questo campo deve contenere almeno un bisogno.";
            }
        }
    }

    return $errore;
}
