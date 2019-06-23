<?php include('functions.php') ?>
<?php 
session_start(); 

if (!isset($_SESSION['email']) || !isset($_SESSION['name']) || !isset($_SESSION['surname'])) {
 $_SESSION['msg'] = "You must log in first";
 header('location: login.php');
}
$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_segments = explode('/', $uri_path);
$idSession = $_SESSION['id'];
$idCurrentProfile = $uri_segments[3];
$idAlbum = $uri_segments[4];
$profile = new Profile();
$functions = new Functions();
$profile = $functions->get_profile($idCurrentProfile);
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
<?php
$functions->show_navigationbar($idSession);
$functions->show_chat($idSession);
?>
<div class="container mt-3 float-left w-75">
  <?php 

  if ($_SESSION['id'] == $idCurrentProfile) $myProfile = true;
  else $myProfile = false;
  $functions->show_coverphoto($profile->get_cover_photo_id(),$idCurrentProfile);
  $functions->show_profilephoto($profile->get_profile_photo_id(),$idCurrentProfile);
  $functions->show_name($profile->get_name(),$profile->get_surname());
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
<?php
$backtoalbum = "/FiveStars/albums.php/".$idCurrentProfile;
$albumdetail = array();
$functions->get_album($idAlbum,$albumdetail);
?>
<a class="btn btn-danger mt-3 ml-3 mb-3" href=<?php echo $backtoalbum;?> >Back To Albums</a>
<h2 class="ml-3"><?php echo $albumdetail[0];?></h2>
<?php
$functions->display_stars($albumdetail[1]);
?>
<h5 class="ml-5"><?php echo $albumdetail[1];?></h5>
<?php
if ($idSession !== $idCurrentProfile) $functions->show_rate($idAlbum,'album');
$photos = array();
$functions->get_photosfromalbum($idAlbum,$photos);
for ($i = 1; $i <= sizeof($photos) ; $i = $i + 1) :
  if (($i - 1) % 4 == 0) : ?>
    <div class="container">
      <div class="row">
      <?php endif ?>
      <div class="col-sm-3">
        <?php
        $functions->show_photowithclick($photos[$i - 1]->get_id(),$i - 1);
        ?>
      </div>
      <?php if (($i - 1) % 4 == 4) : ?>
      </div>
    </div>
  <?php endif ?> 
<?php endfor ?> 

<?php  if (isset($_SESSION['id']) && $_SESSION['id'] == $idCurrentProfile) : ?>
  <?php 
  $functions = new Functions();
  $functions->show_uploadphotoinmodal("myModal",$idAlbum,0, "Add Photo");?>

</div>
<?php endif ?>

</div>

</body>
</html>