/* Modem Speed Measurinator
 * 
 * Polls modem WAN stats regularly for throughput numbers, calculates speed,
 * then outputs to fancy graph
 *
 * @author  Tom Kisha
 * @version 0.1
 * @site    https://github.com/ohiobccc/wsnt
 *
 * PLEASE NOTE: This code was mostly thrown together hastily, most methods should
 * be refactored so things don't get too messy. Some class properties are not even 
 * being used currently. 
 */

// Refactor to poll for modem information first THEN recurring poll for WAN statistics

var md = {

    ajaxFile: String('lib/sagemcurl.php'),
    DObj: new Date(),
    poller: false, // contains the iterater
    ip: false,
    mac: false,
    port: Number(50580),
    yAxisScale: false,
    pollingInterval: false,
    pollingData: new Array(),
    rxSpeed: false,
    txSpeed: false,
    lastPlot: Number(0),
    isRendered: false,

    startPolling: function () {
        md.pollingInterval = Number($('#i').val() * 1000);
        md.ip = $('#ip').val();
        md.mac = $('#mac').val();
        md.port = $('#port').val();

        if (md.yAxisScale == 'auto') md.yAxisScale = null;

        // Temp Workaround
        // Simulate first 8 points so auto-scroll shows multiple points during live updates
        // Otherwise only shows 2 points at a time
        md.rxSpeed = [
			[md.DObj.getTime(), 0],
			[md.DObj.getTime() + 200, 0],
			[md.DObj.getTime() + 400, 0],
			[md.DObj.getTime() + 600, 0],
			[md.DObj.getTime() + 800, 0],
			[md.DObj.getTime() + 1000, 0],
			[md.DObj.getTime() + 1200, 0],
			[md.DObj.getTime() + 1400, 0]
		];
        md.txSpeed = [
			[md.DObj.getTime(), 0],
			[md.DObj.getTime() + 200, 0],
			[md.DObj.getTime() + 400, 0],
			[md.DObj.getTime() + 600, 0],
			[md.DObj.getTime() + 800, 0],
			[md.DObj.getTime() + 1000, 0],
			[md.DObj.getTime() + 1200, 0],
			[md.DObj.getTime() + 1400, 0]
		];
        // Generic AJAX "please wait"
        $('#chart').html('<h2 style="margin-top:2em;">Polling modem for data, please wait...<br /><div id="pollPercent">0%</div><img src="img/ajaxloader_slate.gif" />');
        md.pollModem();
        md.poller = setInterval("md.pollModem();", md.pollingInterval);
    },


    // Doesnt work
    stopPolling: function () {
        clearInterval(md.poller);
        clearInterval(md.highcharts.chart.events.load);
    },

    // Get modem/WAN stats on regular intervals, called by 'setInterval'
    pollModem: function () {
        var formStuff = String('');
        formStuff += 'ip=' + md.ip;
        formStuff += '&mac=' + md.mac;
        formStuff += '&port=' + md.port;
        formStuff += '&q=' + 'wanstats';

        var modemData = $.post(md.ajaxFile, formStuff, function (data) {
            md.updateData(jQuery.parseJSON(data));
        });
    },

    // Take last two polling Array elements, calculate interval, then push to
    // speed calculation method
    updateData: function (data) {
        md.pollingData.push(data);

        if (md.pollingData.length > 2) {
            var currentPollID = Number(md.pollingData.length - 1);
            var previousPollID = Number(md.pollingData.length - 2);

            var latestPollRX = md.pollingData[currentPollID]['rxBytes'];
            var previousPollRX = md.pollingData[previousPollID]['rxBytes'];

            var latestPollTX = md.pollingData[currentPollID]['txBytes'];
            var previousPollTX = md.pollingData[previousPollID]['txBytes'];

            var currentTimestamp = md.pollingData[currentPollID]['timestamp'];
            var previousTimestamp = md.pollingData[previousPollID]['timestamp'];

            var interval = Number((currentTimestamp) - (previousTimestamp));

            // Temp workaround for 1704G, make default scale if nothing is returned.
            if (data.info.dsRate > 0) {
                md.yAxisScale = data.info.dsRate;
            } else {
                md.yAxisScale = 4500;
            }

            md.calculateLastSpeed('rx', latestPollRX - (2150), previousPollRX, interval);
            md.calculateLastSpeed('tx', latestPollTX - (4200), previousPollTX, interval);
        }
    },

    // Calculate speeds in bits/bytes, and push to calcualted speed array, call chart renderer
    calculateLastSpeed: function (type, current, previous, interval) {

        var bitsTransferred = Number((((current - previous) * 8) / 1000));
        var speed = Number(bitsTransferred / interval);
        // sometimes the calculated speed and also accounting for WAN stats page data
        // transferred results in a negative number. Mark it zero!
        if (speed < 0) speed = 0;
        // Add to calculated speed Array
        switch (type) {
            case 'rx':
                md.rxSpeed.push([new Date().getTime(), speed]);
                break;
            case 'tx':
                md.txSpeed.push([new Date().getTime(), speed]);
                break;
        }
        md.lastPlot++;
        // Update polling loading status based on number of points returned (5 = 100%).
        var percent = (md.lastPlot) * 20;
        $('#pollPercent').html(percent + '%');
        // Check if chart is rendered
        md.renderPlot();
    },

    // Verify if chart is rendered already, if not, spawn chart
    renderPlot: function () {
        // Also make sure there are data points to render (besides the fake inital points)
        if (md.isRendered == false && md.rxSpeed.length > 10 && md.txSpeed.length > 10) {
            md.isRendered = true;
            $('#chart').html('');
            md.chart();
        }
    },


    chart: function () {

        $('#chart').highcharts({
            chart: {

                scrollbar: {
                    enabled: false
                },

                rangeSelector: {
                    selected: 1
                },

                type: 'spline',
                animation: Highcharts.svg, // don't animate in old IE
                marginRight: 10,
                zoomType: 'x',

                events: {
                    load: function () {
                        // set up the updating of the chart each second
                        var seriesRx = this.series[0];
                        var seriesTx = this.series[1];
                        setInterval(function () {
                            //console.log(md.rxSpeed);
                            //console.log(md.txSpeed);
                            var currentPollIDRx = Number(md.rxSpeed.length - 1);
                            var currentPollIDTx = Number(md.txSpeed.length - 1);
                            //console.log('CUR ID: '+md.rxSpeed[currentPollID]);
                            //console.log('Full ARR: '+md.rxSpeed);
                            var xRx = md.rxSpeed[currentPollIDRx][0];
                            var yRx = md.rxSpeed[currentPollIDRx][1];

                            var xTx = md.txSpeed[currentPollIDTx][0];
                            var yTx = md.txSpeed[currentPollIDTx][1];
                            seriesRx.addPoint([xRx, yRx], true, true);
                            seriesTx.addPoint([xTx, yTx], true, true);
                            // Adding 100ms to interval ensures there is time to grab and 
                            // process new data
                        }, md.pollingInterval + 100);
                    }
                }
            },
            title: {
                text: 'Modem Traffic Measurinator ALPHA'
            },
            xAxis: {
                type: 'datetime',
                tickPixelInterval: 50
            },
            yAxis: {
                title: {
                    text: 'kbits/s'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }],
                min: 0,
                max: md.yAxisScale
            },
            tooltip: {
                formatter: function () {
                    // Will show bit/byte speed + timestamp
                    return '<b>' + this.series.name + '</b><br/>' +
                        Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) + '<br/>' +
                        Highcharts.numberFormat(this.y, 2) + 'kbits/s' + '<br />' +
                        Highcharts.numberFormat((this.y / 8), 2) + 'kBytes/s';
                }
            },
            legend: {
                enabled: true
            },
            exporting: {
                enabled: false
            },
            series: [{
                color: '#0000FF',
                name: 'Rx',
                data: (function () {
                    var data = md.rxSpeed;
                    md.lastPlot = md.rxSpeed.length;
                    return data;
                }())
            }, {
                color: '#FF0000',
                name: 'Tx',
                data: (function () {
                    var data = md.txSpeed;
                    return data;
                }())
			}]
        });
    },


    mrDebug: function () {}
};
