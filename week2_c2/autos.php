<?php

// Demand a GET parameter
if (!isset($_GET['name']) || strlen($_GET['name']) < 1) {
    die("Name parameter missing");
}

// If the user requested logout go back to index.php
if (isset($_POST['logout'])) {
    header("Location: index.php");
    return;
}

// Establish a connection to the database
$pdo = new PDO("mysql:host=localhost;port=3306;dbname=misc", "fred", "zap");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Handle the POST request for adding a new automobile to the database
if (isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage'])) {
    if (strlen($_POST['make']) < 1) {
        $message = "Make is required";
    } elseif (!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])) {
        $message = "Mileage and year must be numeric";
    } else {
        // Insert a new automobile to the database
        $stmt = $pdo->prepare('INSERT INTO autos (make, year, mileage) VALUES (:mk, :yr, :mi)');
        $stmt->execute(array(
            ':mk' => $_POST['make'],
            ':yr' => $_POST['year'],
            ':mi' => $_POST['mileage'])
        );
        $message = "Record inserted";
    }
}

// Retrieve all automobiles from the database
$stmt = $pdo->query("SELECT make, year, mileage FROM autos");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    if (isset($_REQUEST['name'])) {
        echo "<p>Welcome: ";
        echo htmlentities($_REQUEST['name']);
        echo "</p>";
    }
    // Display the error message, if any
if (isset($message)) {
    echo "<p style='color:green'>" . htmlentities($message) . "</p>";
}

// Display the form for adding a new automobile
?>
<form method="post">
    <p>Make:
        <input type="text" name="make" size="60"/></p>
    <p>Year:
        <input type="text" name="year"/></p>
    <p>Mileage:
        <input type="text" name="mileage"/></p>
    <input type="submit" value="Add">
    <input type="submit" name="logout" value="Logout">
</form>

<h2>Automobiles</h2>
<?php
// Display the list of all automobiles
if (count($rows) > 0) {
    echo "<ul>";
    foreach ($rows as $row) {
        echo "<li>" . htmlentities($row['make']) . " " . $row['year'] . " / " . $row['mileage'] . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No rows found</p>";
}
?>
</div>
</body>
</html>