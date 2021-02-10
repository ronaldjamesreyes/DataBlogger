<?php
    session_start();
    if(!isset($_SESSION['auth']) || empty($_SESSION['auth'])){
        header('Location: login.php');
        exit();
    }
    require_once 'init.php';



    // 
    $usersMostPosted  = $db->query("SELECT *, MAX(blogs_count) AS max_blogs_count FROM (
        SELECT users.*, COUNT(blogs.id) AS blogs_count FROM users
        LEFT JOIN blogs ON blogs.user_id = users.id WHERE DATE(blogs.created_at) = '2020-10-10' GROUP BY users.id
    ) AS users")->fetchAll(\PDO::FETCH_OBJ);


     

    if(isset($_GET['userid']) && $userid = intval($_GET['userid'])){
        // get user
        $stm = $db->prepare("SELECT * FROM `users` WHERE `id` = ? LIMIT 1");
        $stm->execute([$userid]);
        $user = $stm->fetch(\PDO::FETCH_OBJ);
        if($user) {
            // get blogs by users where all comments ar positive
            $stm = $db->prepare("SELECT `blogs`.*, `users`.`first_name`, `users`.`last_name` FROM `blogs`
            LEFT JOIN `users` ON `blogs`.`user_id` = `users`.`id`
            WHERE `blogs`.`user_id` = ?
            AND Not EXISTS (SELECT * FROM comments WHERE comments.blog_id = blogs.id AND sentiment = 'Negative')
            HAVING (SELECT COUNT(*) FROM comments WHERE comments.blog_id = blogs.id) > 0");
            $stm->execute([$userid]);
            $blogs = $stm->fetchAll(\PDO::FETCH_OBJ);
        } else {
            $redirectTo = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
            header("Location: $redirectTo");
            exit();   
        }
    } else {
        $blogs  = $db->query("SELECT 
            `blogs`.*, `users`.`first_name`, `users`.`last_name` 
            FROM `blogs`
            LEFT JOIN `users` ON `blogs`.`user_id` = `users`.`id` ORDER BY `created_at` DESC
        ")->fetchAll(\PDO::FETCH_OBJ);
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
            <h2>
                Blogs
                <?php if(isset($user) && !empty($user)): ?>
                    <span class="text-muted">By  <?= htmlspecialchars($user->first_name) . ' ' . htmlspecialchars($user->last_name) ?></span>
                    Such all comments are positive
                <?php endif;  ?>
            </h2>
            <div class="row mb-5">
                <?php if(count($blogs) > 0): ?>
                    <?php foreach($blogs as $blog): ?>
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-body text-white bg-dark">
                                    <a href="blog.php?id=<?= $blog->id ?>">
                                        <h5 class="card-title"><?= htmlspecialchars($blog->subject) ?></h5>
                                    </a>
                                    <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($blog->tags) ?></h6>
                                    <p class="card-text">
                                        <i class="fas fa-fw fa-xs fa-quote-left text-muted"></i>
                                        <?= htmlspecialchars($blog->description) ?>
                                        <i class="fas fa-fw fa-xs fa-quote-right text-muted"></i>
                                    </p>
                                    <span class="text">By 
                                        <a href="index.php?userid=<?= $blog->user_id ?>">
                                            <?= htmlspecialchars($blog->first_name) . ' ' . htmlspecialchars($blog->last_name) ?>
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-md-12">
                        <div class="text-muted">
                            There's no blog.
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <h2>List of users who posted the most number of blogs</h2>
            <div><small class="text-secondary">List the users who posted the most number of blogs on 10/10/2020</small></div><br>
            <div class="row mb-5">
                <?php foreach($usersMostPosted as $user): ?>
                    <?php if($user->id): ?>
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
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>