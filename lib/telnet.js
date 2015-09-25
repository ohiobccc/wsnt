/* Modem Telnet Client
 * 
 * Interacts with modem to enable port forwarding for telnet (if needed), pushes
 * commands to the modem and returns the output via AJAX calls.
 *
 * @author  Tom Kisha
 * @version 0.1
 * @site    https://github.com/ohiobccc/wsnt
 *
 * PLEASE NOTE: This code was mostly thrown together hastily, most methods should
 * be refactored so things don't get too messy. Some class properties are not even 
 * being used currently. 
 */


var tn = {

    ajaxFile: String('lib/telnet.php'),
    curlFile: String('lib/sagemcurl.php'),
    ip: false,
    mac: false,
    port: false,
    commands: false,

    initCommandPush: function () {
        tn.ip = $('#ip').val();
        tn.mac = $('#mac').val();
        tn.port = $('#port').val();
        tn.commands = $('#commandList').val();
        // Generic AJAX wait icon
        $('#tnOutput').html('<h3 style="margin-top:1em;">Request sent, please wait...<br /><img src="img/ajaxloader_slate.gif" />');
        tn.sendData();
    },
    
    
    // XHR request to push commands to the modem
    sendData: function () {
        var formStuff = String('');
        formStuff += 'ip=' + tn.ip;
        formStuff += '&mac=' + tn.mac;
        formStuff += '&port=' + tn.port;
        formStuff += '&commands=' + tn.commands;

        var modemData = $.post(tn.ajaxFile, formStuff, function (data) {
            $('#tnOutput').html(data);
        });
    },
    
    // Assuming it works, attempts to enable the port for connection
    initTelnetEnable: function () {
        tn.ip = $('#ip').val();
        tn.mac = $('#mac').val();
        tn.port = $('#port').val();

        var formStuff = String('');
        formStuff += 'ip=' + tn.ip;
        formStuff += '&mac=' + tn.mac;
        formStuff += '&port=' + '50580';
        formStuff += '&q=' + 'pfAdd';
        formStuff += '&pfport=' + 23;
        
        // Generic AJAX wait icon
        $('#tnOutput').html('<h3 style="margin-top:1em;">Request sent, please wait...<br /><img src="img/ajaxloader_slate.gif" />');

        var modemData = $.post(tn.curlFile, formStuff, function (data) {
            $('#tnOutput').html(data);
        });




    },
    
    // Assuming it works, attempts to disable the telnet port
    initTelnetDisable: function () {
        tn.ip = $('#ip').val();
        tn.mac = $('#mac').val();
        tn.port = $('#port').val();

        var formStuff = String('');
        formStuff += 'ip=' + tn.ip;
        formStuff += '&mac=' + tn.mac;
        formStuff += '&port=' + '50580';
        formStuff += '&q=' + 'pfRemove';
        formStuff += '&pfport=' + 23;
        
        // Generic AJAX wait icon
        $('#tnOutput').html('<h3 style="margin-top:1em;">Request sent, please wait...<br /><img src="img/ajaxloader_slate.gif" />');

        var modemData = $.post(tn.curlFile, formStuff, function (data) {
            $('#tnOutput').html(data);
        });




    },




    outputTNData: function (data) {



    },



    mrDebug: function () {}
};
