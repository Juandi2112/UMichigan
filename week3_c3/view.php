<?php
require_once "pdo.php";

// Make sure that the profile_id parameter exists
if (!isset($_GET['profile_id'])) {
    die("Missing profile_id parameter");
}

// Fetch the profile record from the database
$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid");
$stmt->execute(array(':pid' => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// If the profile record does not exist, show an error message
if ($row === false) {
    die("Profile not found");
}

// Fetch the position records for the given profile_id
$positions_stmt = $pdo->prepare("SELECT * FROM Position WHERE profile_id = :pid ORDER BY rank");
$positions_stmt->execute(array(':pid' => $_GET['profile_id']));

// Display the profile information
echo "<title>Juan Diego Jimenez Giraldo</title>";
echo "<h1>Profile information</h1>";
echo "<p>First Name: " . htmlentities($row['first_name']) . "</p>";
echo "<p>Last Name: " . htmlentities($row['last_name']) . "</p>";
echo "<p>Email: " . htmlentities($row['email']) . "</p>";
echo "<p>Headline: " . htmlentities($row['headline']) . "</p>";
echo "<p>Summary: " . htmlentities($row['summary']) . "</p>";
echo "<h2>Positions:</h2>";
echo "<ul>";
while ($position = $positions_stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<li>" . htmlentities($position['year']) . ": " . htmlentities($position['description']) . "</li>";
}
echo "</ul>";
echo '<p><a href="index.php">Go back to list</a></p>';
?>
