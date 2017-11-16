<?php

/**
 * Class 'HTTPPageConnection' for the 'libconnect' extension.
 *
 * @author	Torsten Witt <torsten.witt@sub.uni-hamburg.de>
 * @package	TYPO3
 * @subpackage	tx_libconnect
 */

class Tx_Libconnect_Resources_Private_Lib_Httppageconnection {

   /**
    * enable debug for logging errors to devLog
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
     * gets typoscript configuration
     */
    private function setExtPiVars() {
        $this->extPiVars = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.'];
    }

    /**
     * get configuration of the extension
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
     * Get a string from an url
     *
     * @return SimpleXMLElement object
     */
    public function getDataFromHttpPage($url) {
        $result = FALSE;
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
        $response = curl_exec($ch);

        //check response code
        $http_code = curl_getinfo($ch);
        curl_close($ch);//close session
        
        if(($http_code['http_code'] != 200) || ($response == FALSE)){
        
            if ($this->debug){
                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Got HTTP Code ' . $http_code['http_code'] . ' for request: ' . $url, 'libconnect', 1);
            }

            return $result;
        }

        return $response;
    }
}
?>
