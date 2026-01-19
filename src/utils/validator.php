<?php

const REGEX = [
    "nome"        => '/^[\p{L} ]{2,24}$/u',
    "eta"         => '/^(0|[1-9][0-9]{0,4})$/',
    "alt"         => '/^[\p{L}0-9 ,:;\'."\(\)\s]{1,1024}$/u',
    "luogo"       => '/^[\p{L}0-9 ,\/\'"]{1,100}$/u',
    "descrizione" => '/^[\p{L}0-9 ,:;\'."\(\)\?\!\s]{1,}$/u',
    "bisogni"     => '/^[\p{L}0-9 ,:;\'."\(\)\?\!\s]{1,}$/u',
    "storia"      => '/^[\p{L}0-9 ,:;\'."\(\)\?\!\s]{0,}$/u',
];

function validateTextField(string $value, string $pattern): bool
{
    return preg_match($pattern, $value) === 1;
}

function validateNumberField(string $value, string $pattern): bool
{
    return preg_match($pattern, $value) === 1;
}

function validateRadioField(?string $value, array $allowedValues): bool
{
    return $value !== null && in_array($value, $allowedValues, true);
}

function validateFileField(array $file, int $maxSize, array $allowedMimeTypes): bool
{
    if (!isset($file["tmp_name"], $file["size"], $file["type"])) {
        return false;
    }

    if ($file["size"] > $maxSize) {
        return false;
    }

    if (!in_array($file["type"], $allowedMimeTypes, true)) {
        return false;
    }

    return true;
}

function validateTextareaField(string $value, string $pattern): bool
{
    return preg_match($pattern, $value) === 1;
}

?>