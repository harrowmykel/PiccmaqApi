<?php
class ProfileParser{
	private $id="";
	private $auth_username="";
	private $subtitle="";
	private $is_admin="";
	private $frnds_data=null;
	private $auth_data=null;
	private $online="";
	private $last_seen="";
	private $error=null;
	private $empty=true;
	private $bio="";
	private $verified="";
	private $piccoins;
	private $join_date;
	private $email;
	private $dob;
	private $mob;
	private $yob;
	private $total_seen_time;
	private $gender;
	private $phone;
	private $total_friends;

	public function load($data){
		$this->empty = true;
		if(empty(@$data->error)){
			$this->empty = false;
			$this->loadThis($data);
		}else{
			$mError = new ApiError();
			$mError->setError(@$data->error);
			$mError->setError_more(@$data->error_more);
			$mError->setErrorTime(@$data->time);
			$this->setError($mError);
		}
	}

	public function loadThis($data){
		$this->setId(@$data->id);
		$this->setAuth_username(@$data->auth_username);
		$this->setSubtitle(@$data->subtitle);
		$this->setIs_admin(@$data->is_admin);
		$this->setLast_seen(@$data->last_seen);
		$this->setOnline(@$data->online);
		$this->setBio(@$data->bio);
		$this->setVerified(@$data->verified);
		$this->setPiccoins(@$data->piccoins);
		$this->setJoin_date(@$data->join_date);
		$this->setEmail(@$data->email);
		$this->setDob(@$data->dob);
		$this->setMob(@$data->mob);
		$this->setYob(@$data->yob);
		$this->setTotal_seen_time(@$data->total_seen_time);
		$this->setGender(@$data->gender);
		$this->setPhone(@$data->phone);
		$this->setTotal_friends(@$data->total_friends);

		$mFrnds_data = new FrndsData();
		$mFrnds_data->setR_rcvd(@$data->frnds_data->r_rcvd);
		$mFrnds_data->setR_frnds(@$data->frnds_data->r_frnds);
		$mFrnds_data->setR_sent(@$data->frnds_data->r_sent);
		$this->setFrnds_data($mFrnds_data);

		$mAuth_data = new AuthData();
		$mAuth_data->setAuth(@$data->auth_data->auth);
		$mAuth_data->setFullname(@$data->auth_data->fullname);
		$mAuth_data->setAuth_img(@$data->auth_data->auth_img);
		$this->setAuth_data($mAuth_data);

	}

	public function isEmpty() { return $this->empty; }
	public function setId($id) { $this->id = $id; }
	public function getId() { return $this->id; }
	public function setAuth_username($auth_username) { $this->auth_username = $auth_username; }
	public function getAuth_username() { return $this->auth_username; }
	public function setSubtitle($subtitle) { $this->subtitle = $subtitle; }
	public function getSubtitle() { return $this->subtitle; }
	public function setIs_admin($is_admin) { $this->is_admin = $is_admin; }
	public function getIs_admin() { return $this->is_admin; }
	public function setFrnds_data($frnds_data) { $this->frnds_data = $frnds_data; }
	public function getFrnds_data() { return $this->frnds_data; }
	public function setAuth_data($auth_data) { $this->auth_data = $auth_data; }
	public function getAuth_data() { return $this->auth_data; }
	public function setOnline($online) { $this->online = $online; }
	public function getOnline() { return $this->online; }
	public function setLast_seen($last_seen) { $this->last_seen = $last_seen; }
	public function getLast_seen() { return $this->last_seen; }
	public function setBio($bio) { $this->bio = $bio; }
	public function getBio() { return $this->bio; }
	public function setVerified($verified) { $this->verified = $verified; }
	public function getVerified() { return $this->verified; }
	public function setPiccoins($piccoins) { $this->piccoins = $piccoins; }
	public function getPiccoins() { return $this->piccoins; }
	public function setError($error) { $this->error = $error; }
	public function getError() { return $this->error; }
	public function setJoin_date($join_date) { $this->join_date = $join_date; }
	public function getJoin_date() { return $this->join_date; }
	public function setEmail($email) { $this->email = $email; }
	public function getEmail() { return $this->email; }
	public function setDob($dob) { $this->dob = $dob; }
	public function getDob() { return $this->dob; }
	public function setMob($mob) { $this->mob = $mob; }
	public function getMob() { return $this->mob; }
	public function setYob($yob) { $this->yob = $yob; }
	public function getYob() { return $this->yob; }
	public function setTotal_seen_time($total_seen_time) { $this->total_seen_time = $total_seen_time; }
	public function getTotal_seen_time() { return $this->total_seen_time; }
	public function setGender($gender) { $this->gender = $gender; }
	public function getGender() { return $this->gender; }
	public function setPhone($phone) { $this->phone = $phone; }
	public function getPhone() { return $this->phone; }
	public function setTotal_friends($total_friends) { $this->total_friends = $total_friends; }
	public function getTotal_friends() { return $this->total_friends; }

}

class FrndsData
{
	private $r_rcvd="";
	private $r_frnds="";
	private $r_sent="";
	public function setR_rcvd($r_rcvd) { $this->r_rcvd = $r_rcvd; }
	public function getR_rcvd() { return $this->r_rcvd; }
	public function setR_sent($r_sent) { $this->r_sent = $r_sent; }
	public function getR_sent() { return $this->r_sent; }
	public function setR_frnds($r_frnds) { $this->r_frnds = $r_frnds; }
	public function getR_frnds() { return $this->r_frnds; }
}

class AuthData
{
	private $auth="";
	private $auth_img="";
	private $fullname="";

	public function setAuth($auth) { $this->auth = $auth; }
	public function getAuth() { return $this->auth; }
	public function setAuth_img($auth_img) { $this->auth_img = $auth_img; }
	public function getAuth_img() { return $this->auth_img; }
	public function setFullname($fullname) { $this->fullname = $fullname; }
	public function getFullname() { return $this->fullname; }
}

class RecivData
{
	private $reciv="";
	private $reciv_img="";
	private $fullname="";

	public function setReciv($reciv) { $this->reciv = $reciv; }
	public function getReciv() { return $this->reciv; }
	public function setReciv_img($reciv_img) { $this->reciv_img = $reciv_img; }
	public function getReciv_img() { return $this->reciv_img; }
	public function setFullname($fullname) { $this->fullname = $fullname; }
	public function getFullname() { return $this->fullname; }
}

class ApiError{
	private $error;
	private $time;
	private $error_more;

	public function setError($error) { $this->error = $error; }
	public function getError() { return $this->error; }
	public function setErrorTime($time) { $this->time = $time; }
	public function getErrorTime() { return $this->time; }
	public function setError_more($error_more) { $this->error_more = $error_more; }
	public function getError_more() { return $this->error_more; }
}

class PostParser{
	private $id;
	private $auth_username;
	private $reciv_username;
	private $time;
	private $timestamp;
	private $subtitle;
	private $reply_to;
	private $phrase;
	private $privacy;
	private $likes;
	private $image;
	private $liked;
	private $web_link;
	private $reciv_data;
	private $auth_data;
	private $comments;
	private $error=null;
	private $empty=true;

	public function load($data){
		$this->empty = true;
		if(empty(@$data->error)){
			$this->empty = false;
			$this->loadThis($data);
		}else{
			$mError = new ApiError();
			$mError->setError(@$data->error);
			$mError->setError_more(@$data->error_more);
			$mError->setErrorTime(@$data->time);
			$this->setError($mError);
		}
	}

	public function loadThis($data){
		$this->setId(@$data->id);
		$this->setAuth_username(@$data->auth_username);
		$this->setReciv_username(@$data->reciv_username);
		$this->setTime(@$data->time);
		$this->setTimestamp(@$data->timestamp);
		$this->setSubtitle(@$data->subtitle);
		$this->setReply_to(@$data->reply_to);
		$this->setPhrase(@$data->phrase);
		$this->setPrivacy(@$data->privacy);
		$this->setLikes(@$data->likes);
		$this->setImage(@$data->image);
		$this->setLiked(@$data->liked);
		$this->setWeb_link(@$data->web_link);
		$this->setComments(@$data->comments);

		$mReciv_data = new RecivData();
		$mReciv_data->setReciv(@$data->reciv_data->reciv_img);
		$mReciv_data->setFullname(@$data->reciv_data->fullname);
		$mReciv_data->setReciv_img(@$data->reciv_data->reciv_img);
		$this->setReciv_data($mReciv_data);

		$mAuth_data = new AuthData();
		$mAuth_data->setAuth(@$data->auth_data->auth);
		$mAuth_data->setFullname(@$data->auth_data->fullname);
		$mAuth_data->setAuth_img(@$data->auth_data->auth_img);
		$this->setAuth_data($mAuth_data);
	}

	public function isEmpty() { return $this->empty; }
	public function setId($id) { $this->id = $id; }
	public function getId() { return $this->id; }
	public function setAuth_username($auth_username) { $this->auth_username = $auth_username; }
	public function getAuth_username() { return $this->auth_username; }
	public function setReciv_username($reciv_username) { $this->reciv_username = $reciv_username; }
	public function getReciv_username() { return $this->reciv_username; }
	public function setTime($time) { $this->time = $time; }
	public function getTime() { return $this->time; }
	public function setTimestamp($timestamp) { $this->timestamp = $timestamp; }
	public function getTimestamp() { return $this->timestamp; }
	public function setSubtitle($subtitle) { $this->subtitle = $subtitle; }
	public function getSubtitle() { return $this->subtitle; }
	public function setReply_to($reply_to) { $this->reply_to = $reply_to; }
	public function getReply_to() { return $this->reply_to; }
	public function setPhrase($phrase) { $this->phrase = $phrase; }
	public function getPhrase() { return $this->phrase; }
	public function setPrivacy($privacy) { $this->privacy = $privacy; }
	public function getPrivacy() { return $this->privacy; }
	public function setLikes($likes) { $this->likes = $likes; }
	public function getLikes() { return $this->likes; }
	public function setImage($image) { $this->image = $image; }
	public function getImage() { return $this->image; }
	public function setLiked($liked) { $this->liked = $liked; }
	public function getLiked() { return $this->liked; }
	public function setWeb_link($web_link) { $this->web_link = $web_link; }
	public function getWeb_link() { return $this->web_link; }
	public function setReciv_data($reciv_data) { $this->reciv_data = $reciv_data; }
	public function getReciv_data() { return $this->reciv_data; }
	public function setAuth_data($auth_data) { $this->auth_data = $auth_data; }
	public function getAuth_data() { return $this->auth_data; }
	public function setError($error) { $this->error = $error; }
	public function getError() { return $this->error; }
	public function setComments($comments) { $this->comments = $comments; }
	public function getComments() { return $this->comments; }
}

class SuccessError{
	private $id="";
	private $success;
	private $time;
	private $success_more;
	private $error=null;

	public function load($data){
		if(!empty(@$data->success)){
			$this->setSuccess(@$data->success);
			$this->setSuccess_more(@$data->success_more);
			$this->setSuccessTime(@$data->time);
		}else{
			$mError = new ApiError();
			$mError->setError(@$data->error);
			$mError->setError_more(@$data->error_more);
			$mError->setErrorTime(@$data->time);
			$this->setError($mError);
		}
	}

	public function isSuccessful() { return !empty($this->getSuccess()); }
	public function setId($id) { $this->id = $id; }
	public function getId() { return $this->id; }
	public function setSuccess($success) { $this->success = $success; }
	public function getSuccess() { return $this->success; }
	public function setSuccessTime($time) { $this->time = $time; }
	public function getSuccessTime() { return $this->time; }
	public function setSuccess_more($success_more) { $this->success_more = $success_more; }
	public function getSuccess_more() { return $this->success_more; }
	public function setError($error) { $this->error = $error; }
	public function getError() { return $this->error; }
}

class LikesParser{
	private $id;
	private $auth_username;
	private $liked;
	private $frnds_data;
	private $auth_data;
	private $error=null;
	private $empty=true;

	public function load($data){
		$this->empty = true;
		if(empty(@$data->error)){
			$this->empty = false;
			$this->setId(@$data->id);
			$this->setAuth_username(@$data->auth_username);
			$this->setLiked(@$data->liked);

			$mFrnds_data = new FrndsData();
			$mFrnds_data->setR_rcvd(@$data->frnds_data->r_rcvd);
			$mFrnds_data->setR_frnds(@$data->frnds_data->r_frnds);
			$mFrnds_data->setR_sent(@$data->frnds_data->r_sent);
			$this->setFrnds_data($mFrnds_data);

			$mAuth_data = new AuthData();
			$mAuth_data->setAuth(@$data->auth_data->auth);
			$mAuth_data->setFullname(@$data->auth_data->fullname);
			$mAuth_data->setAuth_img(@$data->auth_data->auth_img);
			$this->setAuth_data($mAuth_data);
		}else{
			$mError = new ApiError();
			$mError->setError(@$data->error);
			$mError->setError_more(@$data->error_more);
			$mError->setErrorTime(@$data->time);
			$this->setError($mError);
		}
	}

	public function isEmpty() { return $this->empty; }
	public function setId($id) { $this->id = $id; }
	public function getId() { return $this->id; }
	public function setAuth_username($auth_username) { $this->auth_username = $auth_username; }
	public function getAuth_username() { return $this->auth_username; }
	public function setLiked($liked) { $this->liked = $liked; }
	public function getLiked() { return $this->liked; }
	public function setFrnds_data($frnds_data) { $this->frnds_data = $frnds_data; }
	public function getFrnds_data() { return $this->frnds_data; }
	public function setAuth_data($auth_data) { $this->auth_data = $auth_data; }
	public function getAuth_data() { return $this->auth_data; }
	public function setError($error) { $this->error = $error; }
	public function getError() { return $this->error; }

}

class MsgParser{
	private $id;
	private $auth_username;
	private $subtitle;
	private $reciv_data;
	private $auth_data;
	private $reciv_username;
	private $timestamp;
	private $time;
	private $confirm;
	private $image;
	private $error=null;
	private $empty=true;

	public function load($data){
		$this->empty = true;
		if(empty(@$data->error)){
			$this->empty = false;
			$this->setId(@$data->id);
			$this->setAuth_username(@$data->auth_username);
			$this->setSubtitle(@$data->subtitle);
			$this->setTime(@$data->time);
			$this->setTimestamp(@$data->timestamp);
			$this->setConfirm(@$data->confirm);
			$this->setImage(@$data->image);
			$this->setReciv_username(@$data->reciv_username);

			$mAuth_data = new AuthData();
			$mAuth_data->setAuth(@$data->auth_data->auth);
			$mAuth_data->setFullname(@$data->auth_data->fullname);
			$mAuth_data->setAuth_img(@$data->auth_data->auth_img);
			$this->setAuth_data($mAuth_data);

			$mReciv_data = new RecivData();
			$mReciv_data->setReciv(@$data->reciv_data->reciv_img);
			$mReciv_data->setFullname(@$data->reciv_data->fullname);
			$mReciv_data->setReciv_img(@$data->reciv_data->reciv_img);
			$this->setReciv_data($mReciv_data);
		}else{
			$mError = new ApiError();
			$mError->setError(@$data->error);
			$mError->setError_more(@$data->error_more);
			$mError->setErrorTime(@$data->time);
			$this->setError($mError);
		}
	}

	public function setReciv_username($reciv_username) { $this->reciv_username = $reciv_username; }
	public function getReciv_username() { return $this->reciv_username; }
	public function setId($id) { $this->id = $id; }
	public function getId() { return $this->id; }
	public function setAuth_username($auth_username) { $this->auth_username = $auth_username; }
	public function getAuth_username() { return $this->auth_username; }
	public function setSubtitle($subtitle) { $this->subtitle = $subtitle; }
	public function getSubtitle() { return $this->subtitle; }
	public function setTimestamp($timestamp) { $this->timestamp = $timestamp; }
	public function getTimestamp() { return $this->timestamp; }
	public function setTime($time) { $this->time = $time; }
	public function getTime() { return $this->time; }
	public function setConfirm($confirm) { $this->confirm = $confirm; }
	public function getConfirm() { return $this->confirm; }
	public function setImage($image) { $this->image = $image; }
	public function getImage() { return $this->image; }
	public function setAuth_data($auth_data) { $this->auth_data = $auth_data; }
	public function getAuth_data() { return $this->auth_data; }
	public function setReciv_data($reciv_data) { $this->reciv_data = $reciv_data; }
	public function getReciv_data() { return $this->reciv_data; }
	public function isEmpty() { return $this->empty; }
	public function setError($error) { $this->error = $error; }
	public function getError() { return $this->error; }

}

class NotifParser{
	private $id;
	private $link;
	private $subject;
	private $object;
	private $notify_id;
	private $obj_id;
	private $time;
	private $timestamp;
	private $seen;
	private $auth_username;
	private $auth_data;

	private $error=null;
	private $empty=true;

	public function load($data){
		$this->empty = true;
		if(empty(@$data->error)){
			$this->empty = false;
			$this->setId(@$data->id);
			$this->setAuth_username(@$data->auth_username);
			$this->setLink(@$data->link);
			$this->setTime(@$data->time);
			$this->setTimestamp(@$data->timestamp);
			$this->setSubject(@$data->subject);
			$this->setObject(@$data->object);
			$this->setNotify_id(@$data->notify_id);
			$this->setObj_id(@$data->obj_id);
			$this->setSeen(@$data->seen);

			$mAuth_data = new AuthData();
			$mAuth_data->setAuth(@$data->auth_data->auth);
			$mAuth_data->setFullname(@$data->auth_data->fullname);
			$mAuth_data->setAuth_img(@$data->auth_data->auth_img);
			$this->setAuth_data($mAuth_data);
		}else{
			$mError = new ApiError();
			$mError->setError(@$data->error);
			$mError->setError_more(@$data->error_more);
			$mError->setErrorTime(@$data->time);
			$this->setError($mError);
		}
	}

	public function isEmpty() { return $this->empty; }
	public function setId($id) { $this->id = $id; }
	public function getId() { return $this->id; }
	public function setLink($link) { $this->link = $link; }
	public function getLink() { return $this->link; }
	public function setSubject($subject) { $this->subject = $subject; }
	public function getSubject() { return $this->subject; }
	public function setObject($object) { $this->object = $object; }
	public function getObject() { return $this->object; }
	public function setNotify_id($notify_id) { $this->notify_id = $notify_id; }
	public function getNotify_id() { return $this->notify_id; }
	public function setObj_id($obj_id) { $this->obj_id = $obj_id; }
	public function getObj_id() { return $this->obj_id; }
	public function setSeen($seen) { $this->seen = $seen; }
	public function getSeen() { return $this->seen; }
	public function setTime($time) { $this->time = $time; }
	public function getTime() { return $this->time; }
	public function setTimestamp($timestamp) { $this->timestamp = $timestamp; }
	public function getTimestamp() { return $this->timestamp; }
	public function setAuth_username($auth_username) { $this->auth_username = $auth_username; }
	public function getAuth_username() { return $this->auth_username; }
	public function setAuth_data($auth_data) { $this->auth_data = $auth_data; }
	public function getAuth_data() { return $this->auth_data; }
}

class NotifVarParser{
	private $msgs;
	private $chat_msgs;
	private $frnds;
	private $notif_numb;

	private $error=null;
	private $empty=true;

	public function load($data){
		$this->empty = true;
		if(empty(@$data->error)){
			$this->empty = false;
			$this->setMsgs(@$data->msgs);
			$this->setChat_msgs(@$data->chat_msgs);
			$this->setFrnds(@$data->frnds);
			$this->setNotif_numb(@$data->notif_numb);
		}else{
			$mError = new ApiError();
			$mError->setError(@$data->error);
			$mError->setError_more(@$data->error_more);
			$mError->setErrorTime(@$data->time);
			$this->setError($mError);
		}
	}

	public function isEmpty() { return $this->empty; }
	public function setMsgs($msgs) { $this->msgs = $msgs; }
	public function getMsgs() { return $this->msgs; }
	public function setChat_msgs($chat_msgs) { $this->chat_msgs = $chat_msgs; }
	public function getChat_msgs() { return $this->chat_msgs; }
	public function setFrnds($frnds) { $this->frnds = $frnds; }
	public function getFrnds() { return $this->frnds; }
	public function setNotif_numb($notif_numb) { $this->notif_numb = $notif_numb; }
	public function getNotif_numb() { return $this->notif_numb; }

}

class ScrambleParser{
	private $id;
	private $auth_username;
	private $auth_data;
	private $current_word;
	private $score;
	private $error=null;
	private $empty=true;

	public function load($data){
		$this->empty = true;
		if(empty(@$data->error)){
			$this->empty = false;
			$this->setId(@$data->id);
			$this->setAuth_username(@$data->auth_username);
			$this->setCurrent_word(@$data->current_word);
			$this->setScore(@$data->score);

			$mAuth_data = new AuthData();
			$mAuth_data->setAuth(@$data->auth_data->auth);
			$mAuth_data->setFullname(@$data->auth_data->fullname);
			$mAuth_data->setAuth_img(@$data->auth_data->auth_img);
			$this->setAuth_data($mAuth_data);
		}else{
			$mError = new ApiError();
			$mError->setError(@$data->error);
			$mError->setError_more(@$data->error_more);
			$mError->setErrorTime(@$data->time);
			$this->setError($mError);
		}
	}

	public function setCurrent_word($current_word) { $this->current_word = $current_word; }
	public function getCurrent_word() { return $this->current_word; }
	public function setScore($score) { $this->score = $score; }
	public function getScore() { return $this->score; }
	public function setId($id) { $this->id = $id; }
	public function getId() { return $this->id; }
	public function setAuth_username($auth_username) { $this->auth_username = $auth_username; }
	public function getAuth_username() { return $this->auth_username; }
	public function setImage($image) { $this->image = $image; }
	public function getImage() { return $this->image; }
	public function setAuth_data($auth_data) { $this->auth_data = $auth_data; }
	public function getAuth_data() { return $this->auth_data; }
	public function isEmpty() { return $this->empty; }
	public function setError($error) { $this->error = $error; }
	public function getError() { return $this->error; }

}
?>