<!doctype html>
<html>

	<head>
		<meta charset="UTF-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title>Modem Telnet Interface</title>
		<!-- JS -->
		<script type="text/javascript" src="lib/jquery-2.1.3.min.js"></script>
		<script type="text/javascript" src="lib/jquery-ui/jquery-ui.min.js"></script>
		<script type="text/javascript" src="lib/highcharts/js/highcharts.js"></script>
		<script type="text/javascript" src="lib/telnet.js"></script>
		<!-- CSS -->
		<link rel="stylesheet" href="lib/jquery-ui/jquery-ui.min.css" />
		<link rel="stylesheet" href="css/slate/style.css" />
	</head>
<body style="overflow:auto !important;">
	<div>
	<input type="button" class="stdButton" value="Enable Telnet Port" onclick="tn.initTelnetEnable();" /> &nbsp; 
	<input type="button" class="stdButton" value="Disable Telnet Port" onclick="tn.initTelnetDisable();" />
	</div>
	<input type="text" class="stdInput" size="15" id="mac" placeholder="MAC" />
	<input type="text" class="stdInput" size="12" id="ip" placeholder="IP" />
	<input type="text" class="stdInput" size="2" id="port" name="port" title="port" value="23" disabled="disabled" />
	<input type="button" class="stdButton" value="Connect &amp; Execute" onclick="tn.initCommandPush();" /><br />
	<textarea id="commandList" class="stdInput" style="font-family: Courier;" rows="3" cols="47" placeholder="Type each command on its own line"></textarea>
	<div id="tnOutput">NOTE: Make sure port TCP/23 is forwarded to LAN_CURRENT_IP before attempting to use this tool.</div>
  	<hr />
  	<div>
      <h3>Sample Commands...</h3>
      <div><b>Wireless Scan</b></div>
      <blockquote>wlctl scan<br /><br />wlctl scanresults</blockquote>
      <div><b>Reset (Factory Restore) Modem </b></div>
      <blockquote>restoredefaults</blockquote>
      <div><b>View Firewall</b> (Works with Firewall Setting on <em>and</em> off)</div>
      <blockquote>sh<br />iptables -L</blockquote>
      <div><b>Show ARP Table</b></div>
      <blockquote>arp show</blockquote>
        <div><b>Turning Wireless on and off</b> (not persistent across reboots)</div>
      <blockquote>wlctl down<br />wlctl up</blockquote>
      <div><b>Show All Ports States</b></div>
      <blockquote>ifconfig</blockquote>
        <div><b>Turning Ethernet Ports on and off</b> (not persistent across reboots)</div>
      <blockquote>ifconfig <em>eth0</em> down<br />ifconfig <em>eth0</em> up<br />
      eth0 = Port 1, eth1 = Port 2, eth2 = Port 3, eth3 = Port4
      </blockquote>
    </div>
</body>
</html>
