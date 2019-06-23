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
                echo '<form method="POST" action="/fivestars/server.php">';
                echo '<button id="delete_Photo" class="btn btn-danger ml-3" onclick="delete_photo()">Remove</button>';
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