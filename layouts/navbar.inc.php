<nav class="navbar navbar-expand-lg navbar-light bg-light static-top border-bottom">
  <div class="container">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarResponsive">
      <ul class="navbar-nav">
        <li class="nav-item px-1">
          <a class="nav-link" href="index.php">Home</a>
        </li>
        <li class="nav-item px-1">
          <a class="nav-link" href="create_blog.php">Post a Blog</a>
        </li>
        <li class="nav-item px-1">
          <a class="nav-link" href="blogsByUser.php">Blogs by User</a>
        </li>
        <li class="nav-item px-1">
          <a class="nav-link" href="followers.php">Followers</a>
        </li>
        <li class="nav-item px-1">
          <a class="nav-link" href="usersWithoutBlogs.php" >Inactive Users</a>
        </li>
        <li class="nav-item px-1">
          <a class="nav-link" href="5_6.php">User Miscellaneous</a>
        </li>
        
      </ul>
      <ul class="navbar-nav ml-auto">
        <li class="nav-item px-1">
          <a class="btn btn-info btn-sm" href="initialize.php">Initialize Database</a>
        </li>
        <li class="nav-item px-1 dropdown">
          <a class="nav-link" href="#" data-toggle="dropdown">
            <?= (isset($_SESSION['auth']) && !empty($_SESSION['auth'])) ? $_SESSION['auth']['last_name'].' '.$_SESSION['auth']['first_name'] : '' ?>
            <i class="far fa-fw fa-user"></i>
          </a>
          <div class="dropdown-menu dropdown-menu-right">
            <?php if(isset($_SESSION['auth']) && !empty($_SESSION['auth'])): ?>
              <a href="logout.php" class="dropdown-item">Sign Out</a>
            <?php else: ?>
              <a href="login.php" class="dropdown-item">Login</a>
            <?php endif; ?>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>