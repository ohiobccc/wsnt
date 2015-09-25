<?php
/* AJAX Dispatcher / Sagem Interface [cURL]
 * 
 * Receives request via AJAX > Logs into modem via cURL > Grabs Page > Returns data
 * Can also submit configuration changes as long as the paramers match up
 *
 * @author	Tom Kisha
 * @version	0.1
 * 
 * PLEASE NOTE: This code was mostly thrown together hastily, most methods should
 * be refactored so things don't get too messy. Some class properties are not even 
 * being used currently.
*/

libxml_use_internal_errors(TRUE);



class SagemInterface {
    
    public $user;
    public $password;
    public $wanIP;
    public $port;
    public $connectionType;
    public $wanMAC;
    public $authorized;
    public $ch;
    public $currentHTML;
    public $currentTimestamp;
    public $status;
    public $scraper;
	
	// Populates object properties with default base connection params
    function __construct ($mdm) {
        $this->status = false;
        $this->user = $mdm['user'];
        $this->wanIP = $mdm['ip'];
        $this->wanMAC = $mdm['mac'];
        $this->port = $mdm['port'];
        $this->connectionType = $mdm['connectionType'];
        $this->password = $this->generateSessionPassword ($mdm['passPre'], $this->wanMAC);
        $this->connectionURL = $this->generateRemoteURL($this->connectionType, $this->wanIP, $this->port);
        $this->setStatus(1);
    }
    
	// Not used, but could be
    public function getStatus() {
        switch ($this->status) {
            case false:
                return 'false: Could not initialize...';
            break;
            case 0:
                return '0: Constructor Init';
            break;
            case 1:
                return '1: Constructor Complete';
            break;
            case 2:
                return '2: cURL Initialized';
            break;
            case 3:
                return '3: cURL Executed';
            break;
            default:
                return 'Unknown State';
            break;
        }
    }
    
    
    public function setStatus($recall) {
        $this->status = $recall;
    }
    
    
  	// Generates modem login password
    public function generateSessionPassword($pre, $mac) {
        $tmp = strtolower($mac);
        $out = $pre . substr($tmp, 6, 6);
        return $out;
    }
    
  	
    public function generateRemoteURL ($type, $wanIP, $port) {
        switch ($type) {
            case 'http':
                $out = 'http://';
            break;
            default:
                $out = 'http://';
            break;
        }
        $out .= $wanIP . ':' . $port;
        return $out;
    }
    
  	// Standard cURL lheaders for remote requests
    public function getCurlHeaders () {
        return Array(
            'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language: en-us,en;q=0.5',
            'Accept-Encoding: gzip,deflate',
            'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'Keep-Alive: 115',
            'Connection: keep-alive',
            'Authentication: Basic '.base64_encode($this->user . ":" . $this->password)
        );
    }
    
  	// Initializes cURL, but does not execute/start session
  	// Normally this would be a private/protected method since it shouldnt be called 
    // from the outside
    public function initSession ($page) {
        $ch = curl_init();
        $this->status = 2;
        curl_setopt($ch, CURLOPT_URL, $this->connectionURL.$page);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user.':'.$this->password);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.104 Safari/537.36"); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getCurlHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
        $this->status = 3;
        return $ch;
    }

  	// Not really needed
    public function nextPage ($page, $ch) {
        $this->status = 2;
        curl_setopt($ch, CURLOPT_URL, $this->connectionURL.$page);
	 curl_setopt($ch, CURLOPT_POST, false);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, "");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_USERPWD, $this->user.':'.$this->password);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/38.0.2125.104 Safari/537.36"); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getCurlHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
	 curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $this->status = 3;
        return $ch;
    }    

    
    
	// Calls for session initialization then executes and returns page output
    public function getPageHTML ($page) {
        $this->ch = $this->initSession($page);
        $this->currentHTML = curl_exec($this->ch);
        //curl_close($this->ch);
        return $this->currentHTML;
    }
    
    // Add timestampt to object (for speed measurements)
    public function setTimestamp () {
        $this->currentTimestamp = time();
    }
}


class SagemPageScrapers {
    
   
    public $htmlSource;
    public $htmlDOM;
    public $htmlXPath;
    public $htmlQuery;
    
    
    function __construct ($sourceHTML) {
        $this->htmlSource = $sourceHTML;
		$this->htmlDOM = new DOMDocument();
		$this->htmlDOM->loadHTML($this->htmlSource);
		libxml_clear_errors();
		$this->htmlXPath = new DOMXPath($this->htmlDOM);
    }
    
    // Scrapes WAN Statistics for pertinent data (for speed measurement)
    public function parseWANStats ($timestamp) {
        $stats = Array ();
        $this->htmlQuery = $this->htmlXPath->query('//td');
		$stats['rxBytes'] = (int) $this->htmlQuery->item(16)->nodeValue;
		$stats['txBytes'] = (int) $this->htmlQuery->item(20)->nodeValue;
		
		$stats['rxErrors'] = (int) $this->htmlQuery->item(18)->nodeValue;
		$stats['txErrors'] = (int) $this->htmlQuery->item(22)->nodeValue;
		
		$stats['rxDrops'] = (int) $this->htmlQuery->item(19)->nodeValue;
		$stats['txDrops'] = (int) $this->htmlQuery->item(23)->nodeValue;
		
		$stats['timestamp'] = (int) ($timestamp - 18000);
		return $stats;
    }
    
    // Scrapes amin info page
    public function parseMainInfo () {
        $stats = Array();
        $this->htmlQuery = $this->htmlXPath->query('//td');
        
        $stats['model'] = substr(htmlspecialchars($this->htmlQuery->item(2)->nodeValue), 42, 10);
        $stats['firmware'] = $this->htmlQuery->item(22)->nodeValue;
        $stats['uptime'] = $this->htmlQuery->item(25)->nodeValue;
        $stats['usRate'] = $this->htmlQuery->item(33)->nodeValue;
        $stats['dsRate'] = $this->htmlQuery->item(35)->nodeValue;
        
        return $stats;
    }
    
    //TODO
    public function rebootModem () {
        
        
        
    }
    
    //TODO
    public function factoryResetModem () {
        
        
        
        
    }
    

};


// Default modem session params
$mdm = Array();
$mdm['ip'] = false;
$mdm['mac'] = false;
$mdm['user'] = '###WINDSTREAMPROPRIETARY###';
$mdm['passPre'] = '###WINDSTREAMPROPRIETARY###';
$mdm['port'] = '50580';
$mdm['connectionType'] = 'http';

// Check for query HTTP POST param "q", assume the rest are present, otherwise nothing will happen
if ($_POST) {
	if (isset($_POST['q'])) {
		$out = '';
		$mdm['ip'] = $_POST['ip'];
		$mdm['mac'] = $_POST['mac'];
		$mdm['port'] = $_POST['port'];
      	// "q" dictates what routines to perform
		switch ($_POST['q']) {
			case 'wanstats':
				/* WAN Statistics */
				$wanStatsPageData = new SagemInterface($mdm);
				$wanStatsPageData->getPageHTML('/statswan.cmd');
				$wanStatsPageData->setTimestamp();
				$wanStatsPageData->scraper = new SagemPageScrapers($wanStatsPageData->currentHTML);
				$wanStats = $wanStatsPageData->scraper->parseWANStats($wanStatsPageData->currentTimestamp);				

				/* xDSL Statistics */
				$mainInfoPageData = new SagemInterface($mdm);
				$mainInfoPageData->getPageHTML('/info.html');
				$mainInfoPageData->setTimestamp();
				$mainInfoPageData->scraper = new SagemPageScrapers($mainInfoPageData->currentHTML);
				$mainInfo = $mainInfoPageData->scraper->parseMainInfo($mainInfoPageData->currentTimestamp);
				$wanStats['info'] = $mainInfo;
				$out = $wanStats;
			break;
			case 'pfAdd':
				$mainInfoPageData = new SagemInterface($mdm);
				// One of these will work depending on interface type/nomenclature
            	// **This way of doing it has significantly slowed function, should find a way to determine i/o 
                // interface and submit request only once (will make it much faster). If that's not possible, 
            	// then create a conditional to determine if request succeeds, then break out.**
            	// TODO: Determine whether ppp0, ppp0.0, ppp1.0, atm0.0, atm1.0 - execute once
            	$mainInfoPageData->getPageHTML('/scvrtsrv.html');
            	preg_match_all('/&sessionKey=\d*/mi', $mainInfoPageData->currentHTML, $sessionKey);
            	$mainInfoPageData->getPageHTML('/scvrtsrv.cmd?action=add&srvName=wsc-tn&dstWanIf=ppp0.0&srvAddr=CURRENT_LAN_IP&proto=1,&eStart=23,&eEnd=23,&iStart=23,&iEnd=23,'.$sessionKey[0][0]);
            	
            	$mainInfoPageData->getPageHTML('/scvrtsrv.html');
            	preg_match_all('/&sessionKey=\d*/mi', $mainInfoPageData->currentHTML, $sessionKey);
           	 	$mainInfoPageData->getPageHTML('/scvrtsrv.cmd?action=add&srvName=wsc-tn&dstWanIf=ppp1.0&srvAddr=CURRENT_LAN_IP&proto=1,&eStart=23,&eEnd=23,&iStart=23,&iEnd=23,'.$sessionKey[0][0]);				
               
            	$mainInfoPageData->getPageHTML('/scvrtsrv.html');
            	preg_match_all('/&sessionKey=\d*/mi', $mainInfoPageData->currentHTML, $sessionKey);
            	$mainInfoPageData->getPageHTML('/scvrtsrv.cmd?action=add&srvName=wsc-tn&dstWanIf=atm0.0&srvAddr=CURRENT_LAN_IP&proto=1,&eStart=23,&eEnd=23,&iStart=23,&iEnd=23,'.$sessionKey[0][0]);
                
            	$mainInfoPageData->getPageHTML('/scvrtsrv.html');
            	preg_match_all('/&sessionKey=\d*/mi', $mainInfoPageData->currentHTML, $sessionKey);
            	$mainInfoPageData->getPageHTML('/scvrtsrv.cmd?action=add&srvName=wsc-tn&dstWanIf=atm1.0&srvAddr=CURRENT_LAN_IP&proto=1,&eStart=23,&eEnd=23,&iStart=23,&iEnd=23,'.$sessionKey[0][0]);
            
            	$mainInfoPageData->getPageHTML('/scvrtsrv.html');
            	preg_match_all('/&sessionKey=\d*/mi', $mainInfoPageData->currentHTML, $sessionKey);
            	$mainInfoPageData->getPageHTML('/scvrtsrv.cmd?action=add&srvName=wsc-tn&dstWanIf=ppp0&srvAddr=CURRENT_LAN_IP&proto=1,&eStart=23,&eEnd=23,&iStart=23,&iEnd=23,'.$sessionKey[0][0]);
           		
			break;
			case 'pfRemove':
				$mainInfoPageData = new SagemInterface($mdm);
				
            	// One of these will work depending on interface type/nomenclature
            	// Same as pfAdd, this was a quick and dirty hack.
            	// TODO: Determine whether ppp0, ppp0.0, ppp1.0, atm0.0, atm1.0 - execute once
            	$mainInfoPageData->getPageHTML('/scvrtsrv.html');
				preg_match_all('/&sessionKey=\d*/mi', $mainInfoPageData->currentHTML, $sessionKey);
            	$mainInfoPageData->getPageHTML('/scvrtsrv.cmd?action=remove&rmLst=wsc-tn|CURRENT_LAN_IP|23|23|TCP|23|23|ppp0.0'.$sessionKey[0][0]);
				
            	$mainInfoPageData->getPageHTML('/scvrtsrv.html');
				preg_match_all('/&sessionKey=\d*/mi', $mainInfoPageData->currentHTML, $sessionKey);
            	$mainInfoPageData->getPageHTML('/scvrtsrv.cmd?action=remove&rmLst=wsc-tn|CURRENT_LAN_IP|23|23|TCP|23|23|ppp1.0'.$sessionKey[0][0]);
            	
            	$mainInfoPageData->getPageHTML('/scvrtsrv.html');
				preg_match_all('/&sessionKey=\d*/mi', $mainInfoPageData->currentHTML, $sessionKey);
            	$mainInfoPageData->getPageHTML('/scvrtsrv.cmd?action=remove&rmLst=wsc-tn|CURRENT_LAN_IP|23|23|TCP|23|23|atm0.0'.$sessionKey[0][0]);
            	
            	$mainInfoPageData->getPageHTML('/scvrtsrv.html');
				preg_match_all('/&sessionKey=\d*/mi', $mainInfoPageData->currentHTML, $sessionKey);
            	$mainInfoPageData->getPageHTML('/scvrtsrv.cmd?action=remove&rmLst=wsc-tn|CURRENT_LAN_IP|23|23|TCP|23|23|atm0.0'.$sessionKey[0][0]);
            
            	$mainInfoPageData->getPageHTML('/scvrtsrv.html');
				preg_match_all('/&sessionKey=\d*/mi', $mainInfoPageData->currentHTML, $sessionKey);
            	$mainInfoPageData->getPageHTML('/scvrtsrv.cmd?action=remove&rmLst=CURRENT_LAN_IP|23|23|TCP|23|23,'.$sessionKey[0][0]);
            	//print_r($mainInfoPageData->currentHTML);
			break;
			default:
            	// Error out because something went wrong
				$out .= 'AJAX Parameter Error:';
				$out .= print_r($_POST);
			
		}
    }
}


// WAN Statistics Request Size: 4300 bytes
// LAN Statistics Request Size: 12300 bytes

// Return retrieved data to XHR request
// Encode for JSON if long in, otherwise spit out generic text for callback.
if (count($out) > 2) {
  	echo json_encode($out);
} else {
    echo 'Request finished... If commands return errors, please manually check port forward, troubleshoot connection problems, or check command syntax.';
}
/* DEBUG STUFF
echo '<h2> Rx: '.$rxSpeed['bytes'].' kB/s ('.$rxSpeed['bits'].' kb/s)</h2>';
echo '<h2> Tx: '.$txSpeed['bytes'].' kB/s ('.$txSpeed['bits'].' kb/s)</h2>';
echo '<div>';
var_dump($wanParse);
echo '</div>';
echo '<div>';
var_dump($pageData);
echo '</div>';
*/

?>
