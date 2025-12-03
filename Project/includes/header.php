<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$header_categories = [];
if (!isset($db)) {
    $db_path = __DIR__ . '/db_connect.php';
    if (file_exists($db_path)) {
        require $db_path;
    }
}
if (isset($db)) {
    $stmt_header_cat = $db->query("SELECT category_id, name FROM categories ORDER BY name ASC");
    $header_categories = $stmt_header_cat->fetchAll(PDO::FETCH_ASSOC);
}
$header_q = $_GET['q'] ?? '';
$header_category_id = $_GET['category_id'] ?? '';
?>
<script src="https://cdn.tiny.cloud/1/tiun0my8wp7befqxzhevij5fnc41hch2zll6497wl7kf6mu9/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.tinymce && document.querySelector('textarea.wysiwyg')) {
        tinymce.init({
            selector: 'textarea.wysiwyg',
            menubar: false,
            plugins: 'link lists',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link | removeformat',
            branding: false,
            setup: function (editor) {
                editor.on('init', function () {
                    if (editor.getElement().form) {
                        editor.getElement().form.addEventListener('submit', function () {
                            tinymce.triggerSave();
                        });
                    }
                });
            }
        });
    }
});
</script>
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
                <li class="main-menu-item"><a href="gallery.php">Gallery</a></li>
            </ul>
        </nav>
        <form method="get" action="search.php" class="sitesearch-form">
            <input id="sitesearch" class="sitesearch-input" type="text" name="q" value="<?= htmlspecialchars($header_q) ?>" placeholder="Search">
            <select name="category_id" class="sitesearch-select">
                <option value="">All Categories</option>
                <?php foreach ($header_categories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>" <?= $header_category_id == $cat['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</header>
