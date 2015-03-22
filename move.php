<?php
require_once("core.php");
$pregunta = (INT)$_GET['id'];
$query = mysqli_query($con, "SELECT * FROM problems WHERE id = '".$pregunta."' LIMIT 1");
$row = mysqli_fetch_assoc($query);
$numrows = mysqli_num_rows(mysqli_query($con, "SELECT * FROM problems WHERE contest='".$row['contest']."'"));
if ($_GET['do'] == "up")
{
	if ($row['num'] == "1")
	{
		echo "Es el primero!";
	}
	else
	{
		$numant2 = $row['num']-1;
		$pregunta2 = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM problems WHERE num = '".$numant2."' AND contest = '".$row['contest']."' LIMIT 1"));
		$var1 = array("newn" => $row['num']-1, "id" => $pregunta);
		$var2 = array("newn" => $row['num'], "id" => $pregunta2['id']);
		if (mysqli_query($con, "UPDATE problems SET num = '".$var1['newn']."' WHERE id = '".$var1['id']."' LIMIT 1"))
		{
			if (mysqli_query($con, "UPDATE problems SET num = '".$var2['newn']."' WHERE id = '".$var2['id']."' LIMIT 1"))
			{
				header("Location: admincontest.php?id=".$row["contest"]);
			}
			else
			{
				echo "Mysqli1 error: ".mysqli_error($con);
			}
		}
		else
		{
			echo "Mysqli1 error: ".mysqli_error($con);
		}
	}
}
if ($_GET['do'] == "down")
{
	if ($numrows == $row['num'])
	{
		echo "Es el último!";
	}
	else
	{
		$numant2 = $row['num']+1;
		$pregunta2 = mysqli_fetch_assoc(mysqli_query($con, "SELECT * FROM problems WHERE num = '".$numant2."' AND contest = '".$row['contest']."' LIMIT 1"));
		$var1 = array("newn" => $row['num']+1, "id" => $pregunta);
		$var2 = array("newn" => $row['num'], "id" => $pregunta2['id']);
		if (mysqli_query($con, "UPDATE problems SET num = '".$var1['newn']."' WHERE id = '".$var1['id']."' LIMIT 1"))
		{
			if (mysqli_query($con, "UPDATE problems SET num = '".$var2['newn']."' WHERE id = '".$var2['id']."' LIMIT 1"))
			{
				header("Location: admincontest.php?id=".$row["contest"]);
			}
			else
			{
				echo "Mysqli1 error: ".mysqli_error($con);
			}
		}
		else
		{
			echo "Mysqli1 error: ".mysqli_error($con);
		}
	}
}
?>