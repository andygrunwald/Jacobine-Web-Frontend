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

namespace Extension\Analysis\DataManager;

class GitProjects extends Base {

    /**
     * Returns the data
     *
     * @return array
     */
    public function getData() {
        $rows = array();
        $select = 'id, uri';
        $from = 'repositories';
        $where = '';
        $orderBy = 'uri';

        $tmpRows = $this->getDatabase()->exec_SELECTgetRows($select, $from, $where, '', $orderBy, '', 'id');
        foreach ($tmpRows as $key => $row) {
            $uriParts = parse_url($row['uri']);
            // Every git uri starts with / ... strip it
            $path = substr($uriParts['path'], 1);

            $rows[$key] = $path;
        }

        return $rows;
    }
}