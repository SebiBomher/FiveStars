<?php include('profileclass.php') ?>
<?php

class Functions{

  function get_newsfeed($email){

  }
  function get_profile($id){
    $profile = new Profile();
    $db = mysqli_connect('localhost', 'root', '', 'socialsite');
  //$email = $_SESSION['email'];
    $sql = "SELECT * FROM user WHERE Id = '$id'";
    $query = mysqli_query($db, $sql);
    $result = mysqli_fetch_array($query);
    $server = new Functions();
    $profile->set_id($result[0]);
    $profile->set_name($result[1]);
    $profile->set_surname($result[2]);
    $profile->set_email($result[3]);
    $profile->set_rating($result[5]);
    $profile->set_score($result[6]);
    $profile->set_phone_number($result[7]);
    $profile->set_profile_photo_id($result[8]);
    $profile->set_cover_photo_id($result[9]);
    return $profile;
  }
  function get_imageblob($id){
    $db = mysqli_connect('localhost', 'root', '', 'socialsite');
    $sql = "SELECT * FROM photo WHERE id = $id";
    $sth = mysqli_query($db, $sql);
    $result = mysqli_fetch_array($sth);
    return $result['Image'];
  }

  function show_name($name,$surname)
  {	echo '<div class="container mt-3 text-center">';
  echo '<h3>'.$name.' '.$surname.'<h3>';
}


function show_coverphoto($blob)
{
 echo '<div class="container mt-3 text-center">';
 echo '<img src="data:image/jpeg;base64,'.base64_encode( $blob ).'"class="center-block border border-dark rounded" width="851" height="315"/>';
 echo '</div>';
}

function show_profilephoto($blob)
{
 echo '<div class="container mt-3 text-center">';
 echo '<img src="data:image/jpeg;base64,'.base64_encode( $blob ).'"class="rounded-circle border-dark rounded" width="180" height="180"/>';
 echo '</div>';
}
function show_profilephotowithchange($blob)
{
  echo '<div class="container mt-3 text-center">';
  echo '<img src="data:image/jpeg;base64,'.base64_encode( $blob ).'"class="rounded-circle border-dark rounded" width="180" height="180"/>';
  echo '</div>';
}
function show_smallprofilephoto($blob)
{
  echo '<div class="container">';
  echo '<div class="dropdown">';
  echo '<button class="btn btn-primary rounded-circle border-dark rounded" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  >';
  echo '<img src="data:image/jpeg;base64,'.base64_encode( $blob ).'"class="rounded-circle border-dark rounded" width="30" height="30"/>';
  echo '</button>';
  echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
  <a class="dropdown-item" href="#">Action</a>
  <a class="dropdown-item" href="#">Another action</a>
  <a class="dropdown-item" href="#">Something else here11111111111</a>
  </div>';
  echo '</div>';
  echo '</div>';
}

}
?>