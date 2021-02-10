<?php
session_start();
if(isset($_SESSION['auth']) && !empty($_SESSION['auth'])){
    header('Location: index.php');
    exit();
}

require_once 'init.php';
$hasError = false;
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : NULL;
    $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : NULL;
    $email = isset($_POST['email']) ? trim($_POST['email']) : NULL;
    $username = isset($_POST['username']) ? trim($_POST['username']) : NULL;
    $password = isset($_POST['password']) ? trim($_POST['password']) : NULL;
    $password_confirmation = isset($_POST['password_confirmation']) ? trim($_POST['password_confirmation']) : NULL;
    if(empty($username)){
        $usernameError = 'Please enter a username.';
        $hasError = true;
    }
    if(empty($email)){
        $emailError = 'Please enter an email.';
        $hasError = true;
    }
    if(empty($first_name)){
        $firstNameError = 'Please enter your first name.';
        $hasError = true;
    }
    if(empty($last_name)){
        $lastNameError = 'Please enter your last name.';
        $hasError = true;
    }
    if(empty($password)){
        $passwordError = 'Please enter your password.';
        $hasError = true;
    }
    if(!empty($password) && (strlen($password) < 6)){
        $passwordError = 'Password must have atleast 6 characters.';
        $hasError = true;
    }
    if(empty($password_confirmation)){
        $passwordConfirmationError = 'Please confirm password.';
        $hasError = true;
    }
    if(!empty($password_confirmation) && !empty($password) && ($password_confirmation != $password)){
        $passwordConfirmationError = 'Password did not match.';
        $hasError = true;
    }
    if(!$hasError){
        // check if users exists (unique emai, username)
        $stm2 = $db->prepare("SELECT COUNT(`id`) from `users` WHERE `email` = ? OR `username` = ?");
        $stm2->execute([$email, $username]);
        if($stm2->fetch(\PDO::FETCH_COLUMN) > 0){
            $emailError = 'Username / Email is already taken.';
            $hasError = true;
        } else {
            $stm = $db->prepare("
                INSERT INTO `users` 
                (`first_name`, `last_name`, `username`, `email`, `password`)
                VALUES
                (:first_name, :last_name, :username, :email, :password)
            ");
            if($stm->execute([
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':username' => $username,
                ':email' => $email,
                ':password' => password_hash($password, PASSWORD_DEFAULT)
            ])){
                header('Location: login.php');
                exit();
            }else{
                $emailError = 'Something went wrong. Please try again later.';
                $hasError = true;
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= APP_NAME ?> | Sign Up</title>
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

        @media (max-width: 767px) {
            .main-page {
                padding: 1rem !important;
            }
            .main-page .form {
                width: 100% !important;
                padding-top: 40px;
                padding-bottom: 40px;
            }
        }
        @media (max-width: 600px) {
            form.login {
                min-width: unset;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
      <div class="wrapper-style">
        <h2 class="text" id="title">Sign Up</h2>
        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
            <div class="form-group">
                <label class="text" for="first_name">First Name:</label>
                <input type="text" class="form-control <?= ($hasError && isset($firstNameError) && !empty($firstNameError))? 'is-invalid':''; ?>" id="first_name" placeholder="First Name" name="first_name" value="<?= (isset($first_name) && !empty($first_name)) ? $first_name : '' ?>" required autofocus>
                <?php if($hasError && isset($firstNameError) && !empty($firstNameError)): ?>
                    <div class="invalid-feedback"><?= $firstNameError ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label class="text" for="last_name">Last Name:</label>
                <input type="text" class="form-control <?= ($hasError && isset($lastNameError) && !empty($lastNameError))? 'is-invalid':''; ?>" id="last_name" placeholder="Last Name" name="last_name" value="<?= (isset($last_name) && !empty($last_name)) ? $last_name : '' ?>" required>
                <?php if($hasError && isset($lastNameError) && !empty($lastNameError)): ?>
                    <div class="invalid-feedback"><?= $lastNameError ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label class="text" for="email">Email:</label>
                <input type="email" class="form-control <?= ($hasError && isset($emailError) && !empty($emailError))? 'is-invalid':''; ?>" id="email" placeholder="Email" name="email" value="<?= (isset($email) && !empty($email)) ? $email : '' ?>" required>
                <?php if($hasError && isset($emailError) && !empty($emailError)): ?>
                    <div class="invalid-feedback"><?= $emailError ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label class="text" for="username">Username:</label>
                <input type="text" class="form-control <?= ($hasError && isset($usernameError) && !empty($usernameError))? 'is-invalid':''; ?>" id="username" placeholder="Username" name="username" value="<?= (isset($username) && !empty($username)) ? $username : '' ?>" required>
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
                <label class="text" for="password_confirmation">Confirm Password:</label>
                <input type="password" class="form-control <?= ($hasError && isset($passwordConfirmationError) && !empty($passwordConfirmationError))? 'is-invalid':''; ?>" id="password_confirmation" placeholder="Confirm Password" name="password_confirmation" value="<?= (isset($password_confirmation) && !empty($password_confirmation)) ? $password_confirmation : '' ?>" required>
                <?php if($hasError && isset($passwordConfirmationError) && !empty($passwordConfirmationError)): ?>
                    <div class="invalid-feedback"><?= $passwordConfirmationError ?></div>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block mb-2">Sign up</button>
            </div>
            <div>
                <span class="text">Already have an account?</span> <a href="login.php">Login Here</a>
            </div>
        </form>
       </div>
    </div>    
</body>
</html>
