<?php include('functions.php') ?>
<?php 
session_start(); 

if (!isset($_SESSION['email']) || !isset($_SESSION['name']) || !isset($_SESSION['surname'])) {
 $_SESSION['msg'] = "You must log in first";
 header('location: login.php');
}

$idSession = $_SESSION['id'];
$idCurrentProfile = $_GET['user'];
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
<div class="container mt-3">
  <?php 

  $server->show_coverphoto($server->get_imageblob($profile->get_cover_photo_id()));
  $server->show_profilephoto($server->get_imageblob($profile->get_profile_photo_id()));
  $server->show_name($profile->get_name(),$profile->get_surname());

  $userNameSurname = "profile.php?user=".$idCurrentProfile;
  $userAbout = "about.php?user=".$idCurrentProfile;
  $userAlbums = "albums.php?user=".$idCurrentProfile;
  $userFriends = "friends.php?user=".$idCurrentProfile;
  ?>
</div>
<ul class="nav nav-tabs justify-content-center">
  <li class="nav-item">
    <a class="nav-link" href=<?php echo $userNameSurname;?> >Timeline</a>
  </li>
  <li class="nav-item">
    <a class="nav-link active" href=<?php echo $userAlbums;?> >Albums</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href=<?php echo $userAbout;?> >About</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href=<?php echo $userFriends;?> >Friends</a>
  </li>
</ul>
<div class="container mt-3">
  <form action="upload.php" method="POST" enctype="multipart/form-data">
    <div class="input-group">
      <label>File: </label>
      <input type="file" name="image" />
      <div class="input-group">
        <button type="submit" class="btn" name="upload_photo">Upload Photo</button>
      </div>
    </div>
  </form>
  
</div>
</div>


</body>
</html>