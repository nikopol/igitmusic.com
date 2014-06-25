<?php

$GIFT_FILE = "cadeau.mp3";
$EMAIL_FILE = "emails.json";

function baseurl(){
	$prot=stripos($_SERVER['SERVER_PROTOCOL'],'HTTPS')===false?'http://':'https://';
	$host=$_SERVER['HTTP_HOST'];
	$port=$_SERVER['SERVER_PORT']==80?'':':'.$_SERVER['SERVER_PORT'];
	$path=preg_replace('/[^\/]*$/', '', $_SERVER['REQUEST_URI']);
	return $prot.$host.$port.$path;
}

function jsonfile($fn, $data=null){
	return $data != null 
		? file_put_contents($fn, json_encode($data))
		: file_exists($fn)
			? json_decode(file_get_contents($fn),true)
			: false;
}

function send_file($f,$inline=false){
	header('Content-Description: File Transfer');
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: '.filesize($f));
	header('Content-Type: '.mime_content_type($f));
	if($inline){
		header('Content-Disposition: inline; filename='.basename($f));
	} else {
		header('Content-Disposition: attachment; filename='.basename($f));
	}
	readfile($f);
}

$error='';
if(!empty($_POST['email'])) {
	$email=strtolower($_POST['email']);
	if(preg_match('/^.+@.+\.\w+$/', $email)) {
		$f=jsonfile($EMAIL_FILE);
		if(!$f) $f=array();
		if(!array_key_exists($email,$f)) {
			$f[$email]=date('Y-m-d H:i:s');
			jsonfile($EMAIL_FILE,$f);
			send_file($GIFT_FILE);
		} else {
			$error='email déjà connu';
		}
	} else {
		$error='email invalide';
	}
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<meta name="author" content="niko" />
	<meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
	<title>IGIT EMAIL FORM</title>
	<style>
		.error { color: red; }
	</style>
	<!-- <link href='harry.css' rel='stylesheet' type='text/css'> -->
	<!-- <script src="miniko.js"></script> -->
</head>
<body>
	<?php if($error) { ?><div class="error"><?php echo $error ?></div><?php } ?>
	<form action="?" method="POST">
		<input type="text" name="email" placeholder="email..."/>
		<input type="submit">
	</form>
	<a href="<?php echo baseurl()?>">retour à la page maison</a>
</body>
</html>