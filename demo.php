<?php
require 'engine.php';
$db = new pfdb("user.pfdb");
$query = $db->find("forename where id = 3");

$forename = ["john", "alan", "steve", "peter", "jack", "dave", "simon"];
$surname = ["simpson", "brown", "jackson", "todd", "wilson", "white", "moffatt"];
$total = 100;

$time_start = microtime(true);
for ($i=0; $i < $total; $i++) { 
	$f = $forename[rand(0, 6)];
	$s = $surname[rand(0, 6)];
	echo $db->insert("*, $f, $s, demo@jakebown.com, letmein");
}
$time_end = microtime(true);
$time = $time_end - $time_start;

echo "completed $total insert queries in $time seconds\n";
?>