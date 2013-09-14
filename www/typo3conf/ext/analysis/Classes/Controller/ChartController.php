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

namespace Extension\Analysis\Controller;

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Extension\Analysis\Utility\Analysis;
use Extension\Analysis\Utility\Database;
use Extension\Analysis\Utility\Naming;
use Extension\Analysis\Utility\Loading;

class ChartController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

    const CHART_TEMPLATE_FOLDER = 'Analysis';

    /**
     * Initializes the current action
     *
     * @return void
     */
    public function initializeAction() {
        $extensionName = $this->request->getControllerExtensionKey();
        Loading::vendorLibraries($extensionName);
    }

    /**
     * Output a list view of news
     *
     * @return void
     */
    public function indexAction() {
        $cObjData = $this->configurationManager->getContentObject();

        $className = $cObjData->data['select_key'];
        $className = Naming::analysisClassName($className);

        $analysisConfiguration = array();

        // Execute the analysis
        $analysisDatabase = Database::getAnalysisDatabaseConnection();
        $analysisObj = Analysis::initAnalysisObject($className, $analysisDatabase, $analysisConfiguration);
        $analysisObj->generate();

        // Chose a different template if necessary
        $templateName = $analysisObj->getTemplate();
        if ($templateName !== null) {
            $extPath = ExtensionManagementUtility::extPath($this->request->getControllerExtensionKey());
            $pathParts = ['Resources', 'Private', 'Templates', $this->request->getControllerName(), self::CHART_TEMPLATE_FOLDER, $templateName];
            $templatePath = $extPath . implode(DIRECTORY_SEPARATOR, $pathParts) . '.html';
            $this->view->setTemplatePathAndFilename($templatePath);
        }

        $javaScriptFiles = $analysisObj->getJavaScriptFiles();


        if (count($javaScriptFiles) > 0) {
            $pageRenderer = $GLOBALS['TSFE']->getPageRenderer();
            /** @var $pageRenderer \TYPO3\CMS\Core\Page\PageRenderer */

            $siteRelPath = ExtensionManagementUtility::siteRelPath($this->request->getControllerExtensionKey());
            foreach($javaScriptFiles as $jsFile) {
                $pathParts = ['Resources', 'Public', 'Js', 'Charts', $jsFile];
                $jsFilePath = $siteRelPath . implode(DIRECTORY_SEPARATOR, $pathParts);
                $pageRenderer->addJsFooterFile($jsFilePath);
            }
        }

        $baseVariables = array(
            'content' => $analysisObj->getContent(),
            'javascript' => $analysisObj->getJavaScript()
        );
        $this->view->assignMultiple(array_merge($baseVariables, $analysisObj->getTemplateVariable()));
    }
}