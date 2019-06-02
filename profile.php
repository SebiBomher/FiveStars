<?php include('functions.php') ?>
<?php 
session_start(); 
$errors = array();
if (!isset($_SESSION['email']) || !isset($_SESSION['name']) || !isset($_SESSION['surname'])) {
 $_SESSION['msg'] = "You must log in first";
 header('location: login.php');
}
$idSession = $_SESSION['id'];
$idCurrentProfile = $_GET['user'];
$profile = new Profile();
$server = new Functions();
$profile = $server->get_profile($idCurrentProfile);
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
  ?>
  <button class="open-button" onclick="openForm()">Open Form</button>

  <div class="d-none form-popup" id="myForm">
    <form class="form-container" style>
      <h1>NewProfilePicture</h1>
      <button type="submit" class="btn cancel" onclick="closeForm()">Close</button>
    </form>
  </div> 
  <script type="text/javascript">
    function openForm() {
      document.getElementById("myForm").className = "form-popup";
    }

    function closeForm() {
      document.getElementById("myForm").className = "d-none form-popup";
    } 
  </script>
  <?php
  $server->show_name($profile->get_name(),$profile->get_surname());

  $userNameSurname = "profile.php?user=".$idCurrentProfile;
  $userAbout = "about.php?user=".$idCurrentProfile;
  $userAlbums = "albums.php?user=".$idCurrentProfile;
  $userFriends = "friends.php?user=".$idCurrentProfile;
  ?>
</div>
<ul class="nav nav-tabs justify-content-center">
  <li class="nav-item">
    <a class="nav-link active" href=<?php echo $userNameSurname;?> >Timeline</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href=<?php echo $userAlbums;?> >Albums</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href=<?php echo $userAbout;?> >About</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href=<?php echo $userFriends;?> >Friends</a>
  </li>
</ul>
<div class="container mt-3">
  <?php  if (isset($_SESSION['id']) && $_SESSION['id'] == $idCurrentProfile) : ?>
    <form class="form" action="server.php"method="POST" enctype="multipart/form-data">
      <?php include('errors.php'); ?>
      <div class="form-group">
       <label>Status Update:</label>
       <textarea class="form-control" rows="3" cols="45" name="description" placeholder="What's on your mind?"></textarea>
     </div>

     <label class="btn btn-default">
      Photo/video <input class="btn btn-defaulttype" type="file" name="image">
    </label>
    <button type="submit" class="btn btn-default" name="upload_article">Update Status</button>
  </form>
<?php endif ?>

</div>
<?php  if (isset($_SESSION['id']) && $_SESSION['id'] != $idCurrentProfile) : ?>
  <form class="form" action="server.php "method="POST">
    <input class="text d-none" type="text" name="id" value=<?php echo $idCurrentProfile;?> >
    <button type="submit" class="btn btn-default" name="send_friend_request">Add friend</button>
  </form>
<?php endif ?>
</body>
</html>