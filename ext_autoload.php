<?php
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('libconnect');
$default = array(
	'Tx_libconnect_Resources_Private_Lib_Dbis' => $extensionPath . 'Resources/Private/Lib/Dbis.php',
    'Tx_libconnect_Resources_Private_Lib_Ezb' => $extensionPath . 'Resources/Private/Lib/Ezb.php',
    'Tx_libconnect_Resources_Private_Lib_Zdb' => $extensionPath . 'Resources/Private/Lib/Zdb.php',
    'Tx_libconnect_Resources_Private_Lib_Xmlpageconnection' => $extensionPath . 'Resources/Private/Lib/Xmlpageconnection.php',
    'Tx_libconnect_Resources_Private_Lib_Httppageconnection' => $extensionPath . 'Resources/Private/Lib/Httppageconnection.php'
);

return $default;
?>
