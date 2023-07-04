<?php



function flash() {
    if (isset($_SESSION['error'])) {
        echo "<p style='color: red'>".$_SESSION['error']."</p>";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['success'])) {
        echo "<p style='color: green'>".$_SESSION['success']."</p>";
        unset($_SESSION['success']);
    }
}

function validate() {
    if (empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['headline']) || empty($_POST['summary'])) {
       return "All fields are required";
    } 
    return true;
}

function validatePos() {
    for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;
        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];
        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            return "All fields are required";
        }

        if ( ! is_numeric($year) ) {
            return "Position year must be numeric";
        }
    }
    return true;
}

function validateEdu() {
    for($i=1; $i<=9; $i++) {
        if (! isset($_POST['year_edu'.$i])) continue;
        if (! isset($_POST['school_edu'.$i])) continue;
        $year = $_POST['year_edu'.$i];
        $school = $_POST['school_edu'.$i];

        if (strlen($year) == 0 || strlen($school) == 0) {
            return "All fields are required";
        }

        if (! is_numeric($year)) {
            return "Education year must be numeric";
        }
    }
    return true;
}

function loadPos($pdo, $profile_id) {
    $statement = $pdo->prepare("SELECT * FROM position WHERE profile_id = :pid ORDER BY rank");

    $statement->execute(array(
        ":pid" => $profile_id
    ));
    
    $positions = array();

    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $positions[] = $row;
    }

    return $positions;
}

function loadEdus($pdo, $profile_id) {


    $statement = $pdo->prepare("SELECT e.profile_id, e.institution_id, institution.name, e.rank, e.year FROM education as e LEFT JOIN institution ON e.institution_id = institution.institution_id WHERE profile_id = :pid");

    $statement->execute(array(":pid" => $profile_id));
    
    $positions = array();

    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
        $positions[] = $row;
    }

    return $positions;

}

