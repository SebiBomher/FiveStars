<?php include('classes.php') ?>
<?php

class Functions{


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
		$profile->set_rating($result[7]);
		$profile->set_score($result[5]);
		$profile->set_phone_number($result[6]);
		$profile->set_profile_photo_id($result[8]);
		$profile->set_cover_photo_id($result[9]);
		$profile->set_privilege($result[16]);
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
	function show_title($title)
	{ 
		echo '<div class="container mt-3 text-center">';
		echo '<h3>'.$title.'<h3>';
		echo '</div>';
	}

	function show_coverphoto($id,$idCurrent)
	{
		$functions = new Functions();
		echo '<div class="container mt-3 text-center">';
		echo '<img src="data:image/jpeg;base64,'.base64_encode($functions->get_imageblob($id)).'"class="" data-toggle="modal" data-target="#myModala" width="851" height="315"/>';
		$functions->show_photoinmodal($id,'a');
		if ($idCurrent == $_SESSION['id']) {
			$db = mysqli_connect('localhost', 'root', '', 'socialsite');
			$sql = "SELECT * FROM album WHERE OwnerID = $idCurrent AND Name = 'Profile'";
			$sth = mysqli_query($db, $sql);
			$row = mysqli_fetch_assoc($sth);
			$functions->show_uploadphotoinmodal("myModalCover",$row['ID'],2, "Change Cover Photo");
		}
		echo '</div>';
	}

	function show_profilephoto($id,$idCurrent)
	{
		$functions = new Functions();
		echo '<div class="container mt-3 text-center">';
		echo '<img class="rounded-circle border-dark rounded" data-toggle="modal" data-target="#myModalb" src="data:image/jpeg;base64,'.base64_encode($functions->get_imageblob($id)).'" width="180" height="180"/>';
		$functions->show_photoinmodal($id,'b');
		if ($idCurrent == $_SESSION['id']) {
			$db = mysqli_connect('localhost', 'root', '', 'socialsite');
			$sql = "SELECT * FROM album WHERE OwnerID = $idCurrent AND Name = 'Profile'";
			$sth = mysqli_query($db, $sql);
			$row = mysqli_fetch_assoc($sth);
			$functions->show_uploadphotoinmodal("myModalProfile",$row['ID'],1,"Change Profile Photo");
		}
		echo '</div>';
	}
	function show_profilephotoicon1($blob)
	{

		echo '<img src="data:image/jpeg;base64,'.base64_encode( $blob ).'"class="rounded-circle border-dark rounded" width="30" height="30"/>';

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
		echo '<div class="dropdown-menu overflow-auto" aria-labelledby="dropdownMenuButton">';
		$functions->notificationshow($id);
		echo '</div>';
		echo '</div>';
	}
	function notificationshow($id)
	{
		$functions = new Functions();
		$db = mysqli_connect('localhost', 'root', '', 'socialsite');
		$sql = "SELECT * FROM notifications WHERE Profile_id = $id ORDER BY Time DESC" ;
		$result = mysqli_query($db, $sql);
		while ($row = mysqli_fetch_assoc($result)){
			$notificationType = $row['Type'];
			$notificationId = $row['Notification_id'];
			$Time = $row['Time'];
			echo '<div class="">';
			if (strcmp($notificationType,'friendship') == 0)
			{
				$functions->friend_request_notification($notificationId,$Time);
			}
			else if (strcmp($notificationType,'comment') == 0)
			{
				$functions->comment_notification($notificationId,$Time);
			}
			else if (strcmp($notificationType,'note') == 0)
			{
				$functions->note_notification($notificationId,$Time);
			}
			echo '</div>';
		}


	}
	function note_notification($id,$Time){
		$functions = new Functions();
		$db = mysqli_connect('localhost', 'root', '', 'socialsite');
		$sql = "SELECT * FROM notes WHERE Id = $id";
		$result = mysqli_query($db, $sql);
		$row = mysqli_fetch_assoc($result);
		$note = $row['Note'];
		$type = $row['Type'];
		$to_id = $row['To_Id'];
		$fromnoteid = $row['Note_Giver'];
		$sql = "SELECT * FROM user WHERE Id = '$fromnoteid'";
		$result = mysqli_query($db, $sql);
		$row = mysqli_fetch_assoc($result);
		$fullname = $row['Name'].' '.$row['Surname'];
		if (strcmp($type,'profile') == 0) echo '<a class="dropdown-item" href="/fivestars/profile.php/'.$_SESSION['id'].'">'.$fullname.' has rated your profile with '.$note.' stars! <p class="text-muted">'.$Time.'</p> </a>';
		else if (strcmp($type,'comment') == 0) {
			$sql = "SELECT * FROM comments WHERE Id = '$row[To_Id]'";
			$result = mysqli_query($db, $sql);
			$row = mysqli_fetch_assoc($result);
			echo '<a class="dropdown-item" href="/fivestars/article.php/'.$row['Article_Id'].'">'.$fullname.' has rated your comment with '.$note.' stars! <p class="text-muted">'.$Time.'</p> </a>';
		}
		else if (strcmp($type,'album') == 0) echo '<a class="dropdown-item" href="/fivestars/albumview.php/'.$_SESSION['id'].'/'.$to_id.'">'.$fullname.' has rated your album with '.$note.' stars! <p class="text-muted">'.$Time.'</p> </a>';
		else if (strcmp($type,'article') == 0) echo '<a class="dropdown-item" href="/fivestars/article.php/'.$to_id.'">'.$fullname.' has rated your article with '.$note.' stars! <p class="text-muted">'.$Time.'</p> </a>';
	}
	function comment_notification($id,$Time)
	{
		$functions = new Functions();
		$db = mysqli_connect('localhost', 'root', '', 'socialsite');
		$sql = "SELECT * FROM comments WHERE Id = $id";
		$result = mysqli_query($db, $sql);
		$row = mysqli_fetch_assoc($result);
		$article_id = $row['Article_Id'];
		$fromcommentid = $row['Author_Id'];
		$sql = "SELECT * FROM user WHERE Id = $fromcommentid";
		$result = mysqli_query($db, $sql);
		$row = mysqli_fetch_assoc($result);
		$fullname = $row['Name'].' '.$row['Surname'];
		echo '<a class="dropdown-item" href="/fivestars/article.php/'.$article_id.'">'.$fullname.' has commented on one of your photos!<p class="text-muted">'.$Time.'</p> </a>';
	}
	function friend_request_notification($id,$Time)
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
				echo '<a class="dropdown-item" href="/FiveStars/profile.php/'.$row1[0].'"> You are now friends with '.$row1[1]." ".$row1[2].'<p class="text-muted">'.$Time.'</p> </a>';
			}
			else if (strcmp($status,'refuesed') == 0){
				echo '<a class="dropdown-item" href="/FiveStars/profile.php/'.$row1[0].'"> You have refuesed to be friends with'.$row1[1]." ".$row1[2].'<p class="text-muted">'.$Time.'</p> </a>';
			}
			echo '</a>';
		}

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


	function display_stars($rating)
	{
		$server = new Functions();
		if ($rating == 0) for ($i = 0; $i < 5; $i = $i + 1) $server->show_profilephotoicon1($server->get_imageblob(87));
		if ($rating >= 1 && $rating < 2){
			for ($i = 0; $i < 1; $i = $i + 1) $server->show_profilephotoicon1($server->get_imageblob(86));
				for ($i = 0; $i < 4; $i = $i + 1) $server->show_profilephotoicon1($server->get_imageblob(87));
			}
		else if ($rating >= 2 && $rating < 3){
			for ($i = 0; $i < 2; $i = $i + 1) $server->show_profilephotoicon1($server->get_imageblob(86));
				for ($i = 0; $i < 3; $i = $i + 1) $server->show_profilephotoicon1($server->get_imageblob(87));
			}
		else if ($rating >= 3 && $rating < 4){
			for ($i = 0; $i < 3; $i = $i + 1) $server->show_profilephotoicon1($server->get_imageblob(86));
				for ($i = 0; $i < 2; $i = $i + 1) $server->show_profilephotoicon1($server->get_imageblob(87));
			}
		else if ($rating >= 4 && $rating < 5){
			for ($i = 0; $i < 4; $i = $i + 1) $server->show_profilephotoicon1($server->get_imageblob(86));
				for ($i = 0; $i < 1; $i = $i + 1) $server->show_profilephotoicon1($server->get_imageblob(87));
			}
		else if ($rating == 5){
			for ($i = 0; $i < 5; $i = $i + 1) $server->show_profilephotoicon1($server->get_imageblob(86));
		}
}
function show_photoinmodal($id,$i)
{
	$server = new Functions();
	$db = mysqli_connect('localhost', 'root', '', 'socialsite');
	$sql = "SELECT * FROM article WHERE Photo_Id = $id";
	$result = mysqli_query($db, $sql);
	$row = mysqli_fetch_assoc($result);
	$description = $row['Description'];
	$article_id = $row['Id'];
	$note = $row['Note'];
	$owner = $row['Author_Id'];
	$time = $row['Time'];
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
	if ((strcmp($_SESSION['status'],'administrator') == 0 || strcmp($_SESSION['status'],'moderator') == 0) && !(($id == 4) || ($id == 75) || ($id == 86) ||  ($id == 87))){
		$topass = "'".$i."'";
		echo '<form class="text-center" method="POST" action="/fivestars/server.php">';
		echo '<input id="delete_photo_id'.$i.'" type="text" class="form-control d-none" name="id" value="'.$article_id.'" >';
		echo '<h3 id="confirm'.$i.'" class="d-none">Are you sure you want to delete this?</h3>';
		echo '<button id="delete_button'.$i.'" type="submit" class="btn btn-danger d-none" name="delete_photo">Delete</button>';
		echo '</form>';
		echo '<button id="delete_Photo'.$i.'" class="btn btn-danger" onclick="delete_photo('.$topass.')">Delete</button>';
	}
	if (!(($id == 4) || ($id == 75) || ($id == 86) ||  ($id == 87))){
		echo '<form class="form-inline" method="POST" action="/fivestars/server.php">';
		echo '<input type="text" class="form-control d-none" name="reporter_id" value="'.$_SESSION['id'].'" >';
		echo '<input type="text" class="form-control d-none" name="content_id" value="'.$article_id.'" >';
		echo '<button type="submit" class="btn btn-xs btn-danger" name="report_content">Report</button>';
		echo '</form">';
	}
	echo '<button type="button" class="close" data-dismiss="modal">&times;</button>';
	echo '</div>';
	echo '<div class="modal-body">';
	$functions->show_photofull($id);
	echo '<p id="description'.$i.'" class="text-center">'.$description.'</p>';
	echo '<p class="text-muted">'.$time.'</p>';
	echo '<h2 class="text-center">';
	$server->display_stars($note);
	echo '</h2>';
	if ($note == 0) echo '<h2 class="text-center">No rating</h2>';
	else echo '<h2 class="text-center">'.$note.'</h2>';
	if (!($_SESSION['id'] == 1 && (($id == 4) || ($id == 75) || ($id == 86) ||  ($id == 87))) && ($owner == $_SESSION['id'])) 
	{
		echo '<form class="text-center" method="POST" action="/fivestars/server.php">';
		echo '<input id="change_description'.$i.'" type="text" class="form-control d-none" name="newdescription" value="'.$description.'" >';
		echo '<button id="change_button'.$i.'" type="submit" class="btn btn-primary d-none" name="edit_description">Save changes</button>';

		echo '<input id="delete_photo_id'.$i.'" type="text" class="form-control d-none" name="id" value="'.$article_id.'" >';
		echo '<h3 id="confirm'.$i.'" class="d-none">Are you sure you want to delete this?</h3>';
		echo '<button id="delete_button'.$i.'" type="submit" class="btn btn-danger d-none" name="delete_photo">Delete</button>';
		echo '</form>';

		$topass = "'".$i."'";
		echo '<button id="edit_cancel'.$i.'" class="btn btn-danger d-none" onclick="cancel_edit_photo('.$topass.')">Cancel</button>';
		echo '<button id="edit_description'.$i.'" class="btn btn-primary" onclick="edit_photo('.$topass.')">Edit</button>';
		echo '<button id="delete_Photo'.$i.'" class="btn btn-danger" onclick="delete_photo('.$topass.')">Delete</button>';
	}


	if ($owner !== $_SESSION['id']) $functions->show_rate($article_id,'article');
	echo '<form class="form-inline" method="POST" action="/fivestars/server.php">';
	echo '<input type="text" class="d-none" name="photo_id" value="'.$article_id.'">';
	echo '<input type="text" class="form-control mt-3 w-75" name="comment">';
	echo '<button type="submit" class="btn btn-default mt-3 w-25" name="send_comment">Comment</button>';
	echo '</form>';
	echo '</div>';
	$functions->show_commentsnormal($article_id);
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
function show_rate($id,$type){
	$db = mysqli_connect('localhost', 'root', '', 'socialsite');
	$functions = new Functions();
	$sql = "SELECT * FROM notes WHERE Note_Giver = $_SESSION[id] AND To_Id = '$id'";
	$results = mysqli_query($db, $sql);
	echo '<form class="form-inline" method="POST" action="/fivestars/server.php">';
	if (mysqli_num_rows($results) == 1) echo '<h5 class="d-inline ">Your rate</h5>';
	echo '<select class="form-control ml-3 w-25 d-inline" id="rate" name="note">';
	if (mysqli_num_rows($results) == 1) {
		$row = mysqli_fetch_assoc($results);
		$currentnote = $row['Note'];
		if ($currentnote == 1) echo '<option selected="selected">1</option>';
		else echo '<option>1</option>';
		if ($currentnote == 2) echo '<option selected="selected">2</option>';
		else echo '<option>2</option>';
		if ($currentnote == 3) echo '<option selected="selected">3</option>';
		else echo '<option>3</option>';
		if ($currentnote == 4) echo '<option selected="selected">4</option>';
		else echo '<option>4</option>';
		if ($currentnote == 5) echo '<option selected="selected">5</option>';
		else echo '<option>5</option>';
	} else{
		echo '<option>1</option>';
		echo '<option>2</option>';
		echo '<option>3</option>';
		echo '<option>4</option>';
		echo '<option>5</option>';
	}
	'</select>';
	echo '<input type="text" class="form-control d-none" name="content_type" value="'.$type.'">';
	echo '<input type="text" class="form-control d-none" name="content_id" value="'.$id.'">';
	echo '<button type="submit" class="btn btn-default ml-2 w-25" name="send_rate">Rate</button>';
	echo '</form>';
}


function show_commentsnormal($id)
{
	$db = mysqli_connect('localhost', 'root', '', 'socialsite');
	$functions = new Functions();
	$sql = "SELECT * FROM comments WHERE Article_Id = $id ORDER BY Time DESC";
	$result = mysqli_query($db, $sql);
	$i = 0;
	while ($row = mysqli_fetch_assoc($result)){
		$commId = $row['Id'];
		$Author_Id = $row['Author_Id'];
		$comment = $row['Comment'];
		$time = $row['Time'];
		$rating = $row['Rating'];
		$sql1 = "SELECT * FROM user WHERE Id = $Author_Id";
		$result1 = mysqli_query($db, $sql1);
		$row1 = mysqli_fetch_assoc($result1);
		$author_name = $row1['Name'].' '.$row1['Surname'];
		$profile_photo_id = $row1['profile_photo_id'];
		echo '<div class="card">';
		echo '<div class="card-body">';
		if (($Author_Id == $_SESSION['id'])) 
		{
			echo '<form class="text-center" method="POST" action="/fivestars/server.php">';
			echo '<input id="delete_photo_id_comment'.$i.'" type="text" class="form-control d-none" name="id" value="'.$commId.'" >';
			echo '<h3 id="confirm_comment'.$i.'" class="d-none">Are you sure you want to delete this?</h3>';
			echo '<button id="delete_button_comment'.$i.'" type="submit" class="btn btn-danger d-none" name="delete_comment">Delete</button>';
			echo '</form>';

			$topass = "'".$i."'";
			echo '<button id="edit_cancel_comment'.$i.'" class="btn btn-danger d-none" onclick="cancel_delete_comment('.$topass.')">Cancel</button>';
			echo '<button id="delete_Photo_comment'.$i.'" class="btn btn-danger" onclick="delete_comment('.$topass.')">Delete</button>';
		}

		echo '<h5 class="card-title d-inline  ">'.$functions->show_profilephotoicon1($functions->get_imageblob($profile_photo_id)).' '.$author_name.'</h5>';
		echo '<p class="card-text float-right">'.$comment.'</p>';
		echo '</div>';
		echo '<div class="card-footer text-muted">';
		echo '<p class ="float-left">'.$time.'</p>';
		if ($Author_Id !== $_SESSION['id']) $functions->show_rate($commId,'comment');
		if ($rating == 0) echo '<p class ="float-right">No rating</p>';
		else echo '<p class ="float-right">'.$rating.'</p>';
		echo '<p class ="float-right">'.$functions->display_stars($rating).'</p>';
		echo '</div>';
		echo '</div>';
		$i = $i + 1;
	}
}
function get_article($id)
{
	$functions = new Functions();
	$db = mysqli_connect('localhost', 'root', '', 'socialsite');
	$sql1 = "SELECT * FROM article WHERE Id = $id";
	$result1 = mysqli_query($db, $sql1);
	$row1 = mysqli_fetch_assoc($result1);
	$sql2 = "SELECT * FROM user WHERE Id = '$row1[Author_Id]'";
	$result2 = mysqli_query($db, $sql2);
	$row2 = mysqli_fetch_assoc($result2);
	echo '<div class="container" >';
	echo '<div class="card text-center mt-5 mb-5 w-50 h-50" >';
	echo '<h5 class="card-header">'.$row2['Name'].' '.$row2['Surname'].'</h5>';
	echo '<div class="card-body">';
	echo '<p id="description" class="text-center">'.$row1['Description'].'</p>';
	if ($row1['Photo_Id'] != null ) $functions->show_photowithclick($row1['Photo_Id'],0);
	$functions->display_stars($row1['Note']);
	if ($row1['Note'] == 0) echo '<h2 class="text-center">No rating</h2>';
	else echo '<h2 class="text-center">'.$row1['Note'].'</h2>';
	$functions->show_commentsnormal($row1['Id']);
	echo '</div>';
	echo '</div>';
	echo '</div>';
}
function get_for_review(){
	$functions = new Functions();
	$db = mysqli_connect('localhost', 'root', '', 'socialsite');
	$sql = "SELECT * FROM reports";
	$result = mysqli_query($db, $sql);
	while ($row = mysqli_fetch_assoc($result)){
		$functions->get_article($row['Content_Id']);
	}
}
function show_newsfeed($id)
{
	$functions = new Functions();
	$db = mysqli_connect('localhost', 'root', '', 'socialsite');
	$sql = "SELECT * FROM friendship WHERE User1 = $id OR User2 = $id";
	$result = mysqli_query($db, $sql);
	$articlesarray = array();
	while ($row = mysqli_fetch_assoc($result)){
		$friendsID1 = $row['User1'];
		$friendsID2 = $row['User2'];
		if ($friendsID2 == $id) $friendsID = $friendsID1;
		else if ($friendsID1 == $id) $friendsID = $friendsID2;
		$sql1 = "SELECT * FROM article WHERE Author_Id = $friendsID";
		$result1 = mysqli_query($db, $sql1);
		while ($row1 = mysqli_fetch_assoc($result1)){
			$article = new Photo();
			$article->set_time($row1['Time']);
			$article->set_id($row1['Id']);
			array_push($articlesarray,$article);
		}
	}
	usort($articlesarray, array("Photo", "timecomp"));
	for ($i = 0; $i < sizeof($articlesarray); $i = $i + 1){
		$article_id = $articlesarray[$i]->get_id();
		$sql = "SELECT * FROM article WHERE Id = '$article_id'";
		$result = mysqli_query($db, $sql);
		$row1 = mysqli_fetch_assoc($result);
		$sql2 = "SELECT * FROM user WHERE Id = '$row1[Author_Id]'";
		$result2 = mysqli_query($db, $sql2);
		$row2 = mysqli_fetch_assoc($result2);
		echo '<div class="container" >';
		echo '<div class="card text-center mt-5 mb-5 w-50 h-50" >';
		echo '<h5 class="card-header">'.$row2['Name'].' '.$row2['Surname'].' <p class="text-muted">'.$articlesarray[$i]->get_time().'</p></h5>';
		echo '<div class="card-body">';
		echo '<p id="description" class="text-center">'.$row1['Description'].'</p>';
		if ($row1['Photo_Id'] != null ) $functions->show_photowithclick($row1['Photo_Id'],$i);
		$functions->display_stars($row1['Note']);
		if ($row1['Note'] == 0) echo '<h2 class="text-center">No rating</h2>';
		else echo '<h2 class="text-center">'.$row1['Note'].'</h2>';
        //$functions->show_commentsnormal($row1['Id']);
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
}
function show_timeline($id){
	$functions = new Functions();
	$db = mysqli_connect('localhost', 'root', '', 'socialsite');
	$sql = "SELECT * FROM article WHERE Author_Id = '$id' ORDER BY Time DESC";
	$result = mysqli_query($db, $sql);
	$i = 0;
	while ($row = mysqli_fetch_assoc($result)){
		$sql2 = "SELECT * FROM user WHERE Id = '$row[Author_Id]'";
		$result2 = mysqli_query($db, $sql2);
		$row2 = mysqli_fetch_assoc($result2);
		echo '<div class="container text-center">';
		echo '<div class="card text-center mt-5 mb-5 w-50 h-50" >';
		echo '<h5 class="card-header">'.$row2['Name'].' '.$row2['Surname'].'<p class="text-muted">'.$row['Time'].'</p></h5>';
		echo '<div class="card-body">';
		if ($row['Photo_Id'] != null ) $functions->show_photowithclick($row['Photo_Id'],$i);
		$functions->display_stars($row['Note']);
		if ($row['Note'] == 0) echo '<h2 class="text-center">No rating</h2>';
		else echo '<h2 class="text-center">'.$row['Note'].'</h2>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		$i = $i + 1;
	}
}
function is_Moderator($id){
	$db = mysqli_connect('localhost', 'root', '', 'socialsite');
	$sql = "SELECT * FROM friendship WHERE User1 = $id AND User1 = '$_SESSION[id]' OR User2 = $id AND User1 = '$_SESSION[id]'";
	$result = mysqli_query($db, $sql);
	if (mysqli_num_rows($result) == 1) return true;
	else return false;
}
function is_friend($id){
	$db = mysqli_connect('localhost', 'root', '', 'socialsite');
	$sql = "SELECT * FROM friendship WHERE User1 = $id AND User2 = '$_SESSION[id]' OR User2 = $id AND User1 = '$_SESSION[id]'";
	$result = mysqli_query($db, $sql);
	if (mysqli_num_rows($result) == 1) return true;
	else return false;
}
function show_navigationbar($id)
{
	$functions = new Functions();
	echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top" style="z-index: 2">';
	echo '<a class="navbar-brand" href="#">FiveStars</a>';
	if (strcmp($_SESSION['status'],'administrator') == 0 || strcmp($_SESSION['status'],'moderator') == 0) 
		echo '<a class="navbar-brand" href="/fivestars/review.php">Review</a>';
	echo '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">';
	echo '<span class="navbar-toggler-icon"></span>';
	echo '</button>';
	echo '<div class="collapse navbar-collapse" id="navbarSupportedContent">';
	echo '<ul class="navbar-nav mr-auto">';
	echo '<li class="nav-item active">';
	echo '<a class="nav-link" href="/fivestars/logout.php">Logout<span class="sr-only">(current)</span></a>';
	echo '</li>';
	echo '</ul>';
	echo '<ul class="navbar-nav ml-auto">';
	echo '<li class="nav-item active">';
	if (isset($_SESSION['name']) && isset($_SESSION['surname']) && isset($_SESSION['id'])) {
		$profile1 = $functions->get_profile($id);
		$functions->show_smallprofilephotodropdown($functions->get_imageblob($profile1->get_profile_photo_id()),$id);
	}
	echo '</li>';
	echo '<li class="nav-item active">';
	if (isset($_SESSION['name']) && isset($_SESSION['surname']) && isset($_SESSION['id']))
		echo '<a class="nav-link" href="/fivestars/profile.php/'.$_SESSION['id'].'" id="profile_view">'.$_SESSION['name'].' '.$_SESSION['surname'].'<span class="sr-only">(current)</span></a>';
	echo '</li>';
	echo '<li class="nav-item active">';
	echo '<a class="nav-link" href="/fivestars/main.php">Home<span class="sr-only">(current)</span></a>';
	echo '</li>';
	echo '<form class="form-inline" method="POST" action="/fivestars/search.php">';
	echo '<input class="form-control mr-sm-2" type="search" placeholder="Search" name="query" aria-label="Search">';
	echo '<button class="btn btn-light my-2 my-sm-0" type="submit" name="search">Search</button>';
	echo '</form>';
	echo '</ul>';
	echo '</div>';
	echo '</nav>';
}
function show_chat($id)
{
	echo '<script type="text/javascript" src="http://localhost/fivestars/scripts.js"></script>';
	echo '<div class="float-right container sticky-top" style="height: 100vh; width: 350px; z-index: 1">';
	echo '<h3 class="card-header invisible">Chat</h3>';
	echo '<h3 class="card-header">Chat</h3>';
	echo '<div class="card bg-light h-100">';
	echo '<div class="card-body">';
	$profiles = array();
	$functions = new Functions();
	$functions->recive_friends($id,$profiles);
	$blank = "'_blank'";
	$size = "'left=20,top=20,width=500,height=500,toolbar=1,resizable=0'";
	for ($i = 0; $i < sizeof($profiles); $i = $i + 1)
	{

		echo '<a class="card-text float-right mt-3" href="/fivestars/message.php/'.$_SESSION['id'].'/'.$profiles[$i]->get_id().'" onclick="window.open(this.href, '.$blank.', '.$size.'); return false;">'.$profiles[$i]->get_name().' '.$profiles[$i]->get_surname().'</a>';
		echo '<p class="card-text " href="/fivestars/profile/php/'.$profiles[$i]->get_id().'">'.$functions->show_profilephotoicon($functions->get_imageblob($profiles[$i]->get_profile_photo_id())).'</p>';

	}
	
	echo '</div>';
	echo '</div>';
	echo '</div>';
}
function get_about($id,&$about,&$Birthday)
{
	$functions = new Functions();
	$db = mysqli_connect('localhost', 'root', '', 'socialsite');
	$sql = "SELECT * FROM user WHERE Id = $id";
	$result = mysqli_query($db, $sql);
	$row = mysqli_fetch_assoc($result);
	$Birthday = $row['Birthday'];
	array_push($about,$row['City']);
	array_push($about,$row['Education']);
	array_push($about,$row['Birthday']);
	array_push($about,$row['Description']);
	array_push($about,$row['Interests']);
	array_push($about,$row['Phone']);
}
function get_album($id,&$album){
	$db = mysqli_connect('localhost', 'root', '', 'socialsite');
	$sql = "SELECT * FROM album WHERE ID = $id";
	$result = mysqli_query($db, $sql);
	while ($row = mysqli_fetch_assoc($result)){
		array_push($album,$row['Name']);
		array_push($album,$row['Note']);
	}

}
function get_chat($idYou,$idOther){
	echo $idYou.' '.$idOther;
	$functions = new Functions();
	$profileYou = $functions->get_profile($idYou);
	$profileToMessage = $functions->get_profile($idOther);
	
	$chat = array();
	$db = mysqli_connect('localhost', 'root', '', 'socialsite');
	$sql = "SELECT * FROM message WHERE From_user = '$idYou' AND To_user = '$idOther' OR To_user = '$idYou' AND From_user = '$idOther'";
	$result = mysqli_query($db, $sql);
	while ($row = mysqli_fetch_assoc($result)){
		$message = new Message();
		$message->set_id($row['Id']);
		$message->set_message($row['Message']);
		$message->set_sender($row['From_user']);
		$message->set_reciver($row['To_user']);
		$message->set_time($row['Time']);
		array_push($chat,$message);
	}
	usort($chat, array("Message", "timecomp"));
	for ($i = 0; $i < sizeof($chat); $i++)
	{
		if ($message->get_sender() == $idYou){
			echo '<div class="d-inline">';
			echo '<p class="float-right">'.$chat[$i]->get_message();
			$functions->show_profilephotoicon($functions->get_imageblob($profileYou->get_profile_photo_id()));
			echo '</p>';
			echo '</div>';
		}
		else if ($message->get_sender() == $idOther){
			echo '<div class="d-inline">';
			echo '<p class="float-left">'.$chat[$i]->get_message();
			$functions->show_profilephotoicon($functions->get_imageblob($profileToMessage->get_profile_photo_id()));
			echo '</p>';
			echo '</div>';
		}
	}
}
}
?>