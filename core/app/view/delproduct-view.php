<?php
$product = ProductData::getById($_GET["id"]);
$product->del();

Core::redir("././?view=products");
?>