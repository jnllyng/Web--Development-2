<?php
require('includes/db_connect.php');
session_start();

$type = $_GET['type'] ?? '';
$selectedCategory = $_GET['category'] ?? '';
$selectedFamily = $_GET['family'] ?? '';
$selectedStatus = $_GET['status'] ?? '';
$searchName = $_GET['name'] ?? '';

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$photosPerPage = 20;
$offset = ($page - 1) * $photosPerPage;

$type_list = ['Animal', 'Plant'];

$category_stmt = $db->prepare("SELECT * FROM categories ORDER BY name ASC");
$category_stmt->execute();
$category_list = $category_stmt->fetchAll(PDO::FETCH_ASSOC);

$family_stmt = $db->prepare("SELECT DISTINCT family FROM species ORDER BY family ASC");
$family_stmt->execute();
$family_list = $family_stmt->fetchAll(PDO::FETCH_COLUMN);

$status_stmt = $db->prepare("SELECT DISTINCT status FROM species ORDER BY status ASC");
$status_stmt->execute();
$status_list = $status_stmt->fetchAll(PDO::FETCH_COLUMN);

$where = [];
$params = [];

if ($type) {
    $where[] = "s.type = ?";
    $params[] = $type;
}

if ($selectedCategory) {
    $where[] = "s.category_id = ?";
    $params[] = $selectedCategory;
}

if ($selectedFamily) {
    $where[] = "s.family = ?";
    $params[] = $selectedFamily;
}

if ($selectedStatus) {
    $where[] = "s.status = ?";
    $params[] = $selectedStatus;
}

if ($searchName) {
    $where[] = "(s.scientific_name LIKE ? OR s.common_name LIKE ?)";
    $params[] = "%$searchName%";
    $params[] = "%$searchName%";
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

$countStmt = $db->prepare("
    SELECT COUNT(*) 
    FROM photos p 
    JOIN species s ON p.species_id = s.species_id
    $where_sql
");
$countStmt->execute($params);
$totalPhotos = $countStmt->fetchColumn();
$totalPages = ceil($totalPhotos / $photosPerPage);

$stmt = $db->prepare("
    SELECT p.photo_id, p.photo_url, p.species_id,
           s.common_name, s.scientific_name, s.type, s.category_id
    FROM photos p
    JOIN species s ON p.species_id = s.species_id
    $where_sql
    ORDER BY p.upload_date DESC
    LIMIT $photosPerPage OFFSET $offset
");
$stmt->execute($params);
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pagesPerGroup = 10;
$currentGroup = ceil($page / $pagesPerGroup);
$totalGroups = ceil($totalPages / $pagesPerGroup);

$startPage = ($currentGroup - 1) * $pagesPerGroup + 1;
$endPage = min($currentGroup * $pagesPerGroup, $totalPages);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Species Photo Gallery</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
<?php include('includes/header.php'); ?>

<main id="main" class="main-content">

    <div class="gallery-filters">

        <label for="filter-name">Name:</label>
        <form method="GET" style="display:inline;">
            <input type="text" id="filter-name" name="name"
                   value="<?= htmlspecialchars($searchName) ?>" placeholder="Search name...">
            <button type="submit" class="btn-search">Search</button>
        </form>

        <label>Type:</label>
        <select onchange="location='?type=' + this.value + '&category=<?= urlencode($selectedCategory) ?>&family=<?= urlencode($selectedFamily) ?>&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>&page=1'">
            <option value="">-All-</option>
            <?php foreach ($type_list as $t): ?>
                <option value="<?= htmlspecialchars($t) ?>" <?= $type == $t ? 'selected' : '' ?>>
                    <?= htmlspecialchars($t) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Category:</label>
        <select onchange="location='?type=<?= urlencode($type) ?>&category=' + this.value + '&family=<?= urlencode($selectedFamily) ?>&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>&page=1'">
            <option value="">-All-</option>
            <?php foreach ($category_list as $cat): ?>
                <option value="<?= $cat['category_id'] ?>" <?= $selectedCategory == $cat['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Family:</label>
        <select onchange="location='?type=<?= urlencode($type) ?>&family=' + this.value + '&category=<?= urlencode($selectedCategory) ?>&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>&page=1'">
            <option value="">-All-</option>
            <?php foreach ($family_list as $fam): ?>
                <option value="<?= htmlspecialchars($fam) ?>" <?= $selectedFamily == $fam ? 'selected' : '' ?>>
                    <?= htmlspecialchars($fam) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Status:</label>
        <select onchange="location='?type=<?= urlencode($type) ?>&family=<?= urlencode($selectedFamily) ?>&category=<?= urlencode($selectedCategory) ?>&status=' + this.value + '&name=<?= urlencode($searchName) ?>&page=1'">
            <option value="">-All-</option>
            <?php foreach ($status_list as $st): ?>
                <option value="<?= htmlspecialchars($st) ?>" <?= $selectedStatus == $st ? 'selected' : '' ?>>
                    <?= htmlspecialchars($st) ?>
                </option>
            <?php endforeach; ?>
        </select>

    </div>

    <section class="highlights">
        <div class="gallery-container">

            <?php if ($photos): ?>
                <?php foreach ($photos as $photo): ?>
                    <div class="photo-card">
                        <a href="species_details.php?id=<?= $photo['species_id'] ?>">
                            <div class="image-wrapper">
                                <img src="<?= htmlspecialchars($photo['photo_url']) ?>" 
                                     alt="<?= htmlspecialchars($photo['common_name'] ?: $photo['scientific_name']) ?>" 
                                     class="species_photo">
                            </div>
                        </a>
                        <div class="info">
                            <h3><?= htmlspecialchars($photo['common_name'] ?: $photo['scientific_name']) ?></h3>
                            <p><?= htmlspecialchars($photo['scientific_name']) ?> | <?= htmlspecialchars($photo['type']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No photos found.</p>
            <?php endif; ?>

        </div>
    </section>

    <div class="pagination">

        <?php if ($currentGroup > 1): ?>
            <a href="?page=<?= $startPage - 1 ?>&type=<?= urlencode($type) ?>&category=<?= urlencode($selectedCategory) ?>&family=<?= urlencode($selectedFamily) ?>&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>">Prev</a>
        <?php endif; ?>

        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <a href="?page=<?= $i ?>&type=<?= urlencode($type) ?>&category=<?= urlencode($selectedCategory) ?>&family=<?= urlencode($selectedFamily) ?>&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>"
               class="<?= $page == $i ? 'active' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($currentGroup < $totalGroups): ?>
            <a href="?page=<?= $endPage + 1 ?>&type=<?= urlencode($type) ?>&category=<?= urlencode($selectedCategory) ?>&family=<?= urlencode($selectedFamily) ?>&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>">Next</a>
        <?php endif; ?>

    </div>
</main>

<?php include('includes/footer.php'); ?>
</body>
</html>
