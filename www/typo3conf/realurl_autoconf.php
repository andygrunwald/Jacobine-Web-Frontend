<?php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl'] = array (
    '_DEFAULT' => array (
        'init' => array (
            'enableCHashCache' => true,
            'appendMissingSlash' => 'ifNotFile,redirect',
            'adminJumpToBackend' => true,
            'enableUrlDecodeCache' => true,
            'enableUrlEncodeCache' => true,
            'emptyUrlReturnValue' => '/',
        ),
        'pagePath' => array (
            'type' => 'user',
            'userFunc' => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
            'spaceCharacter' => '-',
            'languageGetVar' => 'L',
            'rootpage_id' => '1',
        ),
        'fileName' => array (
            'defaultToHTMLsuffixOnPrev' => 0,
            'acceptHTMLsuffix' => 1,
            'index' => array (
                'print' => array (
                    'keyValues' => array (
                        'type' => 98,
                    ),
                ),
            ),
        ),
        'postVarSets' => array(
            '_DEFAULT' => array(
                'analysis' => array(
                    // @todo sync git / gerrit projects to typo3 database
                    // via scheduler to use git repo names in url
                    // with the usage of lookUpTable
                    // @todo rename this to "git-project"?
                    array(
                        'GETvar' => 'tx_analysis_chart[project]',
                    ),
                )
            ),
        ),
    ),
);