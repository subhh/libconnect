<?php
namespace Sub\Libconnect\UserFunctions;
/**
 * Class 'IsfirstPlugInUserFunction' for the 'libconnect' extension.
 *
 * @author      Torsten Witt
 * @package     TYPO3
 * @subpackage  tx_libconnect
 */

class IsfirstPlugInUserFunction{
    /**
     * Checks if plugin is the first on the page
     *
     * @param string    $type: Name des gewählten PlugIns
     * @param integer    $uid: Uid des PlugIns aus Tabelle tt_content
     *
     * @return boolean
     */
    static function IsfirstPlugInUserFunction($type, $uid) {
        $pid = $GLOBALS['TSFE']->id;

        $list_type = 'libconnect_'.$type;

        $select = 'uid, pid, list_type, sorting';
        $from = 'tt_content';
        $where = 'pid = "'.$pid.'" AND list_type = "'.$GLOBALS['TYPO3_DB']->quoteStr($list_type, 'tt_content').'" AND deleted = "0" AND hidden = "0"';
        $groupBy = '';
        $orderBy = 'sorting asc';
        $limit = '0,1';
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select, $from, $where, $groupBy, $orderBy, $limit);

        //if current UID of the is not on top of the page, CSS shouldn´t loaded
        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
            if($row['uid'] != $uid){
                return FALSE;
            }
        }

        return TRUE;
    }

}
?>
