<?php include('server.php') ?>
<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>
<body>
  <?php include('errors.php'); ?>
  <div class="container">
    <h2 class="text-center">Register</h2>
  <form role="form" class="form-horizontal" method="post" action="register.php">
  	<div class="form-group">
  	  <label>Name</label>
  	  <input type="text" class="form-control" name="name" >
  	</div>
    <div class="form-group">
      <label>Surname</label>
      <input type="text" class="form-control" name="surname" >
    </div>
    <div class="form-group">
      <label>Phone Number</label>
      <input type="text" class="form-control" name="phone" >
    </div>
  	<div class="form-group">
  	  <label>Email</label>
  	  <input type="email" class="form-control" name="email" >
  	</div>
  	<div class="form-group">
  	  <label>Password</label>
  	  <input type="password" class="form-control" name="password_1">
  	</div>
  	<div class="form-group">
  	  <label>Confirm password</label>
  	  <input type="password" class="form-control" name="password_2">
  	</div>
  	<div class="form-group">
  	  <button type="submit" class="btn btn-default" name="reg_user">Register</button>
  	</div>
  	<p>
  		Already a member? <a href="login.php">Sign in</a>
  	</p>
  </form>
</div>
</body>
</html>