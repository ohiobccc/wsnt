<?php
$ip = '';
$mac = '';
$autoStart = false;
if ($_GET) {
	if (isset($_GET['mac']) && isset($_GET['ip'])) {
		$autoStart = true;
		$ip = $_GET['ip'];
		$mac = $_GET['mac'];
	}
}
?>
<!doctype html>
<html>

	<head>
		<meta charset="UTF-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title>Modem Speed Measurainator</title>
		<!-- JS -->
		<script type="text/javascript" src="lib/jquery-2.1.3.min.js"></script>
		<script type="text/javascript" src="lib/jquery-ui/jquery-ui.min.js"></script>
		<script type="text/javascript" src="lib/highcharts/js/highcharts.js"></script>
		<script type="text/javascript" src="lib/speed.js"></script>
		<!-- CSS -->
		<link rel="stylesheet" href="lib/jquery-ui/jquery-ui.min.css" />
		<link rel="stylesheet" href="css/slate/style.css" />
		<style type="text/css">
			#chart {
				width: 100%;
				height: 92%;
				text-align: center;
			}
		</style>
	</head>
<body>
	<div class="inline"></div>
	<input type="text" class="stdInput" size="15" id="mac" placeholder="MAC" value="<?php echo $mac; ?>" />
	<input type="text" class="stdInput" size="12" id="ip" placeholder="IP" value="<?php echo $ip; ?>" />
	Int:<input type="text" class="stdInput" size="1" id="i" placeholder="int" disabled="disabled" value="5" />
	<input type="button" class="stdButton" value="Start" onclick="md.startPolling();" />
	<input type="button" class="stdButton" value="Stop" onclick="md.stopPolling();" />
	<input type="hidden" id="port" name="port" value="50580" />
	<div id="curValue"></div>
	<div id="chart"></div>
	<div id="test"></div>
</body>
	<?php if ($autoStart) { echo '<script>md.startPolling();</script>'; } ?>
</html>
