<?php
require_once("core.php");
if (getrole(3)) {
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "unlinked")
  $msg = '<p class="alert-danger">Files unlinked successfully</p>';
?>
<!DOCTYPE html>
<html>
<head>
<?php require ("head.php"); ?>
<title>Debug - <?php echo $appname; ?></title>
</head>
<body>
<div class="content">
	<?php include "nav.php"; ?>
	<article>
		<?php anuncio(); ?>
		<?php require("sidebar.php"); ?>
		<div class="text right large">
		<?php
		if (isset($_GET["action"]) && ($_GET["action"] == "cleanfiles" || $_GET["action"] == "missingfiles")) {
			$files_solutions = array();
			$files_img = array();

			// First let's begin with submissions
			$query = mysqli_query($con, "SELECT solution FROM submissions");
			while ($row = mysqli_fetch_assoc($query)) {
				if (!empty($row["solution"])) {
					$solution = json_decode($row["solution"], true);
					$files_solutions[] = $solution["output"];
					$files_solutions[] = $solution["sourcecode"];
				}
			}

			$query = mysqli_query($con, "SELECT io FROM problems");
			while ($row = mysqli_fetch_assoc($query)) {
				if (!empty($row["io"])) {
					$solution = json_decode($row["io"], true);
					foreach ($solution["files"] as $file) {
						$files_img[] = $file;
					}
				}
			}

			echo "<p><a href='debug.php'>Go back</a></p>";
		}

		if (isset($_GET["action"]) && $_GET["action"] == "cleanfiles") {
			$dir = new DirectoryIterator("uploaded_solutions/");
			foreach ($dir as $fileinfo) {
			    if (!$fileinfo->isDot()) {
			        if (!in_array($fileinfo->getFilename(), $files_solutions)) {
			        	if (!unlink("uploaded_solutions/".$fileinfo->getFileName())) {
							echo "<p class='alert-danger'>Couldn't delete file uploaded_solutions/".$fileinfo->getFileName()."</p>";
						} else {
							//echo "<p class='alert-success'>File uploaded_solutions/".$fileinfo->getFileName()." successfully unlinked!</p>";
						}
			        }
			    }
			}

			$dir2 = new DirectoryIterator("uploaded_img/");
			foreach ($dir2 as $fileinfo) {
			    if (!$fileinfo->isDot()) {
			        if (!in_array($fileinfo->getFilename(), $files_img)) {
			        	if (!unlink("uploaded_img/".$fileinfo->getFileName())) {
							echo "<p class='alert-danger'>Couldn't delete file uploaded_img/".$fileinfo->getFileName()."</p>";
						} else {
							//echo "<p class='alert-success'>File uploaded_img/".$fileinfo->getFileName()." successfully unlinked!</p>";
						}
			        }
			    }
			}

			echo "<p>Finished cleaning</p>";
		}

		if (isset($_GET["action"]) && $_GET["action"] == "missingfiles") {
			foreach ($files_solutions as $file) {
				if (!file_exists("uploaded_solutions/".$file)) {
					echo "<p class='alert-danger'>File uploaded_solutions/".$file." is referenced but doesn't exist.</p>";
				}
			}

			foreach ($files_img as $file) {
				if (!file_exists("uploaded_img/".$file)) {
					echo "<p class='alert-danger'>File uploaded_img/".$file." is referenced but doesn't exist.</p>";
				}
			}

			echo "<p>Finished checking for missing files.</p>";
		}

		if (!isset($_GET["action"])) {
		?>
			<h1>Debug <sup style="color: orange;">WARNING</sup></h1>
			<p><a href="debug.php?action=cleanfiles">Clean Files</a></p>
			<p><a href="debug.php?action=missingfiles">Check Missing Files</a></p>
		<?php
		}
		?>
		</div>
	</article>
</div>
</body>
</html>
<?php
} else {
	header('HTTP/1.0 404 Not Found');
}
?>