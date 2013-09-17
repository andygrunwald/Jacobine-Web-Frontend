<?php

use \TYPO3\CMS\Frontend\Utility\EidUtility;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Utility\HttpUtility;
use \TYPO3\CMS\Core\Http\AjaxRequestHandler;

$className = GeneralUtility::_GET('className');
$chartDataObj = GeneralUtility::makeInstance('Extension\\Analysis\\EId\\ChartData');
/* @var $chartDataObj \Extension\Analysis\EId\ChartData */

try {
    $chartDataObj->setClassName($className);
} catch (\Exception $e) {
    HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_400);
}

EidUtility::connectDB();

$getKeys = ['project'];
$configuration = array(
    'project' => 0
);
foreach ($getKeys as $getPart) {
    $value = intval(GeneralUtility::_GET($getPart));
    if ($value) {
        $configuration[$getPart] = intval($value);
    }
}

$content = $chartDataObj->getData($configuration);

// @todo Add support for CONTENT_TYPE header like
// \\TYPO3\\CMS\\Core\\Http\\AjaxRequestHandler
// but only for FE / eID. AjaxRequestHandler needs a backend context
// This would be awesome to get a generic handler of content in different formats :)
// until this was developed, we exit this as json

$chartDataObj->outputAsJson($content);

HttpUtility::setResponseCodeAndExit(HttpUtility::HTTP_STATUS_200);