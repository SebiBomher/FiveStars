
<?php include('functions.php') ?>
<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer; 
use PHPMailer\PHPMailer\Exception; 
require './PHPMailer/src/Exception.php'; 
require './PHPMailer/src/PHPMailer.php'; 
require './PHPMailer/src/SMTP.php';
// variabile
$username = "";
$email    = "";
$errors = array(); 

// conexiune la baza de date
$db = mysqli_connect('localhost', 'root', '', 'socialsite');

// Inregistrare
if (isset($_POST['reg_user'])) {
	$name = mysqli_real_escape_string($db, $_POST['name']);
	$surname = mysqli_real_escape_string($db, $_POST['surname']);
	$phone = mysqli_real_escape_string($db, $_POST['phone']);
	$email = mysqli_real_escape_string($db, $_POST['email']);
	$password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
	$password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // Verificare daca form-ul e corect scris
  // adaugam la erori ce nu e bine
	if (empty($name)) { array_push($errors, "Name is required"); }
	if (empty($surname)) { array_push($errors, "Surname is required"); }
	if (empty($email)) { array_push($errors, "Email is required"); }
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { array_push($errors, "Invalid email format"); }
	if (empty($password_1)) { array_push($errors, "Password is required"); }
	if ($password_1 != $password_2) { array_push($errors, "The passwords do not match"); }
	if (strlen($password_1) <= 5) { array_push($errors, "Password must be at least 6 characters long"); }
  // Verificam daca mai exista acelasi email 
  // Nu trebuie sa existe user si email identice
	$user_check_query = "SELECT * FROM user WHERE email='$email' LIMIT 1";
	$result = mysqli_query($db, $user_check_query);
	$user = mysqli_fetch_assoc($result);

	if ($user) {
    // daca email-ul exista
		if ($user['email'] === $email) {
			array_push($errors, "Email already exists");
		}
	}

  // Daca nu apar erori inregistram
	if (count($errors) == 0) {
  	$password = md5($password_1);//criptam parola inainte de adaugare

  	if (empty($phone)) $phone = NULL;
  	$query = "INSERT INTO user (Name, Surname, Email, Password, Score, Rating, profile_photo_id, cover_photo_id) 
  	VALUES('$name','$surname', '$email', '$password', 1000, 3.5, 4, 75)";
  	
  	mysqli_query($db, $query);
  	$_SESSION['id'] = $id;
  	$_SESSION['name'] = $name;
  	$_SESSION['surname'] = $surname;
  	if (!empty($phone)) { $_SESSION['phone'] = $phone; }
  	$_SESSION['email'] = $email;
  	$_SESSION['password'] = $password;
  	header('location: mail.php');
  }
}

// Logare user
if (isset($_POST['login_user'])) {
	$email = mysqli_real_escape_string($db, $_POST['email']);
	$password = mysqli_real_escape_string($db, $_POST['password']);

  //Verificam creditentialele
	if (empty($email)) {
		array_push($errors, "email is required");
	}
	if (empty($password)) {
		array_push($errors, "Password is required");
	}
  //Daca totul e bine incepem sesiunea
	if (count($errors) == 0) {
		$password = md5($password);
		$query = "SELECT * FROM user WHERE Email='$email' AND Password='$password'";

		$results = mysqli_query($db, $query);

		if (mysqli_num_rows($results) == 1) {
			$row = mysqli_fetch_row($results);
			$_SESSION['id'] = $row[0];
			$_SESSION['name'] = $row[1];
			$_SESSION['surname'] = $row[2];
			$_SESSION['email'] = $email;
			$_SESSION['success'] = "You are now logged in";
			header('location: main.php');
		}else {
			array_push($errors, "Wrong email/password combination");
		}
	}
}

if (isset($_POST['upload_article'])){

	$queryId = NULL;
	$description = NULL;
	$profile = new Profile();
	$server = new Functions();

	$profile = $server->get_profile($_SESSION['email']);
	$user_id = $profile->get_id();

	$description = mysqli_real_escape_string($db, $_POST['description']);
	if(file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])){
		$image = addslashes(file_get_contents($_FILES['image']['tmp_name'])); //SQL Injection defence!
		$image_name = '_'.time().'.'.addslashes($_FILES['image']['name']);

		$query = "INSERT INTO photo (Image, image_name) VALUES ('$image','$image_name')";
		//$stmt = $pdo->prepare($query);
		mysqli_query($db, $query);

		$query = "SELECT Id FROM photo WHERE image_name = '$image_name'";
		//$stmt = $pdo->prepare($query);
		$results = mysqli_query($db, $query);
		$row = mysqli_fetch_row($results);
		if (!empty($description)){
			$query = "INSERT INTO article (Author_Id, Photo_Id, Description) VALUES ('$user_id','$row[0]','$description')";
			//$stmt = $pdo->prepare($query);
			mysqli_query($db, $query);
		}
		else{
			$query = "INSERT INTO article (Author_Id, Photo_Id) VALUES ('$user_id','$row[0]')";
			//$stmt = $pdo->prepare($query);
			mysqli_query($db, $query);
		}
	}
	else if (!(file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name']))){
		if (!empty($description)){
			$query = "INSERT INTO article (Author_Id, Description) VALUES ('$user_id','$description')";
			//$stmt = $pdo->prepare($query);
			mysqli_query($db, $query);
		}
		else if (empty($description)){
			array_push($errors, "Your status update is empty!");
			echo '<script>alert("Your status update is empty!");</script>';
		}
	}
	
	header('location: profile.php?user='.$_SESSION['id']);
}

if (isset($_POST['view_photo'])){
	$id = mysqli_real_escape_string($db, $_POST['id']);
	$sql = "SELECT * FROM photo WHERE id = $id";
	$sth = mysqli_query($db, $sql);
	$result = mysqli_fetch_array($sth);
	echo '<img src="data:image/jpeg;base64,'.base64_encode( $result['Image'] ).'"/>';
}

if (isset($_POST['send_friend_request'])){
	$id = mysqli_real_escape_string($db, $_POST['id']);
	$sql = "SELECT * FROM friendship WHERE User2 = $id";
	$result = mysqli_query($db, $sql);
	echo 'kkt1';
	if (!$result || mysqli_num_rows($result) == 0)
	{
		$sql = "INSERT INTO friendship (User1, User2, Status) VALUES ('$_SESSION[id]','$id', 0)";
		mysqli_query($db, $sql);
		echo 'kkt2';
	}
	//header('location: profile.php?user='.$_SESSION['id']);
}
function recive_friendRequest($id){
	$sql = "SELECT User1 FROM friendship WHERE User2 = $id";
	$result = mysqli_query($db, $sql);
	while ($row = mysqli_fetch_assoc($result)){
		$idFriend = $row['User1'];
		$sql1 = "SELECT * FROM user WHERE id = $idFriend";
		$result1 = mysqli_query($db, $sql);
		$row1 = mysqli_fetch_row($results1);
		echo '<a class="dropdown-item" href="#">'.$row1['Name']." ".$row1['Surname'].' has sent you a friend request!</a>';
	}
}

?>