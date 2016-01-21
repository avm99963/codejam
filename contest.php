<?php
require_once("core.php");
require_once("contest_helper.php");

initi18n("contest");

$contest = (int)$_GET["id"];

$query = mysqli_query($con, "SELECT * FROM contests WHERE id = ".$contest);

if (!mysqli_num_rows($query)) {
  die(i18n("contest", "contestdoesntexist"));
}

$row = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="css/contest.css">
    <link rel="icon" href="img/favicon.ico">
    <title><?=i18n("contest", "title")?> – <?=$row["name"]?> – <?=$appname?></title>
    <?php initi18n_js("contest_js"); ?>
    <script src="js/contest.js"></script>
    <script>var contest = <?=(INT)$_GET["id"]?>;</script>
    <?php
    $now = time();

    if ($now > $row["endtime"]) {
    ?>
    <script>
      window.addEventListener('load', function() {
        toast.create('<?=i18n("contest", "contestended")?>', 5000);
        $('#time').parentNode.innerHTML = '<span style=\'text-align: center; display: block;\'><?=i18n("contest", "contestended2")?></span>';
      });
      var competitionhasendedyey = true;
    </script>
    <?php
    } elseif ($now >= $row["starttime"]) {
    ?>
    <script>
      var contestends = <?=$row["endtime"]?>;
      var competitionhasendedyey = false;
      window.addEventListener('load', doTimer);
    </script>
    <?php
    }
    if ($now < $row["starttime"] && getrole() > 0) {
    ?>
    <script>
      window.addEventListener('load', function() {
        var buttons = $all(".solve_btn");
        for (var i = 0; i< buttons.length; i++) {
          $(".solve_msg[data-problem-id='"+buttons[i].getAttribute("data-problem-id")+"'][data-type='"+buttons[i].getAttribute("data-type")+"']").innerText = "<?=i18n("contest", "contestnotstarted")?>";
          buttons[i].parentNode.removeChild(buttons[i]);
        }
      });
    </script>
    <?php        
    }
    ?>
  </head>
  <body>
    <?php
    if (!loggedin()) {
      die(i18n("global", "notloggedin"));
    }
    ?>
    <header>
      <div id="mainbar">
        <h1 id="title"><?=$row["name"]?></h1>
      </div>
      <nav>
        <ul>
          <?php
          if ($now < $row["starttime"] && getrole() == 0) {
            die("<span style='color: red;'>".i18n("contest", "contestnotstarted")."</span>");
          }
          if (!isinvited($row["id"]) && getrole() == 0) {
            die("<span style='color: red;'>".i18n("contest", "notinvited")."</span>");
          }
          $query2 = mysqli_query($con, "SELECT * FROM problems WHERE contest = ".$contest." ORDER BY num");
          $problems = array();
          if (mysqli_num_rows($query2)) {
            for ($i = 0; $i < mysqli_num_rows($query2); $i++) {
              $problems[$i] = mysqli_fetch_assoc($query2);
              if ($i == 0) {
                $active = ' class="active"';
              } else {
                $active = '';
              }
              echo '<li'.$active.' data-problem-id="'.$problems[$i]["id"].'">'.$problems[$i]["name"].'</li>';
            }
          } else {
            die("<span style='color: red;'>".i18n("contest", "noproblems")."</span>");
          }
          ?>
        </ul>
      </nav>
    </header>
    <main>
      <section>
        <?php
        if ($now <= $row["endtime"]) {
          $query3 = mysqli_query($con, "SELECT * FROM submissions WHERE contest = {$contest} AND user_id = {$_SESSION['id']}");

          $submissions = array();

          foreach ($problems as $problem) {
            $submissions[$problem["id"]] = array(
              "small" => array(),
              "large" => array()
            );
          }

          if (mysqli_num_rows($query3)) {
            for ($i = 0; $i < mysqli_num_rows($query3); $i++) {
              $submission = mysqli_fetch_assoc($query3);
              if ((($submission["time"] + (($submission["type"] == 0) ? 4 : 8) * 60) >= $now) && !isset($submission["valid"])) {
                $submissions[$submission["problem"]][(($submission["type"] == 0) ? "small" : "large")][] = $submission;
              }
            }
          }
        }

        foreach ($problems as $i => $problem) {
          if ($i != 0) {
            $hidden = " hidden";
          } else {
            $hidden = "";
          }
          $io = json_decode($problem["io"], true);
        ?>
        <div class="problem" data-problem-id="<?=$problem["id"]?>"<?=$hidden?>>
          <h2><?=$problem["name"]?></h2>
          <?php
          if ($now > $row["endtime"]) {
          ?>
          <p style="color: gray;"><?=i18n("contest", "openforpractice")?></p>
          <?php
          }
          ?>
          <table class="solve">
            <?php
            foreach(array("small", "large") as $type) {
            $type_file = (($type == "small") ? "in1_sinput" : "in_linput");
            if ($now <= $row["endtime"]) {
              if (count($submissions[$problem["id"]][$type])) {
                $submissionactive = true;
                $submission = array_pop($submissions[$problem["id"]][$type]);
                if ($type == "small") {
                  $try = $submission["try"];
                } else {
                  $try = null;
                }
              } else {
                $submissionactive = false;
              }
            } else {
              $submissionactive = false;
            }
            ?>
            <tr>
              <td>
                <?=i18n("contest", $type."input")?><br>
                <?=$io["pts"][$type]?> <?=i18n("contest", "pts")?>
              </td>
              <td>
                <button class="solve_btn" data-problem-id="<?=$problem["id"]?>" data-type="<?=$type?>"<?=($submissionactive ? " hidden" : "")?>><?=i18n("contest", "solve".$type)?></button>
                <div class="solve_container" data-problem-id="<?=$problem["id"]?>" data-type="<?=$type?>"<?=($submissionactive ? "" : " hidden")?>>
                  <?php
                  if ($now > $row["endtime"]) {
                  ?>
                  <div class="file_download"><img src="img/file.gif"> <a href="download.php?problem=<?=$problem["id"]?>&type=<?=$type_file?>" data-problem-id="<?=$problem["id"]?>" data-type="<?=$type?>"><?=i18n("contest", "downloadfile", array(getfilename($problem["id"], $type_file).".in"))?></a></div>
                  <div class="output_file"><?=i18n("contest", "output")?>: <input type="file" class="output"></div>
                  <div class="source_file notneeded"><?=i18n("contest", "sourcecode")?>: <?=i18n("contest", "notneeded")?></div>
                  <div class="navigation"><button class="submit"><?=i18n("contest", "sendsolution")?></button> <button class="hide"><?=i18n("contest", "hide")?></button></div>
                  <?php
                  } else {
                  ?>
                  <div class="file_download">
                  <?php  
                  if ($submissionactive) {
                  ?>
                  <img src="img/file.gif"> <a href="download.php?problem=<?=$problem["id"]?>&type=<?=$type_file?>" data-problem-id="<?=$problem["id"]?>" data-type="<?=$type?>"><?=i18n("contest", "downloadfile", array(getfilename($problem["id"], convertfileshorthand($type, "in", $try)).".in"))?></a>
                  <?php
                  }
                  ?>
                  </div>
                  <div class="output_file"><?=i18n("contest", "output")?>: <input type="file" class="output"></div>
                  <div class="source_file"><?=i18n("contest", "sourcecode")?>: <input type="file" class="sourcecode"></div>
                  <div class="navigation">
                    <button class="submit"><?=i18n("contest", "sendsolution")?></button>
                    <div class="time"></div>
                  </div>
                  <?php
                  }
                  ?>
                </div>
                <div class="solve_msg" data-problem-id="<?=$problem["id"]?>" data-type="<?=$type?>"></div>
                <?php
                if ($submissionactive) {
                ?>
                <script>
                counters["<?=($problem["id"]."-".$type)?>"] = {
                  problemId: <?=$problem["id"]?>,
                  type: "<?=$type?>",
                  endtime: <?=($submission["time"] + (($type == "small") ? 4 : 8) * 60)?><?php if ($type == "small") { ?>,
                  ntry: <?=$submission["try"]?><?php } ?>
                };
                </script>
                <?php
                }
                ?>
              </td>
            </tr>
            <?php
            }
            ?>
          </table>
          <div class="description"><?=$problem["description"]?></div>
        </div>
        <?php
        }
        ?>
      </section>
      <aside>
        <details open>
          <summary><?=i18n("contest", "time")?></summary>
          <div class="detailed">
            <span id="time"></span>
          </div>
        </details>
        <details open>
          <summary><?=i18n("contest", "stats")?></summary>
          <div class="detailed stats">
            <p><?=i18n("contest", "rank")?>: <span id="rank" class="value">--</span></p>
            <p><?=i18n("contest", "score")?>: <span id="score" class="value">--</span></p>
          </div>
        </details>
        <details open>
          <summary><?=i18n("contest", "submissions")?></summary>
          <div class="detailed">
            <?php
            foreach ($problems as $i => $problem) {
              $io = json_decode($problem["io"], true);
              echo '<div class="submission" data-problem-id="'.$problem["id"].'"><div class="title">'.$problem["name"].'</div><table><tr><td>'.$io["pts"]["small"].'pt</td><td class="small_submission"></td></tr></table><table><tr><td>'.$io["pts"]["large"].'pt</td><td class="large_submission"></td></tr></table></div>';
            }
            ?>
          </div>
        </details>
        <details open>
          <summary><?=i18n("contest", "topscores")?></summary>
          <div class="detailed nopadding">
            <table id="topscores">
              <tbody>
              </tbody>
            </table>
            <span id="leaderboardlink"><a href="leaderboard.php?id=<?=$contest?>" target="_blank"><?=i18n("contest", "leaderboard")?></a></span>
          </div>
        </details>
      </aside>
    </main>
  </body>
</html>