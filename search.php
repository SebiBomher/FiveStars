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
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="#">Entropy</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
     <span class="navbar-toggler-icon"></span>
   </button>
   <div class="collapse navbar-collapse" id="navbarSupportedContent">
     <ul class="navbar-nav mr-auto">
       <li class="nav-item active">
        <a class="nav-link" href="logout.php">Logout<span class="sr-only">(current)</span></a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
     <li class="nav-item active">
      <?php  if (isset($_SESSION['name']) && isset($_SESSION['surname']) && isset($_SESSION['id'])) : ?>
      <?php
      $profile1 = $server->get_profile($idSession);
      $server->show_smallprofilephotodropdown($server->get_imageblob($profile1->get_profile_photo_id()),$idSession);
      ?>
    <?php endif ?>
  </li>
  <li class="nav-item active">
    <?php  if (isset($_SESSION['name']) && isset($_SESSION['surname']) && isset($_SESSION['id'])) : ?>
    <?php $userNameSurname = "profile.php?user=".$_SESSION['id']; ?>
    <a class="nav-link" href=<?php echo $userNameSurname; ?> id="profile_view"><?php echo $_SESSION['name'].' '.$_SESSION['surname']; ?><span class="sr-only">(current)</span></a>
  <?php endif ?>
</li>
<li class="nav-item active">
  <a class="nav-link" href="main.php">Home<span class="sr-only">(current)</span></a>
</li>
<form class="form-inline" method="POST" action="search.php">
  <input class="form-control mr-sm-2" type="search" placeholder="Search" name="query" aria-label="Search">
  <button class="btn btn-light my-2 my-sm-0" type="submit" name="search">Search</button>
</form>
</ul>

</div>
</nav>
<div class="content">
 <!-- Mesaj de notificare -->
 <?php if (isset($_SESSION['success'])) : ?>
  <div class="error success" >
   <h3>
    <?php 
    echo $_SESSION['success']; 
    unset($_SESSION['success']);
    ?>
  </h3>
</div>
<?php endif ?>

<!-- Informatii despre userul logat -->


</div>

<table class="table">
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
            echo '<a class="nav-link" href="profile.php?user='.$row['Id'].'">'.$row['Name']." ".$row['Surname'].'<span class="sr-only">(current)</span></a>';
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