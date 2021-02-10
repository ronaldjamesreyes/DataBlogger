<?php
    session_start();
    if(!isset($_SESSION['auth']) || empty($_SESSION['auth'])){
        header('Location: login.php');
        exit();
    }
    require_once 'init.php';
    $usesr_without_blog  = $db->query("SELECT * FROM `users` WHERE `id` NOT IN (SELECT DISTINCT(`user_id`) FROM `blogs`) ORDER BY `created_at` DESC")->fetchAll(\PDO::FETCH_OBJ);
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
            <h2>List of users who never posted a blog</h2>
            <div><small class="text-secondary">Display all the users who never posted a blog</small></div><br>
            <div class="row mb-5">
                <?php foreach($usesr_without_blog as $user): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body text-white bg-dark">
                                <a href="index.php?userid=<?= $user->id ?>">
                                    <h5 class="card-title"><?= htmlspecialchars($user->first_name) . ' ' . htmlspecialchars($user->last_name) ?></h5>
                                </a>
                                <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($user->username) ?></h6>
                                <p class="card-text"><?= htmlspecialchars($user->email) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>