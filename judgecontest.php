<?php
require_once("core.php");
require_once("contest_helper.php");
if (getrole())
{
$msg = "";
if (isset($_GET['msg']) && $_GET['msg'] == "editsuccess")
  $msg = '<p class="alert-success">Competición editada satisfactoriamente</p>';
?>
<!DOCTYPE html>
<html>
  <head>
    <?php require ("head.php"); ?>
    <title>Juzgar soluciones – <?=$appname?></title>
    <style>
    table {
      width: 100%;
      border-top: 0.1em solid #c3d9ff;
      border-bottom: 0.1em solid #c3d9ff;
      border-collapse: collapse;
    }
    thead {
      text-align: left;
      border-bottom: 1px solid #cccccc;
    }
    td, th {
      padding: 3px 6px;
    }
    tbody tr:nth-child(odd) {
      background: #efefef;
    }
    </style>
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
            die("<div class='alert-danger'>Esta competición todavía no ha empezado</div>");
          }

          $query3 = mysqli_query($con, "SELECT id, name, io FROM problems WHERE contest = {$id}");

          if (mysqli_num_rows($query3)) {
            $problems = array();
            for ($i = 0; $i < mysqli_num_rows($query3); $i++) {
              $problem = mysqli_fetch_assoc($query3);
              $problems[$problem["id"]] = $problem;
              $problems[$problem["id"]]["io"] = json_decode($problems[$problem["id"]]["io"], true);
            }
          } else {
            die("<div class='alert-danger'>Esta competición no tiene problemas</div>");
          }
          ?>
          <?=$msg?>
          <h1><a href="admincontest.php?id=<?=$_GET["id"]?>"><span class='icon svg-ic_chevron_left_24px'></span></a> <span>Juzgar respuestas de <?=$row["name"]?></span></h1>
          <?php
          $query2 = mysqli_query($con, "SELECT * FROM submissions WHERE contest = {$id}");
          if (mysqli_num_rows($query2)) {
            $leaderboard = leaderboard($id);
            ?>
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Tiempo de inicio</th>
                  <th>Tiempo de envío</th>
                  <th>Username</th>
                  <th>Problema</th>
                  <th>Tipo</th>
                  <th>Válido</th>
                  <th>Juzgado</th>
                  <th>Código fuente</th>
                  <th>Salida</th>
                  <th>Juzgar</th>
                </tr>
              </thead>
              <tbody>
            <?php
            while ($row2 = mysqli_fetch_assoc($query2)) {
              ?>
              <tr>
                <td><?=$row2["id"]?></td>
                <td><?=format_time(($row2["time"] - $row["starttime"]))?></td>
                <td><?=((!empty($row2["timesent"])) ? format_time(($row2["timesent"] - $row["starttime"])) : "--")?></td>
                <td><?=userdata("username", $row2["user_id"])?></td>
                <td><?=$problems[$row2["problem"]]["name"]?></td>
                <td><?=(($row2["type"] == 1) ? "large" : "small-".$row2["try"])?></td>
                <td><?=(($row2["valid"] == 1) ? "<img src='img/checkmark.png'>" : "")?></td>
                <td><?=(($row2["judged"] === "1") ? "<img src='img/thumb_up.svg' style='width:15px'>" : (($row2["judged"] === "0") ? "<img src='img/thumb_down.svg' style='width:15px'>" : ""))?></td>
                <td><a href="submission_download.php?id=<?=$row2["id"]?>&file=sourcecode"><img src="img/download.png"></a></td>
                <td><a href="submission_download.php?id=<?=$row2["id"]?>&file=output"><img src="img/download.png"></a></td>
                <td>
                <?php
                if ($row2["type"] == 0 && $leaderboard[array_search_multidimensional($leaderboard, "user_id", $row2["user_id"])]["submissions"][$row2["problem"]]["small"]["count"] != $row2["try"]) {
                  // Not shown
                } else {
                ?>
                  <a href="judgesubmission.php?id=<?=$row2["id"]?>&judge=1"><img src="img/thumb_up.svg" style="width:15px"></a> <a href="judgesubmission.php?id=<?=$row2["id"]?>&judge=0"><img src="img/thumb_down.svg" style="width:15px"></a><?=(isset($row2["judged"]) ? ' <a href="judgesubmission.php?id='.$row2["id"].'&judge=2"><img src="img/cross.svg" style="width:15px"></a>' : "")?>
                </td>
                <?php
                }
                ?>
              </tr>
              <?php
            }
            ?>
              </tbody>
            </table>
            <?php
          } else {
            echo "No hay ningun envío";
          }
          ?>
    		</div>
    	</article>
    </div>
  </body>
</html>
<?php
}
else
{
  header('HTTP/1.0 404 Not Found');
}
?>