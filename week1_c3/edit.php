<?php
require_once "pdo.php";
session_start();

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    die('ACCESS DENIED');
}

// Check if POST data was received
if ( isset($_POST['profile_id']) && isset($_POST['first_name']) && isset($_POST['last_name'])
     && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary']) ) {

    // Data validation
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1
        || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = 'All fields are required';
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }

    if ( ! filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email address';
        header("Location: edit.php?profile_id=".$_POST['profile_id']);
        return;
    }

    // Update the data in the database
    $stmt = $pdo->prepare("UPDATE Profile SET first_name = :fn,
            last_name = :ln, email = :em, headline = :he, summary = :su
            WHERE profile_id = :pid");
    $stmt->execute(array(
        ':fn' => $_POST['first_name'],
        ':ln' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':pid' => $_POST['profile_id']
    ));

    // Success message
    $_SESSION['success'] = 'Record updated';
    header('Location: index.php');
    return;
}

// If no POST data was received, check if the profile_id parameter was sent via GET
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

// Load the data of the specified profile
$stmt = $pdo->prepare("SELECT * FROM Profile WHERE profile_id = :pid");
$stmt->execute(array(':pid' => $_GET['profile_id']));
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// If the profile is not found, show an error message
if ( $profile === false ) {
    $_SESSION['error'] = 'Invalid profile_id';
    header('Location: index.php');
    return;
}

// Load the profile data into variables for easier use in the HTML form
$first_name = htmlentities($profile['first_name']);
$last_name = htmlentities($profile['last_name']);
$email = htmlentities($profile['email']);
$headline = htmlentities($profile['headline']);
$summary = htmlentities($profile['summary']);
$profile_id = $profile['profile_id'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Updating Profile</title>
    <?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
    <h1>Updating Profile</h1>
    <?php
    // Show error message if any
    if (isset($_SESSION['error'])) {
        echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
        unset($_SESSION['error']);
    }
    ?>
        <form method="post">
    <p>First Name:
        <input type="text" name="first_name" size="60" value="<?= $first_name ?>"></p>
    <p>Last Name:
        <input type="text" name="last_name" size="60" value="<?= $last_name ?>"></p>
    <p>Email:
        <input type="text" name="email" size="30" value="<?= $email ?>"></p>
    <p>Headline:<br>
        <input type="text" name="headline" size="80" value="<?= $headline ?>"></p>
    <p>Summary:<br>
        <textarea name="summary" rows="8" cols="80"><?= $summary ?></textarea></p>
    <input type="hidden" name="profile_id" value="<?= $profile_id ?>">
    <p>
        <input type="submit" value="Save">
        <a href="index.php">Cancel</a>
    </p>
</form>
