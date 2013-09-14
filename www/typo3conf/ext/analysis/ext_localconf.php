<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionManagementUtility::addPItoST43($_EXTKEY, 'ContentMain/class.tx_analysis_pi_content_main.php', '_pi1', 'list_type', 1);

ExtensionUtility::configurePlugin(
    'Extension.' . $_EXTKEY,
    'Chart',
    array(
        'Chart' => 'index'
    )
);

/**
 * eID: ChartData
 */
$GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['chartData'] = 'EXT:' . $_EXTKEY . '/EId/ChartData.php';

/**
 * PageTsConfig
 */
ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:analysis/Configuration/TSConfig/pageTsConfig.ts">');