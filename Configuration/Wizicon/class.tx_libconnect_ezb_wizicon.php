<?php

class tx_libconnect_ezb_wizicon {

    function proc($wizardItems)	{
        global $LANG;

        $wizardItems['plugins_tx_libconnect_ezb'] = array(
            'icon'=>'EXT:libconnect/wiz_icon.gif',
            'title'=> 'Plugin EZB',
            'description'=> 'Plugin zur Einbindung von EZB',
            'params'=>'&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=libconnect_ezb'
        );

        return $wizardItems;
    }
}

?>
