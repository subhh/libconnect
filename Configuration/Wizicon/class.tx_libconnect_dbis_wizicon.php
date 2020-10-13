<?php

class tx_libconnect_dbis_wizicon
{
    public function proc($wizardItems)
    {
        global $LANG;

        $wizardItems['plugins_tx_libconnect_dbis'] = [
            'icon'=>'EXT:libconnect/wiz_icon.gif',
            'title'=> 'Plugin DBIS',
            'description'=> 'Plugin zur Einbindung von DBIS',
            'params'=>'&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=libconnect_dbis'
        ];

        return $wizardItems;
    }
}
