<?php include('functions.php') ?>
<?php 
session_start(); 
$errors = array();
if (!isset($_SESSION['email']) || !isset($_SESSION['name']) || !isset($_SESSION['surname'])) {
 $_SESSION['msg'] = "You must log in first";
 header('location: login.php');
}

$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_segments = explode('/', $uri_path);
$idSession = $_SESSION['id'];
$idCurrentProfile = $uri_segments[3];
$profile = new Profile();
$functions = new Functions();
$profile = $functions->get_profile($idCurrentProfile);
if (strcmp($profile->get_privilege(),'administrator') == 0 || strcmp($profile->get_privilege(),'moderator') == 0) $isMod = 1; else $isMod = 0;
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
  //$functions->show_chat($idSession);
  ?>
  <div class="container mt-3 w-75 text-center">
    <?php 
    
    $functions->show_coverphoto($profile->get_cover_photo_id(),$idCurrentProfile);
    $functions->show_profilephoto($profile->get_profile_photo_id(),$idCurrentProfile);
    $functions->show_name($profile->get_name(),$profile->get_surname());
    if ($isMod == 1) $functions->show_title($profile->get_privilege());
    $functions->display_stars($profile->get_rating());
    ?>
    <h2><?php if ($profile->get_rating() == 0) echo 'No rating'; else echo $profile->get_rating();?></h2>
    <?php
    if ($idCurrentProfile !== $idSession) $functions->show_rate($idCurrentProfile,'profile');
    $userNameSurname = "/FiveStars/profile.php/".$idCurrentProfile;
    $userAbout = "/FiveStars/about.php/".$idCurrentProfile;
    $userAlbums = "/FiveStars/albums.php/".$idCurrentProfile;
    $userFriends = "/FiveStars/friends.php/".$idCurrentProfile;
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
  <div class="container mt-3 text-center">
    <?php  if (isset($_SESSION['id']) && $_SESSION['id'] != $idCurrentProfile && !($functions->is_friend($idCurrentProfile))) : ?>
      <form class="form" action="/fivestars/server.php "method="POST">
        <input class="text d-none" type="text" name="id" value=<?php echo $idCurrentProfile;?> >
        <button type="submit" class="btn btn-default" name="send_friend_request">Add friend</button>
      </form>
    <?php endif ?>
    <?php  if (strcmp($_SESSION['status'],'administrator') == 0 && $isMod == 0) : ?>
      <form class="form" action="/fivestars/server.php "method="POST">
        <input class="text d-none" type="text" name="id" value=<?php echo $idCurrentProfile;?> >
        <button type="submit" class="btn btn-default" name="make_mod">Make Moderator</button>
      </form>
    <?php endif ?>
    <?php  if (isset($_SESSION['id']) && $_SESSION['id'] == $idCurrentProfile) : ?>
      <form class="form" action="/FiveStars/server.php" method="POST" enctype="multipart/form-data">
        <?php include('errors.php'); ?>
        <div class="form-group">
         <label>Status Update:</label>
         <textarea class="form-control" rows="3" cols="45" name="description" placeholder="What's on your mind?"></textarea>
       </div>
       <input class="text d-none" type="text" name="AlbumId" value="0" >
       <input class="text d-none" type="text" name="NewProfilePicture" value="0">
       <label class="btn btn-default">
        Photo/video <input class="btn btn-defaulttype" type="file" name="image">
      </label>
      <button type="submit" class="btn btn-default" name="upload_article">Update Status</button>
    </form>
  <?php endif ?>
  <?php
  $functions->show_timeline($idCurrentProfile);
  ?>
</div>


</div>

</body>
</html>