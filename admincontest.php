<?php
require_once("core.php");
initi18n("admincontest");
if (getrole())
{
$msg = "";
if (isset($_GET['msg']) && in_array($_GET['msg'], array("editsuccess", "addproblemsuccess", "editproblemsuccess", "deleteproblemsuccess"))) {
  $msg = "<p class='alert-success'>".i18n("global", "msg_".$_GET['msg'])."</p>";
}
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require ("head.php"); ?>
    <title><?=i18n("admincontest", "title")?> – <?=$appname?></title>
    <style>
    td, th {
      padding:5px;
    }
    table {
      border-collapse:collapse;
    }
    table, th, td {
      border: 1px solid black;
    }
    #modal-dialog-bg {
      opacity: 0.75;
      background: #fff;
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      z-index: 1002;
    }
    #modal-dialog {
      position: absolute;
      width: 490px;
      height: 314px;
      left: Calc(50% - 490px / 2);
      top: Calc(50% - 314px / 2);
      z-index: 1003;
      -webkit-box-shadow: 0 4px 16px rgba(0,0,0,.2);
      -moz-box-shadow: 0 4px 16px rgba(0,0,0,.2);
      box-shadow: 0 4px 16px rgba(0,0,0,.2);
      background: #fff;
      background-clip: padding-box;
      border: 1px solid rgba(0,0,0,.333);
      outline: 0;
      padding: 30px 42px;
    }
    #modal-dialog iframe {
      width: 100%;
      height: 100%;
      border: 0;
    }
    #modal-dialog iframe.loading {
      display: none;
    }
    #share {
      outline: 0;
      float: right;
    }
    div .text {
      position: relative;
    }
    #status_box {
      border: 1px solid rgb(126, 126, 126);
      border-radius: 5px;
      width: 125px;
      margin: 10px 0 0 0;
      padding: 5px;
      text-align: center;
      font-family: Arial;
      position: absolute;
      bottom: 10px;
      right: 10px;
    }

    #status_box.notstarted {
      background-color: rgb(213, 187, 146);
    }

    #status_box.finished {
      background-color: rgb(146, 203, 213);
    }

    #status_box.live {
      background-color: rgb(146, 213, 149);
    }

    #status_title {
      margin: 0;
      font-size: 14px;
    }

    #status_text {
      margin: 5px 0 0 0;
      font-size: 13px;
      line-height: 15px;
    }
    </style>
    <?php initi18n_js("share_js"); ?>
    <script src="js/share.js"></script>
  </head>
  <body>
    <div class="content">
      <?php require("nav.php"); ?>
    	<article>
        <?php anuncio(); ?>
        <?php if (getrole() > 0) {
          require("sidebar.php");
        ?>
        <div class="text right large">
        <?php } else { ?>
        <div class="text">
        <?php
        }
          if (!isset($_GET["id"]))
            die("<div class='alert-danger'>".i18n("admincontest", "nonexistent_contest")."</div>");
          $id = (INT)$_GET["id"];
          $query = mysqli_query($con, "SELECT * FROM contests WHERE id = '{$id}'");

          if (!mysqli_num_rows($query))
            die("<div class='alert-danger'>".i18n("admincontest", "nonexistent_contest")."</div>");

          $row = mysqli_fetch_assoc($query);

          $now = time();

          if ($now < $row["starttime"]) {
            $status = "notstarted";
          } elseif ($now > $row["endtime"]) {
            $status = "finished";
          } else {
            $status = "live";
          }
          $status_text = i18n("admincontest", "status_".$status);
          ?>
          <script>var contest = <?=(INT)$_GET["id"]?>;</script>
          <?=$msg?>
          <div class="float right">
            <div><button id="share" class="g-button g-button-share"><?=i18n("admincontest", "invite")?></button></div>
          </div>
          <h1><?=i18n("admincontest", "subtitle")?> <?=$row["name"]?></h1>
          <p><?php if (getrole() > 1) { ?><a href="addproblem.php?id=<?=$row["id"]?>"><span class="icon svg-ic_note_add_24px"></span></a> <a href="addproblem.php?id=<?=$row["id"]?>"><?=i18n("admincontest", "addproblem")?></a><?php } if (isadmin()) { ?> | <a href="editcontest.php?id=<?=$row["id"]?>"><span class="icon svg-ic_mode_edit_24px"></span></a> <a href="editcontest.php?id=<?=$row["id"]?>"><?=i18n("admincontest", "editcontest")?></a> | <a href="deletecontest.php?id=<?=$row["id"]?>"><span class="icon svg-ic_delete_24px"></span></a> <a href="deletecontest.php?id=<?=$row["id"]?>"><?=i18n("admincontest", "deletecontest")?></a><?php } if ($status != "notstarted") { ?> | <a href="judgecontest.php?id=<?=$row["id"]?>"><span class="icon svg-ic_thumbs_up_down_24px"></span></a> <a href="judgecontest.php?id=<?=$row["id"]?>"><?=i18n("admincontest", "judgecontest")?></a><?php } ?></p>

          <h3><?=i18n("admincontest", "problems")?></h3>
          <?php
          $problems_query = mysqli_query($con, "SELECT * FROM problems WHERE contest = '{$id}' ORDER BY num");
          if ($numrows = mysqli_num_rows($problems_query)) {
          ?>
          <table>
            <thead>
              <tr>
                <th><?=i18n("admincontest", "problems_id")?></th>
                <th><?=i18n("admincontest", "problems_title")?></th>
                <th><?=i18n("admincontest", "problems_small")?></th>
                <th><?=i18n("admincontest", "problems_large")?></th>
              </tr>
            </thead>
            <tbody>
          <?php
          $i = 0;
          while ($row = mysqli_fetch_assoc($problems_query)) {
            $io = json_decode($row["io"], true);
            if (getrole() > 1) {
              $added = "<td><a href='editproblem.php?id=".$row['id']."'><span class='icon svg-ic_mode_edit_24px'></span></a>".(isadmin() ? "<br><a href='deleteproblem.php?id=".$row['id']."'><span class='icon svg-ic_delete_24px'></span></a>" : "")."</td><td>".(($i != 0) ? "<a href='move.php?id=".$row['id']."&do=up'><span class='icon svg-ic_keyboard_arrow_up_24px'></span></a>" : "")."<br>".(($i < ($numrows - 1)) ? "<a href='move.php?id=".$row['id']."&do=down'><span class='icon svg-ic_keyboard_arrow_down_24px'></span></a>" : (isadmin() ? "" : "<br>"))."</td>".(isadmin() ? "<td><a href='intermove.php?id=".$row['id']."'><span class='icon svg-ic_open_with_24px'></span></a></td>" : "");
            } else {
              $added = "";
            }
            $i++;
            echo "<tr><td>".$row["id"]."</td><td><a href='problem.php?id=".$row["id"]."'>".htmlspecialchars($row["name"])."</a></td><td>".$io["pts"]["small"]." pts</td><td>".$io["pts"]["large"]." pts</td>".$added."</tr>";
          }
          ?>
            </tbody>
          </table>
          <?php
          } else {
            echo "<p style='text-align:center;'>".i18n("admincontest", "noproblems")."</p>";
          }
          ?>
          <div id="status_box" class="<?=$status?>">
            <h3 id="status_title"><?=i18n("admincontest", "status")?></h3>
            <p id="status_text"><?=$status_text?></p>
          </div>
    		</div>
    	</article>
    </div>
    <div id="modal-dialog-bg" hidden></div>
    <div id="modal-dialog" hidden></div>
  </body>
</html>
<?php
}
else
{
  header('HTTP/1.0 404 Not Found');
}
?>