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
        // Nota: bisogni lo gestiamo come caso speciale se è ancora da esplodere
        // ma se fosse già HTML, potremmo mettere '<ul><li>'
    ];

    private static $listaEsplosa = [
        'bisogni',
    ];

    /**
     * Processa una lista intera di righe (es. risultato di getList)
     */
    public static function pulisciLista(array $list) {
        if (empty($list)) return [];
        
        // array_map passa ogni riga al metodo pulisciRiga
        return array_map([self::class, 'pulisciRiga'], $list);
    }

    /**
     * Processa una singola riga (array associativo)
     */
    public static function pulisciRiga($row) {
        if (!is_array($row)) return $row;

        $cleanedRow = [];
        foreach ($row as $key => $value) {
            $cleanedRow[$key] = self::pulisciValore($key, $value);
        }
        return $cleanedRow;
    }

    /**
     * Logica decisionale centrale
     */
    public static function pulisciValore($key, $value) {
        if (in_array($key, self::$listaEsplosa)) {
            return self::convertiInLista($value);
        }

        if (array_key_exists($key, self::$registroTagHtmlPermessi)) {
            $allowedTags = self::$registroTagHtmlPermessi[$key];
            return strip_tags($value, $allowedTags);
        }

        // Default: nessun tag permesso
        return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5);
    }

    /**
     * Helper per trasformare stringhe "A;B;C" in <ul><li>...
     * Include la sanitizzazione dei singoli elementi.
     */
    private static function convertiInLista($rawValue,$separatore=';') {
        if (empty($rawValue)) return '';

        // Decode -> Explode
        $decoded = html_entity_decode($rawValue, ENT_QUOTES | ENT_HTML5);
        $items = explode($separatore, $decoded);

        // Pulisci ogni elemento (permettendo magari solo il grassetto)
        $cleanItems = [];
        foreach ($items as $item) {
            $item = trim($item);
            if ($item !== '') {
                // Qui decidiamo che dentro i bullet point si può usare solo il grassetto
                $cleanItems[] = strip_tags($item, '<strong>');
            }
        }

        if (empty($cleanItems)) return '';

        // Costruisci HTML sicuro
        return "<ul><li>" . implode("</li><li>", $cleanItems) . "</li></ul>";
    }
}