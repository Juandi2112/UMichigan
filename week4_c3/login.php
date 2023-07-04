<?php 
session_start();

if (isset($_POST['cancel'])) {
    // Redirect the browser to index.php
    header("Location: index.php");
    return;
}

require_once "pdo.php";

if (isset($_POST['email']) && isset($_POST['pass'])) {

    // Sanitize user input
    $email = htmlentities($_POST['email']);
    $password = htmlentities($_POST['pass']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Both email and password are required";
    } else if (strpos($email, '@') === false) {
        $_SESSION['error'] = "Email address must contain '@' symbol";
    } else {
        $stmt = $pdo->prepare('SELECT user_id, name, password FROM users WHERE email = :em');
        $stmt->execute(array(':em' => $email));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row === false) {
            $_SESSION['error'] = "Incorrect email or password";
        } else {
            $salt = 'XyZzy12*_';
            $hash = hash('md5', $salt.$password);
            if ($hash === $row['password']) {
                $_SESSION['name'] = $row['name'];
                $_SESSION['user_id'] = $row['user_id'];
                header("Location: index.php");
                return;
            } else {
                $_SESSION['error'] = "Incorrect email or password";
            }
        }
    }
    // Redirect the browser to login.php
    header("Location: login.php");
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Juan Diego Jimenez Giraldo</title>
<script>
function doValidate() {
    console.log('Validating...');
    try {
        em = document.getElementById('email').value;
        pw = document.getElementById('pass').value;
        console.log("Validating em=" + em + " pw=" + pw);
        if (em == null || em == "" || pw == null || pw == "") {
            alert("Both fields must be filled out");
            return false;
        }
        if (em.indexOf('@') == -1) {
            alert("Invalid email address");
            return false;
        }
        return true;
    } catch(e) {
        return false;
    }
    return false;
}
</script>
</head>
<body>
<div class="container">
    <h1>Please Log In</h1>
    <?php
    if (isset($_SESSION['error'])) {
        echo '<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n";
        unset($_SESSION['error']);
    }
    ?>
    <form method="POST">
        <label for="email">Email</label>
        <input type="text" name="email" id="email"><br>
        <label for="pass">Password</label>
        <input type="password" name="pass" id="pass"><br>
        <input type="submit" onclick="return doValidate();" value="Log In">
        <input type="submit" name="cancel" value="Cancel">
    </form>

</div>
</body>
</html>
