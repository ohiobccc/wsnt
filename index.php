<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>WS Noter v3.00</title>
    <!-- JS -->
    <script type="text/javascript" src="lib/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="lib/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="lib/zeroclipboard/ZeroClipboard.js"></script>
    <script type="text/javascript" src="lib/noter.js"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="lib/jquery-ui/jquery-ui.min.css" />
    <link rel="stylesheet" href="css/slate/style.css" />
</head>

<body>
    <header>
        <nav>
            <ul>
                <li>
                    <img src="img/icon_hamburger.png" alt="menu" title="Main Menu" width="22" height="22" />
                    <div>
                        <div class="navMenuContainer">
                            <div>Core Tools
                                <ul>
                                    <li><a class="navMenuLink" href="http://www.google.com" target="_tab">BCCC DataCannon</a></li>
                                    <li><a class="navMenuLink" href="http://www.google.com" target="_tab">POTS DataCannon</a></li>
                                    <li><a class="navMenuLink" href="http://www.google.com" target="_tab">Remedy</a></li>
                                    <li><a class="navMenuLink" href="http://www.google.com" target="_tab">ISPP</a></li>
                                    <li><a class="navMenuLink" href="http://www.google.com" target="_tab">CSC</a></li>
                                    <li><a class="navMenuLink" href="http://www.google.com" target="_tab">HDM</a></li>
                                    <li><a class="navMenuLink" href="http://www.google.com" target="_tab">MSS</a></li>
                                </ul>
                            </div>
                            <div>Header 2
                                <ul>
                                    <li>Item 1</li>
                                    <li>Item 2</li>
                                    <li>Item 3</li>
                                </ul>
                            </div>

                        </div>
                    </div>
                </li>
                <li onclick="modal.show('modalLogins')">&nbsp;P&nbsp;</li>
                <li>Equip
                    <div>Menu 2 Items</div>
                </li>

                <li>Contacts
                    <div>Menu 3 Items</div>
                </li>
            </ul>
            <div class="formButtons">
                <input type="button" class="stdButton" id="copyNotes" value="Copy" title="Double-Click to copy notes to clipboard." data-clipboard-target="hiddenClippy" />
                <input type="button" class="stdButton" id="clearNotes" value="Clear" title="Clear Notes" onclick="noteMgr.clearNotes();" />
            </div>
            <img style="float: right; margin-right: 0.5em;" src="img/icon_popout.png" width="22" height="22" title="Pop Out" alt="Pop Out" onclick="popMeOut('./index.php', 400, 300, 'no', 'no', 'no', 'wsnoter3');" />
        </nav>
    </header>
    <div class="spacerTiny">&nbsp;</div>
    <section id="acctInfo">
        <input type="text" class="stdInput char10" id="aiStn" placeholder="BTN/STN" oninput="noteMgr.removeWhitespace('aiStn')" />
        <input type="text" class="stdInput char10" id="aiStn2" placeholder="BTN/STN" oninput="noteMgr.removeWhitespace('aiStn2')" />
        <input type="text" class="stdInput char8" id="aiEid" placeholder="e/nID" oninput="noteMgr.removeWhitespace('aiEid')" />
    </section>
    <section id="call">
        <input type="text" class="stdInput char12" id="aiWanIp" placeholder="WAN IP" oninput="noteMgr.removeWhitespace('aiWanIp')" />
        <input type="text" class="stdInput char15" id="aiMac" placeholder="MAC" oninput="noteMgr.removeWhitespace('aiMac')" />
        <input type="button" class="stdButton" id="openRAWindow" value="O" title="Open Modem RA Window" onclick="genModemRemoteLoginData()" />
        <input type="button" class="stdButton" id="copyPass" value="P" title="Copy RA Passwd" data-clipboard-target="hiddenPasswd" />
        <input type="button" class="stdButton" id="measureTraffic" value="M" title="Measure Traffic" onclick="openTrafficWindow()" />
    </section>
    <section id="user">
        <input type="text" class="stdInput char25" id="aiUsr" placeholder="User ID" oninput="noteMgr.removeWhitespace('aiUsr')" />
        <input type="text" class="stdInput char12" id="aiPasswd" placeholder="Password" oninput="noteMgr.removeWhitespace('aiPasswd')" />
    </section>
    <section id="user2">
        <input type="text" class="stdInput char25" id="aiUsr2" placeholder="User ID" oninput="noteMgr.removeWhitespace('aiUsr2')" />
        <input type="text" class="stdInput char12" id="aiPasswd2" placeholder="Password" oninput="noteMgr.removeWhitespace('aiPasswd2')" />
    </section>
    <section id="user3">
        <input type="text" class="stdInput char12" id="aiCbr" placeholder="CBR" />
        <input type="text" class="stdInput char25" id="aiTicket" placeholder="Ticket" />
    </section>
    <section id="cust">
        <input type="text" class="stdInput char12" id="aiCCI" placeholder="CCI" />
        <input type="text" class="stdInput char25" id="aiIssue" placeholder="Issue" />
    </section>
    <section id="conn" class="dimText">
        tr:
        <div id="hasTrainGroup" class="inline" title="Adds Note: Train:(Y|N)">
            <input type="radio" id="hasTrainYes" name="hasTrain" value="yes" />
            <label for="hasTrainYes">Y</label>
            <input type="radio" id="hasTrainNo" name="hasTrain" value="no" />
            <label for="hasTrainNo">N</label>
        </div>
        ip:
        <div id="hasIPGroup" class="inline" title="Adds Note: WANIP:(Y|N)">
            <input type="radio" id="hasIPYes" name="hasIP" value="yes" />
            <label for="hasIPYes">Y</label>
            <input type="radio" id="hasIPNo" name="hasIP" value="no" />
            <label for="hasIPNo">N</label>
        </div>
        ispp:
        <div id="hasISPPGroup" class="inline" title="Adds Note: ISPP:(OK|INVALID)">
            <input type="radio" id="hasISPPYes" name="hasISPP" value="ok" />
            <label for="hasISPPYes">ok</label>
            <input type="radio" id="hasISPPNo" name="hasISPP" value="invalid" />
            <label for="hasISPPNo">bad</label>
        </div>
        mir:
        <div id="hasMIRORGroup" class="inline" title="Adds Note: MIROR:(OK|INVALID)">
            <input type="radio" id="hasMIRORYes" name="hasMIROR" value="ok" />
            <label for="hasMIRORYes">ok</label>
            <input type="radio" id="hasMIRORNo" name="hasMIROR" value="invalid" />
            <label for="hasMIRORNo">bad</label>
        </div>
    </section>
    <section id="conn2" class="dimText">
        mss:
        <div id="hasMSSGroup" class="inline" title="Adds Note: MSS:(OK|INVALID)">
            <input type="radio" id="hasMSSYes" name="hasMSS" value="ok" />
            <label for="hasMSSYes">ok</label>
            <input type="radio" id="hasMSSNo" name="hasMSS" value="invalid" />
            <label for="hasMSSNo">bad</label>
        </div>
        cams:
        <div id="hasCAMSGroup" class="inline" title="Adds Note: CAMS:(OK|INVALID)">
            <input type="radio" id="hasCAMSYes" name="hasCAMS" value="ok" />
            <label for="hasCAMSYes">ok</label>
            <input type="radio" id="hasCAMSNo" name="hasCAMS" value="invalid" />
            <label for="hasCAMSNo">bad</label>
        </div>
        p:
        <div id="hasPingGroup" class="inline" title="Adds Note: Ping:(OK|PKT LOSS)">
            <input type="radio" id="hasPingYes" name="hasPing" value="ok" />
            <label for="hasPingYes">ok</label>
            <input type="radio" id="hasPingNo" name="hasPing" value="pkt loss" />
            <label for="hasPingNo">bad</label>
        </div>
        s:
        <div id="hasSigGroup" class="inline" title="Adds Note: Signal:(Good|Bad)">
            <input type="radio" id="hasSigYes" name="hasSig" value="Good" />
            <label for="hasSigYes">ok</label>
            <input type="radio" id="hasSigNo" name="hasSig" value="Bad" />
            <label for="hasSigNo">bad</label>
        </div>
    </section>
    <section id="noteGroup">
        <textarea id="aiNotes" class="stdInput" rows="2" cols="28" placeholder="Notes"></textarea>
    </section>

    <div id="hiddenClippy" class="hideMe"></div>
    <div id="hiddenPasswd" class="hideMe"></div>
    <div id="modalDimmer" class="hideMe"></div>
    
    

    <!-- MODAL WINDOWS -->

    <div id="modalLogins" class="hideMe">
        <div class="modalCloser">
            <div class="modalCloser" onclick="modal.hide('modalLogins');"><img src="./img/icon_close.png" /></div>
        </div>
        <div class="navMenuContainer">
            <div class="smallerText">
                System
                <ul>
                    <li>HDM</li>
                    <li>CSC</li>
                    <li>QHost</li>
                </ul>
            </div>
            <div>Logins
                <ul>
                    <li>U
                        <input type="text" id="gpUsrHDM" class="stdInput char6" value="tier2_team" />
                        <input type="button" class="stdButton" value="C" id="gpUsrHDMButton" data-clipboard-target="gpUsrHDM" /> P
                        <input type="text" id="gpPassHDM" class="stdInput char6" value="WggtTg2b" />
                        <input type="button" class="stdButton" id="gpPassHDMButton" value="C" data-clipboard-target="gpPassHDM" />
                    </li>
                    <li>U
                        <input type="text" id="gpUsrCSC" class="stdInput char6" value="bcccdatacannon" />
                        <input type="button" class="stdButton" value="C" id="gpUsrCSCButton" data-clipboard-target="gpUsrCSC" /> P
                        <input type="text" id="gpPassCSC" class="stdInput char6" value="xebraDeCeSAd6Pe4" />
                        <input type="button" class="stdButton" value="C" id="gpPassCSCButton" data-clipboard-target="gpPassCSC" />
                    </li>
                    <li>U
                        <input type="text" id="gpUsrQH" class="stdInput char6" value="iatelddesm" />
                        <input type="button" class="stdButton" value="C" id="gpUsrQHButton" data-clipboard-target="gpUsrQH" /> P
                        <input type="text" id="gpPassQH" class="stdInput char6" value="cyclones" />
                        <input type="button" class="stdButton" value="C" id="gpPassQHButton" data-clipboard-target="gpPassQH" />
                    </li>
                    <li>Item 2</li>
                    <li>Item 3</li>
                </ul>
            </div>
        </div>
    </div>
</body>

</html>
