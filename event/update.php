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

if (empty($_POST['title'])) {
    new HttpStatusCode(400, 'Title cannot be blank.'); 
}
// time range
$startTime= explode(':', $_POST['start_time']);
$endTime = explode(':', $_POST['end_time']);
if ($startTime[0] > $endTime[0] || ($startTime[0]==$endTime[0] && $startTime[1]>$endTime[1])) {
    new HttpStatusCode(400, 'Time range error.'); 
}

$sql = 'UPDATE events 
        SET title=:title, start_time=:start_time, end_time=:end_time, description=:description
        WHERE id=:id';
$statement = $pdo ->prepare($sql);
$statement-> bindValue(':id', $_POST['id'], PDO::PARAM_INT);
$statement-> bindValue(':title', $_POST['title'], PDO::PARAM_STR);
$statement-> bindValue(':start_time', $_POST['start_time'], PDO::PARAM_STR);
$statement-> bindValue(':end_time', $_POST['end_time'], PDO::PARAM_STR);
$statement-> bindValue(':description', $_POST['description'], PDO::PARAM_STR);

if ($statement->execute()) {  
    $id = $pdo -> lastInsertId();

    $sql = 'SELECT id, title, start_time FROM events WHERE id=:id';
    $statement = $pdo ->prepare($sql);
    $statement-> bindValue(':id', $_POST['id'], PDO::PARAM_INT);
    $statement->execute();
    $event = $statement->fetch(PDO::FETCH_ASSOC);   //輸出成ASSOC array

    $event['start_time'] = substr($event['start_time'], 0, 5);

    echo json_encode($event);                     //印給前端
}
