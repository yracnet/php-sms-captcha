<?php
 $cookie = $_POST['kie'];
 if(!$cookie){
  $rep=get_response('http://websms.nuevatel.com/websms3.php', 'Cookie: ');
	echo 'NEW SESSION: '.$cookie.'<br/>';
    $cookie = $rep[1];
    $rep=get_response('http://websms.nuevatel.com/connect.php', $cookie);
 }
 echo "$cookie<br/>";
?>
==========================SEND SMS========================<br/>
<?php 
	$msg = $_POST['msg'];
	$cel = $_POST['cel'];
	if($msg & $cel){
		for($o=0;$o<10;$o++){
		$rep = get_response('http://websms.nuevatel.com/new_img.php', $cookie);
		$token = $rep[2];
		$rep = get_response('http://websms.nuevatel.com/img.php?code='.$token, $cookie);
		echo $rep[0].'<br/>';
		$base64 = base64_encode($rep[2]);
		$img =  imagecreatefromstring($rep[2]);
		$img = fillcaptcha($img);	
		$code0 = ImageCreateTrueColor(30,50);
		$code1 = ImageCreateTrueColor(30,50);
		$code2 = ImageCreateTrueColor(30,50);
		$code3 = ImageCreateTrueColor(30,50);
		imagecopy($code0, $img, 0, 0, 00, 0, 30, 50);
		imagecopy($code1, $img, 0, 0, 30, 0, 30, 50);
		imagecopy($code2, $img, 0, 0, 60, 0, 30, 50);
		imagecopy($code3, $img, 0, 0, 90, 0, 30, 50);
		$value0 = buscarvalor($code0);
		$value1 = buscarvalor($code1);
		$value2 = buscarvalor($code2);
		$value3 = buscarvalor($code3);
		echo "<h2><img src='data:image/png;base64,$base64' width='80'/> == $value0-$value1-$value2-$value3</h2>";
		echo "<sub>CEL: $cel, MSG: $msg</sub><br/>";
		$captcha = "$value0$value1$value2$value3";
		$send = "http://websms.nuevatel.com/send.php?valor1=hacking&valor1=hacking&valor2=$cel&valor2=$cel&valor3=$o>$msg&valor4=$captcha&valor4=$captcha&valor5=0&valor6=9:00&valor7=&";
		$rep = get_response($send, $cookie);
		echo '<sub>SEND SMS: '.$rep[0].'</sub><br/>';
		echo '<sub>RESPUESTA: '.$rep[2].'</sub><br/>';
		}
	}
?>
<style>
*{margin:0;padding:0;}
</style>
<form method="post">
   CEL:<input name="cel" type="text" />
   <br/>
   MSG:<textarea name="msg"></textarea>
   <input name="kie" type="text" value="<?php echo $cookie; ?>"/>
   <br/>
   <input type="submit" value="Enviar SMS"/>
</form>
<?php 	
function buscarvalor($image){	
	mysql_connect("localhost","root","") or die( "Unable to select database"); 
	mysql_select_db("captcha") or die( "Unable to select database");  
	$N = imagecolorallocate($image, 0,0,0);
	$max = 0;
	$val = 0;
	$result = mysql_query("select * from captcha.c_captcha_car where value != '-'");
	while($row = mysql_fetch_array($result)){	
		$total = 0;
		$codigo =  imagecreatefromstring(base64_decode($row[image]));
		for($x=0;$x<30;$x++){
			for($y=0;$y<50;$y++){
				if(imagecolorat($image, $x, $y) == $N && imagecolorat($codigo, $x, $y) == $N ){
					$total++;
				}
			}
		}
		$total = $total / $row[total];
		if($total > $max){
		   $max = $total;
		   $val = $row[value];
		}
	}
	return $val;
}
function get_response($url, $cookie='', $method='GET'){
	$opts = array(
	  'http'=>array(
		'method'=>$method,
        //'proxy' => 'tcp://177.125.167.253:8080',
        //'proxy' => 'tcp://200.27.183.100:3128', //4s-10s
        //'proxy' => 'tcp://200.114.103.213:8080', 6s-3m 
        //'request_fulluri' => true,
		'header'=>"Accept-language: en\r\n" .
				  "Connection: keep-alive\r\n" .
				  "Host: websms.nuevatel.com\r\n" .
				  "Referer: http://websms.nuevatel.com/websms3.php\r\n" .
				  "User-Agent	Mozilla/5.0 (Windows NT 6.1; rv:18.0) Gecko/20100101 Firefox/3.5\r\n" .
				  "$cookie\r\n"
	  )
	);
	$cookie = '';
	$context = stream_context_create($opts);
	$body = @file_get_contents($url , false, $context);
	foreach($http_response_header as $k){
	   if( stripos($k, 'Set-Cookie', 0) === 0){
	     $cookie = substr($k,4);
		 break; 
	   }
	}
    //echo 'State: '.$http_response_header[0]."\n";
	//echo "$cookie\n";
	//echo "URL: $url\n";
	//echo 'Sess: '.$cookie."\n";
	//echo 'Data: '.$body."\n";
    //echo "--------------------------------------------------------------\n";
	return array(0=>$http_response_header[0],1=>$cookie,2=>$body);
}
function fillcaptcha($img){
  $res = ImageCreateTrueColor(120,50);
  $B = imagecolorallocate($res, 255,255,255);
  $Na = imagecolorallocate($res, 255,0,0);
  $Nb = imagecolorallocate($res, 0,0,255);
  $Nc = imagecolorallocate($res, 0,255,0);
  $N = imagecolorallocate($res, 0,0,0);
  for($x=0;$x<120;$x++){
	for($y=0;$y<50;$y++){
		if(imagecolorat($img, $x, $y) != $N){
			imagesetpixel ($res, $x, $y, $N);
		} else if(imagecolorat($img, $x+1, $y) != $N){
			imagesetpixel ($res, $x, $y, $Na);
		} else if(imagecolorat($img, $x-1, $y) != $N){
			imagesetpixel ($res, $x, $y, $Nb);
		} else if(imagecolorat($img, $x-1, $y+1) != $N){
			imagesetpixel ($res, $x, $y, $Nc);
		}else{
			imagesetpixel ($res, $x, $y, $B);
		}
	}
  }
  imagefill ($res, 0, 0, $N);
  imagefill ($res, 119, 49, $N);
  imagefill ($res, 119, 0, $N);
  imagefill ($res, 0, 49, $N);
  
  for($x=0;$x<120;$x++){
	for($y=0;$y<50;$y++){
		if(imagecolorat($res, $x, $y) != $B){
			imagesetpixel ($img, $x, $y, $N);
		} else {
			imagesetpixel ($img, $x, $y, $B);
		}
	}
  }
  imagecolortransparent($img, $B);
  return $img;
}


?>
