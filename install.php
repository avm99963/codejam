<?php require_once("core.php"); ?>
<!DOCTYPE html>
<html>
<head>
  <?php require ("head.php"); ?>
  <title>Install <?php $appname; ?></title>
</head>
<body>
<div class="content">
	<article>
		<div class="text" style='margin-top:10px;'>
		<h1 style='text-align:center;'>Install!</h1>
<?php
if (isset($_GET['install']) && $_GET['install'] == "1") {
  $sql = array();

  $sql["users"] = "CREATE TABLE users 
  (
  id INT(13) NOT NULL AUTO_INCREMENT, 
  PRIMARY KEY(id),
  username VARCHAR(50),
  name VARCHAR(50),
  surname VARCHAR(100),
  email VARCHAR(100),
  role INT(2),
  password VARCHAR(50)
  )";

  $sql["contests"] = "CREATE TABLE contests 
  (
  id INT(13) NOT NULL AUTO_INCREMENT, 
  PRIMARY KEY(id),
  name VARCHAR(50),
  description VARCHAR(1000),
  privacy INT(1),
  starttime VARCHAR(13),
  endtime VARCHAR(13)
  )";

  $sql["problems"] = "CREATE TABLE problems 
  (
  id INT(13) NOT NULL AUTO_INCREMENT, 
  PRIMARY KEY(id),
  name VARCHAR(100),
  description TEXT,
  contest INT(13),
  num INT(13),
  io TEXT
  )";
  $sql["configuration"] = "CREATE TABLE configuration 
  (
  starred_contest INT(13),
  contests_link INT(1)
  )";
  $sql["securitykeys"] = "CREATE TABLE securitykeys (
    id integer primary key AUTO_INCREMENT,
    user_id integer,
    keyHandle varchar(255),
    publicKey varchar(255),
    certificate text,
    counter integer,
    dateadded integer,
    deviceadded varchar(255),
    lastuseddate integer,
    lastuseddevice varchar(255)
  )";
  $sql["submissions"] = "CREATE TABLE submissions (
    id integer primary key AUTO_INCREMENT,
    user_id integer,
    contest integer,
    problem integer,
    type integer,
    try integer,
    solution text,
    time integer,
    timesent integer,
    valid integer(1),
    judged integer(1)
  )";
  $sql["invitations"] = "CREATE TABLE invitations (
    id integer primary key AUTO_INCREMENT,
    user_id integer,
    contest integer
  )";
  $sql["2stepverification"] = "CREATE TABLE 2stepverification (
    id integer primary key AUTO_INCREMENT,
    user_id integer,
    enabled integer(1),
    secret varchar(32)
  )";
  foreach ($sql as $key => $query) {
    if (mysqli_query($con, $query)) {
      echo "<p style='color:green;'>Table '".$key."' created successfully.</p>";
    } else {
      die ("<p style='color:red;'>Error creating table '".$key."': " . mysqli_error($con) . "</p>");
    }
  }
  if(!mkdir("uploaded_img")) {
      echo "<p style='color:red;'>Error creating the folder uploaded_img. No permission to make it? Please, create it yourself.</p>";
  } else {
  	echo "<p style='color:green;'>Folder for problems input/output files created.</p>";
  }
  if(!mkdir("uploaded_solutions")) {
      echo "<p style='color:red;'>Error creating the folder uploaded_solutions. No permission to make it? Please, create it yourself.</p>";
  } else {
    echo "<p style='color:green;'>Folder for solution files created.</p>";
  }
  $username = htmlspecialchars(mysqli_real_escape_string($con, $_POST['username']));
  $name = htmlspecialchars(mysqli_real_escape_string($con, $_POST['name']));
  $surname = htmlspecialchars(mysqli_real_escape_string($con, $_POST['surname']));
  $email = mysqli_real_escape_string($con, $_POST['email']);
  $password = mysqli_real_escape_string($con, $_POST['password']);
  $sql6 = "INSERT INTO users (username, name, surname, email, role, password) VALUES ('".$username."', '".$name."', '".$surname."', '".$email."', 3, '".password_hash($_POST["password"], PASSWORD_DEFAULT)."')";
    if (mysqli_query($con,$sql6))
    {
    echo "<p style='color:green;'>Admin user created.</p>";
    }
  else
    {
    die ("<p style='color:red;'>Error creating the admin user: " . mysqli_error($con) . "</p>");
    }
  echo "<p style='color:orange;'>Please, delete the file install.php!</p>";
  echo "<p><a href='index.php'>Go back and login with the data that you provided</a></p>";
} else {
  // Select * from table_name will return false if the table does not exist.
  $val = mysqli_query($con, "SELECT * FROM users");
  if($val !== FALSE) {
  	echo "<p>The app is already installed!</p>";
  } else {
  ?>
  		<p>Welcome to the installer! Fill in your hyperadmin user data and click continue to create the Database :-)</p>
          <form action="install.php?install=1" method="POST" id="install-form">
              <p><label for="username">Usuario</label>: <input type="text" name="username" id="username" required="required" maxlength="50"></p>
              <p><label for="name">Nombre</label>: <input type="text" name="name" id="name" required="required" maxlength="50"></p>
              <p><label for="surname">Apellidos</label>: <input type="text" name="surname" id="surname" required="required" maxlength="100"></p>
              <p><label for="email">Email</label>: <input type="email" name="email" id="email" required="required" maxlength="100"></p>
              <p><label for="password">Contraseña</label>: <input type="password" name="password" id="password" required="required" maxlength="50"></p>
  		        <p><input type="submit" value="Install now!" class="button-link"></p>
          </form>
  <?php
  }
}
?>
		</div>
	</article>
</div>
</body>
</html>