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
$profile = new Profile();
$functions = new Functions();
$profile = $functions->get_profile($idCurrentProfile);
if (strcmp($profile->get_privilege(),'administrator') == 0 || strcmp($profile->get_privilege(),'moderator') == 0) $isMod = 1; else $isMod = 0;
?>

<!DOCTYPE html>
<html>
<head>
  <script type="text/javascript" src="http://localhost/fivestars/scripts.js"></script>
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
  <div class="container mt-3 float-left w-75">
    <?php 

    if ($_SESSION['id'] == $idCurrentProfile) $myProfile = true;
    else $myProfile = false;
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
      <a class="nav-link" href=<?php echo $userNameSurname;?> >Timeline</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href=<?php echo $userAlbums;?> >Albums</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href=<?php echo $userAbout;?> >About</a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href=<?php echo $userFriends;?> >Friends</a>
    </li>
  </ul>
  <div class="container mt-3">
    <table class="table">
      <thead>
        <tr>
          <th scope="col">Photo</th>
          <th scope="col">Name</th>
        </tr>
      </thead>
      <?php
      $profiles = array();
      $functions = new Functions;
      $functions->recive_friends($idCurrentProfile,$profiles);
      ?>
      <tbody>

        <?php
        for ($i = 0; $i < sizeof($profiles); $i = $i + 1)
        {
          ?>
          <tr>
            <td>
              <?php
              $functions->show_profilephotoicon($functions->get_imageblob($profiles[$i]->get_profile_photo_id()));
              ?>
            </td>
            <td>
              <?php
              echo '<a class="nav-link d-inline" href="/fivestars/profile.php/'.$profiles[$i]->get_id().'">'.$profiles[$i]->get_name()." ".$profiles[$i]->get_surname().'<span class="sr-only">(current)</span></a>';
              if ($idCurrentProfile == $idSession) {
                echo '<button id="cancel_remove_friend" class="btn btn-primary ml-3 d-none" onclick="cancel_remove_friend()" >Cancel</button>';
                echo '<button id="remove_friend" class="btn btn-danger ml-3" onclick="remove_friend()" >Remove</button>';
                echo '<form method="POST" action="/fivestars/server.php">';
                echo '<h5 id="warning_label" class="d-none">Are you sure you want to unfriend this user?</h5>';
                echo '<input type="text" class="form-control d-none" name="id" value="'.$profiles[$i]->get_id().'">';
                echo '<button type="submit" id="confirm_remove_friend" class="btn btn-danger ml-3 d-none" name="remove_friend" >Remove Friend</button>';
                echo '</form>';
              }
              ?>
            </td>
            <?php
          }
          ?>

        </tbody>
      </table>
      
    </div>
  </div>


</body>
</html>