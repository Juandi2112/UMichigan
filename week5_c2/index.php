<?php
require_once "pdo.php";
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Juan Diego Jimenez Giraldo</title>
<?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
<h1>Welcome to the Automobiles Database</h1>

<?php
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}
if ( isset($_SESSION['success']) ) {
    echo '<p style="color:green">'.$_SESSION['success']."</p>\n";
    unset($_SESSION['success']);
}
?>

<?php
if ( isset($_SESSION['name']) ) { // Si el usuario ha iniciado sesión
    echo "<p>Welcome: ";
    echo htmlentities($_SESSION['name']);
    echo "</p>";

    // Mostrar la tabla con la información de la base de datos
    $stmt = $pdo->query("SELECT make, model, year, mileage, autos_id FROM autos");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($rows) > 0) {
        echo "<table border='1'><thead><tr><th>Make</th><th>Model</th><th>Year</th><th>Mileage</th><th>Action</th></tr></thead><tbody>";
        foreach ($rows as $row) {
            echo "<tr><td>" . htmlentities($row['make']) . "</td><td>" . $row['model'] . "</td><td>" . $row['year'] . "</td><td>" . $row['mileage'] . "</td><td>";
            echo('<a href="edit.php?autos_id='.$row['autos_id'].'">Edit</a> / ');
            echo('<a href="delete.php?autos_id='.$row['autos_id'].'">Delete</a>');
            echo "</td></tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No rows found</p>";
    }
    echo "<br/><a href='add.php'>Add New Entry</a> | <a href='logout.php'>Logout</a>";
} else { // Si el usuario no ha iniciado sesión
    echo "<p><a href='login.php'>Please log in</a> to access the Automobiles Database.</p>";
}
?>

</body>
</html>
