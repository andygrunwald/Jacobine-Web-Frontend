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
 * Class PHPLoc
 *
 * PHPLoc (loc, cloc, ...) of extracted releases
 *
 * @package Extension\Analysis\Analysis
 */
class PHPLoc extends Base {

    /**
     * Generates a analysis
     *
     * @return string
     */
    public function generate() {
        $this->setTemplateVariable(array(
            'container' => array(
                'id' => 'chart_phploc',
            )
        ));

        $this->setJavaScriptFiles(array('PHPLoc.js'));

        return true;
    }

    public function getData() {
        $dataSets = $this->execDataQuery();
        $dataSets = $this->prepareData($dataSets);

        return $dataSets;
    }

    private function execDataQuery() {
        $database = $this->getAnalyticDatabase();

        $select = '
            v.version,
            p.directories AS `Directories`,
            p.files AS `Files`,
            p.loc AS `Lines of Code (LOC)`,
            p.cloc AS `Comment Lines of Code (CLOC)`,
            p.ncloc AS `Non-Comment Lines of Code (NCLOC)`,
            p.ccn AS `Cyclomatic Complexity`,
            p.ccn_methods AS `Cyclomatic Complexity of methods`,
            p.interfaces AS `Interfaces`,
            p.traits AS `Traits`,
            p.classes AS `Classes`,
            p.abstract_classes AS `Abstract classes`,
            p.concrete_classes AS `Concrete classes`,
            p.anonymous_functions AS `Anonymous functions`,
            p.functions AS `Functions`,
            p.methods AS `Methods`,
            p.public_methods AS `Public methods`,
            p.non_public_methods AS `Non public methods`,
            p.non_static_methods AS `Non static methods`,
            p.static_methods AS `Static methods`,
            p.constants AS `Constants`,
            p.class_constants AS `Class constants`,
            p.global_constants AS `Global constants`,
            p.test_classes AS `Test classes`,
            p.test_methods AS `Test methods`,
            p.ccn_by_lloc AS `Cyclomatic Complexity / LLOC`,
            p.ccn_by_nom AS `Cyclomatic Complexity / Number of Methods`,
            p.lloc_by_noc AS `Average class length`,
            p.lloc_by_nom AS `Average method length`,
            p.lloc_by_nof AS `Average function length`,
            p.namespaces AS `Namespaces`,
            p.lloc AS `Logical Lines of Code (LLOC)`,
            p.lloc_classes AS `Logical Lines of Code (LLOC) in Classes`,
            p.lloc_functions AS `Logical Lines of Code (LLOC) in Functions`,
            p.lloc_global AS `Logical Lines of Code (LLOC) Not in classes or functions`,
            p.named_functions AS `Named functions`,
            p.method_calls AS `Method Calls`,
            p.static_method_calls AS `Method Calls (static methods)`,
            p.instance_method_calls AS `Method Calls (non static)`,
            p.attribute_accesses AS `Attribute Accesses`,
            p.static_attribute_accesses AS `Attribute Accesses (static)`,
            p.instance_attribute_accesses AS `Attribute Accesses (non static)`,
            p.global_accesses AS `Global Accesses`,
            p.global_variable_accesses AS `Global Accesses - Global Variables`,
            p.super_global_variable_accesses AS `Global Accesses - Super-Global Variables`,
            p.global_constant_accesses AS `Global Accesses - Global Constants`';

        $from = '
            phploc p INNER JOIN versions v ON (
                p.version = v.id
            )';
        $orderBy = 'v.branch ASC, v.`date` ASC';

        return $database->exec_SELECTgetRows($select, $from, '', '', $orderBy);
    }

    private function prepareData(array $dataSets) {
        // Collect versions (label for x) and statistics
        $categories = array();
        foreach ($dataSets as $row) {
            $categories[$row['version']] = true;
        }
        $categories = array_keys($categories);

        // Build series by array keys
        $series = array();
        $lastVersion = null;
        foreach ($dataSets as $row) {
            foreach ($row as $key => $value) {
                $value = ((strpos($value, '.')) ? (float) $value: (int) $value);
                $series[$key][] = $value;
            }
        }
        unset($series['version']);

        return array($categories, $series);
    }
}