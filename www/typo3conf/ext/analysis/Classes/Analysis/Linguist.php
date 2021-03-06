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

class Linguist extends Base {

    /**
     * Generates a analysis
     *
     * @return string
     */
    public function generate() {
        $this->setTemplateVariable(array(
            'container' => array(
                'id' => 'chart_languages',
            )
        ));

        $this->setJavaScriptFiles(array('Linguist.js'));

        return true;
    }

    public function getData() {
        $dataSets = $this->execDataQuery();
        $dataSets = $this->prepareData($dataSets);

        return $dataSets;
    }

    private function execDataQuery() {
        $database = $this->getAnalyticDatabase();

        $select = 'v.version, l.language, l.percent';
        $from = '
            linguist l INNER JOIN versions v ON (
                l.version = v.id
            )';
        $where = 'l.percent > 0';
        $orderBy = 'v.branch ASC, v.`date` ASC';

        return $database->exec_SELECTgetRows($select, $from, $where, '', $orderBy);
    }

    private function prepareData(array $dataSets) {
        // Collect versions (label for x) and languages
        $languages = array();
        $categories = array();
        foreach ($dataSets as $row) {
            $languages[$row['language']] = true;
            $categories[$row['version']] = true;
        }
        $languages = array_keys($languages);
        $categories = array_keys($categories);

        // Build one data series per language
        $series = $lastVersionLanguageHash = array();
        $lastVersion = null;
        foreach ($dataSets as $row) {
            // The first version got no "lastVersion"
            if ($lastVersion === null) {
                $lastVersion = $row['version'];
            }

            // If one release got not all languages (see $languages array)
            // we have to fill this with 0 because every data series
            // must got the same number of entries
            if ($lastVersion !== $row['version']) {
                $series = $this->addEmptyValuesForMissingLanguages($series, $languages, $lastVersionLanguageHash);
                $lastVersionLanguageHash = array();
            }

            // Add the language percent to the data series
            $series[$row['language']][] = (float) $row['percent'];
            $lastVersionLanguageHash[$row['language']] = true;

            $lastVersion = $row['version'];
        }

        // Execute the empty fill up for the last version :)
        $series = $this->addEmptyValuesForMissingLanguages($series, $languages, $lastVersionLanguageHash);

        return array($categories, $series);
    }

    /**
     * Fill the series with an empty value, if the current release
     * got not all language entries.
     *
     * @param array $series
     * @param array $languages
     * @param array $languageHash
     * @return array
     */
    private function addEmptyValuesForMissingLanguages($series, $languages, $languageHash) {
        foreach($languages as $language) {
            if (isset($languageHash[$language]) === false) {
                $series[$language][] = 0;
            }
        }
        return $series;
    }
}