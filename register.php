<?php
session_start();
if ($_SESSION["loggedin"] == true) {
  header("location: index.php");
  exit;
}

include ('../db.php');
try {
    $pdo = new PDO("mysql:host=$db[host];dbname=$db[dbname];port=$db[port];
    charset=$db[charset]", $db['username'], $db['password']);
} catch (PDOException $e) {
    echo "Database connection failed.";
    exit;
}

// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else {
        $sql = "SELECT username FROM users WHERE username = :username";
        $statement = $pdo->prepare($sql);
        $statement -> bindValue(':username', $_POST["username"], PDO::PARAM_STR);
        $statement -> execute();
        $result = $statement ->fetch(PDO::FETCH_ASSOC);

        if ($result['username'] == $_POST['username']) {
            $username_err = "This username is already taken.";
        } else {
             $username = trim($_POST["username"]);
        }
    }

    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

        $sql = "INSERT INTO users (username, `password`) VALUES (:username, :password)";
        $statement = $pdo->prepare($sql);
        $statement -> bindValue(':username', $_POST['username'], PDO::PARAM_STR);
        $statement -> bindValue(':password', $_POST['password'], PDO::PARAM_STR);
        $result = $statement -> execute();
        if ($result) {
            header("location: login.php"); // Redirect to login page
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
</head>
<body>
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; margin:0 auto; position: relative; top:80px;}
    </style>

    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="register.php" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-default" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>
</body>
</html>
