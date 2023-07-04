<?php
session_start();
require_once "pdo.php";

$stmt = $pdo->query("SELECT user_id, first_name, last_name, headline, profile_id FROM profile");

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
    <title>Juan Diego Jimenez Giraldo</title>
</head>
<body>
<div class="container">
    <h1>Juan's Resume Registry</h1>

    <?php if (isset($_SESSION['user_id'])): ?>
        <p>
            <a href="add.php">Add New Entry</a> |
            <a href="logout.php">Logout</a>
        </p>
    <?php else: ?>
        <p>
            <a href="login.php">Please log in</a>
        </p>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <p style="color: green"><?= $_SESSION['success'] ?></p>
        <?php unset($_SESSION['success']) ?>
    <?php endif; ?>

    <?php if (count($rows) > 0): ?>
        <table border="1">
            <tr>
                <th>Name</th>
                <th>Summary</th>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <th>Action</th>
                <?php endif; ?>
            </tr>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td>
                        <a href="view.php?profile_id=<?= $row['profile_id'] ?>">
                            <?= htmlentities($row['first_name']) ?> <?= htmlentities($row['last_name']) ?>
                        </a>
                    </td>
                    <td><?= htmlentities($row['headline']) ?></td>
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']): ?>
                        <td>
                            <a href="edit.php?profile_id=<?= $row['profile_id'] ?>">Edit</a> |
                            <a href="delete.php?profile_id=<?= $row['profile_id'] ?>">Delete</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No data</p>
    <?php endif; ?>
</div>
</body>
</html>
