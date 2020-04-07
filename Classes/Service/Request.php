<?php
namespace Sub\Libconnect\Service;

#use TYPO3\CMS\Extbase\Annotation\Inject;
/**
 *
 *
 * @package libconnect
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */


class Request {
    
    private $url = '';
    
    private $query = array(
                        'xmloutput' => 1
                    );
    
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


    public function Request(){
        $requestFactory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Http\RequestFactory::class);
        
        #$jar = new \GuzzleHttp\Cookie\CookieJar;
        
        $additionalOptions = [
            'headers' => ['Cache-Control' => 'no-cache'],
            'allow_redirects' => true,
            'query' => $this->getQuery(),
            'headers' => ['Accept' => 'text/xml; charset=UTF8']
        ];
        
        
        $response = $requestFactory->request($this->getUrl(), 'GET', $additionalOptions);
        
        $content = FALSE;
        
        // Get the content as a string on a successful request
        if ($response->getStatusCode() === 200) {

            if (strpos($response->getHeaderLine('Content-Type'), 'text/xml; charset=utf-8') === 0) {
                $content = $this->getXml($response);
            }

        }else{
            if ($this->debug){
                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Got HTTP Code ' . $http_code['http_code'] . ' for request: ' . $url, 'libconnect', 1);
            }

        }
        
        return $content;
    }
    
    /**
     * 
     * @param type $response
     * @return array
     */
    public function getXml($response){
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

}
