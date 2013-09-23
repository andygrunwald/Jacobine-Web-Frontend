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
 * Class CommitsPerHour
 *
 * Git commits over time (by hour)
 *
 * @package Extension\Analysis\Analysis\Git
 */
class CommitsPerHour extends \Extension\Analysis\Analysis\Base {

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
                'id' => 'chart_commitsperhour',
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

        $this->setJavaScriptFiles(array('Git/CommitsPerHour.js'));

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

        $uidIndexField = 'hour';
        $select = '
            COUNT(scmlog.id) AS cnt,
            DATE_FORMAT(scmlog.date, \'%H\') AS hour';
        $from = 'scmlog' . $repositoriesJoin;
        $where = '';
        $groupBy = 'hour';
        $orderBy = 'hour';

        return $database->exec_SELECTgetRows($select, $from, $where, $groupBy, $orderBy, '', $uidIndexField);
    }

    private function prepareData(array $dataSets) {
        // If we got to few commits, fill up array with default values
        if ($dataSets < 24) {
            for ($i = 0; $i <= 23; $i++) {
                if (array_key_exists($i, $dataSets) === false) {
                    $dataSets[$i] = 0;
                }
            }
        }

        $preparedSet = array();
        foreach ($dataSets as $key => $row) {
            // We add +1 here, to get "rid" sorting of .getJSON in JQuery
            $preparedSet[$key + 1] = (int) $row['cnt'];
        }

        return $preparedSet;
    }

}