<?php
session_start();
if(isset($_SESSION['auth']) && !empty($_SESSION['auth'])){
    header('Location: index.php');
    exit();
}

// $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi => password
require_once 'init.php';
$hasError = false;
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = isset($_POST['username']) ? trim($_POST['username']) : NULL;
    $password = isset($_POST['password']) ? trim($_POST['password']) : NULL;
    if(empty($username)){
        $usernameError = 'Please enter a username.';
        $hasError = true;
    }
    if(empty($password)){
        $passwordError = 'Please enter your password.';
        $hasError = true;
    }

    if(!$hasError){
        $stm = $db->prepare("SELECT * FROM `users` WHERE `username` = ?");
        $stm->execute([$username]);
        $userData = $stm->fetch(\PDO::FETCH_ASSOC);
        if($userData && password_verify($password, $userData['password'])){
            $_SESSION['auth'] = $userData;
            header('Location: index.php');
            exit();
        }else{
            $usernameError = 'These identifiers do not match our records.';
            $hasError = true;
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= APP_NAME ?> | Login</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Cairo&display=swap">
    <style>
    body{ 
        font-family: 'Cairo', sans-serif;
        font: 14px; 
    }

    .wrapper{
        width:100%;
        margin:auto;
        width: 525px; 
        padding: 20px;
        max-width:525px;
        min-height:450px;
        position:relative;
        background:url(https://images.pexels.com/photos/1420440/pexels-photo-1420440.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=750&w=1260) no-repeat center;
        box-shadow:0 12px 15px 0 rgba(0,0,0,.24),0 17px 50px 0 rgba(0,0,0,.19);
    }

    .wrapper-style{
        width:100%;
        height:100%;
        position:relative;
        padding:90px 70px 50px 70px;
        background:rgba(0,0,0,.6);
    }

    .text{
        font-weight:bold;
        color: white;
    }

    #text_footer{
        font-size:15px;
    }

    #title{
        font-size:40px;
        text-align: center;
    }

    .btn{
        border: none;
        font-weight:700px;
        padding: 10px 20px;
        border-radius: 25px;
        background: rgba(255,255,255,.1);
    }
    </style>
</head>
<body>
    <div class="wrapper">
      <div class="wrapper-style">
        <h2 class="text" id="title">Login</h2>
        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label class="text" for="username">Username:</label>
                <input type="text" class="form-control <?= ($hasError && isset($usernameError) && !empty($usernameError))? 'is-invalid':''; ?>" id="username" placeholder="Username" name="username" value="<?= (isset($username) && !empty($username)) ? $username : '' ?>" required autofocus>
                <?php if($hasError && isset($usernameError) && !empty($usernameError)): ?>
                    <div class="invalid-feedback"><?= $usernameError ?></div>
                <?php endif; ?>
            </div>    
            <div class="form-group">
                <label class="text" for="password">Password:</label>
                <input type="password" class="form-control <?= ($hasError && isset($passwordError) && !empty($passwordError))? 'is-invalid':''; ?>" id="password" placeholder="Password" name="password" value="<?= (isset($password) && !empty($password)) ? $password : '' ?>" required>
                <?php if($hasError && isset($passwordError) && !empty($passwordError)): ?>
                    <div class="invalid-feedback"><?= $passwordError ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block mb-2">Login</button>
            </div>
            <p class="text" id="text_footer">Don't have an account? <a href="register.php">Sign Up Here</a></p>
        </form>
        </div>
    </div>    
</body>
</html>
