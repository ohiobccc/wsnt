'use strict';
/* Noter
 * 
 * Takes and formats notes for DataCannon. Also, provides specific functionality for 
 * interacting with Sagem modems, and quick access to BCCC usernames and passwords.
 * This project is far from feature completion.
 *
 * @author  Tom Kisha
 * @version 0.1
 * @site    https://github.com/ohiobccc/wsnt
 *
 * PLEASE NOTE: This code was mostly thrown together hastily, most methods should
 * be refactored so things don't get too messy. Some class properties are not even 
 * being used currently. 
 */



var nl = "\r\n";
// Load and initialize ZeroClipboard when DOM is ready
$(document).ready(function() {
	PageInit();
	ZeroClipboard.config({ 
		swfPath: './lib/zeroclipboard/ZeroClipboard.swf',
		moviePath: './lib/zeroclipboard/ZeroClipboard.swf',
		forceHandCursor: true,
        wmode: 'opaque'
	});

	setTimeout("initClippy()", 1000);

});

// Quick and dirty modal functionality.
var modal = {
    
    currentEle: false,
    isActive: false,
    
    
    show: function (ele) {
        modal.currentEle = ele;
        var modalEle = $('#'+ele);
        var dimmer = $('#modalDimmer');
        dimmer.attr('class', 'modalDimmer');
        modalEle.attr('class', 'modalVisibleFull');
        modal.processOptions(ele);
        modal.isActive = true;
    },
    
    hide: function (ele) {
        var modalEle = $('#'+modal.currentEle);
        var dimmer = $('#modalDimmer');
        dimmer.attr('class', 'hideMe');
        modalEle.attr('class', 'hideMe');
        modal.isActive = false;
    },
    
    // If any dialog windows need specific functionality, do it here
    processOptions: function (ele) {
        switch (ele) {
            case 'modalLogins':
                noteMgr.clippyHDMUser = new ZeroClipboard($('#gpUsrHDMButton'));
                noteMgr.clippyHDMPass = new ZeroClipboard($('#gpPassHDMButton'));
                noteMgr.clippyCSCUser = new ZeroClipboard($('#gpUsrCSCButton'));
                noteMgr.clippyCSCPass = new ZeroClipboard($('#gpPassCSCButton'));
                noteMgr.clippyQHUser = new ZeroClipboard($('#gpUsrQHButton'));
                noteMgr.clippyQHPass = new ZeroClipboard($('#gpPassQHButton'));
            break;
        }
    },
    
    
    
    mrDebug: function () { }  
};



var noteMgr = {
	
	notesRefresher: false,
	clippy: false,
	clippyPass: false,
    clippyHDMUser: false,
    clippyHDMPass: false,
    clippyCSCUser: false,
    clippyCSCPass: false,
    clippyQHUser: false,
    clippyQHPass: false,
	notesOut: false,
	
	compileNotes: function () {
		var cci = $('#aiCCI').val();
		var eid = $('#aiEid').val();
		var issue = $('#aiIssue').val();
		var hasTrain = $("input:radio[name='hasTrain']:checked").val();
		var hasIP = $("input:radio[name='hasIP']:checked").val();
		var hasISPP = $("input:radio[name='hasISPP']:checked").val();
		var hasMIROR = $("input:radio[name='hasMIROR']:checked").val();
		var hasMSS = $("input:radio[name='hasMSS']:checked").val();
		var hasCAMS = $("input:radio[name='hasCAMS']:checked").val();
		var hasPing = $("input:radio[name='hasPing']:checked").val();
		var hasSig = $("input:radio[name='hasSig']:checked").val();
		var notes = $('#aiNotes').val();
		var statNotes = '';
		var out = '';
		
		if (cci.length > 0) { out += '--CCI:'+cci+' ' };
		if (eid.length > 0) { out += '--EmpID:'+eid+' ' };
		if (issue.length > 0) { out += 'Issue:'+issue+'.' };
		if (out.length > 0) { out += "\n"; }
        /*
		if (hasTrain !== undefined) { statNotes += 'Has Train:'+hasTrain+'|'; };
		if (hasIP !== undefined) { statNotes += 'Has IP:'+hasIP+'|'; };
		if (hasISPP !== undefined) { statNotes += 'ISPP:'+hasISPP+'|'; };
		if (hasMIROR !== undefined) { statNotes += 'MIROR:'+hasMIROR+'|'; };
		if (hasMSS !== undefined) { statNotes += 'MSS:'+hasMSS+'|'; };
		if (hasCAMS !== undefined) { statNotes += 'CAMS:'+hasCAMS+'|'; };
		if (hasPing !== undefined) { statNotes += 'Ping:'+hasPing+'|'; };
		if (hasSig !== undefined) { statNotes += 'Signal:'+hasSig+'|'; };
        */
		if (statNotes.length > 0) { 
			out += statNotes.substr(0, (statNotes.length - 1));
			out += "\n";
		}
		if (notes.length > 0) { out += 'Notes:'+notes+'.' };
		
		noteMgr.notesOut = out;
		$('#hiddenClippy').text(out);
		
	},
	
	clearNotes: function () {
		
		$('#aiStn').val('');
		$('#aiStn2').val('');
		$('#aiStn').val('');
		$('#aiWanIp').val('');
		$('#aiMac').val('');
		$('#aiUsr').val('');
		$('#aiPasswd').val('');
		$('#aiUsr2').val('');
		$('#aiPasswd2').val('');
		$('#aiCbr').val('');
		$('#aiTicket').val('');
		
		
		
		$('#aiCCI').val('');
		$('#aiEid').val('');
		$('#aiIssue').val('');
		$("input:radio[name='hasTrain']").prop('checked', false);
		$("input:radio[name='hasIP']").prop('checked', false);
		$("input:radio[name='hasISPP']").prop('checked', false);
		$("input:radio[name='hasMIROR']").prop('checked', false);
		$("input:radio[name='hasMSS']").prop('checked', false);
		$("input:radio[name='hasCAMS']").prop('checked', false);
		$("input:radio[name='hasPing']").prop('checked', false);
		$("input:radio[name='hasSig']").prop('checked', false);
		$('#aiNotes').val('');
	},
	
	// Get rid of the pesky whitespace characters from all the tools that create them
	removeWhitespace: function (field) {
		
		$("#"+field).keyup(function(){
			var start = this.selectionStart, end = this.selectionEnd;
			
			var whitespaceReg = /\s/gi;
			var inputVal = $('#'+field).val();
			inputVal = inputVal.replace(whitespaceReg, '');
			$('#'+field).val(inputVal);			
			

			this.setSelectionRange(start, end);
		});
		
	},
	
	mrDebug: function () { }
};


function initClippy () {
	noteMgr.clippy = new ZeroClipboard($('#copyNotes'));
	noteMgr.clippyPass = new ZeroClipboard($('#copyPass'));
    // Logins
    setInterval("genSagemPassword()", 1400);
}

function PageInit () {
	$("#hasTrainGroup").buttonset();
	$("#hasTrainGroup").tooltip();
	$("#hasIPGroup").buttonset();
	$("#hasIPGroup").tooltip();
	$("#hasISPPGroup").buttonset();
	$("#hasISPPGroup").tooltip();
	$("#hasMIRORGroup").buttonset();
	$("#hasMIRORGroup").tooltip();
	$("#hasMSSGroup").buttonset();
	$("#hasMSSGroup").tooltip();
	$("#hasCAMSGroup").buttonset();
	$("#hasCAMSGroup").tooltip();
	$("#hasPingGroup").buttonset();
	$("#hasPingGroup").tooltip();
	$("#hasSigGroup").buttonset();
	$("#hasSigGroup").tooltip();
	
	noteMgr.notesRefresher = setInterval("noteMgr.compileNotes()", 800);
}

function genModemRemoteLoginData () {
	
	// Generate URL
	var urlOut = String('http://');
	var ip = $('#aiWanIp').val();
	var mac = $('#aiMac').val();
	urlOut += ip + ':50580';
	
	// Generate Password
	var passOut = String('###WINDSTREAMPROPRIETARY###');
	
    if (mac.length == 12 && ip.length < 7) {
        passOut = passOut + mac.toLowerCase().substr(6, 6);
        $('#hiddenPasswd').val(passOut);
    }

	
	
	
	// Create Popup
	popMeOut (urlOut, 800, 600, 'yes', 'no', 'yes', '1704xRemoteAdmin');
	
}


function genSagemPassword () {
    var mac = $('#aiMac').val();
    if (mac.length == 12) {
        var passOut = String('###WINDSTREAMPROPRIETARY###');
        
        passOut = passOut + mac.toLowerCase().substr(6, 6);
        $('#hiddenPasswd').val(passOut);
    }
    
    
    
    
    // Put Password into field
	
}


function openTrafficWindow () {
	var ip = $('#aiWanIp').val();
	var mac = $('#aiMac').val(); 
	var url = './speed.php';
	if (ip.length > 7 && mac.length == 12) {
		url += '?ip='+ip+'&'+'mac='+mac;
	}
	popMeOut (url, 630, 400, 'yes', 'no', 'yes', 'SagemTraffic');
	
}



function dynamicResizeWindow () {
	var curHeight = Number($('#mainContainer').height());
	var curWidth = Number($('#mainContainer').width());
	var navHeight = Number($('#nav').height());
	var wPadding = Number(14); // compensate for vertical borders (11)
	var hPadding = Number(72); // compensate for horizontal borders/omnibox/titlebar (80)
	var totalW = Number(curWidth+wPadding+2);
	var totalH = Number(curHeight+navHeight+hPadding+2);
	window.resizeTo(totalW, totalH);
};




function popMeOut (theUrl, theWidth, theHeight, wScroll, wTool, wLoc, wName) {
	var theDate = new Date();
	var theName = new String('');
	var theOptions = new String('');
	theName += theDate.getTime();
	theOptions += 'width='        + theWidth;
	theOptions += ',height='      + theHeight;
	theOptions += '"';
	theOptions += ',scrollbars='  + wScroll;
	theOptions += ',toolbar='     + wTool;
	theOptions += ',location='    + wLoc;
	theOptions += ',resizable=1';
    theOptions += ',top=150';
    theOptions += ',left=120';
	theOptions += '"';
	window.open (theUrl, theName, theOptions);
};


