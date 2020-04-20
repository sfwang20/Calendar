<?php
session_start();

include ('../db.php');
try {
    $pdo = new PDO ("mysql:host=$db[host]; dbname=$db[dbname]; port=$db[port];
    charset=$db[charset]",$db['username'],$db['password']);
} catch(PDOException $e) {
    echo "Database Connection failed.";
    echo $e;
    exit;
}

$year = date('Y');
$month = date('m');

//load events
$sql = 'SELECT * FROM events WHERE year=:year AND month=:month
        AND user_id=:user_id ORDER BY `date`, start_time ASC' ;
$statement = $pdo->prepare($sql);
$statement->bindValue(':year', $year, PDO::PARAM_INT);
$statement->bindValue(':month', $month, PDO::PARAM_INT);
$statement->bindValue(':user_id', $_SESSION["id"], PDO::PARAM_STR);
$statement->execute();

$events = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach ($events as $key => $event) {             //10:00:00 > 10:00
    $events[$key]['start_time'] = substr($event['start_time'], 0, 5);
}


$days = cal_days_in_month(CAL_GREGORIAN, $month, $year); //這個月有幾天
// calc padding
//1號是星期幾
$firstDateOfTheMonth = new DateTime("$year-$month-1");
//最後一天是星期幾
$lastDateOfTheMonth = new DateTime("$year-$month-$days");
//calendar要填的padiing
//format('w') will return numeric representation of the day of the week
$frontPadding = $firstDateOfTheMonth->format('w');  //0-6, 0 for Sunday
$backPadding = 6 - $lastDateOfTheMonth->format('w');

for ($i=0; $i < $frontPadding; $i++) {    //填前面的padiing
    $dates[] = null;
}
for ($i=0; $i < $days; $i++) {           //填1~31
    $dates[] = $i + 1;
}
for ($i=0; $i < $backPadding; $i++) {     //填後面的padiing
    $dates[] = null;
}

?>

<script>
    var events = <?= json_encode($events, JSON_NUMERIC_CHECK) ?>;
</script>

<!-- $dates =[];
for ($i=1; $i<=31; $i++) {
    $dates[] = $i;
}

$dates[] = null;
$dates[] = null;
$dates[] = null;
$dates[] = null;
?> -->
