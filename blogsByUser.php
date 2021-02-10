<?php
    session_start();
    if(!isset($_SESSION['auth']) || empty($_SESSION['auth'])){
        header('Location: login.php');
        exit();
    }
    require_once 'init.php';

    $hasError = false;
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $userx = isset($_POST['userx']) ? trim($_POST['userx']) : NULL;
        if(empty($userx)){
            $userxError = 'Please enter a user.';
            $hasError = true;
        }
        if(!$hasError){
            // get user
            $stm = $db->prepare("SELECT * FROM `users` WHERE `username` = ? LIMIT 1");
            $stm->execute([$userx]);
            $user = $stm->fetch(\PDO::FETCH_OBJ);
            if($user) {
                // get blogs by users where all comments ar positive
                $stm = $db->prepare("SELECT `blogs`.*, `users`.`first_name`, `users`.`last_name` FROM `blogs`
                LEFT JOIN `users` ON `blogs`.`user_id` = `users`.`id`
                WHERE `blogs`.`user_id` = ?
                AND Not EXISTS (SELECT * FROM comments WHERE comments.blog_id = blogs.id AND sentiment = 'Negative')
                HAVING (SELECT COUNT(*) FROM comments WHERE comments.blog_id = blogs.id) > 0");
                $stm->execute([$user->id]);
                $blogs = $stm->fetchAll(\PDO::FETCH_OBJ);
            } else {
                $redirectTo = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'blogsByUser.php';
                header("Location: $redirectTo");
                exit();   
            }
        }
    }
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
            <h2>Blogs By User</h2>
            <div><small class="text-secondary">All the users blog(s) which only has positive comments</small></div><br>
            <form class="form-inline align-items-start mb-4" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                <div class="mr-2">
                    <input type="text" class="form-control <?= ($hasError && isset($userxError) && !empty($userxError))? 'is-invalid':''; ?>" id="userx" placeholder="Username ..." name="userx" value="<?= (isset($userx) && !empty($userx)) ? $userx : '' ?>" required>
                    <?php if($hasError && isset($userxError) && !empty($userxError)): ?>
                        <div class="invalid-feedback"><?= $userxError ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary">
                    Get Blog(s)
                </button>
            </form>
            <div class="row mb-5">
                <?php if(isset($user)): ?>
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
                                        <span class="text-muted">By 
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
                <?php endif; ?>
            </div>
        </div>
    </div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>