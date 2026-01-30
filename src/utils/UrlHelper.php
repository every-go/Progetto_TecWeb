<?php
class UrlHelper
{
    // Pagine a cui non fare redirect
    private const BLACK_LIST = [
        'login.php',
        'registrazione.php',
        'logout.php',
        'area_personale.php',
        'ispeziona_animale.php',
        'aggiungiAnimale.php',
        'modificaAnimale.php',
    ];


    // Crea una query HTTP filtrata da una sorgente (es. $_GET)
    public static function createHttpQuery($source, $allowedKeys, $htmlChars = true)
    {
        $whitelist = array_flip($allowedKeys);

        $filtered = array_intersect_key($source, $whitelist);

        return $htmlChars ?
            htmlspecialchars(http_build_query($filtered)) :
            http_build_query($filtered);
    }

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

    // Esclude automaticamente login, logout, ecc. per evitare loop
    public static function getRedirectParam()
    {
        $current = self::getCurrentPageName();

        if (in_array($current, self::BLACK_LIST)) {
            return '';
        }

        return urlencode(self::getCurrentUrl());
    }

    // Controlla se un URL è sicuro (per evitare Open Redirect)
    public static function isSafeRedirect($url)
    {
        if (empty(trim($url))) {
            return false;
        }

        if ($url[0] !== '/') {
            return false;
        }

        if (substr($url, 0, 2) === '//') {
            return false;
        }

        if (preg_match('/[\r\n]/', $url)) {
            return false;
        }

        return true;
    }

    public static function appendRedirect($targetUrl, $keepParams = [])
    {

        $backUrl = "./".self::getCurrentPageName().
            (empty($keepParams) ? '' : '?' . self::createHttpQuery($_GET, $keepParams, false));
            
        if (!$backUrl) {
            return $targetUrl;
        }

        $separator = (strpos($targetUrl, '?') === false) ? '?' : '&';
        return $targetUrl . $separator . 'redirect=' . urlencode($backUrl);
    }
    public static function isCurrentPage($pageName)
    {
        $current = basename($_SERVER['PHP_SELF']);
        // Confrontiamo solo i nomi dei file, ignorando query string o path assoluti
        return $current === basename(parse_url($pageName, PHP_URL_PATH));
    }

    public static function getSafeRedirect($url, $defaultUrl = 'index.php')
    {
        if (empty($url) || !is_string($url)) return $defaultUrl;

        if (($url[0] !== '/' && $url[0] !== '.') || substr($url, 0, 2) === '//') return $defaultUrl;

        if (self::isCurrentPage($url)) return $defaultUrl;

        if (in_array( basename(parse_url($url, PHP_URL_PATH)), self::BLACK_LIST)) return $defaultUrl;

        return $url;
    }
    
}
?>