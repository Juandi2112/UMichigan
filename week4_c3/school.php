<?php

    header("Content-Type: application/json; charset=utf-8");
    require_once "pdo.php";
    
    if (isset($_GET['term'])) {
        if ($_GET['term'] == "")
        {
            echo (json_encode(array()));
            return;
        }
        
        $sql = "SELECT * FROM Institution WHERE name LIKE :prefix";
        $statement = $pdo->prepare($sql);
        $statement->execute(array(
            ":prefix" => "%".$_GET['term']."%"
        ));
        $val = array();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $val[] = $row['name'];
        }
        echo (json_encode($val, JSON_PRETTY_PRINT));
    }