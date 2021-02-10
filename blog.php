<?php
    session_start();
    if(!isset($_SESSION['auth']) || empty($_SESSION['auth'])){
        header('Location: login.php');
        exit();
    }
    require_once 'init.php';

    function canComment($blog, $alert = true){
        Global $db;
        if($blog->user_id == $_SESSION['auth']['id']) {
            // user can't comment on his blog
            if($alert)
                $_SESSION['infoMessage'] = "You can't comment on your vlog.";
            return false;
        } elseif (intval($db->query("SELECT COUNT(`commentid`) from `comments` where `created_at` > CURRENT_DATE() AND `user_id` = {$_SESSION['auth']['id']}")->fetch(\PDO::FETCH_COLUMN)) >= 3) {
            // check max 3 comments (day)
            if($alert)
                $_SESSION['infoMessage'] = "You can give at most 3 comments a day.";
            return false;
        } elseif (intval($db->query("SELECT COUNT(`commentid`) from `comments` where `blog_id` = {$blog->id} AND `user_id` = {$_SESSION['auth']['id']}")->fetch(\PDO::FETCH_COLUMN)) >= 1) {
            // check user can comment one at most for each log
            if($alert)
                $_SESSION['infoMessage'] = "You already commented on this post.";
            return false;
        } else {
            return true;
        }
    }

    // get post with comments
    if(isset($_GET['id']) && $id = intval($_GET['id'])){
        $stm = $db->prepare("SELECT 
        `blogs`.*, `users`.`first_name`, `users`.`last_name` 
        FROM `blogs`
        LEFT JOIN `users` ON `blogs`.`user_id` = `users`.`id`
        WHERE `blogs`.`id` = ? LIMIT 1");
        $stm->execute([$id]);
        $blog = $stm->fetch(\PDO::FETCH_OBJ);
        if(!$blog){
            $redirectTo = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
            header("Location: $redirectTo");
            exit();   
        } else {
            $blog->comments  = $db->query("SELECT `comments`.*, `users`.`first_name`, `users`.`last_name` FROM `comments`
                LEFT JOIN `users` ON `comments`.`user_id` = `users`.`id` 
                WHERE `comments`.`blog_id`= $blog->id 
                ORDER BY `created_at` DESC
            ")->fetchAll(\PDO::FETCH_OBJ);
        }
    } else {
        $_SESSION['infoMessage'] = 'Blog not found.';
        $redirectTo = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
        header("Location: $redirectTo");
        exit();
    }
    
    // create comment
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment']) && canComment($blog)){
        $description = isset($_POST['description']) ? trim($_POST['description']) : NULL;
        $sentiment = isset($_POST['sentiment']) ? trim($_POST['sentiment']) : NULL;
        $blog_id = isset($_POST['blog_id']) ? trim($_POST['blog_id']) : NULL;
        if(empty($description)){
            $_SESSION['validationErrors'][] = 'Please enter a comment.';
        }
        if(empty($sentiment)){
            $_SESSION['validationErrors'][] = 'Please select a sentiment.';
        }
        if(empty($blog_id)){
            $_SESSION['validationErrors'][] = 'Please select a blog.';
        }
        if(empty($_SESSION['validationErrors'])){
            $stm = $db->prepare("
                INSERT INTO `comments` 
                (`description`, `sentiment`, `blog_id`, `user_id`)
                VALUES
                (:description, :sentiment, :blog_id, :user_id)
            ");
            if($stm->execute([
                ':description' => $description,
                ':sentiment' => $sentiment,
                ':blog_id' => $blog_id,
                ':user_id' => $_SESSION['auth']['id']
            ])){
                $_SESSION['successMessage'] = 'Comment has been added successfully.';
                header("Location: blog.php?id=$blog->id");
                exit();
            }else{
                $_SESSION['errorMessage'] = 'Something went wrong. Please try again later.';
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
            <h2><?= htmlspecialchars($blog->subject) ?></h2>
            <div href="blog.php?id=<?= $blog->id ?>" class="card mb-4">
                <div class="card-body text-white bg-dark">
                    <h5 class="card-title"><?= htmlspecialchars($blog->subject) ?></h5>
                    <h6 class="card-subtitle mb-2 "><?= htmlspecialchars($blog->tags) ?></h6>
                    <p class="card-text">
                        <i class="fas fa-fw fa-xs fa-quote-left text-muted"></i>
                        <?= htmlspecialchars($blog->description) ?>
                        <i class="fas fa-fw fa-xs fa-quote-right text-muted"></i>
                    </p>
                    <div class="text-muted">
                        By 
                        <a href="index.php?userid=<?= $blog->user_id ?>">
                            <?= htmlspecialchars($blog->first_name) . ' ' . htmlspecialchars($blog->last_name) ?>
                        </a>
                    </div>
                
                    <?php if(count($blog->comments) > 0): ?>
                        <!-- List comments -->
                        <hr>
                        Comments
                        <?php foreach($blog->comments as $comment): ?>
                            <div class="comment ml-2">
                                <div class="text-muted">
                                    <a href="index.php?userid=<?= $blog->user_id ?>">
                                        <?= htmlspecialchars($comment->first_name) . ' ' . htmlspecialchars($comment->last_name) ?>
                                    </a>
                                </div>
                                <div>Rate: <?= htmlspecialchars($comment->sentiment) ?></div>
                                <p><?= htmlspecialchars($comment->description) ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if(canComment($blog, false)): ?>
                    <hr>
                    <!-- Add comment -->
                    <form action="#" method="POST">
                        <input type="hidden" name="comment">
                        <input type="hidden" name="blog_id" value="<?= $blog->id ?>">
                        <select class="custom-select custom-select-sm mb-2" name="sentiment" required>
                            <option value="" selected>Rate This Post</option>
                            <option value="Positive">Positive</option>
                            <option value="Negative">Negative</option>
                        </select>
                        <textarea class="form-control mb-2" id="description" placeholder="Comment" name="description" required></textarea>
                        <button class="btn btn-sm btn-dark" type="submit">Reply <i class="fas fa-fw fa-check"></i></button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>