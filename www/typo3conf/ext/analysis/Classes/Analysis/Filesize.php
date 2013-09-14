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

namespace Extension\Analysis\Analysis;

/**
 * Class Filesize
 *
 * Filesize of tar.gz file per release
 *
 * @package Extension\Analysis\Analysis
 */
class Filesize extends Base {

    /**
     * Generates a analysis
     *
     * @return string
     */
    public function generate() {
        $this->setTemplateVariable(array(
            'container' => array(
                'id' => 'chart_filesize',
            )
        ));

        $this->setJavaScriptFiles(array('Filesize.js'));

        return true;
    }

    public function getData() {
        $dataSets = $this->execDataQuery();
        $dataSets = $this->prepareData($dataSets);

        return $dataSets;
    }

    private function execDataQuery() {
        $database = $this->getAnalyticDatabase();

        $select = 'version, size_tar';
        $from = 'versions';
        $where = 'size_tar > 0';
        $orderBy = 'branch ASC, `date` ASC';
        $indexField = 'version';

        return $database->exec_SELECTgetRows($select, $from, $where, '', $orderBy, '', $indexField);
    }

    private function prepareData(array $dataSets) {
        $preparedData = array();
        foreach ($dataSets as $key => $value) {
            // Cast it to int, because mysql returns a string
            $preparedData[$key] = (int) $value['size_tar'];
        }

        return $preparedData;
    }
}