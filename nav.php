<?php
require_once("core.php");
initi18n("nav");
?>
<nav>
	<ul>
			<li><a id="home" href="index.php"><?=i18n("nav", "home")?></a></li>
			<?php
			if (isset($_SESSION['id']))
			{
			if (getrole() > 0)
			{
			?>
			<li><a href="dashboard.php"><?=i18n("nav", "dashboard")?></a></li>
			<?php
			} if (!isadmin()) {
			?>
			<li><a href="editprofile.php"><?=i18n("nav", "editprofile")?></a></li>
			<?php
			}
			?>
			<li><a href="contests.php"><?=i18n("nav", "contests")?></a></li>
			<li><a href="logout.php"><?=i18n("nav", "logout")?></a></li>
			<li class="stickright firstnavright" style="color:#57B9FF;"><b><?=i18n("nav", "salute", array(userdata('username')))?></b></li>
			<?php
			} else {
			?>
			<li><a href="register.php"><?=i18n("nav", "register")?></a></li>
			<?php
			}
			?>
	</ul>
</nav>