<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$mappaAvvisi = [
    "avviso1" => "Questo è il messaggio di avviso 1.",
    "avviso2" => "Attenzione: questo è il messaggio di avviso 2.",
    "avviso3" => "Notifica importante: questo è il messaggio di avviso 3."
];


// $messaggio = "";

// if (isset($_SESSION['messaggio_utente'])) {
//     $messaggio = "<div id='avviso' role='alert'> <p>" . $_SESSION['messaggio_utente'] . "</p> <div class='progress-container'>
//         <div class='progress-bar'></div>
//     </div> </div>";
//     unset($_SESSION['messaggio_utente']);
// }


class AvvisiHelper {

    private const SESSION_KEY = 'messaggio_utente';
    private const TAG_PERMESSI = '<span><strong><em><b><i><a><br>';
    private const TIPI_VALIDI = ['alert'=>['alert','error','success'], 'status'=>[  'info', 'warning']];
    /**
     * IMPOSTA UN MESSAGGIO
     */
    public static function set($text, $type = 'info') {
        $_SESSION[self::SESSION_KEY] = [
            'text' => $text,
            'type' => $type
        ];
    }

    /**
     * RECUPERA IL MESSAGGIO (E LO DISTRUGGE)
     * Restituisce null se non c'è nessun messaggio.
     */
    public static function get() {
        if (isset($_SESSION[self::SESSION_KEY])) {
            $message = $_SESSION[self::SESSION_KEY];
            // FONDAMENTALE: Lo cancelliamo subito dopo averlo letto
            unset($_SESSION[self::SESSION_KEY]); 
            return $message;
        }
        return null;
    }

    /**
     * GENERA L'HTML DEL MESSAGGIO

     */
    public static function getFormattedHtml() {
        $msg = self::get();

        if (!$msg) {
            return ''; // Nessun messaggio, nessun HTML
        }

        $text = strip_tags($msg['text'], self::TAG_PERMESSI);
        $type = htmlspecialchars($msg['type']);




        // Mappatura classi CSS e ruoli ARIA per accessibilità
        // se in futuro vogliamo aggiungere classi di stile diverse per tipi diversi di messaggi
        // $cssClass = "alert alert-{$type}"; 
        $role = (in_array($type, self::TIPI_VALIDI['alert'])) ? 'alert' : 'status';

        return '<div id="avviso" role="' . $role . '"> <p>' . $text . '</p> <div class="progress-container">
        <div class="progress-bar"></div>
    </div> </div>';
    }
}

?>