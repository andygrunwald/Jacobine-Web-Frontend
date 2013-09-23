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
 * Class ChangesetNotMerged
 *
 * Changesets which are not merged yet ordered by time
 *
 * @package Extension\Analysis\Analysis\Gerrit
 */
class ChangesetNotMerged extends \Extension\Analysis\Analysis\Base {

    public function __construct() {
        $this->setTemplate('Gerrit' . DIRECTORY_SEPARATOR . 'ChangesetNotMerged');
    }

    /**
     * Generates a analysis
     *
     * In this analysis we get every time all not merged changesets.
     * Why? If we get a active project we can only chose the changesets of this project!
     * Yes and no. For the project dropdown we have to get all projects with open changesets.
     * Now we have two possibilies:
     * 1) We send two database queries (one only for projects with open changesets and one for the changesets)
     * 2) We get all open changesets and filter the changesets
     *
     * At the moment, method 2) is implemented
     *
     * @return string
     */
    public function generate() {
        $configuration = $this->getConfiguration();

        // Collecting gerrit projects for select dropdown
        $gerritProjectManager = GeneralUtility::makeInstance('Extension\\Analysis\\DataManager\\GerritProjects', $this->getAnalyticDatabase());
        /* @var $gerritProjectManager \Extension\Analysis\DataManager\Base */
        $gerritProjects = $gerritProjectManager->getData();

        $data = $this->getData();

        $gerritProjects = $this->deleteProjectsWithoutChangesets($gerritProjects, $data);
        $gerritProjects = array('All Gerrit projects') + $gerritProjects;

        $activeProject = 0;
        if (array_key_exists('project', $configuration) === true) {
            $activeProject = intval($configuration['project']);
            $data = $this->deleteChangesetsFromOtherProjects($data, $activeProject);
        }

        // TODO add a "last updated" fact
        // TODO add "mergable" and "patchset count"
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
        $dataSets = $this->execDataQuery();

        return $dataSets;
    }

    private function execDataQuery() {
        $database = $this->getAnalyticDatabase();

        $uidIndexField = 'changesetId';
        $queryArray = array(
            'SELECT' => '
                pc.id AS projectId,
                ba.name AS branchName,
                cs.id AS changesetId,
                cs.subject,
                cs.url,
                cs.created_on',
            'FROM' => '
                gerrie_changeset AS cs
                INNER JOIN gerrie_changeset_status AS css ON (
                    cs.status = css.id
                    AND css.name = "NEW"
                )
                INNER JOIN gerrie_project AS pc ON (
                    pc.id = cs.project
                )
                INNER JOIN gerrie_branch AS ba ON (
                    cs.branch = ba.id
                )',
            'WHERE' => '1',
            'ORDERBY' => 'cs.created_on'
        );

        return $database->exec_SELECTgetRows($queryArray['SELECT'], $queryArray['FROM'], $queryArray['WHERE'], '', $queryArray['ORDERBY'], '', $uidIndexField);
    }

    /**
     * Copies all projects with not merged changesets into a new array and return this
     *
     * @param array $gerritProjects
     * @param array $data
     * @return array
     */
    private function deleteProjectsWithoutChangesets($gerritProjects, $data) {
        $projects = array();
        foreach ($data as $changesetRow) {
            $projects[$changesetRow['projectId']] = $gerritProjects[$changesetRow['projectId']];
        }

        asort($projects);

        return $projects;
    }

    /**
     * Filter changeset data by project id
     *
     * @param array $data
     * @param integer $activeProject
     * @return array
     */
    private function deleteChangesetsFromOtherProjects($data, $activeProject) {
        $projectData = array();

        foreach ($data as $key => $changesetRow) {
            if ($changesetRow['projectId'] == $activeProject) {
                $projectData[$key] = $changesetRow;
            }
        }

        return $projectData;
    }
}