<?php include('functions.php') ?>
<?php 
session_start(); 

if (!isset($_SESSION['email']) || !isset($_SESSION['name']) || !isset($_SESSION['surname'])) {
 $_SESSION['msg'] = "You must log in first";
 header('location: login.php');
}
if (strcmp($_SESSION['status'],'administrator') == 0 || strcmp($_SESSION['status'],'administrator') == 0) $isMod = 1; else $isMod = 0;
$functions = new Functions();
$idSession = $_SESSION['id'];
$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_segments = explode('/', $uri_path);
$idSession = $_SESSION['id'];
$idCurrent = $uri_segments[3];
$idToMessage = $uri_segments[4];
$profileYou = $functions->get_profile($idCurrent);
$profileToMessage = $functions->get_profile($idToMessage);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Home</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- default styles -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/css/star-rating.min.css" media="all" rel="stylesheet" type="text/css" />

  <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-star-rating/4.0.6/js/star-rating.min.js" type="text/javascript"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script>
    $(function () {

      $('form').on('submit', function (e) {

        e.preventDefault();

        $.ajax({
          type: 'POST',
          url: '/fivestars/message_server.php',
          data: $('form').serialize(),
          success: function () {

            alert($('form').serialize());
            $('textarea').val('');
          }
        });

      });

    });
  </script>
</head>
<body>
  <div class = "fixed-top d-inline text-center">
    <?php $functions->show_profilephotoicon($functions->get_imageblob($profileToMessage->get_profile_photo_id())); ?>
    <h2 class= "d-inline ml-3"><?php echo $profileToMessage->get_name().' '.$profileToMessage->get_surname() ?></h2>
  </div>

    <?php
    $functions->get_chat($idCurrent,$idToMessage)
    ?>

  <form class="input-group fixed-bottom">
    <input class="d-none" type="text" placeholder="Search" name="idToSend" value=<?php echo $idToMessage;?>>
    <input class="d-none" type="text" placeholder="Search" name="idYou" value=<?php echo $idCurrent;?>>
    <textarea class="form-control" aria-label="With textarea" name="message" placeholder="Say something nice!"></textarea>
    <div class="input-group-prepend">
      <button class = "btn btn-success">Submit</button>
    </div>
  </form>
</body>
</html>