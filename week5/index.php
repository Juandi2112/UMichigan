<!DOCTYPE html>
<html>
<head>
    <title>Juan Diego Jiménez PHP</title>
</head>
<body>
<?php
		$name = "Juan Diego Jiménez";
		$ascii_art = "
            .----------------. 
           | .--------------. |
           | |     _____    | |
           | |    |_   _|   | |
           | |      | |     | |
           | |   _  | |     | |
           | |  | |_' |     | |
           | |  `.___.'     | |
           | |              | |
           | '--------------' |
            '----------------' 
       ";
		$hash = hash('sha256', $name);
	?>

	<h1><?php echo $name ?> PHP</h1>

    <p>The SHA256 hash of 'Juan Diego Jiménez' is: <?php echo $hash ?></p>
    <pre>ASCII ART:</pre>
	<pre><?php echo $ascii_art ?></pre>

</body>
</html>