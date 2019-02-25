<?php
namespace Sub\Libconnect\UserFunctions;
/**
 * Class 'IsfirstPlugInUserFunction' for the 'libconnect' extension.
 *
 * @author      Torsten Witt
 * @package     TYPO3
 * @subpackage  tx_libconnect
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

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

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
        $statement = $queryBuilder
           ->select('uid', 'pid', 'list_type', 'sorting')
           ->from('tt_content')
           ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid)),
                $queryBuilder->expr()->eq('list_type', $queryBuilder->createNamedParameter($list_type)),
                $queryBuilder->expr()->isNotNull('deleted'),
                $queryBuilder->expr()->isNotNull('hidden')
           )
           ->execute();

        //if current UID of the is not on top of the page, CSS shouldn´t loaded
        while ($row = $statement->fetch()) {

            if($row['uid'] == $uid){

                return TRUE;
            }
        }

        return FALSE;
    }
}
?>
