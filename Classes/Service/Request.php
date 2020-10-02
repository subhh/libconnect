<?php
namespace Sub\Libconnect\Service;

/**
 *
 * @author Torsten Witt <torsten.witt@sub.uni-hamburg.de>
 * @package libconnect
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License, version 3 or later
 *
 */

class Request {
    
    private $url = '';
    
    private $query = array();
    
    public function getUrl(){
        return $this->url;
    }
    
    public function setUrl($url){
        $this->url = $url;
    }
    
    public function getQuery(){
        return $this->query;
    }
    
    public function setQuery($addtitionalQuery){
        $query = $this->getQuery();
        
        $query = array_merge($query, $addtitionalQuery);
        
        $this->query = $query;
    }


    public function Request($urldecode = TRUE){
        $requestFactory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Http\RequestFactory::class);
        
        #$jar = new \GuzzleHttp\Cookie\CookieJar;

        if($urldecode){
            $query = urldecode( http_build_query($this->getQuery(), null, '&') );
        }else{
            $query = $this->getQuery();
        }
     
        $additionalOptions = [
            'headers' => ['Cache-Control' => 'no-cache'],
            'allow_redirects' => true,
            'query' => $query,
            'headers' => ['Accept' => 'text/xml; charset=UTF8']
        ];

        $response = FALSE;

        try {
            $response = $requestFactory->request($this->getUrl(), 'GET', $additionalOptions);

        } catch (\Exception $e) {
            //echo Psr7\str($requestFactory->getRequest());
            //echo Psr7\str($response->getResponse());exit;

            if ($this->debug){
                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Got HTTP Code ' . $response->getStatusCode() . ' for request: '.  $this->url. http_build_query($this->getQuery(), null, '&'), 'libconnect', 1);
            }

            return FALSE;
        }

        $content = FALSE;

        // Get the content as a string on a successful request
        if ($response->getStatusCode() === 200) {
            $contentType = str_replace(" ", "", strtolower($response->getHeaderLine('Content-Type')));

            if (strpos($contentType, 'text/xml;charset=utf-8') === 0) {//DBIS, services.dnb.de
                $content = $this->getXml($response);
            }elseif (strpos($contentType, 'text/xml;charset=iso-8859-1') === 0) {//EZB
                $content = $this->getXml($response);
            }elseif(strpos($contentType, 'application/rdf+xml;charset=utf-8') === 0){//title history
                $content = $this->getText($response);

            //moreDetails
            }elseif(preg_match('/text\/html;charset=(iso-8859-1)?(utf-8)?/', $contentType, $matches)){
                $content = $this->getText($response);
            }

            else {
                return FALSE;
            }

        }else{
            if ($this->debug){
                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Got HTTP Code ' . $response->getStatusCode() . ' for request: '.  $this->url. http_build_query($this->getQuery(), null, '&'), 'libconnect', 1);
            }

        }

        return $content;
    }
    
    /**
     * returns content of request as simplexml object
     * 
     * @param obj $response
     * @return simplexml
     */
    private function getXml($response){
        //simplexml_load_string will produce E_WARNING error messages for each error
        //found in the XML data. Therefore suppress error messages in any mode and
        //handle errors for debug-mode differently.
        //parse the XML data.
        libxml_use_internal_errors(TRUE);

        $content = simplexml_load_string($response->getBody()->getContents());

        $error_array = libxml_get_errors();
        libxml_clear_errors();

        //log url to devlog in debug-mode if XML data contained errors.
        if (count($error_array) > 0) {
            if ($this->debug) {
                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('XML data contained errors: '.$url, 'libconnect', 1);
            }
        }

        if ($this->debug) {
            $error_array = libxml_get_errors();
            if (count($error_array) > 0) {
                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('XML data contained errors: '.$url, 'libconnect', 1);
            }
        }

        //reset libxml error buffering and clear any existing libxml errors
        libxml_use_internal_errors(FALSE);
        
        return $content;
    }

    /**
     * returns content of response
     *
     * @param obj $response
     * @return string
     */
    private function getText($response){
        $content = $response->getBody()->getContents();

        return $content;
    }
}
