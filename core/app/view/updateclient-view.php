<?php

if(count($_POST)>0){
	$user = PersonData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->lastname = $_POST["lastname"];
	$user->address1 = $_POST["address1"];
	$user->email1 = $_POST["email1"];
	$user->phone1 = $_POST["phone1"];
	$user->company = addslashes($_POST["grado"]);
	$user->update_client();

	print "<script>window.location='./?view=clients';</script>";
}
?>