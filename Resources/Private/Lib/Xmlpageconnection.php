<?php

/**
 * Class 'XMLPageConnection' for the 'libconnect' extension.
 *
 * @author	Björn Heinermann <hein@zhaw.ch>
 * @package	TYPO3
 * @subpackage	tx_libconnect
 */

class Tx_Libconnect_Resources_Private_Lib_Xmlpageconnection {

   /**
    * Enable debug for logging errors to devLog
    *
    */
    private $debug = FALSE;    

    private $extPiVars;
    private $proxy;
    private $proxyPort;

    public function __construct() {
        $this->setExtPiVars();
        $this->setExtConfVars();
        $this->setProxy();
        $this->setProxyPort();
    }

    /**
     * Läd die im typoscript gesetzten Variablen.
     */
    private function setExtPiVars() {
        $this->extPiVars = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.'];
    }

    /**
     * Lädt die in der Extension Konfiguration gesetzten Variablen.
     */
    private function setExtConfVars() {
        $ext_conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['libconnect']);
        if ($ext_conf['debug'] == TRUE){
            $this->debug = TRUE;
        }elseif ($ext_conf['debug'] == FALSE){
            $this->debug = FALSE;
        }
    }

    /**
     * set proxy server
     */
    private function setProxy() {
        $this->proxy = $this->extPiVars['proxy'];
    }

    /**
     * set port of proxy
     */
    private function setProxyPort() {
        $this->proxyPort = $this->extPiVars['proxy_port'];
    }

    /**
     * get xml data from an url
     *
     * @return SimpleXMLElement object
     */
    public function getDataFromXMLPage($url) {

        $xmlObj = FALSE;
        $ch = curl_init();
 
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT, 80);
        if ($this->proxy && $this->proxyPort) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxy);
            curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxyPort);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        $result = curl_exec($ch);

        //curl_exec with CURLOPT_RETURNTRANSFER set 1 returns FALSE on error or
        //the result on success, so check for result.
        if ($result) {

            //simplexml_load_string will produce E_WARNING error messages for each error 
            //found in the XML data. Therefore suppress error messages in any mode and
            //handle errors for debug-mode differently.
            libxml_use_internal_errors(TRUE);

            //parse the XML data.
            $xmlObj = simplexml_load_string($result);
            
            //log url to devlog in debug-mode if XML data contained errors.
            if ($this->debug) {
                $error_array = libxml_get_errors();
                if (count($error_array) > 0) {
                    t3lib_div::devLog('XML data contained errors: '.$url, 'libconnect', 1);
                }
            }

            //reset libxml error buffering and clear any existing libxml errors
            libxml_use_internal_errors(FALSE);
        }

        //check response code
        $http_code = curl_getinfo($ch);
        curl_close($ch);//close session

        if($http_code['http_code'] != 200){
            $xmlObj = FALSE;

            if ($this->debug){
                t3lib_div::devLog('Got HTTP Code ' . $http_code['http_code'] . ' for request: ' . $url, 'libconnect', 1);
            }

            return $xmlObj;
        }

        /* HINWEIS FEHLERPRUEFUNG: 
         * Die Funktion curl_error() wie auch curl_getinfo() haben keine brauchbaren Rückgabewerte
         * zurückgegeben. Deswegen die Pruefung ob ein Object erzeugt wurde und es ein
         * SimpleXMLElement ist. 
         */
        if ($xmlObj != FALSE) { //get_class produces E_WARNING for non-object arguments
            if (!is_object($xmlObj) && get_class($xmlObj) == 'SimpleXMLElement' ) {
                $xmlObj = FALSE;
            }
        }

        return $xmlObj;
    }
}
?>