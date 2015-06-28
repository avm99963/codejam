<?php
require_once("core.php");
if (getrole())
{
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "editsuccess")
  $msg = '<p class="alert-success">Competición editada satisfactoriamente</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "addproblemsuccess")
  $msg = '<p class="alert-success">Problema añadido satisfactoriamente</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "editproblemsuccess")
  $msg = '<p class="alert-success">Problema editado satisfactoriamente</p>';
if (isset($_GET['msg']) && $_GET['msg'] == "deleteproblemsuccess")
  $msg = '<p class="alert-success">Problema eliminado satisfactoriamente</p>';
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require ("head.php"); ?>
    <title>Administrar competición – <?=$appname?></title>
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
            die("<div class='alert-danger'>Esta competición no existe</div>");
          $id = (INT)$_GET["id"];
          $query = mysqli_query($con, "SELECT * FROM contests WHERE id = '{$id}'");

          if (!mysqli_num_rows($query))
            die("<div class='alert-danger'>Esta competición no existe</div>");

          $row = mysqli_fetch_assoc($query);

          $now = time();

          if ($now < $row["starttime"]) {
            $status = "notstarted";
            $status_text = "Todavía no ha empezado";
          } elseif ($now > $row["endtime"]) {
            $status = "finished";
            $status_text = "Ya ha acabado";
          } else {
            $status = "live";
            $status_text = "¡En vivo!";
          }
          ?>
          <script>var contest = <?=(INT)$_GET["id"]?>;</script>
          <?=$msg?>
          <div class="float right">
            <div><button id="share" class="g-button g-button-share">Invitar</button></div>
          </div>
          <h1>Administrar <?=$row["name"]?></h1>
          <p><?php if (getrole() > 1) { ?><a href="addproblem.php?id=<?=$row["id"]?>"><span class="icon svg-ic_note_add_24px"></span></a> <a href="addproblem.php?id=<?=$row["id"]?>">Añadir problema</a><?php } if (isadmin()) { ?> | <a href="editcontest.php?id=<?=$row["id"]?>"><span class="icon svg-ic_mode_edit_24px"></span></a> <a href="editcontest.php?id=<?=$row["id"]?>">Editar datos de la competición</a> | <a href="deletecontest.php?id=<?=$row["id"]?>"><span class="icon svg-ic_delete_24px"></span></a> <a href="deletecontest.php?id=<?=$row["id"]?>">Eliminar competición</a><?php } if ($status != "notstarted") { ?> | <a href="judgecontest.php?id=<?=$row["id"]?>"><span class="icon svg-ic_thumbs_up_down_24px"></span></a> <a href="judgecontest.php?id=<?=$row["id"]?>">Juzgar respuestas</a><?php } ?></p>

          <h3>Problemas</h3>
          <?php
          $problems_query = mysqli_query($con, "SELECT * FROM problems WHERE contest = '{$id}' ORDER BY num");
          if ($numrows = mysqli_num_rows($problems_query)) {
          ?>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Small input</th>
                <th>Large input</th>
              </tr>
            </thead>
            <tbody>
          <?php
          $i = 0;
          while ($row = mysqli_fetch_assoc($problems_query)) {
            $io = json_decode($row["io"], true);
            if (getrole() > 1) {
              $added = "<td><a href='editproblem.php?id=".$row['id']."'><span class='icon svg-ic_mode_edit_24px'></span></a><br><a href='deleteproblem.php?id=".$row['id']."'><span class='icon svg-ic_delete_24px'></span></a></td><td>".(($i != 0) ? "<a href='move.php?id=".$row['id']."&do=up'><span class='icon svg-ic_keyboard_arrow_up_24px'></span></a>" : "")."<br>".(($i < ($numrows - 1)) ? "<a href='move.php?id=".$row['id']."&do=down'><span class='icon svg-ic_keyboard_arrow_down_24px'></span></a>" : "")."</td><td><a href='intermove.php?id=".$row['id']."'><span class='icon svg-ic_open_with_24px'></span></a></td>";
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
            echo "<p style='text-align:center;'>No hay ningún problema en esta competición :-O</p>";
          }
          ?>
          <div id="status_box" class="<?=$status?>">
            <h3 id="status_title">Estado</h3>
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