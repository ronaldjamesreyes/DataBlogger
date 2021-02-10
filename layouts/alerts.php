<div class="mt-2">
    <?php if(isset($_SESSION['infoMessage']) && !empty($_SESSION['infoMessage'])): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['infoMessage']; unset($_SESSION['infoMessage']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['successMessage']) && !empty($_SESSION['successMessage'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['successMessage']; unset($_SESSION['successMessage']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['errorMessage']) && !empty($_SESSION['errorMessage'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['errorMessage']; unset($_SESSION['errorMessage']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if(isset($_SESSION['validationErrors']) && !empty($_SESSION['validationErrors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <span>The data was invalid.</span>
            <ul class="mb-0">
                <?php foreach($_SESSION['validationErrors'] as $err): ?>
                    <li><?php echo $err;?></li>
                <?php endforeach; unset($_SESSION['validationErrors']);?>
            </ul>
        </div>
    <?php endif; ?>
</div>