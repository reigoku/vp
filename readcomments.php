<?php
	require_once "../config.php";
	
	// loon andmebaasiga ühenduse
	$conn = new mysqli($server_host, $server_user_name, $server_password, $database);
	//määran suhtlemisel kasutatava kooditabeli
	$conn->set_charset("utf8");
	
	//valmistame ette andmete saatmise SQL käsu
	$stmt = $conn->prepare("SELECT comment, grade, added from vp_daycomment_2");
	echo $conn->error;
	//seome saadavad andmed muutujatega
	$stmt->bind_result ($comment_db, $grade_db, $added_db);
	//täidame käsu
	$stmt->execute();
	//if($stmt->fetch()){
		//mis kirjega teha
	//}
	$comment_html = null;
	//kui tuleb teadmata arv kirjeid
	while($stmt->fetch()){
		//echo $comment_db;
		//<p>kommentaar, hinne päevale: 6, lisatud xx/xx/xxxx/</p>
		$comment_html .= "<p>" .$comment_db .", hinne päevale: " .$grade_db .", lisatud " .$added_db;
	}
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title>reigo kurgpõld kirjutas selle</title>
</head>
<body>

<img src="pics/vp_banner_gs.png">

<h1>reigo kurgpõld kirjuatas selle</h1>
<p>See leht on loodud õppetöö raames ja ei sisalda tõsiseltvõetavalt sisu.</p>
<p>õppetöö toimus <a href="https://www.tlu.ee/" target="_blank">Tallinna Ülikoolis</a> digitehnoloogiate instituudis</p>
<img src="pics\tlu_36.jpg" alt="Tallinna Ülikooli Terra õppehoone">
<?php echo $comment_html; ?>
</body>

</html>
