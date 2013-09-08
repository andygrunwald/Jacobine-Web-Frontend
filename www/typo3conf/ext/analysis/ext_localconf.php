<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addPItoST43($_EXTKEY, 'ContentMain/class.tx_analysis_pi_content_main.php', '_pi1', 'list_type', 1);
