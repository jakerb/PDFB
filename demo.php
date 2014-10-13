<?php
require 'engine.php';
$db = new pfdb("user.pfdb");
$query = $db->find("forename where id = 3");
echo $query;
?>