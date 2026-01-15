<?php




class UrlHelper
{
    // Pagine a cui non fare redirect
    private const BLACK_LIST = [
        'login.php',
        'registrazione.php',
        'logout.php',
        'area_personale.php',
    ];


    // Crea una query HTTP filtrata da una sorgente (es. $_GET)
    public static function createHttpQuery($source, $allowedKeys, $htmlChars = true)
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

    // Restituisce il nome del file (senza parametri GET) della pagina corrente
    public static function getCurrentPageName()
    {
        return basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    }

    // Restituisce il path corrente senza parametri GET
    public static function getCurrentPath()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }


    // Restituisce l'URL corrente completo di parametri GET per i redirect
    public static function getCurrentUrl($paramsWhitelist = [])
    {
        // Recupero il percorso pulito (senza query string)
        $currentPath = self::getCurrentPath();
        $currentPage = basename($currentPath);

        // Controllo Blacklist
        if (in_array($currentPage, self::BLACK_LIST)) {
            return null;
        }

        // Costruisco la query string filtrata
        $queryString = self::createHttpQuery($_GET, $paramsWhitelist,false);

        // Ritorno il path completo
        return $currentPath . ($queryString ? '?' . $queryString : '');
    }

    // Genera la stringa di redirect in modo SICURO
    // Esclude automaticamente login, logout, ecc. per evitare loop
    public static function getRedirectParam()
    {
        $current = self::getCurrentPageName();

        // Se sono in una pagina "nera", non voglio tornare qui dopo il login
        if (in_array($current, self::BLACK_LIST)) {
            return ''; // Ritorna stringa vuota
        }

        // Altrimenti ritorno l'URL completo codificato
        return urlencode(self::getCurrentUrl());
    }

    // Controlla se un URL è sicuro (per evitare Open Redirect)
    public static function isSafeRedirect($url)
    {
        // controllo redirect vuoto
        if (empty(trim($url))) {
            return false;
        }

        // controllo URL assoluto
        if ($url[0] !== '/') {
            return false;
        }

        // Controllo Protocollo: Evitiamo schemi come "http://" o "//"
        if (substr($url, 0, 2) === '//') {
            return false;
        }

        // 4. Controllo  ritorni a capo o caratteri nulli
        if (preg_match('/[\r\n]/', $url)) {
            return false;
        }

        return true;
    }

    /*
    Aggiunge un parametro di redirect alla pagina corrente a un URL target
    @param string $targetUrl L'URL a cui vogliamo aggiungere il redirect
    @param array $keepParams Parametri GET da mantenere nella URL di ritorno
    @return string L'URL target con il parametro di redirect aggiunto
    */
    public static function appendRedirect($targetUrl, $keepParams = [])
    {
        // 1. Calcolo l'URL da cui vengo (pulito)
        $backUrl = self::getCurrentUrl($keepParams);

        // 2. Se sono in una pagina "nera" (es. login), non aggiungo nulla
        if (!$backUrl) {
            return $targetUrl;
        }

        // 3. Capisco se devo usare ? o & per appendere il parametro
        $separator = (strpos($targetUrl, '?') === false) ? '?' : '&';

        // 4. Costruisco il link finale
        return $targetUrl . $separator . 'redirect=' . urlencode($backUrl);
    }

    // Controlla se la pagina corrente corrisponde a quella passata
    public static function isCurrentPage($pageName)
    {
        $current = basename($_SERVER['PHP_SELF']);
        // Confrontiamo solo i nomi dei file, ignorando query string o path assoluti
        return $current === basename(parse_url($pageName, PHP_URL_PATH));
    }


    /*
     Genera un URL di redirect sicuro
    @param string $url L'URL di destinazione del redirect
    @param string $defaultUrl L'URL di default se quello fornito non è sicuro
    @return string L'URL di redirect sicuro 
    */
    public static function getSafeRedirect($url, $defaultUrl = 'index.php')
    {
        // 1. Vuoto o non stringa
        if (empty($url) || !is_string($url)) return $defaultUrl;

        // 2. No esterni o protocolli
        if ($url[0] !== '/' || substr($url, 0, 2) === '//') return $defaultUrl;

        // 3. Loop Check
        if (self::isCurrentPage($url)) return $defaultUrl;


        // 4. Blacklist Check
        if (in_array( basename(parse_url($url, PHP_URL_PATH)), self::BLACK_LIST)) return $defaultUrl;

        return $url;
    }



}
?>