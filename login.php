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

$username = $password = "";
$username_err = $password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST"){

    if (empty(trim($_POST["username"]))){
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if(empty($username_err) && empty($password_err)){

        $sql = "SELECT * FROM users WHERE username = :username";
        $statement = $pdo->prepare($sql);
        $statement -> bindValue(':username', $_POST["username"], PDO::PARAM_STR);
        $result = $statement->execute();
        $user = $statement -> fetch(PDO::FETCH_ASSOC);
        if ($result) {
            if ($user["password"] == $_POST["password"]) {
                // Store data in session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $user['id'];
                $_SESSION["username"] = $user['username'];

                // Redirect user to index page
                header("location:index.php");
            } else {
                $password_err = "The password you entered was not valid.";
            }
        } else {
            $username_err = "No account found with that username.";
        }
    }
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; margin:0 auto; position: relative; top:80px;}
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form action="login.php" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
            <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        </form>
    </div>
</body>
</html>
