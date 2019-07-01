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
	<title>Home</title>
  <script src="scripts.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <script type="text/javascript" src="scripts.js"></script>
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
      <a class="nav-link active" href=<?php echo $userAbout;?> >About</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href=<?php echo $userFriends;?> >Friends</a>
    </li>
  </ul>

  <?php
  $about = array();
  $birthday = null;;
  $functions->get_about($idCurrentProfile,$about,$birthday)
  ?>
  <?php if (isset($_SESSION['id']) && $_SESSION['id'] == $idCurrentProfile) : ?>
    <button id="edit_button" class="btn btn-primary" onclick="edit_about()">Edit</button>
    <button id="change_password_button" class="btn btn-primary" onclick="change_password()">Change password</button>
    <button id="back_button" onclick="back_about()" class="btn btn-danger mt-3" name="edit_back" style="display: none;">Back</button>
    <form id ="abouttableedit1" style="display: none;" method="POST" action="/fivestars/server.php">
      <div class="form-group">
        <label for="City">City</label>
        <textarea type="text" class="form-control" rows="1" name="City" aria-describedby="CityHelp" placeholder="What city do you live in?"><?php if (!empty($about[0])) echo $about[0];?></textarea>
      </div>
      <div class="form-group">
        <label for="Description">Education</label>
        <textarea type="text" class="form-control" rows="1" name="Education" placeholder="What is your education?"><?php if (!empty($about[1])) echo $about[1];?></textarea>
      </div>
      <div class="form-group">
        <label for="Birthday">Birthday</label>
        <input type="date" class="form-control" name="Birthday" value=<?php if ($birthday) echo $birthday;?>>
      </div>
      <div class="form-group">
        <label for="Birthday">Description</label>
        <textarea class="form-control" rows="3" name="Description" placeholder="Tell us something about yourself."><?php if (!empty($about[3])) echo $about[3]?></textarea>
      </div>
      <div class="form-group">
        <label for="Birthday">Interests</label>
        <textarea class="form-control" rows="2" name="Interests" placeholder="What are you interested in?."><?php if (!empty($about[4])) echo $about[4]?></textarea>
      </div>
      <div class="form-group">
        <label for="Description">Phone number</label>
        <textarea type="text" class="form-control" rows="1" name="Phone" placeholder="What is your phone number?"><?php if (!empty($about[5])) echo $about[5];?></textarea>
      </div>
      <button type="submit" class="btn btn-primary d-inline" name="edit_about">Save</button>
    </form>
    <form id ="changepassform" style="display: none;" method="POST" action="/fivestars/server.php">
      <div class="form-group">
        <label>Old Password</label>
        <input type="password" class="form-control" name="oldpassword">
      </div>
      <div class="form-group">
        <label>New Password</label>
        <input type="password" class="form-control" name="newpassword">
      </div>
      <div class="form-group">
        <label>New Password Confirm</label>
        <input type="password" class="form-control" name="newpasswordconfirm">
      </div>
      <button type="submit" class="btn btn-primary d-inline" name="change_password">Save password</button>
    </form>
  <?php endif ?>
  <div id="abouttable">
    <table class="table table-bordered" >

      <tbody>
        <tr>
          <th scope="row">City</th>
          <td colspan=""><?php if (!empty($about[0])) echo $about[0]; else echo 'Undefined'?></td>
        </tr>
        <tr>
          <th scope="row">Education</th>
          <td colspan=""><?php if (!empty($about[1])) echo $about[1]; else echo 'Undefined'?></td>
        </tr>
        <tr>
          <th scope="row">Birthday</th>
          <td colspan=""><?php if ($birthday) echo $birthday; else echo 'Undefined'?></td>
        </tr>
        <tr>
          <th scope="row">Description</th>
          <td colspan=""><?php if (!empty($about[3])) echo $about[3]; else echo 'Undefined'?></td>
        </tr>
        <tr>
          <th scope="row">Interests</th>
          <td colspan=""><?php if (!empty($about[4])) echo $about[4]; else echo 'Undefined'?></td>
        </tr>
        <tr>
          <th scope="row">Phone number</th>
          <td colspan=""><?php if (!empty($about[5])) echo $about[5]; else echo 'Undefined'?></td>
        </tr>
      </tbody>
    </table>
  </div>

</body>
</html>