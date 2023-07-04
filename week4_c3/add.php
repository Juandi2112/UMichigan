<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['name'])) {
    die('ACCESS DENIED');
}

// If the user clicked cancel, redirect back to index.php
if (isset($_POST['cancel'])) {
    header("Location: index.php");
    return;
}

// Establish a connection to the database
require_once "pdo.php";

// Handle the POST request for adding a new profile to the database
if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
    if (strlen($_POST['first_name']) <1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
        $_SESSION['error'] = "All fields are required";
        header("Location: add.php");
        return;
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Email address must contain @";
        header("Location: add.php");
        return;
    } else {
        // Insert a new profile to the database
        $stmt = $pdo->prepare('INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES (:uid, :fn, :ln, :em, :he, :su)');
        $stmt->execute(array(
            ':uid' => $_SESSION['user_id'],
            ':fn' => $_POST['first_name'],
            ':ln' => $_POST['last_name'],
            ':em' => $_POST['email'],
            ':he' => $_POST['headline'],
            ':su' => $_POST['summary'])
        );
        $profile_id = $pdo->lastInsertId();
        $rank = 1;
        for ($i = 1; $i <= 9; $i++) {
            if (!isset($_POST['year' . $i]) || !isset($_POST['desc' . $i])) {
                continue;
            }

            $year = $_POST['year' . $i];
            $desc = $_POST['desc' . $i];

            if (strlen($year) < 1 || strlen($desc) < 1) {
                $_SESSION['error'] = "All fields are required";
                header("Location: add.php");
                return;
            } elseif (!is_numeric($year)) {
                $_SESSION['error'] = "Year must be numeric";
                header("Location: add.php");
                return;
            }
            $stmt = $pdo->prepare('INSERT INTO Position (profile_id, rank, year, description) VALUES (:pid, :rnk, :yr, :desc)');
            $stmt->execute(array(
                ':pid' => $profile_id,
                ':rnk' => $rank,
                ':yr' => $year,
                ':desc' => $desc)
            );
            $rank++;
        }

        $rank = 1;

        for($i = 1; $i <= 9; $i++) {
            if (! isset($_POST['year_edu'.$i])) continue;
            if (! isset($_POST['school_edu'.$i])) continue;
            $year = $_POST['year_edu'.$i];
            $school = $_POST['school_edu'.$i];

            $statement = $pdo->prepare("SELECT * FROM institution WHERE name = :school");

            $statement->execute(array(
                ":school" =>  $school
            ));

            $row = $statement->fetch(PDO::FETCH_ASSOC);

            if ($row == false) {
                $insertStmt = $pdo->prepare("INSERT INTO institution (name) VALUES (:name)");
                $insertStmt->execute(array(
                    ":name" => $school
                ));

                $school_id = $pdo->lastInsertId();
            } else {
                $school_id = $row['institution_id'];
            }
            
            $statement = $pdo->prepare("INSERT INTO education (profile_id, institution_id, rank, year) VALUES (:pid, :id, :rank, :year)");
            
            $statement->execute(array(
                ":pid" => $profile_id,
                ":id" => $school_id,
                ":rank" => $rank,
                ":year" => $year,
            ));

            $rank++;
        }

        $_SESSION['success'] = "Record added";
        header("Location: index.php");
        return;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Juan Diego Jimenez Giraldo</title>
    <?php require_once "bootstrap.php"; ?>
    <script src="https://code.jquery.com/jquery-3.2.1.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
    $(document).ready(function() {
        countEdu = 0;

        $("#addEdu").click((event) => {
            event.preventDefault();
            if (countEdu >= 9) {
                alert("Maximum entries for education reached");
            }
            countEdu++;
            $("#education_fields").append(
                `<div id="education${countEdu}">
                    <p>Year: <input type="text" name="year_edu${countEdu}" value="" /> 
                    <input type="button" value="-" onclick="$(\'#education${countEdu}\').remove(); return false;" />
                    </p>
                    <p>School <input class="school" type="text" name="school_edu${countEdu}" value="" />
                    </p>
                </div>
                `
            );
            $(".school").autocomplete({
                source: "school.php"
            });

        });


        countPos = 0;
        $(document).ready(function() {
            window.console && console.log('Document ready called');
            $('#addPos').click(function(event) {
                event.preventDefault();
                if (countPos >= 9) {
                    alert("Maximum of nine position entries exceeded");
                    return;
                }
                countPos++;
                window.console && console.log("Adding position " + countPos);
                $('#position_fields').append(
                    '<div id="position' + countPos + '"> \
            <p>Year: <input type="text" name="year' + countPos + '" value="" /> \
            <input type="button" value="-" \
                onclick="$(\'#position' + countPos + '\').remove();return false;"></p> \
            <textarea name="desc' + countPos + '" rows="8" cols="80"></textarea>\
            </div>');
            });
        });
    });
    </script>
</head>
<body>
<div class="container">
    <h1>Adding Profile for <?= htmlentities($_SESSION['name']) ?></h1>
    <?php
    // Display error message, if any
    if (isset($_SESSION['error'])) {
        echo '<p style="color:red">' . htmlentities($_SESSION['error']) . "</p>\n";
        unset($_SESSION['error']);
    }
    ?>
    <form method="post">
        <p>First Name:
            <input type="text" name="first_name" size="60"/></p>
        <p>Last Name:
            <input type="text" name="last_name" size="60"/></p>
        <p>Email:
            <input type="text" name="email"/></p>
        <p>Headline:<br>
            <input type="text" name="headline" size="80"/></p>
        <p>Summary:<br>
            <textarea name="summary" rows="8" cols="80"></textarea></p>
            
        <p>Education: <input type="submit" value="+" id="addEdu" /></p>
        <div id="education_fields">

        </div>

        <p>Position: <input type="submit" id="addPos" value="+"></p>
        <div id="position_fields"></div>
        <input type="submit" value="Add">
        <input type="submit" name="cancel" value="Cancel">
    </form>
</div>
</body>
</html>
