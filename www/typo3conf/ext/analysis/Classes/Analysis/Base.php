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

abstract class Base {

    /**
     * Configuration
     *
     * @var array
     */
    protected $configuration = array();

    /**
     * Database connection to analytic database
     *
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected $analyticDatabase = null;

    /**
     * Content for output
     *
     * @var string
     */
    protected $content = '';

    /**
     * JavaScript
     *
     * @var string
     */
    protected $javaScript = '';

    /**
     * JavaScript files to include
     *
     * @var array
     */
    protected $javaScriptFiles = array();

    /**
     * Template name
     *
     * @var null|string
     */
    protected $template = null;

    /**
     * Storage for template variables
     *
     * @var array
     */
    protected $templateVariable = array();

    /**
     * Sets the javascript files
     *
     * @param array $javaScriptFiles
     * @return void
     */
    public function setJavaScriptFiles($javaScriptFiles) {
        $this->javaScriptFiles = $javaScriptFiles;
    }

    /**
     * Sets the template variables
     *
     * @param array $templateVariable
     * @return void
     */
    public function setTemplateVariable($templateVariable)
    {
        $this->templateVariable = $templateVariable;
    }

    /**
     * Gets the template variables
     *
     * @return array
     */
    public function getTemplateVariable() {
        return $this->templateVariable;
    }

    /**
     * Gets the javascript files
     *
     * @return array
     */
    public function getJavaScriptFiles()
    {
        return $this->javaScriptFiles;
    }

    /**
     * Sets the JavaScript
     *
     * @param string $javaScript
     * @return void
     */
    public function setJavaScript($javaScript) {
        $this->javaScript = $javaScript;
    }

    /**
     * Gets the JavaScript
     *
     * @return string
     */
    public function getJavaScript() {
        return $this->javaScript;
    }

    /**
     * Sets the template name
     *
     * @param null|string
     */
    public function setTemplate($template) {
        $this->template = $template;
    }

    /**
     * Gets the template name
     *
     * @return null|string
     */
    public function getTemplate() {
        return $this->template;
    }

    /**
     * Sets the content
     *
     * @param string $content
     * @return void
     */
    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * Gets the generated content
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Sets the database connection
     *
     * @param \TYPO3\CMS\Core\Database\DatabaseConnection $database
     * @return void
     */
    public function setAnalyticDatabase(\TYPO3\CMS\Core\Database\DatabaseConnection $database) {
        $this->analyticDatabase = $database;
    }

    /**
     * Gets the analytic database connection
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    public function getAnalyticDatabase() {
        return $this->analyticDatabase;
    }

    /**
     * Sets the configuration
     *
     * @param array $configuration
     * @return void
     */
    public function setConfiguration(array $configuration) {
        $this->configuration = $configuration;
    }

    /**
     * Gets the configuration
     *
     * @return array
     */
    public function getConfiguration() {
        return $this->configuration;
    }

    /**
     * Returns data needed by analysis
     *
     * @return array
     */
    public abstract function getData();

    /**
     * Generates a analysis` source code
     *
     * @return string
     */
    public abstract function generate();
}