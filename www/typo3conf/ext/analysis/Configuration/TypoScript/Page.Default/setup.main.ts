#########################
# Config & Page Setup
#########################
# Config
#########################

config {
    doctype (
        <!DOCTYPE html
        PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    )
    htmlTag_setParams =
    renderCharset = utf-8
    metaCharset = utf-8
    xmlprologue = none
    xhtml_cleaning = all
    disablePrefixComment = 1
    inlineStyle2TempFile = 1
    removeDefaultJS = 0
    renderCharset = utf-8
    sys_language_uid = 0
    # REAL URL 1 = Enable, 0 = Disable
    tx_realurl_enable = 0
    language = de
    locale_all = de_DE@euro
    linkVars = L(int)
    simulateStaticDocuments = 0
    headerComment (
        -->
            <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <!--
    )
    spamProtectEmailAddresses = 13
    spamProtectEmailAddresses_atSubst = (at)
    # Base URL
    baseURL = http://analysis.local
}

#########################
# Page Setup
#########################

page = PAGE
page {
    typeNum = 0

    # Favicon
    shortcutIcon = {$path.full.img}favicon.ico

    stylesheet =  {$path.full.css}bootstrap.css

    # additional CSS includes
    includeCSS {
        analysis =  {$path.full.css}analysis.css
        responsive =  {$path.full.css}bootstrap-responsive.css
    }

    # JS stuff...
    includeJSlibs {
        jQueryMin = {$path.full.js}Libs/jquery-1.10.2.min.js
        Highstock = {$path.full.js}Libs/Highstock/highstock.js
        HighstockMore = {$path.full.js}Libs/Highstock/highcharts-more.js
        HighstockModuleExport = {$path.full.js}Libs/Highstock/modules/exporting.js
    }

    includeJSFooterlibs {
        jQueryApp = {$path.full.js}app.js
    }

    10 = FLUIDTEMPLATE
    10 {
        file = {$path.full.html}Page.html
        partialRootPath = {$path.full.html}Partials/
        layoutRootPath = {$path.full.html}Layouts/
        variables {
            layout = TEXT
            layout.data = page:backend_layout
        }
    }
}