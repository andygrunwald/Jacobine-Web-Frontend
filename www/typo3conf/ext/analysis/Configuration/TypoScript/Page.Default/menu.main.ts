# MAIN MENU
analysis.menu.main = COA
analysis.menu.main {
    10 = HMENU
    10.entryLevel = 0

    10.1 = TMENU
    10.1.wrap = <ul class="nav">|</ul>
    10.1.NO.wrapItemAndSub = <li>|</li>

    10.1.ACT = 1
    10.1.ACT < .10.1.NO
    10.1.ACT.wrapItemAndSub = <li class="active">|</li>
}