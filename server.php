
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
  	$query = "INSERT INTO user (Name, Surname, Email, Password, Score, Rating, profile_photo_id, cover_photo_id, Phone) 
  	VALUES('$name','$surname', '$email', '$password', 0, 0, 4, 75, '$phone')";
  	mysqli_query($db, $query);

  	$query = "SELECT * FROM user WHERE Email='$email' AND Password='$password'";
  	$result = mysqli_query($db, $query);
  	$row = mysqli_fetch_assoc($result);
  	$id = $row['Id'];
  	$status = $row[16];

  	$query = "INSERT INTO album (OwnerID, Name) 
  	VALUES('$id','Timeline'),('$id','Profile')";
  	mysqli_query($db, $query);

  	$query = "SELECT * from album WHERE OwnerID = '$id' AND Name = 'Profile'";
  	$result = mysqli_query($db, $query);
  	$row = mysqli_fetch_assoc($result);
  	$query = "INSERT INTO albumtophoto (albumId, Photo_id) 
  	VALUES('$row[0]',4),('$row[0]',75)";
  	mysqli_query($db, $query);

  	$_SESSION['id'] = $id;
  	$_SESSION['name'] = $name;
  	$_SESSION['surname'] = $surname;
  	if (!empty($phone)) { $_SESSION['phone'] = $phone; }
  	$_SESSION['email'] = $email;
  	$_SESSION['password'] = $password;
  	$_SESSION['status'] = $status;
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
			$_SESSION['status'] = $row[16];
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
	$user_id = $_SESSION['id'];
	$Albumid = mysqli_real_escape_string($db, $_POST['AlbumId']);
	$isNewProfilePic = mysqli_real_escape_string($db, $_POST['NewProfilePicture']);

	$description = mysqli_real_escape_string($db, $_POST['description']);
	if(file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])){
		$image = addslashes(file_get_contents($_FILES['image']['tmp_name'])); //SQL Injection defence!
		$image_name = '_'.time().'.'.addslashes($_FILES['image']['name']);

		$query = "INSERT INTO photo (Image, image_name) VALUES ('$image','$image_name')";
		mysqli_query($db, $query);

		$query = "SELECT Id FROM photo WHERE image_name = '$image_name'";
		$results = mysqli_query($db, $query);
		$row = mysqli_fetch_row($results);

		if ($Albumid == 0)
		{
			$querytemp = "SELECT * FROM album WHERE OwnerID = '$user_id' AND Name = 'Timeline'";
			$results = mysqli_query($db, $querytemp);
			$rows = mysqli_fetch_row($results);
			$Albumid = $rows[0];
		}
		$query = "INSERT INTO albumtophoto (PhotoId, AlbumId) VALUES ('$row[0]','$Albumid')";
		mysqli_query($db, $query);

		if ($isNewProfilePic == 1)
		{
			$sql = "UPDATE user SET profile_photo_id = '$row[0]' WHERE Id = '$user_id'";
			mysqli_query($db, $sql);
		}
		if ($isNewProfilePic == 2)
		{
			$sql = "UPDATE user SET cover_photo_id = '$row[0]' WHERE Id = '$user_id'";
			mysqli_query($db, $sql);
		}

		if (!empty($description)){
			echo "Poza cu descriere";
			$query = "INSERT INTO article (Author_Id, Photo_Id, Description) VALUES ('$user_id','$row[0]','$description')";
			mysqli_query($db, $query);
		}
		else{
			$query = "INSERT INTO article (Author_Id, Photo_Id) VALUES ('$user_id','$row[0]')";
			echo "Poza fara descriere";
			mysqli_query($db, $query);
		}
	}
	else if (!(file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name']))){
		if (!empty($description)){
			$query = "INSERT INTO article (Author_Id, Description) VALUES ('$user_id','$description')";
			mysqli_query($db, $query);
		}
		else if (empty($description)){
			array_push($errors, "Your status update is empty!");
			echo '<script>alert("Your status update is empty!");</script>';
		}
	}
	else if (!(file_exists($_FILES['image']['tmp_name']) && is_uploaded_file($_FILES['image']['tmp_name'])) && $Albumid !== 0){
		echo '<script>alert("Please choose a photo!");</script>';
	}
	
	header('location: /FiveStars/profile.php/'.$_SESSION['id']);
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
	$sql = "SELECT * FROM friendship WHERE User2 = $id AND User1 = $id";
	$result = mysqli_query($db, $sql);
	if (!$result || mysqli_num_rows($result) == 0)
	{
		$sql = "INSERT INTO friendship (User1, User2) VALUES ('$_SESSION[id]','$id')";
		mysqli_query($db, $sql);
		$sql = "SELECT FriendshipID FROM friendship WHERE User2 = $id AND User1 = '$_SESSION[id]'";
		$result1 = mysqli_query($db, $sql);
		$row = mysqli_fetch_row($result1);
		$notification_Id =  $row[0];
		$sql = "INSERT INTO notifications (Profile_id, Type, Notification_Id) VALUES ('$id','friendship', '$notification_Id')";
		mysqli_query($db, $sql);
	}
	//header('location: /FiveStars/profile.php/'.$id);
}
if (isset($_POST['accept_friend_request'])){
	$id = mysqli_real_escape_string($db, $_POST['id']);
	$sql = "UPDATE friendship SET Status = 'accepted' WHERE User1 = $id and User2 = $_SESSION[id]";
	mysqli_query($db, $sql);
	header('location: /FiveStars/profile.php/'.$_SESSION['id']);
}

if (isset($_POST['reject_friend_request'])){
	$id = mysqli_real_escape_string($db, $_POST['id']);
	$sql = "UPDATE friendship SET Status = 'declined' WHERE User1 = $id and User2 = $_SESSION[id]";
	mysqli_query($db, $sql);
	header('location: /FiveStars/profile.php/'.$_SESSION['id']);
}
if (isset($_POST['remove_friend'])){
	$id = mysqli_real_escape_string($db, $_POST['id']);
	$sql = "SELECT * FROM friendship WHERE User1 = '$id' AND User2 =' $_SESSION[id]' OR User1 = '$id' AND User2 = '$_SESSION[id]'";
	$result = mysqli_query($db, $sql);
	$row = $row = mysqli_fetch_row($result);
	$notId = $row['FriendshipID'];
	$sql = "DELETE FROM friendship WHERE User1 = '$id' AND User2 =' $_SESSION[id]' OR User1 = '$id' AND User2 = '$_SESSION[id]'";
	mysqli_query($db, $sql);
	$sql = "DELETE FROM notifications WHERE Notification_id = '$notId' AND Profile_id = '$_SESSION[id]' OR Notification_id = '$notId' AND Profile_id = '$id'";
	mysqli_query($db, $sql);
	header('location: /FiveStars/profile.php/'.$_SESSION['id']);
}

if (isset($_POST['new_album'])){
	$name = mysqli_real_escape_string($db, $_POST['name']);
	$id = mysqli_real_escape_string($db, $_POST['comment']);
	$sql = "INSERT INTO album (OwnerID, Name) VALUES ('$id','$name')";
	mysqli_query($db, $sql);
	header('location: /FiveStars/albums.php/'.$_SESSION['id']);
}
if (isset($_POST['send_comment'])){
	date_default_timezone_set('Europe/Bucharest');
	$id = mysqli_real_escape_string($db, $_POST['photo_id']);
	$comment = mysqli_real_escape_string($db, $_POST['comment']);
	$timeOfInsert = date("Y-m-d H:i:s");
	$sql = "INSERT INTO comments (Author_Id, Article_Id, Comment, Rating, Time) VALUES ('$_SESSION[id]','$id','$comment',0, '$timeOfInsert')";

	if (!empty($comment)) 
	{
		mysqli_query($db, $sql);
		$sql = "SELECT * FROM comments WHERE Author_Id = '$_SESSION[id]' AND Article_Id = '$id' AND Comment = '$comment' AND Time = '$timeOfInsert'";
		$result = mysqli_query($db, $sql);
		$row = mysqli_fetch_assoc($result);
		$idcomm = $row['Id'];
		$sql = "SELECT * FROM article WHERE Id = '$id'";
		$result = mysqli_query($db, $sql);
		$row = mysqli_fetch_assoc($result);
		$idtonotify = $row['Author_Id'];
		$sql = "INSERT INTO notifications (Profile_id, Notification_id, Type) VALUES ('$idtonotify','$idcomm','comment')";
		mysqli_query($db, $sql);
	}
	header('location: /FiveStars/main.php/');
}
if (isset($_POST['edit_about'])){
	$city = mysqli_real_escape_string($db, $_POST['City']);
	$education = mysqli_real_escape_string($db, $_POST['Education']);
	$birthday = mysqli_real_escape_string($db, $_POST['Birthday']);
	$description = mysqli_real_escape_string($db, $_POST['Description']);
	$interests = mysqli_real_escape_string($db, $_POST['Interests']);
	$phone = mysqli_real_escape_string($db, $_POST['Phone']);
	$sql = "UPDATE user SET City = '$city', Education = '$education', Birthday = '$birthday', Description = '$description', Interests = '$interests', Phone = '$phone' WHERE Id = $_SESSION[id]";
	mysqli_query($db, $sql);
	header('location: /FiveStars/about.php/'.$_SESSION['id']);
}
if (isset($_POST['change_password'])){
	$oldpass = mysqli_real_escape_string($db, $_POST['oldpassword']);
	$newpass = mysqli_real_escape_string($db, $_POST['newpassword']);
	$newpassconfirm = mysqli_real_escape_string($db, $_POST['newpasswordconfirm']);
	$password = md5($oldpass);
	$sql = "SELECT * FROM user WHERE Id = '$_SESSION[id]' AND Password = '$password'";
	$results = mysqli_query($db, $sql);
	if (mysqli_num_rows($results) == 1 && strcmp($newpass,$newpassconfirm) == 0)
	{
		$newpassword = md5($newpass);
		$sql = "UPDATE user SET Password = '$newpassword' WHERE Id = '$_SESSION[id]' AND Password = '$password'";
		mysqli_query($db, $sql);
	}
	header('location: /FiveStars/about.php/'.$_SESSION['id']);
}
if (isset($_POST['edit_description'])){
	$id = mysqli_real_escape_string($db, $_POST['id']);
	$newdescription = mysqli_real_escape_string($db, $_POST['newdescription']);
	$sql = "UPDATE article SET Description = '$newdescription' WHERE Id = '$id'";
	mysqli_query($db, $sql);
	header('location: /FiveStars/profile.php/'.$_SESSION['id']);
}
if (isset($_POST['delete_photo'])){
	$id = mysqli_real_escape_string($db, $_POST['id']);
	$sql = "SELECT * FROM article WHERE Id = '$id'";
	$result = mysqli_query($db, $sql);
	$row = mysqli_fetch_assoc($result);
	if (strcmp($_SESSION['status'],'administrator') == 0 || strcmp($_SESSION['status'],'moderator') == 0 && $row['Author_Id'] !== $_SESSION['id']){
		$sql = "DELETE FROM reports WHERE Content_Id = '$id'";
		mysqli_query($db, $sql);
		
	}
	$sql = "DELETE FROM notes WHERE To_Id = '$id' AND Type='article'";
	mysqli_query($db, $sql);
	$sql = "DELETE FROM article WHERE Id = '$id'";
	mysqli_query($db, $sql);
	
	header('location: /FiveStars/profile.php/'.$_SESSION['id']);
}
if (isset ($_POST['delete_comment'])){
	$id = mysqli_real_escape_string($db, $_POST['id']);
	$sql = "DELETE FROM notifications WHERE Notification_Id = '$id' AND Type = 'comment'";
	mysqli_query($db, $sql);
	$sql = "DELETE FROM notes WHERE To_Id = '$id' AND Type='comment'";
	mysqli_query($db, $sql);
	$sql = "DELETE FROM comments WHERE Id = '$id'";
	mysqli_query($db, $sql);
}
if (isset($_POST['report_content'])){
	$reporter_id = mysqli_real_escape_string($db, $_POST['reporter_id']);
	$article_id = mysqli_real_escape_string($db, $_POST['content_id']);
	$sql = "SELECT * FROM reports WHERE Content_Id = '$article_id' AND Reporter_Id = '$reporter_id'";
	$results = mysqli_query($db, $sql);
	if (!$results || mysqli_num_rows($results) == 0){
		$sql = "INSERT INTO reports (Content_Id, Reporter_Id) VALUES ('$article_id','$reporter_id')";
		mysqli_query($db, $sql);
	}
	header('location: /FiveStars/profile.php/'.$_SESSION['id']);
}
if (isset($_POST['send_rate'])){
	$id = mysqli_real_escape_string($db, $_POST['content_id']);
	$type = mysqli_real_escape_string($db, $_POST['content_type']);
	$note = mysqli_real_escape_string($db, $_POST['note']);
	$giver = $_SESSION['id'];
	$sql = "SELECT * FROM notes WHERE Note_Giver = $_SESSION[id] AND To_Id = '$id'";
	$resultsALL = mysqli_query($db, $sql);
	if (!mysqli_num_rows($resultsALL) == 1) {
		$sql = "INSERT INTO notes (Note_Giver, Type, To_Id, Note) VALUES ('$giver','$type','$id','$note')";
		mysqli_query($db, $sql);
		$sql1 = "SELECT * FROM notes WHERE Note_Giver = '$giver' AND To_Id = '$id' AND Type = '$type' AND Note = '$note'";
		$results = mysqli_query($db, $sql1);
		$rows = mysqli_fetch_assoc($results);
		$NotId = $rows['Id'];
	} else {
		$sql = "UPDATE notes SET Note = '$note' WHERE Note_Giver = '$giver' AND Type = '$type' AND To_Id = '$id'";
		mysqli_query($db, $sql);
	}
	if (strcmp($type,'comment') == 0){
		$sql = "SELECT * FROM comments WHERE Id = $id";
		$results = mysqli_query($db, $sql);
		$row = mysqli_fetch_assoc($results);
		$idtonotify = $row['Author_id'];
		if (!mysqli_num_rows($resultsALL) == 1) {
			$oldrating = $row['rating'];
			$newrating = (($oldrating * $row['total_notes']) + $note)/($row['total_notes'] + 1); 
			$total_note = $row['total_notes'] + 1;
		}
		else {
			$sqlcalc = "SELECT * FROM notes WHERE Note_Giver <> '$giver' AND Type = '$type' AND To_Id = '$id'";
			$resultscalc = mysqli_query($db, $sqlcalc);
			$newrating = 0;
			while ($rowcalc = mysqli_fetch_assoc($resultscalc)){
				$newrating = $newrating + $rowcalc['Note'];
			}
			$newrating = ($newrating + $note) / $row['total_notes'];
			$total_note = $row['total_notes'];
		}
		$sql = "UPDATE comments SET Rating = '$newrating', total_notes = '$total_note' WHERE Id = '$id'";
		mysqli_query($db, $sql);
		if (!mysqli_num_rows($resultsALL) == 1) {
			$sql = "INSERT INTO notifications (Profile_id, Notification_id, Type) VALUES ('$idtonotify','$NotId','note')";
			mysqli_query($db, $sql);
		}
		header('location: /FiveStars/main.php/');
	}
	else if (strcmp($type,'article') == 0){
		$sql = "SELECT * FROM article WHERE Id = $id";
		$results = mysqli_query($db, $sql);
		$row = mysqli_fetch_assoc($results);
		$idtonotify = $row['Author_Id'];
		if (!mysqli_num_rows($resultsALL) == 1) {
			$oldrating = $row['rating'];
			$newrating = (($oldrating * $row['total_notes']) + $note)/($row['total_notes'] + 1); 
			$total_note = $row['total_notes'] + 1;
		}
		else {
			$sqlcalc = "SELECT * FROM notes WHERE Note_Giver <> '$giver' AND Type = '$type' AND To_Id = '$id'";
			$resultscalc = mysqli_query($db, $sqlcalc);
			$newrating = 0;
			while ($rowcalc = mysqli_fetch_assoc($resultscalc)){
				$newrating = $newrating + $rowcalc['Note'];
			}
			$newrating = ($newrating + $note) / $row['total_notes'];
			$total_note = $row['total_notes'];
		}
		$sql = "UPDATE article SET Note = '$newrating', total_notes = '$total_note' WHERE Id = '$id'";
		mysqli_query($db, $sql);
		if (!mysqli_num_rows($resultsALL) == 1) {
			$sql = "INSERT INTO notifications (Profile_id, Notification_id, Type) VALUES ('$idtonotify','$NotId','note')";
			mysqli_query($db, $sql);
		}
		header('location: /FiveStars/main.php/');
	}
	else if (strcmp($type,'profile') == 0){
		$sql = "SELECT * FROM user WHERE Id = $id";
		$results = mysqli_query($db, $sql);
		$row = mysqli_fetch_assoc($results);
		$idtonotify = $row['Id'];
		if (!mysqli_num_rows($resultsALL) == 1) {
			$oldrating = $row['Rating'];
			$newrating = (($oldrating * $row['total_notes']) + $note)/($row['total_notes'] + 1);
			$total_note = $row['total_notes'] + 1;
		}
		else {
			$sqlcalc = "SELECT * FROM notes WHERE Note_Giver <> '$giver' AND Type = '$type' AND To_Id = '$id'";
			$resultscalc = mysqli_query($db, $sqlcalc);
			$newrating = 0;
			while ($rowcalc = mysqli_fetch_assoc($resultscalc)){
				$newrating = $newrating + $rowcalc['Note'];
			}
			$newrating = ($newrating + $note) / $row['total_notes'];
			$total_note = $row['total_notes'];
		}
		$sql = "UPDATE user SET Rating = '$newrating', total_notes = '$total_note' WHERE Id = '$id'";
		mysqli_query($db, $sql);
		if (!mysqli_num_rows($resultsALL) == 1) {
			$sql = "INSERT INTO notifications (Profile_id, Notification_id, Type) VALUES ('$idtonotify','$NotId','note')";
			mysqli_query($db, $sql);
		}
			header('location: /FiveStars/profile.php/'.$id);
	}
	else if (strcmp($type,'album') == 0){
		$sql = "SELECT * FROM album WHERE ID = $id";
		$results = mysqli_query($db, $sql);
		$row = mysqli_fetch_assoc($results);
		$idtonotify = $row['OwnerID'];
		if (!mysqli_num_rows($resultsALL) == 1) {
			$oldrating = $row['rating'];
			$newrating = (($oldrating * $row['total_notes']) + $note)/($row['total_notes'] + 1); 
			$total_note = $row['total_notes'] + 1;
		}
		else {
			$sqlcalc = "SELECT * FROM notes WHERE Note_Giver <> '$giver' AND Type = '$type' AND To_Id = '$id'";
			$resultscalc = mysqli_query($db, $sqlcalc);
			$newrating = 0;
			while ($rowcalc = mysqli_fetch_assoc($resultscalc)){
				$newrating = $newrating + $rowcalc['Note'];
			}
			$newrating = ($newrating + $note) / $row['total_notes'];
			$total_note = $row['total_notes'];
		}
		$sql = "UPDATE album SET Note = '$newrating', total_notes = '$total_note' WHERE ID = '$id'";
		mysqli_query($db, $sql);
		if (!mysqli_num_rows($resultsALL) == 1) {
			$sql = "INSERT INTO notifications (Profile_id, Notification_id, Type) VALUES ('$idtonotify','$NotId','note')";
			mysqli_query($db, $sql);
		}
		header('location: /FiveStars/albumview.php/'.$idtonotify.'/'.$id);
	}

}

?>