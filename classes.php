 <?php
 class Photo{
 	var $id;
 	var $name;
 	function set_id($new_id){
 		$this->id = $new_id;
 	}
 	function set_name($new_name){
 		$this->name = $new_name;
 	}
 	function get_id(){
 		return $this->id;
 	}
 	function get_name(){
 		return $this->name;
 	}
 }
 class Album{
 	var $id;
 	var $ownerid;
 	var $name;
 	function set_id($new_id){
 		$this->id = $new_id;
 	}
 	function set_ownerid($new_ownerid){
 		$this->ownerid = $new_ownerid;
 	}
 	function set_name($new_name){
 		$this->name = $new_name;
 	}
 	function get_id(){
 		return $this->id;
 	}
 	function get_ownerid(){
 		return $this->ownerid;
 	}
 	function get_name(){
 		return $this->name;
 	}
 }
 class Profile{
 	var $id;
 	var $name;
 	var $surname;
 	var $email;
 	var $rating;
 	var $score;
 	var $phone_number;
 	var $cover_photo_id;
 	var $profile_photo_id;

 	function set_id($new_id){
 		$this->id = $new_id;
 	}
 	function set_name($new_name){
 		$this->name = $new_name;
 	}
 	function set_surname($new_surname){
 		$this->surname = $new_surname;
 	}
 	function set_email($new_email){
 		$this->email = $new_email;
 	}
 	function set_rating($new_rating){
 		$this->rating = $new_rating;
 	}
 	function set_score($new_score){
 		$this->score = $new_score;
 	}
 	function set_cover_photo_id($new_cover_photo_id){
 		$this->cover_photo_id = $new_cover_photo_id;
 	}
 	function set_profile_photo_id($new_profile_photo_id){
 		$this->profile_photo_id = $new_profile_photo_id;
 	}
 	function set_phone_number($new_phone_number){
 		$this->phone_number = $new_phone_number;
 	}
 	function get_id(){
 		return $this->id;
 	}
 	function get_name(){
 		return $this->name;
 	}
 	function get_surname(){
 		return $this->surname;
 	}
 	function get_email(){
 		return $this->email;
 	}
 	function get_rating(){
 		return $this->rating;
 	}
 	function get_score(){
 		return $this->score;
 	}
 	function get_cover_photo_id(){
 		return $this->cover_photo_id;
 	}
 	function get_profile_photo_id(){
 		return $this->profile_photo_id;
 	}
 	function get_phone_number(){
 		return $this->phone_number = $new_phone_number;
 	}
 	function cmp($a, $b)
 	{
 		if (strcmp(strtolower($a->get_name()),strtolower($b->get_name()) == 0)){
 			return strcmp(strtolower($a->get_surname()),strtolower($b->get_surname()));
 		}
 		return strcmp(strtolower($a->get_name()),strtolower($b->get_name()));
 	}
 }

 ?>