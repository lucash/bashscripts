#!/usr/bin/php5
<?php
$username = $argv[1];
$letter = array();
$letter[0] = substr($username, 0, 1);
$letter[1] = substr($username, 1, 1);
$count = array();
foreach($letter as $l) {
	$i = 0;
	$count[$l] = 0;
	while ($i < strlen($username)) {
		if (substr($username, $i, 1) == $l) {
			$count[$l]++;
		}
		$i++;
	}
}
foreach ($count as $letter => $c) {
	echo $letter . $c;
}
echo '
';
