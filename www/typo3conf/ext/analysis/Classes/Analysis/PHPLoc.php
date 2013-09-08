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

use Extension\Analysis\Phighchart\Format\XAxisCategories;
use Phighchart\Chart;
use Phighchart\Options\Container;
use Phighchart\Data;
use Phighchart\Renderer\Line;

class PHPLoc extends Base {

    /**
     * Generates a analysis
     *
     * @return string
     */
    public function generate() {
        $dataSets = $this->getData();
        list($categories, $dataSets) = $this->prepareData($dataSets);

        $titleOptions = new Container('title');
        $titleOptions->setText('Programming languages per release');

        // xAxis
        $XAxisOptions = new Container('xAxis');
        $XAxisOptions->setTitle(array('text' => 'Releases', 'enabled' => true));
        $XAxisOptions->setLabels(array('rotation' => 45, 'y' => 20));
        $XAxisOptions->setCategories($categories);
        $XAxisOptions->setMaxZoom(1);
        $XAxisOptions->setMin(20);
        $XAxisOptions->setMax(50);

        $scrollbarOptions = new Container('scrollbar');
        $scrollbarOptions->setEnabled(true);

        // yAxis
        $YAxisOptions = new Container('yAxis');
        $YAxisOptions->setTitle(array('text' => 'Percent', 'enabled' => true));

        $options = new Container('chart');
        $options->setRenderTo('chart_phploc');
        $options->setZoomType('x');

        $creditsOptions = new Container('credits');
        $creditsOptions->setEnabled(false);

        $data = new Data();
        foreach ($dataSets as $statistic => $series) {
            $data->addSeries($statistic, $series);
        }

        $chart = new Chart();
        $chart->setFormat(new XAxisCategories())
              ->addOptions($options)
              ->addOptions($titleOptions)
              ->addOptions($YAxisOptions)
              ->addOptions($XAxisOptions)
              ->addOptions($creditsOptions)
              ->addOptions($scrollbarOptions)
              ->setData($data)
              ->setRenderer(new Line());

        $this->setContent($chart->renderContainer());
        $this->setJavaScript($chart->render());

        return true;
    }

    private function getData() {
        $database = $this->getAnalyticDatabase();

        $select = '
            v.version,
            p.directories,
            p.files';
        /*
        $select = '
            v.version,
            p.directories,
            p.files,
            p.loc,
            p.cloc,
            p.ncloc,
            p.ccn,
            p.ccn_methods,
            p.interfaces,
            p.traits,
            p.classes,
            p.abstract_classes,
            p.concrete_classes,
            p.anonymous_functions,
            p.functions,
            p.methods,
            p.public_methods,
            p.non_public_methods,
            p.non_static_methods,
            p.static_methods,
            p.constants,
            p.class_constants,
            p.global_constants,
            p.test_classes,
            p.test_methods,
            p.ccn_by_loc,
            p.ccn_by_nom,
            p.namespaces';
        */
        /*
         * p.ccn_by_lloc,
            p.lloc,
            p.lloc_classes,
            p.lloc_functions,
            p.lloc_global,
            p.named_functions,
            p.lloc_by_noc,
            p.lloc_by_nom,
            p.lloc_by_nof,
            p.method_calls,
            p.static_method_calls,
            p.instance_method_calls,
            p.attribute_accesses,
            p.static_attribute_accesses,
            p.instance_attribute_accesses,
            p.global_accesses,
            p.global_variable_accesses,
            p.super_global_variable_accesses,
            p.global_constant_accesses';
        */
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