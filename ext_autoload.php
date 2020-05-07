<?php
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('libconnect');
$default = array(
    'Tx_libconnect_Resources_Private_Lib_Dbis' => $extensionPath . 'Resources/Private/Lib/Dbis.php',
    'Tx_libconnect_Resources_Private_Lib_Ezb' => $extensionPath . 'Resources/Private/Lib/Ezb.php',
    'Tx_libconnect_Resources_Private_Lib_Zdb' => $extensionPath . 'Resources/Private/Lib/Zdb.php'
);

return $default;
?>
