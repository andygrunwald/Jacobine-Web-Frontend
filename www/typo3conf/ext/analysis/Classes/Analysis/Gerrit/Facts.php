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
 * Class Facts
 *
 * Facts / Numbers of Gerrit
 *
 * @package Extension\Analysis\Analysis\Gerrit
 */
class Facts extends \Extension\Analysis\Analysis\Base {

    public function __construct() {
        $this->setTemplate('Gerrit' . DIRECTORY_SEPARATOR . 'Facts');
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
        $gerritProjectManager = GeneralUtility::makeInstance('Extension\\Analysis\\DataManager\\GerritProjects', $this->getAnalyticDatabase());
        /* @var $gerritProjectManager \Extension\Analysis\DataManager\Base */
        $gerritProjects = $gerritProjectManager->getData();
        array_unshift($gerritProjects, 'All Gerrit projects');

        // TODO add a "last updated" fact
        // TODO add more facts about first / last comment, etc.
        $this->setTemplateVariable(array(
            'analysis' => $this->getData(),
            'project' => array (
                'values' => $gerritProjects,
                'active' => $activeProject
            )
        ));

        return true;
    }

    public function getData() {
        $configuration = $this->getConfiguration();
        $dataSets = $this->execDataQuery($configuration);
        $dataSets = $this->prepareData($dataSets);

        return $dataSets;
    }

    private function execDataQuery($configuration) {
        $database = $this->getAnalyticDatabase();

        // TODO Make this query project independent
        $query = '
            SELECT
                host AS cnt,
                "serverhost" AS type
            FROM gerrie_server
            WHERE
                id = 1
            UNION
            SELECT
                name AS cnt,
                "servername" AS type
            FROM gerrie_server
            WHERE
                id = 1
            UNION
            ' . $this->getProjectQuery($configuration['project']) . '
            UNION
            ' . $this->getPersonsQuery($configuration['project']) . '
            UNION
            ' . $this->getChangesetQuery($configuration['project']) . '
            UNION
            ' . $this->getPatchsetQuery($configuration['project']) . '
            UNION
            ' . $this->getApprovalQuery($configuration['project']) . '
            UNION
            ' . $this->getCommentQuery($configuration['project']);

        return $database->admin_query($query);
    }

    private function prepareData($res) {
        $output = array();
        $database = $this->getAnalyticDatabase();

        while ($tempRow = $database->sql_fetch_assoc($res)) {
            $output[$tempRow['type']] = $tempRow['cnt'];
        }

        return $output;
    }

    /**
     * Returns SQL query for persons count
     *
     * @param integer $project
     * @return string
     */
    private function getPersonsQuery($project) {
        $query = '
          SELECT
            COUNT(pe.id) AS cnt,
            "person" AS type
          FROM
            gerrie_person AS pe';

        // @todo missing tables: submit_record_labels and file_comments
        if ($project > 0) {
            $query = '
                SELECT
                    COUNT(*) AS cnt,
                    "person" AS type
                FROM
                    (
                        SELECT *
                        FROM (
                            SELECT owner AS person
                            FROM gerrie_changeset AS cs
                            WHERE project = ' . intval($project) . '
                            GROUP BY person
                            UNION ALL
                            SELECT reviewer AS person
                            FROM gerrie_comment AS co
                            INNER JOIN gerrie_changeset AS cs ON (
                                co.changeset = cs.id
                                AND cs.project = ' . intval($project) . '
                            )
                            GROUP BY person
                            UNION ALL
                            SELECT uploader AS person
                            FROM gerrie_patchset AS ps
                            INNER JOIN gerrie_changeset AS cs ON (
                                ps.changeset = cs.id
                                AND cs.project = ' . intval($project) . '
                            )
                            GROUP BY person
                            UNION ALL
                            SELECT author AS person
                            FROM gerrie_patchset AS ps
                            INNER JOIN gerrie_changeset AS cs ON (
                                ps.changeset = cs.id
                                AND cs.project = ' . intval($project) . '
                            )
                            GROUP BY person
                            UNION ALL
                            SELECT `by` AS person
                            FROM gerrie_approval AS ap
                            INNER JOIN gerrie_patchset AS ps ON (
                                ap.patchset = ps.id
                            )
                            INNER JOIN gerrie_changeset AS cs ON (
                                ps.changeset = cs.id
                                AND cs.project = ' . intval($project) . '
                            )
                            GROUP BY person
                        ) AS people
                        GROUP BY person
                    ) AS grouped_people';
        }

        return $query;
    }

    /**
     * Returns SQL query for comment count
     *
     * @param integer $project
     * @return string
     */
    private function getCommentQuery($project) {
        $join = '';
        if ($project > 0) {
            $join = '
                INNER JOIN gerrie_changeset AS cs ON (
                    co.changeset = cs.id
                    AND cs.project = ' . intval($project) . '
                )';
        }

        $query = '
          SELECT
            COUNT(co.id) AS cnt,
            "comment" AS type
          FROM
            gerrie_comment AS co
          ' . $join;

        return $query;
    }

    /**
     * Returns SQL query for approval count
     *
     * @param integer $project
     * @return string
     */
    private function getApprovalQuery($project) {
        $join = '';
        if ($project > 0) {
            $join = '
                INNER JOIN gerrie_patchset AS ps ON (
                    ap.patchset = ps.id
                )
                INNER JOIN gerrie_changeset AS cs ON (
                    ps.changeset = cs.id
                    AND cs.project = ' . intval($project) . '
                )';
        }

        $query = '
          SELECT
            COUNT(ap.id) AS cnt,
            "approval" AS type
          FROM
            gerrie_approval AS ap
          ' . $join;

        return $query;
    }

    /**
     * Returns SQL query for patchset count
     *
     * @param integer $project
     * @return string
     */
    private function getPatchsetQuery($project) {
        $join = '';
        if ($project > 0) {
            $join = '
                INNER JOIN gerrie_changeset AS cs ON (
                    ps.changeset = cs.id
                    AND cs.project = ' . intval($project) . '
                )';
        }

        $query = '
          SELECT
            COUNT(ps.id) AS cnt,
            "patchset" AS type
          FROM
            gerrie_patchset AS ps
          ' . $join;

        return $query;
    }

    /**
     * Returns SQL query for project count
     *
     * @param integer $project
     * @return string
     */
    private function getProjectQuery($project) {
        $where = '';
        if ($project > 0) {
            $where = 'WHERE p.id = ' . intval($project);
        }

        $query = '
          SELECT
            COUNT(p.id) AS cnt,
            "project" AS type
          FROM
            gerrie_project AS p
          ' . $where;

        return $query;
    }

    /**
     * Returns SQL query for changeset count
     *
     * @param integer $project
     * @return string
     */
    private function getChangesetQuery($project) {
        $where = '';
        if ($project > 0) {
            $where = 'WHERE cs.project = ' . intval($project);
        }

        $query = '
            SELECT
              COUNT(cs.id) AS cnt,
              "changeset" AS type
            FROM
              gerrie_changeset as cs
            ' . $where;

        return $query;
    }
}