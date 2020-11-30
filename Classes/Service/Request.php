<?php

namespace Sub\Libconnect\Service;

use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Torsten Witt <torsten.witt@sub.uni-hamburg.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License, version 3 or later
 */
class Request
{
    private $url = '';

    private $query = [];

    /**
     * @var \TYPO3\CMS\Core\Log\Logger
     */
    protected $logger;

    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function setQuery($addtitionalQuery)
    {
        $query = $this->getQuery();

        $query = array_merge($query, $addtitionalQuery);

        $this->query = $query;
    }

    public function Request($urldecode = true)
    {
        $requestFactory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Http\RequestFactory::class);

        //$jar = new \GuzzleHttp\Cookie\CookieJar;

        if ($urldecode) {
            $query = urldecode(http_build_query($this->getQuery(), null, '&'));
        } else {
            $query = $this->getQuery();
        }

        $additionalOptions = [
            'headers' => ['Cache-Control' => 'no-cache'],
            'allow_redirects' => true,
            'query' => $query,
            'headers' => ['Accept' => 'text/xml; charset=UTF8']
        ];

        $response = false;

        try {
            $response = $requestFactory->request($this->getUrl(), 'GET', $additionalOptions);
        } catch (\Exception $e) {
            $this->logger->debug(
                'Got HTTP Code ' . $response->getStatusCode() . ' for request: ' . $this->url .
                http_build_query($this->getQuery(),null, '&'));
            return false;
        }

        $content = false;

        // Get the content as a string on a successful request
        if ($response->getStatusCode() === 200) {
            $contentType = str_replace(' ', '', strtolower($response->getHeaderLine('Content-Type')));

            if (strpos($contentType, 'text/xml;charset=utf-8') === 0) {//DBIS, services.dnb.de
                $content = $this->getXml($response);
            } elseif (strpos($contentType, 'text/xml;charset=iso-8859-1') === 0) {//EZB
                $content = $this->getXml($response);
            } elseif (strpos($contentType, 'application/rdf+xml;charset=utf-8') === 0) {//title history
                $content = $this->getText($response);

                //moreDetails
            } elseif (preg_match('/text\/html;charset=(iso-8859-1)?(utf-8)?/', $contentType, $matches)) {
                $content = $this->getText($response);
            } else {
                return false;
            }
        } else {
            $this->logger->debug(
                'Got HTTP Code ' . $response->getStatusCode() . ' for request: ' . $this->url .
                http_build_query($this->getQuery(),null, '&'));
        }

        return $content;
    }

    /**
     * returns content of request as simplexml object
     *
     * @param obj $response
     * @return simplexml
     */
    private function getXml($response)
    {
        //simplexml_load_string will produce E_WARNING error messages for each error
        //found in the XML data. Therefore suppress error messages in any mode and
        //handle errors for debug-mode differently.
        //parse the XML data.
        libxml_use_internal_errors(true);

        $content = simplexml_load_string($response->getBody()->getContents());

        $error_array = libxml_get_errors();
        libxml_clear_errors();

        //log url to devlog in debug-mode if XML data contained errors.
        if (count($error_array) > 0) {
            $this->logger->debug('XML data contained errors: ' . $this->url, $error_array);
        }

        //reset libxml error buffering and clear any existing libxml errors
        libxml_use_internal_errors(false);

        return $content;
    }

    /**
     * returns content of response
     *
     * @param obj $response
     * @return string
     */
    private function getText($response)
    {
        $content = $response->getBody()->getContents();

        return $content;
    }
}
