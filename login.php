<?php
require_once('core.php');
$username = mysqli_real_escape_string($con, $_POST['username']);
if (empty($username) || empty($_POST["password"])) {
	header("Location: index.php?msg=empty");
	echo "Please fill in all form.";
} else {
	$query = mysqli_query($con, "SELECT * FROM users WHERE username='".$username."'");
	if (mysqli_num_rows($query)) {
		$row = mysqli_fetch_assoc($query) or die(mysqli_error($con));
		if (password_verify($_POST["password"], $row["password"])) {
			if ($twostepverification = twostepverification($row['id'])) {
				$_SESSION['prov_id'] = $row['id'];
				if ($twostepverification < 3) {
					header("Location: secondfactor.php");
				} else {
					die("<a href='https://www.youtube.com/watch?v=rp8hvyjZWHs'>Trust me, i'm an engineer ! what the fuck did just happened here ?</a>");
				}
			} else {
				$_SESSION['id'] = $row['id'];
				header("Location: index.php");
			}
		} else {
			header("Location: index.php?msg=loginwrong");
			echo "User data incorrect :-(";
		}
	} else {
		header("Location: index.php?msg=loginwrong");
		echo "User data incorrect :-(";
	}
}
?>