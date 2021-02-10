<?php
    session_start();
    if(!isset($_SESSION['auth']) || empty($_SESSION['auth'])){
        header('Location: login.php');
        exit();
    }
    require_once 'init.php';

    $sersPostedNegativeComments  = $db->query("SELECT DISTINCT users.* FROM `users` LEFT JOIN comments ON users.id = comments.user_id WHERE id IN (SELECT DISTINCT(`user_id`) FROM `comments` WHERE `sentiment` = 'Negative' AND user_id NOT IN (SELECT DISTINCT(`user_id`) FROM `comments` WHERE `sentiment` = 'Positive' ))")->fetchAll(\PDO::FETCH_OBJ);
    $blogsByUsersPostedNegativeComments = [];

    $sersPostedNegativeCommentsIds = implode(', ', array_map(function($user){ return $user->id; }, $sersPostedNegativeComments));
    if($sersPostedNegativeCommentsIds) {
        // $blogsByUsersPostedNegativeComments = $db->query("SELECT `blogs`.*, `users`.* FROM `blogs`
        // LEFT JOIN `users` ON `blogs`.`user_id` = `users`.`id`
        // WHERE `blogs`.`user_id` IN ($sersPostedNegativeCommentsIds)")->fetchAll(\PDO::FETCH_OBJ);
        // $blogsByUsersPostedNegativeComments = $db->query("SELECT `blogs`.*, `users`.`first_name`, `users`.`last_name` FROM `blogs`
        //     LEFT JOIN `users` ON `blogs`.`user_id` = `users`.`id`
        //     WHERE `blogs`.`user_id` = ?
        //     AND Not EXISTS (SELECT * FROM comments WHERE comments.blog_id = blogs.id AND sentiment = 'Negative')
        //     HAVING (SELECT COUNT(*) FROM comments WHERE comments.blog_id = blogs.id) > 0");
        
        
        
        // SELECT DISTINCT users.* FROM `users` 
        // LEFT JOIN comments ON users.id = comments.user_id 
        // WHERE id IN (
        //     SELECT DISTINCT(`user_id`) FROM `comments` 
        //     WHERE `sentiment` = 'Negative' 
        //     AND user_id NOT IN (SELECT DISTINCT(`user_id`) FROM `comments` WHERE `sentiment` = 'Positive' )
        // )
        
        // SELECT DISTINCT users.* FROM `users` 
        // LEFT JOIN blogs ON users.id = blogs.user_id 
        // WHERE id IN (
        //     SELECT DISTINCT(`user_id`) FROM `comments` 
        //     WHERE `sentiment` = 'Negative' 
        //     AND user_id NOT IN (SELECT DISTINCT(`user_id`) FROM `comments` WHERE `sentiment` = 'Positive' )
        // )


        $blogsByUsersPostedNegativeComments = $db->query("SELECT DISTINCT * FROM (
            SELECT `users`.* FROM `users`
            LEFT JOIN blogs ON users.id = blogs.user_id
            LEFT JOIN comments ON blogs.id = comments.blog_id
            WHERE (comments.sentiment != 'Negative' OR comments.sentiment IS NULL) AND `users`.`id` IN ($sersPostedNegativeCommentsIds)
            HAVING (SELECT COUNT(*) FROM blogs WHERE blogs.user_id = users.id) > 0
        ) AS users")->fetchAll(\PDO::FETCH_OBJ);
            // HAVING (SELECT COUNT(*) FROM comments WHERE comments.blog_id = blogs.id) > 0
        // ddd($blogsByUsersPostedNegativeComments);
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
            <h2>List of users who posted only negative comments</h2>
            <div><small class="text-secondary">Display all the users who posted some comments, but each of them is negative.</small></div><br>
            <div class="row mb-5">
                <?php foreach($sersPostedNegativeComments as $user): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body text-white bg-dark">
                                <a href="index.php?userid=<?= $user->id ?>">
                                    <h5 class="card-title"><?= htmlspecialchars($user->first_name) . ' ' . htmlspecialchars($user->last_name) ?> (<?= htmlspecialchars($user->username) ?>)</h5>
                                </a>
                                <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($user->email) ?></h6>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div><br>
            <h2>List of users that never received any negative comments</h2>
            <div><small class="text-secondary">Display those users such that all the blogs they posted so far never received any negative comments.</small></div><br>
            <div class="row mb-5">
                <?php if(count($blogsByUsersPostedNegativeComments) > 0): ?>
                    <?php foreach($blogsByUsersPostedNegativeComments as $user): ?>
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-body text-white bg-dark">
                                    <a href="index.php?userid=<?= $user->id ?>">
                                        <h5 class="card-title"><?= htmlspecialchars($user->first_name) . ' ' . htmlspecialchars($user->last_name) ?> (<?= htmlspecialchars($user->username) ?>)</h5>
                                    </a>
                                    <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($user->email) ?></h6>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-md-12">
                        <div class="text-muted">
                            There are no users.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>