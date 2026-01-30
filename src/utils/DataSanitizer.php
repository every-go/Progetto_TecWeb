<?php

class DataSanitizer {

    /**
     * REGISTRO DEI CAMPI CON HTML PERMESSO (Allowlist)
     * Chiave = nome del campo nel DB
     * Valore = stringa dei tag permessi per strip_tags
     */
    private static $registroTagHtmlPermessi = [
        'descrizione' => '<strong><em><p>',
        'storia'      => '<strong><em><p>',
    ];

    private static $listaEsplosa = [
        'bisogni',
    ];
    public static function pulisciLista(array $list) {
        if (empty($list)) return [];
        
        return array_map([self::class, 'pulisciRiga'], $list);
    }

    public static function pulisciRiga($row) {
        if (!is_array($row)) return $row;

        $cleanedRow = [];
        foreach ($row as $key => $value) {
            $cleanedRow[$key] = self::pulisciValore($key, $value);
        }
        return $cleanedRow;
    }

    public static function pulisciValore($key, $value) {
        if (in_array($key, self::$listaEsplosa)) {
            return self::convertiInLista($value);
        }

        if (array_key_exists($key, self::$registroTagHtmlPermessi)) {
            $allowedTags = self::$registroTagHtmlPermessi[$key];
            return strip_tags($value, $allowedTags);
        }

        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
    }

    private static function convertiInLista($rawValue,$separatore=';') {
        if (empty($rawValue)) return '';

        $decoded = html_entity_decode($rawValue, ENT_QUOTES | ENT_HTML5);
        $items = explode($separatore, $decoded);

        $cleanItems = [];
        foreach ($items as $item) {
            $item = trim($item);
            if ($item !== '') {
                $cleanItems[] = strip_tags($item, '<strong>');
            }
        }

        if (empty($cleanItems)) return '';

        return "<ul><li>" . implode("</li><li>", $cleanItems) . "</li></ul>";
    }
}
?>