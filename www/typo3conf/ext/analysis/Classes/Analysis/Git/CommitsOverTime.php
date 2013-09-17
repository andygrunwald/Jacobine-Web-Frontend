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

namespace Extension\Analysis\Analysis\Git;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class CommitsOverTime
 *
 * Git commits over time
 *
 * @package Extension\Analysis\Analysis\Git
 */
class CommitsOverTime extends \Extension\Analysis\Analysis\Base {

    /**
     * Constructor
     *
     * Sets the template path of this analysis
     */
    public function __construct() {
        $this->setTemplate('Git' . DIRECTORY_SEPARATOR . 'CommitsOverTime');
    }

    /**
     * Generates a analysis
     *
     * @return string
     */
    public function generate() {
        $configuration = $this->getConfiguration();
        $activeProject = 0;
        if (array_key_exists('project', $configuration) === true) {
            $activeProject = intval($configuration['project']);
        }

        // Collecting git projects for select dropdown
        $gitProjectManager = GeneralUtility::makeInstance('Extension\\Analysis\\DataManager\\GitProjects', $this->getAnalyticDatabase());
        /* @var $gitProjectManager \Extension\Analysis\DataManager\Base */
        $gitProjects = $gitProjectManager->getData();
        $gitProjects = array('All git projects') + $gitProjects;

        $this->setTemplateVariable(array(
            'container' => array(
                'id' => 'chart_commitsovertime',
            ),
            'project' => array (
                'values' => $gitProjects,
                'active' => $activeProject
            )
        ));

        // This part is not really beautiful.
        // If we use a kind of this part more often, we have to outsource it
        // A first use case of a trait?
        if ($activeProject > 0) {
            $javaScript = 'var project = ' . $activeProject . ';';
            $this->setJavaScript($javaScript);
        }

        $this->setJavaScriptFiles(array('Git/CommitsOverTime.js'));

        return true;
    }

    public function getData() {
        $configuration = $this->getConfiguration();
        $dataSet = $this->execDataQuery($configuration);
        return $this->prepareData($dataSet);
    }

    private function execDataQuery($configuration) {
        $database = $this->getAnalyticDatabase();
        /* @var $database \TYPO3\CMS\Core\Database\DatabaseConnection */

        $repositoriesJoin = '';
        if ($configuration['project'] > 0) {
            $repositoriesJoin = '
                INNER JOIN repositories ON (
                    repositories.id = scmlog.repository_id
                    AND repositories.id = ' . intval($configuration['project']) . '
                )';
        }

        // Yes, i know. We do not need the inner join here, BUT with this inner join
        // we got the safety that the relation exists. And this is a nice side effect :)
        $select = '
            ((UNIX_TIMESTAMP(scmlog.date) - TIME_TO_SEC(DATE_FORMAT(scmlog.date, "%H:%i:%s"))) * 1000) AS milliseconds,
            COUNT(scmlog.id) AS cnt';
        $from = 'scmlog' . $repositoriesJoin;
        $where = '';
        $groupBy = 'milliseconds';
        $orderBy = 'milliseconds';

        return $database->exec_SELECTgetRows($select, $from, $where, $groupBy, $orderBy);
    }

    private function prepareData(array $dataSets) {
        $preparedData = array();
        foreach ($dataSets as $value) {
            // Cast it to int, because mysql returns a string
            $preparedData[] = array((int) $value['milliseconds'], (int) $value['cnt']);
        }

        return $preparedData;
    }

}