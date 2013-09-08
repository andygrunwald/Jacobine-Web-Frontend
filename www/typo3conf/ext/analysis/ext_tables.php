<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addPlugin(array(
    'LLL:EXT:analysis/locallang_db.xml:tt_content.list_type.pi1',
    $_EXTKEY . '_pi1',
    ExtensionManagementUtility::extRelPath($_EXTKEY) . 'ext_icon.gif'
));

/**
 * TypoScript inclusion
 */
ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Static/Page.Default/', 'Analysis: Page');