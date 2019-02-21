<?php
namespace Sub\Libconnect\UserFunctions;
/**
 * Class 'user_libconnect_hasSelectedPluginForCSSInclude' for the 'libconnect' extension.
 *
 * @author Björn Heinermann <hein@zhaw.ch>
 * @author Torsten Witt <torsten.witt@sub.uni-hamburg.de>

 * @package     TYPO3
 * @subpackage  tx_libconnect
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Checks which PlugIn is active
 *
 * @param string    $type: Name des gewählten PlugIns
 *
 * @return boolean
 */
function user_libconnect_hasSelectedPluginForCSSInclude($type) {

    $pid = $GLOBALS['TSFE']->id;
    $list_type = 'libconnect_'.$type;

    $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');
    $statement = $queryBuilder
        ->select('uid')
        ->from('tt_content')
        ->where(
             $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid)),
             $queryBuilder->expr()->eq('list_type', $queryBuilder->createNamedParameter($list_type)),
             $queryBuilder->expr()->isNotNull('deleted')
        )
        ->execute();

    while ($row = $statement->fetch()) {
        return TRUE;
    }

    return FALSE;
}
?>
