<!DOCTYPE html>
<head><title>Juan Diego Jimenez Giraldo MD5 Cracker</title></head>
<body>
<h1>MD5 cracker - Juan Diego Jimenez</h1>
<p>This application takes an MD5 hash of a four digit pin and check all 10,000 possible four digit PINs to determine the PIN.</p>
<pre>
Debug Output:
<?php
$goodtext = "Not found";
if ( isset($_GET['md5']) ) {
    $time_pre = microtime(true);
    $md5 = $_GET['md5'];
    $txt = "0123456789";
    $show = 15;


    for($i=0; $i<10000; $i++ ) {
        $pin = sprintf("%04d", $i);

        $check = hash('md5', $pin);
        if ( $check == $md5 ) {
            $goodtext = $pin;
            break;   // Exit the loop
        }

        if ( $show > 0 ) {
            print "$check $pin\n";
            $show = $show - 1;
        }
    }

    $time_post = microtime(true);
    print "Elapsed time: ";
    print $time_post-$time_pre;
    print "\n";
}
?>
</pre>
<p>Original PIN: <?= htmlentities($goodtext); ?></p>
<form>
<input type="text" name="md5" size="60" />
<input type="submit" value="Crack MD5"/>
</form>
<ul>
<li><a href="index.php">Reset this page</a></li>