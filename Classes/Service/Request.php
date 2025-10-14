<?php

namespace Subhh\Libconnect\Service;

use TYPO3\CMS\Core\Http\RequestFactory;
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
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);

        if ($urldecode) {
            $query = urldecode(http_build_query($this->getQuery()));
        } else {
            $query = $this->getQuery();
        }

        $url = sprintf('%s?%s', $this->getUrl(), $query);
        $additionalOptions = [
            'headers' => [
                'Cache-Control' => 'no-cache',
                'Accept' => 'text/xml; charset=UTF8'
            ],
            'allow_redirects' => true
        ];

        try {
            $response = $requestFactory->request($url, 'GET', $additionalOptions);

            if ($response->getStatusCode() !== 200) {
                $this->logger->debug('Request failed', [
                    'code' => $response->getStatusCode(),
                    'url' => $url
                ]);
                return false;
            }
        } catch (\Throwable $e) {
            $this->logger->debug('Request failed: ' . $e->getMessage(), ['url' => $url]);

            return false;
        }

        $contentType = str_replace(' ', '', strtolower($response->getHeaderLine('Content-Type')));

        if (strpos($contentType, 'application/xml') === 0) {//DBIS, services.dnb.de
            $content = $this->getXml($response);
        } elseif (strpos($contentType, 'text/xml;charset=utf-8') === 0) {//DBIS, services.dnb.de
            $content = $this->getXml($response);
        } elseif (strpos($contentType, 'text/xml;charset=iso-8859-1') === 0) {//EZB
            $content = $this->getXml($response);
        } elseif (strpos($contentType, 'application/rdf+xml;charset=utf-8') === 0) {//title history
            $content = $this->getText($response);

            //moreDetails
        } elseif (preg_match('/text\/html;charset=(iso-8859-1)?(utf-8)?/', $contentType, $matches)) {
            $content = $this->getText($response);
        } elseif (strpos($contentType, 'application/json') === 0) {//DBIS, services.dnb.de
            $content = $this->getJson($response);
        } else {

            return false;
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
 
    private function getJson($response)
    {
        $json = $response->getBody()->getContents();
        return json_decode($json, true);
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
