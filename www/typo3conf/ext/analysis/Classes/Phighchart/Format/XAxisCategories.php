<?php

namespace Extension\Analysis\Phighchart\Format;

use Phighchart\Chart;
use Phighchart\Options\Container;
use Phighchart\Format\FormatInterface;

/**
 * Linear chart plot format
 *
 * Slightly modified version of \Phighchart\Format\Linear.
 * This formatter checks if the xAxis got categories.
 * If yes they won`t be overwritten. If no, chose the last series like \Phighchart\Format\Linear.
 *
 * @author Andy Grunwald <andygrunwald@gmail.com>
 */
class XAxisCategories implements FormatInterface
{
    /**
     * Sets the xAxis category labels for the linear chart plots
     *
     * @param  Chart  $chart instance of the current Chart object
     * @return Mixed, Phighchart\Container if the series data is set, boolean false otherwise
     */
    public function getFormatOptions(Chart $chart)
    {
        // xAxis
        $data = $chart->getData();
        if ($data && $seriesData = $data->getSeries()) {
            $xAxis = $chart->getOptionsType('xAxis', new Container('xAxis'));

            if ($xAxis->getOption('categories', null) === null) {
                // create the X-Axis categories from the last seen series
                $xAxis->setCategories(array_keys(array_pop($seriesData)));
            }

            return $xAxis;
        }

        return false;
    }

    /**
     * Returns the plottable chart data
     * @param  Array $seriesData
     * @return Array
     */
    public function getFormattedChartData(Array $seriesData)
    {
        return array_values($seriesData);
    }
}
