<?php

class tx_libconnect_dbis_wizicon {

    function proc($wizardItems)	{
        global $LANG;

        $wizardItems['plugins_tx_libconnect_dbis'] = array(
            'icon'=>\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('libconnect').'wiz_icon.gif',
            'title'=> 'Plugin DBIS',
            'description'=> 'Plugin zur Einbindung von DBIS',
            'params'=>'&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=libconnect_dbis'
        );

        return $wizardItems;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/libconnect/Configuration/Wizicon/class.tx_libconnect_dbis_wizicon.php']){
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/libconnect/Configuration/Wizicon/class.tx_libconnect_dbis_wizicon.php']);
}
?>