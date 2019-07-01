<?php include('functions.php') ?>
<?php 
$db = mysqli_connect('localhost', 'root', '', 'socialsite');
if (isset( $_POST['message']) && isset( $_POST['idYou']) && isset( $_POST['idToSend'])){
  $message = mysqli_real_escape_string($db, $_POST['message']);
  $idSender = mysqli_real_escape_string($db, $_POST['idYou']);
  $idReciver = mysqli_real_escape_string($db, $_POST['idToSend']);
  $sql = "INSERT INTO message ('From_user','To_user','Message') VALUES ('$idSender','$idReciver','$message')";
  mysqli_query($db, $query);
} 
?>
