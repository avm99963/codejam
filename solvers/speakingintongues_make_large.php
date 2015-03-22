<?php
// St. Paul's Code Jam – Speaking in Tongues large problem maker
// @Author: avm99963

header("Content-Type: text/plain");

function getRandomWord($len = 100) {
    $val = '';
	for( $i=0; $i<$len; $i++ ) {
		if (rand(0, 8) == 8 && substr($val, -1) != " " && $val != '') {
			if (strlen($val) == 99)
				break;
			$val .= " ";
		} else {
			$val .= chr(rand(97, 122));
		}
		if (rand(0, 75) == 69)
			break;
	}
	return $val;
}

echo "100\n";

for ($i = 0; $i < 100; $i++) {
    echo getRandomWord(100)."\n";
}
?>