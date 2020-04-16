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


$sql = 'SELECT * FROM events WHERE id=:id';
$statement = $pdo ->prepare($sql);
$statement-> bindValue(':id', $_POST['id'], PDO::PARAM_INT);
$statement->execute();

$event = $statement->fetch(PDO::FETCH_ASSOC);
if ($event == false)
    new HttpStatusCode(400, 'No such an event.');
    
echo json_encode($event);
