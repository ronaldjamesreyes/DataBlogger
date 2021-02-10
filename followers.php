<?php
    session_start();
    if(!isset($_SESSION['auth']) || empty($_SESSION['auth'])){
        header('Location: login.php');
        exit();
    }
    require_once 'init.php';

    $followers = [];
    $hasError = false;
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $userx = isset($_POST['userx']) ? trim($_POST['userx']) : NULL;
        $usery = isset($_POST['usery']) ? trim($_POST['usery']) : NULL;
        if(empty($userx)){
            $userxError = 'Please enter a user.';
            $hasError = true;
        }
        if(empty($usery)){
            $useryError = 'Please enter a user.';
            $hasError = true;
        }
        if(!$hasError){
            $userXFollowing = $db->query("SELECT user FROM followers WHERE follower = '$userx'")->fetchAll(\PDO::FETCH_COLUMN);
            $userYFollowing = $db->query("SELECT user FROM followers WHERE follower = '$usery'")->fetchAll(\PDO::FETCH_COLUMN);
            $intersectFollowing = implode(',', array_map(function($username){ return "'$username'"; }, array_intersect($userXFollowing, $userYFollowing)));
            $followers = [];
            if($intersectFollowing){
                $followers = $db->query("SELECT * FROM users WHERE username IN ($intersectFollowing)")->fetchAll(\PDO::FETCH_OBJ);
            }
        }
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= APP_NAME ?></title>
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/app.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Cairo&display=swap">
</head>
<body>
    <?php require_once LAYOUTS_PATH.'navbar.inc.php';?>
    <div class="main-page">
        <div class="container">
            <?php require_once LAYOUTS_PATH.'alerts.php';?>
            <h2>Get Users who followed by</h2>
            <div><small class="text-secondary">List the users who are followed by both X and Y</small></div><br>
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-body text-white bg-dark">
                            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                                <div class="form-group">
                                    <label for="userx">User X</label>
                                    <input type="text" class="form-control <?= ($hasError && isset($userxError) && !empty($userxError))? 'is-invalid':''; ?>" id="userx" placeholder="Username 1 ..." name="userx" value="<?= (isset($userx) && !empty($userx)) ? $userx : '' ?>" required>
                                    <?php if($hasError && isset($userxError) && !empty($userxError)): ?>
                                        <div class="invalid-feedback"><?= $userxError ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="usery">User Y</label>
                                    <input type="text" class="form-control <?= ($hasError && isset($useryError) && !empty($useryError))? 'is-invalid':''; ?>" id="usery" placeholder="Username 2 ..." name="usery" value="<?= (isset($usery) && !empty($usery)) ? $usery : '' ?>" required>
                                    <?php if($hasError && isset($useryError) && !empty($useryError)): ?>
                                        <div class="invalid-feedback"><?= $useryError ?></div>
                                    <?php endif; ?>
                                </div>
                                <button type="submit" class="btn btn-outline-success btn-block">
                                    <i class="fas fa-fw fa-check"></i>
                                    Get Users followed By
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
               <div class="col-md-6">
                    <?php foreach($followers as $user): ?>
                    <?php if($user->id): ?>
                        <div class="card mb-4">
                            <div class="card-body text-white bg-dark">
                                <a href="index.php?userid=<?= $user->id ?>">
                                    <h5 class="card-title"><?= htmlspecialchars($user->first_name) . ' ' . htmlspecialchars($user->last_name) ?></h5>
                                </a>
                                <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($user->username) ?></h6>
                                <p class="card-text"><?= htmlspecialchars($user->email) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>     
        </div>
    </div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>