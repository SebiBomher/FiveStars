<?php include('functions.php') ?>
<?php 
session_start(); 

if (!isset($_SESSION['email']) || !isset($_SESSION['name']) || !isset($_SESSION['surname'])) {
 $_SESSION['msg'] = "You must log in first";
 header('location: login.php');
}
$idSession = $_SESSION['id'];
$profile = new Profile();
$server = new Functions();
$profile = $server->get_profile($idSession);
$functions = new Functions();
?>

<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>
  <?php 
  $functions->show_navigationbar($idSession);
  $functions->show_chat($idSession);
  ?>


<table class="table position-absolute" style="z-index: -2;">
  <thead>
    <tr>
      <th scope="col">Photo</th>
      <th scope="col">Name</th>
    </tr>
  </thead>
  <tbody>

    <?php
    $db = mysqli_connect('localhost', 'root', '', 'socialsite');
    if (isset($_POST['search'])){
      $query = mysqli_real_escape_string($db, $_POST['query']);
      $sql = "SELECT * FROM user WHERE name LIKE '%$query%' OR surname LIKE '%$query%'";
      $result = mysqli_query($db, $sql);
      while ($row = mysqli_fetch_assoc($result)){
        ?>
        <tr>
          <td>
            <?php
            $server->show_profilephotoicon($server->get_imageblob($row['profile_photo_id']));
            ?>
          </td>
          <td>
            <?php
            echo '<a class="nav-link" href="/FiveStars/profile.php/'.$row['Id'].'">'.$row['Name']." ".$row['Surname'].'<span class="sr-only">(current)</span></a>';
            ?>
          </td>
        </tr>
        <?php
      }
    }
    ?>
  </tbody>
</table>
</body>
</html>