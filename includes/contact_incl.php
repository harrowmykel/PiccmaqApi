<?php

	function contactnotlogged(){
		$name=getPostString("name");
		$email=getPostString("email");
		$title=getPostString("title");
		$text=getPostString("topic");
		$error=(preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\._\-&!?=#]*@[a-zA-Z\._\-]+\.[a-zA-Z\.]+/', $email)) ?"":translate('inv_email');
		if(!empty($error)){
			return $error;
		}
		return inputThought($name, $email, $title, $text);
	}

	function contact(){
		$user=getUsername_();
		$query="SELECT * FROM profiles, members WHERE profiles.user='piccmaq'AND members.user='$user'";
		$row=fetch_assoc($query);
		$name=$row['name'];
		$email=$row['email'];
		$title=getPostString("title");
		$text=getPostString("topic");
		return inputThought($name, $email, $title, $text);
	}


	function inputThought($name, $email, $topic, $text){
		$time=time();
		$query="INSERT INTO contacttable (id, name, email, title, message, time) VALUES (NULL, '$name', '$email', '$topic', '$text', $time)";
		queryMysql($query);
		return translate('email_sent');
	}



?>