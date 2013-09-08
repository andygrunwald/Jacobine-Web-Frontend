<?php
/***************************************************************
 *  Copyright notice
*
*  (c) 2013 Andy Grunwald <andreas.grunwald@gmail.com>
*
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Database\DatabaseConnection;
use \Extension\Analysis\Analysis;

class tx_analysis_pi1 extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin {

    /**
     * Same as class name
     *
     * @var string
     */
    public $prefixId = 'tx_analysis_pi1';

    /**
     * Path to this script relative to the extension dir.
     *
     * @var string
     */
    public $scriptRelPath = 'ContentMain/class.tx_analysis_pi_content_main.php';

    /**
     * The extension key.
     *
     * @var string
     */
    public $extKey  = 'analysis';

    /**
     * Check cHash
     *
     * @var bool
     */
    public $pi_checkCHash = true;

    /**
     * TYPO3 database
     *
     * @var null|\TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $database = null;

    /**
     * Database connection for analysis database
     *
     * @var null|\TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $analysisDatabase = null;

    /**
     * @var null|\TYPO3\CMS\Fluid\View\StandaloneView
     */
    protected $view = null;

    /**
     * Main method of your PlugIn
     *
     * @param	string		$content: The content of the PlugIn
     * @param	array		$conf: The PlugIn Configuration
     * @return	The content that should be displayed on the website
     */
    public function main($content, $conf)	{
        $this->conf = $conf;
        $this->database = $GLOBALS['TYPO3_DB'];
        $this->loadVendorLibraries();

        $this->setView(GeneralUtility::makeInstance('TYPO3\CMS\Fluid\View\StandaloneView'));

        $className = $this->cObj->data['select_key'];
        // @todo build class name + class exists stuff + throw exception

        $className = $this->buildAnalysisClassName($className);
        $analysisObj = $this->generateAnalysis($className);

        // @todo refactor this to using fluid / view object here
        // @todo extract js into file?
        $content = $analysisObj->getContent() . '<script type="text/javascript">' . $analysisObj->getJavaScript() . '</script>';

        return $content;
    }

    protected function loadVendorLibraries() {
        $extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($this->extKey);
        require_once $extensionPath . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    }

    /**
     * Initialize the database connection to analytic database
     *
     * @return void
     */
    protected function initAnalysisDatabaseConnection() {
        $database = GeneralUtility::makeInstance('TYPO3\CMS\Core\Database\DatabaseConnection');
        /* @var $database \TYPO3\CMS\Core\Database\DatabaseConnection */
        $database->setDatabaseHost(ANALYSIS_DB_HOSTNAME);
        $database->setDatabaseUsername(ANALYSIS_DB_USERNAME);
        $database->setDatabasePassword(ANALYSIS_DB_PASSWORD);
        $database->setDatabaseName(ANALYSIS_DB_DATABASE);
        $database->sql_pconnect();
        $database->sql_select_db();

        $this->analysisDatabase = $database;
    }

    /**
     * Gets the database connection for analysis database
     *
     * @return null|\TYPO3\CMS\Core\Database\DatabaseConnection
     */
    public function getAnalysisDatabaseConnection() {
        if ($this->analysisDatabase === null) {
            $this->initAnalysisDatabaseConnection();
        }

        return $this->analysisDatabase;
    }

    /**
     * Sets the view object
     *
     * @param \TYPO3\CMS\Fluid\View\StandaloneView $view
     * @return void
     */
    public function setView(\TYPO3\CMS\Fluid\View\StandaloneView $view) {
        $this->view = $view;
    }

    /**
     * Gets the view object
     *
     * @return null|\TYPO3\CMS\Fluid\View\StandaloneView
     */
    public function getView() {
        return $this->view;
    }

    /**
     * Generates a single analysis
     *
     * @param string $className
     * @return \Extension\Analysis\Analysis\Base
     */
    protected function generateAnalysis($className) {
        $analysisObj = GeneralUtility::makeInstance($className);
        /* @var $analysisObj \Extension\Analysis\Analysis\Base */

        $analysisObj->setConfiguration(array());
        $analysisObj->setAnalyticDatabase($this->getAnalysisDatabaseConnection());

        $analysisObj->generate();

        return $analysisObj;
    }

    protected function buildAnalysisClassName($className) {
        $className = 'Extension\Analysis\Analysis\\' . $className;
        return $className;
    }
}
