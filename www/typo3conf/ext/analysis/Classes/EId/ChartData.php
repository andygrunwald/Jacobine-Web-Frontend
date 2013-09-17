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

namespace Extension\Analysis\EId;

use Extension\Analysis\Utility\Analysis;
use Extension\Analysis\Utility\Database;
use Extension\Analysis\Utility\Naming;

class ChartData {

    const ANALYSIS_BASENAMESPACE = 'Extension\\Analysis\\Analysis\\';

    /**
     * Class name of analysis / chart class name
     *
     * @var null|string
     */
    protected $className = null;

    /**
     * @var null|\Extension\Analysis\Analysis\Base
     */
    protected $analysisObject = null;

    /**
     * Sets the class name of the analysis / chart class
     *
     * @param string $className
     * @throws \InvalidArgumentException
     * @return void
     */
    public function setClassName($className) {
        if ($this->checkIfClassExists($className) === false) {
            throw new \InvalidArgumentException('Class not exists', 1379092989);
        }

        $this->className = $className;
    }

    /**
     * Returns the className
     *
     * @return null|string
     */
    public function getClassName() {
        return $this->className;
    }

    public function getData($configuration = array()) {
        $analysisObject = $this->getAnalysisObject();
        $analysisObject->setConfiguration($configuration);

        $data = $analysisObject->getData();

        return $data;
    }

    /**
     * Output given content as json
     *
     * @param mixed $content
     * @return void
     */
    public function outputAsJson($content) {
        $content = json_encode($content);

        header('Content-type: application/json; charset=utf-8');
        header('X-JSON: ' . true);

        echo $content;
    }

    /**
     * Returns the analysis object
     *
     * @return \Extension\Analysis\Analysis\Base|null
     */
    protected function getAnalysisObject() {
        if ($this->analysisObject === null) {
            $this->analysisObject = $this->initializeAnalysisObject();
        }

        return $this->analysisObject;
    }

    /**
     * Initialization of the analysis object
     *
     * @return \Extension\Analysis\Analysis\Base
     */
    protected function initializeAnalysisObject() {
        $className = Naming::analysisClassName($this->getClassName());

        $analysisDatabase = Database::getAnalysisDatabaseConnection();
        $analysisObj = Analysis::initAnalysisObject($className, $analysisDatabase);

        return $analysisObj;
    }

    /**
     * Checks if the given class exists in namespace self::ANALYSIS_BASENAMESPACE
     * Returns true if the class exists, false otherwise
     *
     * @param string $className
     * @return bool
     */
    protected function checkIfClassExists($className) {
        $result = false;

        if (class_exists(self::ANALYSIS_BASENAMESPACE . $className)) {
            $result = true;
        }

        return $result;
    }
}