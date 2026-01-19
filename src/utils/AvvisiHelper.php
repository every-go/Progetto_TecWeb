<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$mappaAvvisi = [
    "avviso1" => "Questo è il messaggio di avviso 1.",
    "avviso2" => "Attenzione: questo è il messaggio di avviso 2.",
    "avviso3" => "Notifica importante: questo è il messaggio di avviso 3."
];

class AvvisiHelper {

    private const SESSION_KEY = 'messaggio_utente';
    private const TAG_PERMESSI = '<span><strong><em><b><i><a><br>';
    private const TIPI_VALIDI = ['alert'=>['alert','error','success'], 'status'=>[  'info', 'warning']];
    
    public static function set($text, $type = 'info') {
        $_SESSION[self::SESSION_KEY] = [
            'text' => $text,
            'type' => $type
        ];
    }

    public static function get() {
        if (isset($_SESSION[self::SESSION_KEY])) {
            $message = $_SESSION[self::SESSION_KEY];
            unset($_SESSION[self::SESSION_KEY]); 
            return $message;
        }
        return null;
    }

    public static function getFormattedHtml() {
        $msg = self::get();

        if (!$msg) {
            return '';
        }

        $role = (in_array($msg['type'], self::TIPI_VALIDI['alert'])) ? 'alert' : 'status';

        return '<div id="avviso" role="' . $role . '"> <p>' . $msg['text'] . '</p> <div class="progress-container">
        <div class="progress-bar"></div>
    </div> </div>';
    }
}

?>