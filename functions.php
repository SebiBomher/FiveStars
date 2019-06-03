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
  {	
    echo '<div class="container mt-3 text-center">';
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
    if ($a == $b) {
        return 0;
    }
    return ($a < $b) ? -1 : 1;
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
  
  usort($Parray, "cmp");
}
}
?>