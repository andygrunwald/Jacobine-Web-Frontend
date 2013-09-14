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

namespace Extension\Analysis\Utility;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

class Database {

    /**
     * Gets the database connection for analysis database
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    public static function getAnalysisDatabaseConnection() {
        return self::createDatabaseConnection(ANALYSIS_DB_HOSTNAME, ANALYSIS_DB_USERNAME, ANALYSIS_DB_PASSWORD, ANALYSIS_DB_DATABASE);
    }

    /**
     * Initialize a database connection
     *
     * @param string $host Hostname of database server
     * @param string $username Username of database server
     * @param string $password Password of database server
     * @param string $database Database name
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    private static function createDatabaseConnection($host, $username, $password, $database) {
        $databaseObj = GeneralUtility::makeInstance('TYPO3\CMS\Core\Database\DatabaseConnection');
        /* @var $database \TYPO3\CMS\Core\Database\DatabaseConnection */

        $databaseObj->setDatabaseHost($host);
        $databaseObj->setDatabaseUsername($username);
        $databaseObj->setDatabasePassword($password);
        $databaseObj->setDatabaseName($database);
        $databaseObj->sql_pconnect();
        $databaseObj->sql_select_db();

        return $databaseObj;
    }
}