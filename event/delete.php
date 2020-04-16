<?php
header('Content-Type: application/json; charset=utf-8');
include ('../../db.php');
include ('../HttpStatusCode.php');

try {
    $pdo = new PDO ("mysql:host=$db[host]; dbname=$db[dbname]; port=$db[port]; 
    charset=$db[charset]",$db['username'],$db['password']);
} catch(PDOException $e) {
    echo "Database Connection failed.";
    exit;
}


$sql = 'DELETE FROM events WHERE id=:id';
$statement = $pdo ->prepare($sql);
$statement-> bindValue(':id', $_POST['id'], PDO::PARAM_INT);

if ($statement->execute()) {  
    echo json_encode(['id' => $_POST['id']]);
}
else {
    new HttpStatusCode(400, 'Wrong event.');
}
