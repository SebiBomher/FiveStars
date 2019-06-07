<?php include('classes.php') ?>
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
  {	
    echo '<div class="container mt-3 text-center">';
    echo '<h3>'.$name.' '.$surname.'<h3>';
  }


  function show_coverphoto($blob,$idCurrent)
  {
   $functions = new Functions();
   echo '<div class="container mt-3 text-center">';
   echo '<img src="data:image/jpeg;base64,'.base64_encode( $blob ).'"class="center-block border border-dark rounded" width="851" height="315"/>';
   if ($idCurrent == $_SESSION['id']) {
    $db = mysqli_connect('localhost', 'root', '', 'socialsite');
    $sql = "SELECT * FROM album WHERE OwnerID = $idCurrent AND Name = 'Profile'";
    $sth = mysqli_query($db, $sql);
    $row = mysqli_fetch_assoc($sth);
    $functions->show_uploadphotoinmodal("myModalCover",$row['ID'],2, "Change Cover Photo");
  }
   echo '</div>';
 }

 function show_profilephoto($blob,$idCurrent)
 {
  $functions = new Functions();
   echo '<div class="container mt-3 text-center">';
   echo '<img  class="rounded-circle border-dark rounded" data-toggle="modal" data-target="#myModalProfile" src="data:image/jpeg;base64,'.base64_encode( $blob ).'" width="180" height="180"/>';
   if ($idCurrent == $_SESSION['id']) {
    $db = mysqli_connect('localhost', 'root', '', 'socialsite');
    $sql = "SELECT * FROM album WHERE OwnerID = $idCurrent AND Name = 'Profile'";
    $sth = mysqli_query($db, $sql);
    $row = mysqli_fetch_assoc($sth);
    $functions->show_uploadphotoinmodal("myModalProfile",$row['ID'],1,"Change Profile Photo");
  }
   echo '</div>';
 }
 function show_profilephotoicon($blob)
 {

  echo '<img src="data:image/jpeg;base64,'.base64_encode( $blob ).'"class="rounded-circle border-dark rounded" width="50" height="50"/>';

}
function show_smallprofilephotodropdown($blob,$id)
{
  $functions = new Functions();
  echo '<div class="dropdown">';
  echo '<button class="btn btn-primary rounded-circle border-dark rounded" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"  >';
  echo '<img src="data:image/jpeg;base64,'.base64_encode( $blob ).'"class="rounded-circle border-dark rounded" width="30" height="30"/>';
  echo '</button>';
  echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
  $functions->notificationshow($id);
  echo '</div>';
  echo '</div>';
}
function notificationshow($id)
{
  $functions = new Functions();
  $db = mysqli_connect('localhost', 'root', '', 'socialsite');
  $sql = "SELECT * FROM notifications WHERE Profile_id = $id ORDER BY Time" ;
  $result = mysqli_query($db, $sql);
  while ($row = mysqli_fetch_assoc($result)){
    $notificationType = $row['Type'];
    $notificationId = $row['Notification_id'];
    if (strcmp($notificationType,'friendship') == 0)
    {
      $functions->friend_request_notification($notificationId);
    }
  }

  
}
function friend_request_notification($id)
{
  $db = mysqli_connect('localhost', 'root', '', 'socialsite');
  $sql = "SELECT * FROM friendship WHERE FriendshipID = $id";
  $result = mysqli_query($db, $sql);
  while ($row = mysqli_fetch_assoc($result)){
    $idFriend = $row['User1'];
    $status = $row['Status'];
    $sql1 = "SELECT * FROM user WHERE Id = $idFriend";
    $result1 = mysqli_query($db, $sql1);
    $row1 = mysqli_fetch_row($result1);
    if (strcmp($status,'pending') == 0){
      echo '<a class="dropdown-item" href="#">'.$row1[1]." ".$row1[2].' has sent you a friend request! ';
      echo '<form action="server.php" method="POST">';
      echo '<input class="text d-none" type="text" name="id" value='.$idFriend.' >';
      echo '<button class="btn btn-primary" name="accept_friend_request">Accept</button>';
      echo' <button class="btn btn-danger" name="reject_friend_request">Refuse</button>';
      echo '</form>';
    }
    else if (strcmp($status,'accepted') == 0){
      echo '<a class="dropdown-item" href="#"> You are now friends with '.$row1[1]." ".$row1[2];
    }
    else if (strcmp($status,'refuesed') == 0){
      echo '<a class="dropdown-item" href="#"> You have refuesed to be friends with'.$row1[1]." ".$row1[2];
    }
    echo '</a>';
  }

}
function cmp($a, $b)
{
  if (strcmp(strtolower($a->get_name()),strtolower($b->get_name()) == 0)){
    return strcmp(strtolower($a->get_surname()),strtolower($b->get_surname()));
  }
  return strcmp(strtolower($a->get_name()),strtolower($b->get_name()));
}

function recive_friends($id,&$Parray)
{
  $db = mysqli_connect('localhost', 'root', '', 'socialsite');
  $sql = "SELECT * FROM friendship WHERE User1 = $id";
  $result = mysqli_query($db, $sql);
  $i = 0;
  while ($row = mysqli_fetch_assoc($result)){
    $friendid = $row['User2'];
    $sql = "SELECT * FROM user WHERE Id = $friendid";
    $results = mysqli_query($db, $sql);
    while ($rows = mysqli_fetch_assoc($results)){
      $Profile = new Profile();
      $Profile->set_id($rows['Id']);
      $Profile->set_name($rows['Name']);
      $Profile->set_surname($rows['Surname']);
      $Profile->set_profile_photo_id($rows['profile_photo_id']);
    }
    array_push($Parray,$Profile);
    $i = $i + 1;
  }
  $sql = "SELECT * FROM friendship WHERE User2 = $id";
  $result = mysqli_query($db, $sql);
  while ($row = mysqli_fetch_assoc($result)){
    $friendid = $row['User1'];
    $sql = "SELECT * FROM user WHERE Id = $friendid";
    $results = mysqli_query($db, $sql);
    while ($rows = mysqli_fetch_assoc($results)){
      $Profile = new Profile();
      $Profile->set_id($rows['Id']);
      $Profile->set_name($rows['Name']);
      $Profile->set_surname($rows['Surname']);
      $Profile->set_profile_photo_id($rows['profile_photo_id']);
    }
    array_push($Parray,$Profile);
  }
  
  usort($Parray, array("Profile", "cmp"));
}
function show_photofull($id)
{
  $functions = new Functions();
  echo '<img class ="card-img-top" src="data:image/jpeg;base64,'.base64_encode( $functions->get_imageblob($id) ).'"/>';
}
function show_photo($id)
{
  $functions = new Functions();
  echo '<img class ="card-img-top" src="data:image/jpeg;base64,'.base64_encode( $functions->get_imageblob($id) ).'" width="160" height="160"/>';
}
function show_photosimple($id)
{
  $functions = new Functions();
  echo '<img class="d-inline" src="data:image/jpeg;base64,'.base64_encode( $functions->get_imageblob($id) ).'" width="200" height="200"/>';
}
function show_photowithclick($id,$i)
{
  $functions = new Functions();
  $myModal = "#myModal".$i;
  echo '<img class ="card-img-top" data-toggle="modal" data-target='.$myModal.' src="data:image/jpeg;base64,'.base64_encode( $functions->get_imageblob($id) ).'" width="160" height="160" />';
  $functions->show_photoinmodal($id,$i);
}
function show_photoinmodal($id,$i)
{
  $db = mysqli_connect('localhost', 'root', '', 'socialsite');
  $sql = "SELECT * FROM article WHERE Photo_Id = $id";
  $result = mysqli_query($db, $sql);
  $row = mysqli_fetch_assoc($result);
  $description = $row['Description'];
  $owner = $row['Author_Id'];
  $sql = "SELECT * FROM user WHERE Id = $owner";
  $result = mysqli_query($db, $sql);
  $row = mysqli_fetch_assoc($result);
  $fullname = $row['Name']." ".$row['Surname'];
  $functions = new Functions();
  $myModal = "myModal".$i;
  echo '<div class="modal fade" id='.$myModal.' role="dialog">';
  echo '<div class="modal-dialog">';
  echo '<div class="modal-content">';
  echo '<div class="modal-header">';
  echo '<h2 class="text-center">'.$fullname.'</h2>';
  echo '<button type="button" class="close" data-dismiss="modal">&times;</button>';
  echo '</div>';
  echo '<div class="modal-body">';
  $functions->show_photofull($id);
  echo '<p class="text-center">'.$description.'</p>';
  echo '</div>';
  echo '<div class="modal-footer">';
  echo '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
  echo '</div>';
  echo '</div>';
  echo '</div>';
  echo '</div>';

}
function show_uploadphotoinmodal($modalname,$albumname,$profilepic,$tosay)
{
  echo '<div class="container">';
  echo '<button type="button" class="btn btn-primary-outline mt-3 mb-3" data-toggle="modal" data-target=#'.$modalname.'>'.$tosay.'</button>';
  echo '<div class="modal fade" id='.$modalname.' role="dialog">';
  echo '<div class="modal-dialog">';
  echo '<div class="modal-content">';
  echo '<div class="modal-header">';
  echo '<button type="button" class="close" data-dismiss="modal">&times;</button>';
  echo '</div>';
  echo '<div class="modal-body">';
  echo '<form class="form" action="/FiveStars/server.php" method="POST" enctype="multipart/form-data">';
  echo '<div class="form-group">';
  echo '<label>Description:</label>';
  echo '<textarea class="form-control" rows="3" cols="45" name="description" placeholder="Description"></textarea>';
  echo '</div>';
  echo '<input class="text d-none" type="text" name="AlbumId" value='.$albumname.'>';
  echo '<input class="text d-none" type="text" name="NewProfilePicture" value='.$profilepic.'>';
  echo '<label class="btn btn-default"> Photo/video <input class="btn btn-defaulttype" type="file" name="image">';
  echo '</label>';
  echo '</div>';
  echo '<div class="modal-footer">';
  echo '<button type="submit" class="btn btn-default" name="upload_article">Upload photo</button>';
  echo '<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>';
  echo '</form>';
  echo '</div>';
  echo '</div>';
  echo '</div>';
  echo '</div>';
  echo '</div>';
}
function get_albums($id,&$albumarray)
{
  $db = mysqli_connect('localhost', 'root', '', 'socialsite');
  $sql = "SELECT * FROM album WHERE OwnerID = $id";
  $result = mysqli_query($db, $sql);
  while ($row = mysqli_fetch_assoc($result)){
    $album = new Album();
    $album->set_id($row['ID']);
    $album->set_ownerid($row['OwnerID']);
    $album->set_name($row['Name']);
    array_push($albumarray,$album);
  }
}
function get_photosfromalbum($id,&$photoarray)
{
  $db = mysqli_connect('localhost', 'root', '', 'socialsite');
  $sql = "SELECT * FROM albumtophoto WHERE AlbumId = $id";
  $result = mysqli_query($db, $sql);
  while ($row = mysqli_fetch_assoc($result)){
    $imageID = $row['PhotoId'];
    $sql1 = "SELECT * FROM photo WHERE Id = $imageID ORDER BY Time ASC";
    $result1 = mysqli_query($db, $sql1);
    while ($rows = mysqli_fetch_assoc($result1)){
      $photo = new Photo();
      $photo->set_id($imageID);
      $photo->set_name($rows['image_name']);
      array_push($photoarray,$photo);
    }
  }
}
function show_newsfeedarticle($id)
{

}
function show_newsfeed($id)
{
  $functions = new Functions();
  $db = mysqli_connect('localhost', 'root', '', 'socialsite');
  $sql = "SELECT * FROM friendship WHERE User1 = $id OR User2 = $id";
  $result = mysqli_query($db, $sql);
  while ($row = mysqli_fetch_assoc($result)){
    $friendsID1 = $row['User1'];
    $friendsID2 = $row['User2'];
    if ($friendsID2 == $id) $friendsID = $friendsID1;
    else if ($friendsID1 == $id) $friendsID = $friendsID2;
    $sql1 = "SELECT * FROM article WHERE Author_Id = $friendsID ORDER BY Time DESC";
    $result1 = mysqli_query($db, $sql1);
    $i = 0;
    while ($row1 = mysqli_fetch_assoc($result1)){
      echo '<div class="container">';
      echo '<div class="row">';
      echo '<div class="col-md-3 mt-3 mb-3">';
      if ($row1['Photo_Id'] != null ) $functions->show_photowithclick($row1['Photo_Id'],$i);
      echo '</div>';
      echo '</div>';
      echo '</div>';
      $i = $i + 1;
    }
  }
}
}
?>