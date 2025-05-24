<?php

$client = PersonData::getById($_GET["id"]);
$client->del();
Core::redir("././?view=providers");

?>