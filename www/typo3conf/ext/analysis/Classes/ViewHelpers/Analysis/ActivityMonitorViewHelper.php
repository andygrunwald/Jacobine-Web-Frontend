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

namespace Extension\Analysis\ViewHelpers\Analysis;

/**
 * Class ActivityMonitorViewHelper
 *
 * This is a special viewhelper to display the Gerrit\ActivityMonitor analysis.
 * This viewhelper is not really reusable, because it fits a special need.
 *
 * The rendering of the activity viewhelper was not done in a fluid template,
 * because the fluid template will be to complex.
 *
 * For more information about this "Activity Monitor", please have a look at
 * Extension\Analysis\Analysis\Gerrit\ActivityMonitor
 *
 * @package Extension\Analysis\ViewHelpers
 */
class ActivityMonitorViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * @param array $points
     * @param array $persons
     * @param int $columns
     * @param string $tableClasses
     * @return string
     */
    public function render(array $points, array $persons, $columns = 5, $tableClasses = 'table table-striped') {
        $content = '';

        // Determine how many table rows will be exists
        $monthCount = count($points);
        if ($monthCount < $columns) {
            $tables = 1;
        } else {
            $tables = ceil($monthCount / $columns);
        }

        // Render tables
        for ($i = 0; $i < $tables; $i++) {
            $dataToProceed = array_slice($points, ($i * $columns), $columns);

            $content .= '<table ' . $this->buildCssClassPart($tableClasses) . '>';
            $content .= $this->renderTableHeader($dataToProceed);
            $content .= $this->renderTableBody($dataToProceed, $persons);
            $content .= '</table>';
        }

        return $content;
    }

    /**
     * Renders the table header.
     * Content of a table header is YYYY-MM (e.g. 2013-07)
     *
     * @param array $points
     * @return string
     */
    private function renderTableHeader(array $points) {
        $content = '<tr>';
        $format = '<th>%s (<span title="No. of contributers">%d</span> / <span title="Total score">%d</span>)</th>';

        foreach ($points as $yearAndMonth => $pointsPerMonth) {
            $tableHeader = htmlentities($yearAndMonth);
            $numOfContributers = count($pointsPerMonth);
            $totalScore = array_sum($pointsPerMonth);

            $content .= sprintf($format, $tableHeader, $numOfContributers, $totalScore);
        }
        $content .= '<tr>';

        return $content;
    }

    /**
     * Renders the table body.
     * Content of a table body is the name of the contributer + points of activity.
     * E.g. Andy Grunwald (55)
     *
     * @param array $points
     * @param array $persons
     * @return string
     */
    private function renderTableBody(array $points, array $persons) {
        $content = '';
        $columnNum = count($points);
        $highestContributerNum = $this->getMaxNumberOfContributer($points);

        $points = array_values($points);

        for ($i = 0; $i < $highestContributerNum; $i++) {
            $content .= '<tr>';

            for ($j = 0; $j < $columnNum; $j++) {
                $pointRow = array_slice($points[$j], $i, 1, true);

                $person = $pointsOfPerson = $backgroundColor = '';
                if (count($pointRow) === 1) {
                    $person = htmlentities($persons[key($pointRow)], ENT_QUOTES, 'UTF-8');
                    $pointsOfPerson = '(' . intval(current($pointRow)) . ')';
                    $backgroundColor = 'style="background-color: ' . $this->cellBackgroundColor($person) . '"';
                }
                $content .= '<td ' . $backgroundColor . '>' . $person . ' ' . $pointsOfPerson . '</td>';
            }

            $content .= '</tr>';
        }

        return $content;
    }

    /**
     * Builds the css class part if a css class is set
     *
     * @param string $class
     * @return string
     */
    private function buildCssClassPart($class) {
        $content = '';

        if ($class) {
            $content = 'class=" ' . htmlentities($class) . '"';
        }

        return $content;
    }

    /**
     * Determines the max. of contributers per month.
     * You get an array like
     *
     * $points = array(
     *      '2013-06' => array(12 => 5, 17 = 8, 19 => 100),
     *      '2013-05' => array(12 => 999),
     *      '2013-04' => array(17 => 9, 20 = 15, 87 => 98, 65 => 86, 34 => 53)
     * )
     * The result of this array will be 5, because of key '2013-04'
     *
     * @param array $points
     * @return int
     */
    private function getMaxNumberOfContributer(array $points) {
        $num = 0;
        foreach ($points as $pointsOfMonth) {
            $cnt = count($pointsOfMonth);
            $num = ($cnt > $num) ? $cnt: $num;
        }

        return $num;
    }

    /**
     * Calculates a color in base of a string.
     * This is very useful to display the same name with the same color every time
     *
     * @param string $name
     * @return string
     */
    private function cellBackgroundColor($name) {
        $color = substr(md5($name), 6, 6);
        $color = sprintf('rgba(%s, %s, %s, 0.5)', hexdec(substr($color, 0, 2)), hexdec(substr($color, 2, 2)), hexdec(substr($color, 4, 2)));

        return $color;
    }
}