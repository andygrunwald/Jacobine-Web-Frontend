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

namespace Extension\Analysis\Analysis\Gerrit;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ActivityMonitor
 *
 * Activity monitory based of Gerrit code review data
 * Inspired by Tolleiv`s Repo Activity Monitor
 *
 * @link https://github.com/tolleiv/Repo-Activity-Monitor
 * @link http://blog.tolleiv.de/2012/01/visualizing-typo3-core-activity/
 *
 * @package Extension\Analysis\Analysis\Gerrit
 */
class ActivityMonitor extends \Extension\Analysis\Analysis\Base {

    public function __construct() {
        $this->setTemplate('Gerrit' . DIRECTORY_SEPARATOR . 'ActivityMonitor');
    }

    /**
     * Generates a activity monitor analysis
     *
     * @return string
     */
    public function generate() {
        $configuration = $this->getConfiguration();

        // Collecting gerrit projects for select dropdown
        $gerritProjectManager = GeneralUtility::makeInstance('Extension\\Analysis\\DataManager\\GerritProjects', $this->getAnalyticDatabase());
        /* @var $gerritProjectManager \Extension\Analysis\DataManager\Base */
        $gerritProjects = $gerritProjectManager->getData();
        $gerritProjects = array('All Gerrit projects') + $gerritProjects;

        $data = $this->getData();

        $activeProject = 0;
        if (array_key_exists('project', $configuration) === true) {
            $activeProject = intval($configuration['project']);
        }

        $this->setTemplateVariable(array(
            'analysis' => $data,
            'project' => array (
                'values' => $gerritProjects,
                'active' => $activeProject
            )
        ));

        return true;
    }

    public function getData() {
        $configuration = $this->getConfiguration();
        $pointResource = $this->getPoints($configuration);
        list($dataSets, $persons) = $this->prepareData($pointResource);

        $dataSets = array(
            'points' => $dataSets,
            'persons' => $persons
        );
        return $dataSets;
    }

    private function prepareData($resource) {
        $points = $persons = array();
        $database = $this->getAnalyticDatabase();

        while ($tempRow = $database->sql_fetch_assoc($resource)) {
            $points[$tempRow['yearAndMonth']][$tempRow['personId']] += $tempRow['points'];
            $persons[$tempRow['personId']] = $tempRow['name'];
        }

        // Resort the points :)
        foreach ($points as $yearAndMoth => $pointsForAMonth) {
            arsort($points[$yearAndMoth]);
        }

        return array($points, $persons);
    }

    /**
     * Calculates the points of activity per year, month and person.
     *
     * Points:
     *  10 points for creation of a patchset
     *  3 points for a "Verified" approval
     *  1 point for a "Code-Review" approval
     *  1 point for a "SUBM" approval (merge of patchset)
     *
     * @todo Add missing activity
     *  * Comments
     *  * Difference between creater of changeset and patchset
     *  * Difference between uploader and author of patchset
     *
     * @param $configuration
     * @return array|NULL
     */
    private function getPoints($configuration) {
        $database = $this->getAnalyticDatabase();

        $wherePart = '1';
        if ($configuration['project'] > 0) {
            $wherePart = 'project.id = ' . intval($configuration['project']);
        }

        $query = '
            SELECT
                DATE_FORMAT(FROM_UNIXTIME(approval.granted_on), \'%Y-%m\') as yearAndMonth,
                SUM(
                    CASE approval.type
                        WHEN \'Verified\' THEN 3
                        WHEN \'Code-Review\' THEN 1
                        WHEN \'SUBM\' THEN 1
                        ELSE 0
                    END
                ) AS points,
                person.id AS personId,
                person.name
            FROM
                gerrie_project AS project
                INNER JOIN gerrie_changeset AS changeset ON (
                    project.id = changeset.project
                )
                INNER JOIN gerrie_patchset AS patchset ON (
                    changeset.id = patchset.changeset
                )
                INNER JOIN gerrie_approval AS approval ON (
                    patchset.id = approval.patchset
                )
                INNER JOIN gerrie_person AS person ON (
                    person.id = approval.by
                )
            WHERE
                ' . $wherePart . '
            GROUP BY
                yearAndMonth, person.id
            UNION
            SELECT
                DATE_FORMAT(FROM_UNIXTIME(patchset.created_on), \'%Y-%m\') as yearAndMonth,
                SUM(10) AS points,
                person.id AS personId,
                person.name
            FROM
                gerrie_project AS project
                INNER JOIN gerrie_changeset AS changeset ON (
                    project.id = changeset.project
                )
                INNER JOIN gerrie_patchset AS patchset ON (
                    changeset.id = patchset.changeset
                )
                INNER JOIN gerrie_person AS person ON (
                    person.id = patchset.uploader
                )
            WHERE
                ' . $wherePart . '
            GROUP BY
                yearAndMonth, person.id
            ORDER BY
                yearAndMonth DESC, points DESC';

        return $database->admin_query($query);
    }
}