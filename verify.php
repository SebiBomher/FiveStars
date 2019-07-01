<?php include('functions.php') ?>
<?php 
session_start(); 

if (!isset($_SESSION['email']) || !isset($_SESSION['name']) || !isset($_SESSION['surname'])) {
 $_SESSION['msg'] = "You must log in first";
 header('location: login.php');
}
$db = mysqli_connect('localhost', 'root', '', 'socialsite');
$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_segments = explode('/', $uri_path);
$idSession = $_SESSION['id'];
$usermail = $uri_segments[3];
$idhashedpassword = $uri_segments[4];
$query = "SELECT * FROM user WHERE Email='$usermail' AND Password='$idhashedpassword'";
$results = mysqli_query($db, $query);
if (mysqli_num_rows($results) == 1) {
  echo 'exista.';
  echo $usermail;
  echo $idhashedpassword;
  $query = "UPDATE user SET verify_email = '1' WHERE Email = '$usermail' AND Password = '$idhashedpassword'";
  mysqli_query($db, $query);
}
header('location: /fivestars/main.php');



?>

<!DOCTYPE html>
<html>
<head>
</head>
<body>
</div>
</body>
</html>