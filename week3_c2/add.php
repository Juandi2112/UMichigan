<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    die('Not logged in');
}

// If the user clicked cancel, redirect back to view.php
if (isset($_POST['cancel'])) {
    header("Location: view.php");
    return;
}

// Establish a connection to the database
require_once "pdo.php";

// Handle the POST request for adding a new automobile to the database
if (isset($_POST['make']) && isset($_POST['year']) && isset($_POST['mileage'])) {
    if (strlen($_POST['make']) < 1) {
        $_SESSION['error'] = "Make is required";
        header("Location: add.php");
        return;
    } elseif (!is_numeric($_POST['year']) || !is_numeric($_POST['mileage'])) {
        $_SESSION['error'] = "Mileage and year must be numeric";
        header("Location: add.php");
        return;
    } else {
        // Insert a new automobile to the database
        $stmt = $pdo->prepare('INSERT INTO autos (make, year, mileage) VALUES (:mk, :yr, :mi)');
        $stmt->execute(array(
            ':mk' => $_POST['make'],
            ':yr' => $_POST['year'],
            ':mi' => $_POST['mileage'])
        );
        $_SESSION['success'] = "Record inserted";
        header("Location: view.php");
        return;
    }
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
    <h1>Tracking Autos for <?= htmlentities($_SESSION['name']) ?></h1>
    <?php
    // Display error message, if any
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red">' . htmlentities($_SESSION['error']) . "</p>\n";
        unset($_SESSION['error']);
    }
    ?>
    <form method="post">
        <p>Make:
            <input type="text" name="make" size="60"/></p>
        <p>Year:
            <input type="text" name="year"/></p>
        <p>Mileage:
            <input type="text" name="mileage"/></p>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</div>
</body>
</html>
