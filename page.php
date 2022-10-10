<?php
	session_start();
	require_once "../../config.php";
	require_once "fnc_user.php";
	$author_name = "Reigo Kurgpõld";
	$full_time_now = date("d.m.Y H:i:s");
	$weekday_now = date("n");
	//echo $weekday_now
	$weekdaynames_et = ["esmaspäev", "teisipäev", "kolmapäev", "neljapäev", "reede", "laupäev", "pühapäev"];
	//echo $weekdaynames_et[$weekday_now - 1];
	$hours_now = date("H");
	//echo $hours_now
	$part_of_day = "suvaline päeva osa";
	// < > >= <= == != 
	if ($weekday_now <6){
		if($hours_now < 7){
			$part_of_day = "uneaeg";
		}
		if ($hours_now >=8 and $hours_now < 18){
			$part_of_day = "koolipäev";
		}
	}
	if ($weekday_now = 6 or $weekday_now = 7){
		$part_of_day = "nädalavahetus";
	}
	$semester_begin = new DateTime("2022-9-5");
	$semester_end = new DateTime("2022-12-18");
	$semester_duration = $semester_begin->diff($semester_end);
	$semester_duration_days = $semester_duration->format("%r%a");
	$from_semester_begin = $semester_begin->diff(new DateTime("now"));
	$from_semester_begin_days = $from_semester_begin->format ("%r%a");
	
	//juhuslik arv
	//küsin massiivi pikkust
	//echo count($weekdaynames_et);
	//echo $weekdaynames_et[mt_rand(0, count($weekdaynames_et) -1)];
	
	//juhuslik foto
	$photo_dir = "photos";
	//loen kataloogi sisu
	$all_files = array_slice(scandir($photo_dir), 2);
	//kontrollin kas foto on
	$allowed_photo_types = ["image/jpeg", "image/png"];
	//muutuja väärtuse suurendamine $muutuja = $muutuja + 1
	// $muutuja += 5
	// $muutuja ++  (kui on vaja liita 1) $muutuja -- (kui on vaja lahutada 1)
	/*for ($i = 0;$i < count($all_files); $i ++){
		echo $all_files[$i];
	}*/
 	$photo_files = [];
	foreach($all_files as $file_name){
		//echo $filename;
		$file_info = getimagesize($photo_dir ."/" .$file_name);
		//var_dump($file_info)
		if(isset($file_info["mime"])){
			if(in_array($file_info["mime"], $allowed_photo_types)){
				array_push($photo_files, $file_name);
			}
		}
	}
	//var_dump($photo_files);
	$photo_html = '<img src="' .$photo_dir ."/" . $photo_files[mt_rand(0, count($photo_files) - 1)] .'"';
	$photo_html .= ' alt = "Tallinn">';
	 //var_dump($_POST["todays_adjective_input"]);
	 $todays_adjective = "pole midagi sisestatud";
	if(isset($_POST["todays_adjective_input"]) and !empty($_POST["todays_adjective_input"])){
		 $todays_adjective = $_POST["todays_adjective_input"];
	}
		//loome rippmenüü valikud
		//<option value="0">tln_100.jpg</option>
		//<option value="1">tln_100.jpg</option>
		//<option value="2">tln_100.jpg</option>
		//<option value="3">tln_100.jpg</option>
		//<option value="4">tln_100.jpg</option>
		//<option value="5">tln_100.jpg</option>
		$select_html = null;
		for($i = 0;$i<count($photo_files); $i ++){
			$select_html .= '<option value="' .$i .'">';
			$select_html .= $photo_files[$i];
			$select_html .= '</option>';
		}
		if(isset($_POST["photo_select"]) and $_POST["photo_select"] >= 0){
			echo "Valiti pilt nr. " .$_POST["photo_select"];
		}
	$comment_error = null;
		//kas kilkiti päeva kommentaari nuppu
	if(isset($_POST["comment_submit"])){
		if(isset($_POST["comment_input"]) and !empty($_POST["comment_input"])){
			$comment = $_POST["comment_input"];
		} else {
			$comment_error = "Kommentaar jäi kirjutamata";
		}
		
		$grade = $_POST["grade_input"];
		
		if(empty($comment_error)){
			// loon andmebaasiga ühenduse
			$conn = new mysqli($server_host, $server_user_name, $server_password, $database);
			//määran suhtlemisel kasutatava kooditabeli
			$conn->set_charset("utf8");
			//valmistame ette andmete saatmise SQL käsu
			$stmt = $conn->prepare("INSERT INTO vp_daycomment_2 (comment, grade) values(?, ?)");
			echo $conn->error;
			//seome SQL käsu õigete andmetega
			//andmetüübid i - integer d - decimal s - string
			$stmt->bind_param("si", $comment, $grade);
			if($stmt->execute()){
				$grade = 7;
				$comment = null;
			}
			$stmt->execute();
			//sulgeme käsu
			$stmt->close();
			//sulgeme andmebaasi
			$conn->close();
		}
	}
	$login_error = null;
	if(isset($_POST["login_submit"])){
        //login sisse
		$login_error = sign_in($_POST["email_input"], $_POST["password_input"]);
	}
	
	
?>
<!DOCTYPE html>
<html lang="et">
<head>
	<meta charset="utf-8">
	<title><?php echo $author_name;?> kirjutas selle</title>
</head>
<body>

<img src="pics/vp_banner_gs.png">

<h1>reigo kurgpõld kirjutas selle</h1>
<p>See leht on loodud õppetöö raames ja ei sisalda tõsiseltvõetavalt sisu.</p>
<hr>
<h2>Logi sisse</h2>
<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	<input type="email" name="email_input" placeholder="Kasutajatunnus ehk e-post">
	<input type="password" name="password_input" placeholder="salasõna">
	<input type="submit" name="login_submit" value="Logi sisse"><span><strong><?php echo $login_error; ?></strong></span>
</form>
<p>Või <a href = "add_user.php"> loo</a> endale kasutaja</p>
<hr>
<p>õppetöö toimus <a href="https://www.tlu.ee/" target="_blank">Tallinna Ülikoolis</a> digitehnoloogiate instituudis</p>
<p>Lehe avamise hetk: <?php echo $weekdaynames_et[$weekday_now - 1].", ".$full_time_now?></p>
<p>Praegu on <?php echo $part_of_day ?> </p>
<p>Semestri pikkus on <?php echo $semester_duration_days;?> päeva, algusest on möödas <?php echo $from_semester_begin_days;?> päeva</p>
<img src="pics\tlu_36.jpg" alt="Tallinna Ülikooli Terra õppehoone">
<form method="POST">
	<label for="comment_input">Kommentaar tänase päeva kohta (140 tähte)</label>
	<br>
	<textarea id="comment_input" name="comment_input" cols="35" rows="4" placeholder="kommentaar"></textarea>
	<br>
	<label for="grade_input">Hinne tänasele päevale (0-10)</label>
	<input type="number" id="grade_input" name="grade_input" min="0" max="10" step="1" value="5">
	<input type="submit" id="comment_submit" name="comment_submit" value="Salvesta">
	<span><?php echo $comment_error; ?></span>
</form>
<form method="POST">
	<input type="text" id="todays_adjective_input" name = "todays_adjective_input" placeholder="Kirjuta siia omadussõna tänase päeva kohta">
	<input type="submit" id="todays_adjective_submit" value="Saada">
</form>
<p>Omadussõna tänase kohta: <?php echo $todays_adjective; ?></p>
<hr>
<form method="POST">
	<select id="photo_select" name="photo_select">
		<?php echo $select_html; ?>
	</select>
	<input type="submit" id="photo_submit" name="photo_submit" value="Määra foto">
</form>
<hr>
<?php echo $photo_html; ?>
<?php require_once "footer.php"; ?>
