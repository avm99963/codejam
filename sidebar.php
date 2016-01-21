<?php
require_once("core.php");
initi18n("sidebar");
?>
<aside class="text left small">
	<p><span class="icon svg-ic_dashboard_24px"></span> <a href="dashboard.php"><?=i18n("sidebar", "dashboard")?></a></p>
	<?php
	if (isadmin()) {
	?>
	<p><span class="icon svg-ic_group_24px"></span> <a href="users.php"><?=i18n("sidebar", "users")?></a></p>
	<?php
	}
	?>
	<p class="withoutmarginbottom"><span class="icon svg-ic_class_24px"></span> <a href="contests.php"><?=i18n("sidebar", "contests")?></a></p>
		<?php
		$query2 = mysqli_query($con, "SELECT * FROM contests") or die(mysqli_error($con));
		if (mysqli_num_rows($query2))
		{
		while($row = mysqli_fetch_assoc($query2))
		{
			echo '<p class="padding40 withoutmarginbottom" style="margin-top:5px;"><a href="contest.php?id='.$row['id'].'">'.$row['name'].'</a> [<a href="admincontest.php?id='.$row['id'].'">'.i18n("sidebar", "admin_contest").'</a>] [<a href="leaderboard.php?id='.$row['id'].'">'.i18n("sidebar", "leaderboard_contest").'</a>]</p>';
			
		}
		}
	if (getrole() == 3) {
	?>
		<p class="padding40" style="margin-top:5px;"> <a href="newcontest.php"><?=i18n("sidebar", "create_contest")?></a></p>
	<p><span class="icon svg-ic_settings_24px"></span> <a href="configuration.php"><?=i18n("sidebar", "configuration")?></a></p>
	<?php
	}
	?>
	<p><span class="icon svg-ic_security_24px"></span> <a href="2stepverification.php"><?=i18n("sidebar", "2stepverification")?></a></p>
	<p><span class="icon svg-ic_stars_24px"></span> <a href="promote.php"><?=i18n("sidebar", "promote")?></a></p>
	<?php
	if (getrole() == 3) {
	?>
	<p><span class="icon svg-ic_bug_report_24px"></span> <a href="debug.php"><?=i18n("sidebar", "debug")?></a></p>
	<?php
	}
	?>
</aside>