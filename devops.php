<?php
	include('header.php');
?>

<section>

<h2>Devops</h2>

<?php
	$count = 0;
	$start = '/<li id="devops">Devops/';
	$end = '/<!-- end devops -->/';
	$fh = fopen('index.php', 'r');
	while ($line = fgets($fh)) {
		if (preg_match($start, $line)) {
			$count = 1;
		}
		elseif (preg_match($end, $line)) {
			$count = 0;
			break;
		}
		elseif ($count == 1) {
			print $line;
		}
	}
	fclose($fh);	
?>
		
</section>

<?php
	include ('footer.php');
?>
