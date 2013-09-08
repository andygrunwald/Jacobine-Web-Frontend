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

use Phighchart\Chart;
use Phighchart\Options\Container;
use Phighchart\Data;
use Phighchart\Renderer\Line;

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
        $dataSets = $this->getData();
        $dataSets = $this->prepareData($dataSets);

        $titleOptions = new Container('title');
        $titleOptions->setText('tar.gz archives in (mega) byte');

        // xAxis
        $XAxisOptions = new Container('xAxis');
        $XAxisOptions->setTitle(array('text' => 'Releases', 'enabled' => true));
        $XAxisOptions->setLabels(array('rotation' => 45, 'y' => 20));
        $XAxisOptions->setMaxZoom(1);
        $XAxisOptions->setMin(20);
        $XAxisOptions->setMax(50);

        $scrollbarOptions = new Container('scrollbar');
        $scrollbarOptions->setEnabled(true);

        // yAxis
        $YAxisOptions = new Container('yAxis');
        $YAxisOptions->setTitle(array('text' => 'Size in megabyte', 'enabled' => true));

        $creditsOptions = new Container('credits');
        $creditsOptions->setEnabled(false);

        $options = new Container('chart');
        $options->setRenderTo('chart_filesize');
        $options->setZoomType('x');

        $legendOptions = new Container('legend');
        $legendOptions->setEnabled(false);

        $data = new Data();
        $data->addSeries('Filesize', $dataSets);

        $chart = new Chart();
        $chart->addOptions($options)
              ->addOptions($titleOptions)
              ->addOptions($legendOptions)
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