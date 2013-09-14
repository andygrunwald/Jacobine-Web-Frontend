<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

use \TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;

ExtensionManagementUtility::addPlugin(array(
    'LLL:EXT:analysis/locallang_db.xml:tt_content.list_type.pi1',
    $_EXTKEY . '_pi1',
    ExtensionManagementUtility::extRelPath($_EXTKEY) . 'ext_icon.gif'
));

GeneralUtility::loadTCA('tt_content');
$GLOBALS['TCA']['tt_content']['columns']['list_type']['config']['items'][] = array('Analysis', '--div--', ExtensionManagementUtility::extRelPath($_EXTKEY) . 'ext_icon.gif');

ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'Chart',
    'A single Chart'
);

/**
 * TypoScript inclusion
 */
ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Analysis: Extension-Setup');
ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Static/Page.Default/', 'Analysis: Page-Setup');