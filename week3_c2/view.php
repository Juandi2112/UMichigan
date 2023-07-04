<?php
session_start();
if ( ! isset($_SESSION['name']) ) {
    die('Not logged in');
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Juan Diego Jimenez Giraldo</title>
    <?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
    <h1>Automobiles Database</h1>
    <?php
    // Display the welcome message
    if (isset($_SESSION['name'])) {
        echo "<p>Welcome: ";
        echo htmlentities($_SESSION['name']);
        echo "</p>";
    }
    // Display the success message, if any
    if ( isset($_SESSION['success']) ) {
        echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
        unset($_SESSION['success']);
    }
    ?>
    <table border="1">
    <?php
    require_once "pdo.php";
    $stmt = $pdo->query("SELECT make, model, year, mileage FROM autos");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($rows) > 0) {
        echo "<thead><tr><th>Make</th><th>Year</th><th>Mileage</th></tr></thead><tbody>";
        foreach ($rows as $row) {
            echo "<tr><td>" . htmlentities($row['make']) . "</td><td>" . $row['year'] . "</td><td>" . $row['mileage'] . "</td></tr>";
        }
        echo "</tbody>";
    } else {
        echo "<p>No rows found</p>";
    }
    ?>
    </table>
    <br/>
    <a href="add.php">Add New Entry</a> | <a href="logout.php">Logout</a>
</div>
</body>
</html>