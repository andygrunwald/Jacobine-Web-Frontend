# SUBMENU
analysis.menu.sub = COA
analysis.menu.sub {
    10 = HMENU
    10.if.isTrue.numRows {
        table = pages
        where = pid=this
    }
    10.wrap = <ul class="nav nav-list">|</ul>
    10.entryLevel = 1

    #Subnav Title: Parentpage
    10.1 = TMENU
    10.1.expAll = 1
    10.1.NO.doNotLinkIt = 1
    10.1.NO.wrapItemAndSub = <li class="nav-header">|</li>

    10.1.CUR = 1
    10.1.CUR < .10.1.NO
    10.1.CUR.wrapItemAndSub = <li class="active">|</li>

    10.2 < .10.1
    10.2.NO.doNotLinkIt = 0
    10.2.NO.wrapItemAndSub = <li>|</li>
    10.2.CUR.doNotLinkIt = 0

    10.3 < .10.2

    10.stdWrap.wrap = <div class="span3"><div class="well sidebar-nav">|</div></div>
}