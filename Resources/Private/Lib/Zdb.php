<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 André Lahmann (andre.lahmann@uni-leipzig.de) - Leipzig University Library (LUL)
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * documentation: http://www.zeitschriftendatenbank.de/services/journals-online-print/
 * documentation: http://www.zeitschriftendatenbank.de/fileadmin/user_upload/ZDB/pdf/services/JOP_Spezifikation_XML-Dienst.pdf
 * documentation: http://www.zeitschriftendatenbank.de/fileadmin/user_upload/ZDB/pdf/services/JOP_Dokumentation_XML-Dienst.pdf
 *
 */

/**
 * Typoscript Settings
 *      settings.enableLocationData = 1
 *      settings.validStatesList = 2,3
 *      settings.EZBSearchResultsPageId = pageID
 *      settings.DBISSearchResultsPageId = pageID
 *      settings.useIconService = 1/0
 *      zdbsid = id:phrase
 *      zdbbibid = BIBID
 *      zdbsigel = SIGEL
 *      zdbisil = ISIL
 *      zdbbik = BIK
 *
 */

class Tx_libconnect_Resources_Private_Lib_Zdb {

   /**
    * enable debug for logging errors to devLog
    */
    private $debug = FALSE;

   /**
    * Source-Identifier (sid – Vendor-ID:Database-ID) needs to be arranged with
    * the ZDB (contact: Mr. Rolschewski, mailto: johann.rolschewski@sbb.spk-berlin.de)
    */
    private $sid = NULL;

   /**
    * library authentication parameters to display correct availability
    */
    private $bibid = NULL;
    private $sigel = NULL;
    private $isil = NULL;
    private $bik = NULL;

   /**
    * non-open-url conform pid arguments-string
    *
    */
    private $pid = '';
    private $onlyPrintFlag = true;

   /**
    * request URLs
    */
    //private $briefformat_request_url = 'http://services.dnb.de/fize-service/gvr/brief.xml?';
    private $fullformat_request_url = 'http://services.dnb.de/fize-service/gvr/full.xml?';

    // XML Data Object
    private $XMLPageConnection;

    /**
    * Class Constructor
    */
    function __construct() {

        $ext_conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['libconnect']);
        if ($ext_conf['debug'] == TRUE) {
            $this->debug = TRUE;
        }
        if ($ext_conf['debug'] == FALSE) {
            $this->debug = FALSE;
        }

        $this->XMLPageConnection = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_libconnect_resources_private_lib_xmlpageconnection');

        if(!$this->getSid()) {
            //todo: error message
            //error_log('typo3 extension libconnect - missing ZDB source-identifier: refer to documentation - chapter configuration.');
            if ($this->debug) {
                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('invalid SID given: ' . $this->sid, 'libconnect', 1);
            }

            return FALSE;
        }

        //get the bibid
        if (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbbibid'])) {
            $this->bibid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbbibid'];
        }
        //if no explicit bibid for zdb is set, try to find the bibid which needs to setup for libconnect without zdb-support
        elseif (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid'])) {
            $this->bibid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid'];
        }

        //get the library sigel
        if (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbsigel'])) {
            $this->sigel = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbsigel'];
        }
        //get the isil
        if (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbisil'])) {
            $this->isil = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbisil'];
        }
        //get the bik
        if (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbbik'])) {
            $this->bik = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbbik'];
        }

        $this->pid = urlencode((!empty($this->bibid) ? 'bibid=' . $this->bibid .'&' : '') .
                               (!empty($this->sigel) ? 'sigel=' . $this->sigel .'&' : '') .
                               (!empty($this->isil) ? 'isil=' . $this->isil .'&' : '') .
                               (!empty($this->bik) ? 'bik=' . $this->bik : ''));

        //remove last &(urlencode: %26) if existent (only if bik is empty but any other info above is given)
        if (strlen($this->pid) - 3 == strrpos($this->pid, '%26')) {
            $this->pid = substr($this->pid, 0, strlen($this->pid) - 3);
        }

        //only print location data are requested (default is off, so online and print will be delivered)
        if ($this->onlyPrintFlag) {
            $this->pid .= (strlen($this->pid) > 0 ? urlencode('&print=1') : urlencode('print=1'));
        }
    }

    /**
    * returns detail information of the position of a journal
    *
    * @param JournalIdentifier string
    * @param ZDBID string
    *
    * @return string
    */
    public function getJournalLocationDetails($journalIdentifier, $ZDBID, $genre = 'journal'){
        /**
        * to identify a journal either the ISSN or the eISSN is mandatory as primary key
        * alternatively the ZDB-ID is allowed but does not conform with Open-URL-Standard
        * - JournalIdentifier is a complete Get-Parameter-String
        *    e.g. "issn=1234567-4"
        * - ZDBID is a string of a ZDB-ID
        *
        */
        if(empty($journalIdentifier) && empty($ZDBID)){

            return FALSE;
        }else {
            if(!empty($ZDBID)) {
                $this->pid .= (strlen($this->pid) > 0 ? urlencode("&zdbid={$ZDBID}") : urlencode("zdbid={$ZDBID}"));
            }
            if(!empty($journalIdentifier)) {
                $journalIdentifier = "&{$journalIdentifier}";
            }
        }

        $url = "{$this->fullformat_request_url}sid={$this->sid}" . (!empty($this->pid) ? "&pid={$this->pid}" : "" ) . $journalIdentifier . "&genre={$genre}";

        $xml_request = $this->XMLPageConnection->getDataFromXMLPage($url);

        $locationDetail = array();

        // root-element = OpenURLResponseXML->Full/Brief
        // only Full-objects got all the info we want
        if (! is_object($xml_request->Full)) {
            if ($this->debug) {
                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('invalid XML-Object - URL: ' . $url, 'libconnect', 1);
            }

            return FALSE;
        } elseif (property_exists($xml_request->Full, 'Error')) {
            /**
             * possible Error-Codes:
             *     Code        Meaning
             *    -----------------------------------------------
             *     m-issn      ISSN fehlt!
             *     genre       Genre nicht journal oder article!
             *     f-issn      ISSN mit falschen Format!
             *     unknown     Unbekannter Fehler
             *
             */
            if ($this->debug) {
                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Error-Code: ' . $xml_request->Full->Error->attributes()->code . ' - URL: ' . $url, 'libconnect', 1);
            }

            return FALSE;
        }

        $locationDetail['library'] = (string) $xml_request->Full->PrintData->Library;

        //check if returned XML-Object provides ResultList (as only this contains location data, exit if no ResultList is provided)
        if (is_object($xml_request->Full->PrintData->ResultList) && get_class($xml_request->Full->PrintData->ResultList) == 'SimpleXMLElement') {
            $tmpStates = $xml_request->Full->PrintData->ResultList->children();
            $tmpResultList = $xml_request->Full->PrintData->ResultList->children();
        } else {
            if ($this->debug) {
                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('invalid ResultList - URL: ' . $url, 'libconnect', 1);
            }

            return FALSE;
        }

        //as the script is stil running the XML-Object contains all the data necessary for the location information, so continue
       /**
        * two branches: <ElectronicData> : electronic availability
        *               <PrintData> : print
        *
        * as only the location of the printed journal is of any concern, only the
        * branch PrintData is regarded
        *
        * states
        * ------
        * -1, 10 = Error: ZDB-ID, ISSN, Sigel or ISBN unknown or not unique
        * 2 = available
        * 3 = limited availability (moving wall, etc.)
        * 4 = journal not available
        *
        * check for any valid (for display) state - default is 2 and 3 (custom
        * configuration in typoscript):
        *     tx_libconnect.validStatesList = 1,2,3,4,5,6
        */
        $validStatesArray = (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['validStatesList']) && !empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['validStatesList']) ?
                             explode(',', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['validStatesList']) :
                             array(2,3)
                            );
        array_walk($validStatesArray, create_function('&$val', '$val = trim($val);')); //remove all whitespaces from states
        $validStateFlag = FALSE;
        if (count($tmpStates)) {
            foreach($tmpStates as $tmpState) {
                if (in_array($tmpState->attributes()->state, $validStatesArray)){
                    $validStateFlag = TRUE;
                }
            }
        }
        //no valid state found -> exit
        if(!$validStateFlag) {
            if ($this->debug) {
                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('non valid state - URL: ' . $url, 'libconnect', 0);
            }

            return FALSE;
        }

       /**
        * Result
        * --------
        *     Attributes:     (integer)    state
        *     Children:       (string)     Title
        *                     (string)     Location
        *                     (string)     Signature
        *                     (string)     Period
        *
        * subelement Additionals is not considered as it's not used for PrintData yet
        *
        */

        $locationDetail['resultList'] = array();
        if(count($tmpResultList)) {
            foreach($tmpResultList as $tmpResult) {
                if(in_array($tmpResult->attributes()->state, $validStatesArray)) {
                    array_push($locationDetail['resultList'],
                        array('state'     => (int) $tmpResult->attributes()->state,
                            'Title'     => (string) $tmpResult->Title,
                            'Location'  => (string) $tmpResult->Location,
                            'Signature' => (string) $tmpResult->Signature,
                            'Period'    => (string) $tmpResult->Period)
                    );
                }
            }
        }

        /**
        * Reference
        * ---------
        *     Children:       (string)     URL
        *                     (string)     Label
        */
        if (is_object($xml_request->Full->PrintData->References) && get_class($xml_request->Full->PrintData->References) == 'SimpleXMLElement') {
            $tmpReferences = $xml_request->Full->PrintData->References->children();
            $locationDetail['references'] = array();
            if(count($tmpReferences)) {
                foreach($tmpReferences as $tmpReference) {
                    array_push($locationDetail['references'],
                        array('URL'   => (string) $tmpReference->URL,
                            'Label' => (string) $tmpReference->Label)
                    );
                }
            }
        }

        /**
         * Icon-Service
         * ------------
         *
         */
         $locationDetail['iconRequest'] = $this->buildIconRequest($journalIdentifier, $genre);
         $locationDetail['iconInfoUrl'] = $this->buildIconInfoUrl($journalIdentifier, $genre);

        if ($this->debug) {
            \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Request successful - URL: ' . $url, 'libconnect', 0);
        }

        return $locationDetail;
    }

    /**
     * helper function build ICON-service request
     * (http://www.zeitschriftendatenbank.de/fileadmin/user_upload/ZDB/pdf/services/JOP_Dokumentation_Icon-Dienst.pdf)
     *
     * @return string
     */
    private function buildIconRequest($journalIdentifier, $genre){

        return "https://services.dnb.de/fize-service/gvr/icon?sid='.{$this->sid}" . (!empty($this->pid) ? "&pid={$this->pid}" : '' ) . $journalIdentifier . "&genre={$genre}";
    }

    /**
     * helper function build ICON-info url
     * (http://www.zeitschriftendatenbank.de/fileadmin/user_upload/ZDB/pdf/services/JOP_Dokumentation_Icon-Dienst.pdf)
     *
     * @return string
     */
    private function buildIconInfoUrl($journalIdentifier, $genre){

        return "https://services.dnb.de/fize-service/gvr/html-service.htm?sid={$this->sid}" . (!empty($this->pid) ? "&pid={$this->pid}" : '' ) . $journalIdentifier . "&genre={$genre}";
    }

    /**
     * helper function get SID
     *
     * @return string
     */
    private function getSid(){
        $this->sid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['zdbsid'];

        if(is_null($this->sid) or !$this->sid or empty($this->sid)){

            return FALSE;
        }

        return TRUE;
    }
}
?>
