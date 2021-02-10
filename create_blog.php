<?php
    session_start();
    if(!isset($_SESSION['auth']) || empty($_SESSION['auth'])){
        header('Location: login.php');
        exit();
    }
    require_once 'init.php';

    $todayPostsCount = (int) $db->query("SELECT COUNT(`id`) from `blogs` where `created_at` > CURRENT_DATE() AND `user_id` = {$_SESSION['auth']['id']}")->fetch(\PDO::FETCH_COLUMN);
     
    if($todayPostsCount >= 2) {
        $_SESSION['infoMessage'] = "You can't post new blog today.";
        header('Location: index.php');
        exit();
    }


    $hasError = false;
    if($_SERVER['REQUEST_METHOD'] == 'POST' && $todayPostsCount < 2){
        $subject = isset($_POST['subject']) ? trim($_POST['subject']) : NULL;
        $tags = isset($_POST['tags']) ? trim($_POST['tags']) : NULL;
        $description = isset($_POST['description']) ? trim($_POST['description']) : NULL;
        if(empty($subject)){
            $subjectError = 'Please enter a subject.';
            $hasError = true;
        }
        if(empty($description)){
            $descriptionError = 'Please enter a description.';
            $hasError = true;
        }
        if(empty($tags)){
            $tagsError = 'Please enter a tags.';
            $hasError = true;
        }
        if(!$hasError){
            $stm = $db->prepare("
                INSERT INTO `blogs` 
                (`subject`, `tags`, `description`, `user_id`)
                VALUES
                (:subject, :tags, :description, :user_id)
            ");
            if($stm->execute([
                ':subject' => $subject,
                ':tags' => $tags,
                ':description' => $description,
                ':user_id' => $_SESSION['auth']['id']
            ])){
                $_SESSION['successMessage'] = 'Post has been added successfully.';
                header('Location: index.php');
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
    <title><?= APP_NAME ?> | New Blog</title>
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
            <h2>Post a Blog</h2>
            <div class="card mb-4">
                <div class="card-body text-white bg-dark">
                    <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject">Subject</label>
                                    <input type="text" class="form-control <?= ($hasError && isset($subjectError) && !empty($subjectError))? 'is-invalid':''; ?>" id="subject" placeholder="subject..." name="subject" value="<?= (isset($subject) && !empty($subject)) ? $subject : '' ?>" required>
                                    <?php if($hasError && isset($subjectError) && !empty($subjectError)): ?>
                                        <div class="invalid-feedback"><?= $subjectError ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="tags">Tags</label>
                                    <input type="text" class="form-control <?= ($hasError && isset($tagsError) && !empty($tagsError))? 'is-invalid':''; ?>" id="tags" placeholder="tags..." name="tags" value="<?= (isset($tags) && !empty($tags)) ? $tags : '' ?>" required>
                                    <?php if($hasError && isset($tagsError) && !empty($tagsError)): ?>
                                        <div class="invalid-feedback"><?= $tagsError ?></div>
                                    <?php endif; ?>
                                </div>
                                <button type="submit" class="btn btn-outline-success btn-block">
                                    <i class="fas fa-fw fa-check"></i>
                                    Submit
                                </button>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control <?= ($hasError && isset($descriptionError) && !empty($descriptionError))? 'is-invalid':''; ?>" id="description" placeholder="Description..." name="description" rows="6" required><?= (isset($description) && !empty($description)) ? $description : '' ?></textarea>
                                    <?php if($hasError && isset($descriptionError) && !empty($descriptionError)): ?>
                                        <div class="invalid-feedback"><?= $descriptionError ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script src="plugins/jquery/jquery.min.js"></script>
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>