<?php 

    require_once "pdo.php";
    require_once "util.php";
    
    session_start();

    if (isset($_POST['cancel'])) {
        header("Location: index.php");
        return;
    }

    if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])  ) {
        if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['headline']) || empty($_POST['summary'])) {
            $_SESSION['error'] = "All fields are required";
            header("Location: edit.php?profile_id=".$_GET['profile_id']);
            return;
        }

        // if (strpos($_POST['email'], "@") == false) {
        //     $_SESSION['error'] = "Invalid email address";
        //     header("Location: edit.php?profile_id=".$_GET['profile_id']);
        //     return; 
        // }

        $msg = validatePos();
        if (is_string($msg)) {
            $_SESSION['error'] = $msg;
            header("Location: edit.php?profile_id=".$_GET['profile_id']);
            return;
        }

        // $sql = "INSERT INTO profile (user_id, first_name, last_name, email, headline, summary) VALUES (:uid, :fn, :ln, :em, :he, :su)";

        $sql = "UPDATE profile SET first_name = :fn, last_name = :ln, email = :em , headline = :he, summary = :su WHERE profile_id = :pi";

        $statement = $pdo->prepare($sql);

        $statement->execute(array(
            ":fn" => $_POST['first_name'],
            ":ln" => $_POST['last_name'],
            ":em" => $_POST['email'],
            ":he" => $_POST['headline'],
            ":su" => $_POST['summary'],
            ":pi" => $_GET['profile_id']
        ));

        $statement = $pdo->prepare("DELETE FROM position WHERE profile_id = :pid");

        $statement->execute(array(
            ":pid" => $_GET['profile_id']
        ));

        
        $rank = 1;

        for($i = 1; $i <= 9; $i++) {
            if (! isset($_POST['year'.$i])) continue;
            if (! isset($_POST['desc'.$i])) continue;
            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];
            
            $statement = $pdo->prepare("INSERT INTO position (profile_id, rank, year, description) VALUES(:pid, :rank, :year, :desc)");
            
            $statement->execute(array(
                ":pid" => $_GET['profile_id'],
                ":rank" => $rank,
                ":year" => $year,
                ":desc" => $desc
            ));

            $rank++;
        }

        $_SESSION['success'] = "Profile updated";
        header("Location: index.php");
        return;

    }

    $positions = loadPos($pdo, $_GET['profile_id']);
    
    if (!isset($_GET['profile_id'])) {
        $_SESSION['error'] = "Could not load profile";
        header("Location: index.php");
        return;
    }
    
    $statement = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :profile_id");
    
    $statement->execute(array(
        ":profile_id" => $_GET["profile_id"],
    ));

    $row = $statement->fetch(PDO::FETCH_ASSOC);

    if ($row == false) {
        $_SESSION['error'] = "Could not load profile";
        header("Location: index.php");
        return;
    }

    $first_name = htmlentities($row["first_name"]);
    $last_name = htmlentities($row["last_name"]);
    $email = htmlentities($row["email"]);
    $headline = htmlentities($row["headline"]);
    $summary = htmlentities($row["summary"]);
?>
<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
    <title>Juan Diego Jimenez Giraldo</title>
</head>
<body>
<div class="container">

    <h1>Editing Profile for <?php if (isset($_SESSION['name'])) {
        echo $_SESSION['name']; 
    } ?> </h1>

    <?php 
        if (isset($_SESSION['error'])) {
            echo "<p style='color: red'>".$_SESSION['error']."</p>";
            unset($_SESSION['error']);
        }
    ?>

    <form method="POST">
        <p>
            First Name: <input type="text" name="first_name" value="<?= $first_name; ?>" />
        </p>
        <p>
            Last Name: <input type="text" name="last_name" value="<?= $last_name; ?>" />
        </p>
        <p>
            Email: <input type="text" name="email" value="<?= $email; ?>" />
        </p>
        <p>
            Headline: <input type="text" name="headline" value="<?= $headline; ?>" />
        </p>
        <p>
            Summary: <textarea name="summary" rows="8" cols="80"><?= $summary; ?></textarea>
        </p>
        <input type="hidden" name="profile_id" value="<?= $_GET['profile_id']; ?>"></input>

        <?php 

            $pos = 0;
            echo "<p>Position: <input type='submit' id='addPos' value='+' />";
            echo "<div id='position_fields'>";

            foreach( $positions as $position) {
                $pos++;
                echo "<div id='position".$pos."'>";
                echo "<p>Year: <input type='text' name='year".$pos."'  value='".$position['year']."' />";
                echo "<input type='submit' value='-' onclick='$(\"#position".$pos."\").remove(); return false;' /></p>";
                echo "<p><textarea name='desc".$pos."' rows='8' cols='80'>".$position['description']."</textarea>";
                echo "</div>";
            }

            echo "</div></p>";
        ?>
        <input type="submit" name="submit" value="Save" />
        <input type="submit" name="cancel" value="Cancel" />
    </form>

    <script>
    $(document).ready(function() {
        countPos = <?= $pos ?>;
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
    </div>
</body>

</html>