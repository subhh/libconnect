<?php

class tx_libconnect_dbis_wizicon {

    function proc($wizardItems)	{
        global $LANG;

        $wizardItems['plugins_tx_libconnect_dbis'] = array(
            'icon'=>\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('libconnect').'wiz_icon.gif',
            'title'=> 'Plugin DBIS',
            'description'=> 'Plugin zur Einbindung von DBIS',
            'params'=>'&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=libconnect_dbis'
        );

        return $wizardItems;
    }
}
?>
