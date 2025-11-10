<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="main-header">
    <div class="top-bar">
        <div class="grid-container top-right">
            <ul class="auth-links">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="admin/dashboard.php">Admin Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Logout (<?= htmlspecialchars($_SESSION['username']); ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="grid-container main-nav">
        <a href="index.php" class="logo">Manitoba Nature Archive</a>
        <nav class="main-menu">
            <ul class="main-menu-list">
                <li class="main-menu-item"><a href="animal.php">Animal</a></li>
                <li class="main-menu-item"><a href="plant.php">Plant</a></li>
                <li class="main-menu-item"><a href="gallery.php">Photo</a></li>
            </ul>
        </nav>
        <form method="get" action="search.php" class="sitesearch-form">
            <input id="sitesearch" class="sitesearch-input" type="text" name="q" placeholder="Search">
        </form>
    </div>
</header>
