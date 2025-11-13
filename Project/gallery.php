<?php
require('includes/db_connect.php');
session_start();


$type = $_GET['type'] ?? '';
$selectedFamily = $_GET['family'] ?? '';
$selectedTaxonomy = $_GET['taxonomy'] ?? '';
$selectedStatus = $_GET['status'] ?? '';
$searchName = $_GET['name'] ?? '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$photosPerPage = 20;
$offset = ($page - 1) * $photosPerPage;

$type_list = ['Animal', 'Plant']; 
$family_list = $db->query("SELECT DISTINCT family FROM categories ORDER BY family ASC")->fetchAll(PDO::FETCH_COLUMN);
$taxonomy_list = $db->query("SELECT DISTINCT taxonomy FROM categories ORDER BY taxonomy ASC")->fetchAll(PDO::FETCH_COLUMN);
$status_list = $db->query("SELECT DISTINCT status FROM categories ORDER BY status ASC")->fetchAll(PDO::FETCH_COLUMN);

$where = [];
$params = [];

if ($type) {
    $where[] = "c.type = ?";
    $params[] = $type;
}
if ($selectedFamily) {
    $where[] = "c.family = ?";
    $params[] = $selectedFamily;
}
if ($selectedTaxonomy) {
    $where[] = "c.taxonomy = ?";
    $params[] = $selectedTaxonomy;
}
if ($selectedStatus) {
    $where[] = "c.status = ?";
    $params[] = $selectedStatus;
}
if ($searchName) {
    $where[] = "(c.scientific_name LIKE ? OR c.common_name LIKE ?)";
    $params[] = "%$searchName%";
    $params[] = "%$searchName%";
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

$countStmt = $db->prepare("SELECT COUNT(*) FROM photos p JOIN categories c ON p.category_id = c.category_id $where_sql");
$countStmt->execute($params);
$totalPhotos = $countStmt->fetchColumn();
$totalPages = ceil($totalPhotos / $photosPerPage);
$stmt = $db->prepare("
    SELECT p.photo_id, p.photo_url, c.category_id, c.scientific_name, c.common_name, c.type
    FROM photos p
    JOIN categories c ON p.category_id = c.category_id
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
                <input type="text" id="filter-name" name="name" value="<?= htmlspecialchars($searchName) ?>"
                    placeholder="Search name...">
                <button type="submit" class="btn-search">Search</button>
            </form>

            <label>Family:</label>
            <select
                onchange="location='?type=<?= urlencode($type) ?>&family=' + this.value + '&taxonomy=<?= urlencode($selectedTaxonomy) ?>&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>&page=1'">
                <option value="">-All-</option>
                <?php foreach ($family_list as $fam): ?>
                    <option value="<?= htmlspecialchars($fam) ?>" <?= $selectedFamily == $fam ? 'selected' : '' ?>>
                        <?= htmlspecialchars($fam) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Taxonomy:</label>
            <select
                onchange="location='?type=<?= urlencode($type) ?>&family=<?= urlencode($selectedFamily) ?>&taxonomy=' + this.value + '&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>&page=1'">
                <option value="">-All-</option>
                <?php foreach ($taxonomy_list as $tax): ?>
                    <option value="<?= htmlspecialchars($tax) ?>" <?= $selectedTaxonomy == $tax ? 'selected' : '' ?>>
                        <?= htmlspecialchars($tax) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Status:</label>
            <select
                onchange="location='?type=<?= urlencode($type) ?>&family=<?= urlencode($selectedFamily) ?>&taxonomy=<?= urlencode($selectedTaxonomy) ?>&status=' + this.value + '&name=<?= urlencode($searchName) ?>&page=1'">
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
                            <a href="species_details.php?id=<?= $photo['category_id'] ?>">
                                <div class="image-wrapper">
                                    <img src="<?= htmlspecialchars($photo['photo_url']) ?>"
                                        alt="<?= htmlspecialchars($photo['common_name'] ?: $photo['scientific_name']) ?>"
                                        class="species_photo">
                                </div>
                            </a>
                            <div class="info">
                                <h3><?= htmlspecialchars($photo['common_name'] ?: $photo['scientific_name']) ?></h3>
                                <p><?= htmlspecialchars($photo['scientific_name']) ?> |
                                    <?= htmlspecialchars($photo['type']) ?>
                                </p>
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
                <a
                    href="?page=<?= $startPage - 1 ?>&type=<?= urlencode($type) ?>&family=<?= urlencode($selectedFamily) ?>&taxonomy=<?= urlencode($selectedTaxonomy) ?>&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>">Prev</a>
            <?php endif; ?>
            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <a href="?page=<?= $i ?>&type=<?= urlencode($type) ?>&family=<?= urlencode($selectedFamily) ?>&taxonomy=<?= urlencode($selectedTaxonomy) ?>&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>"
                    class="<?= $page == $i ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
            <?php if ($currentGroup < $totalGroups): ?>
                <a
                    href="?page=<?= $endPage + 1 ?>&type=<?= urlencode($type) ?>&family=<?= urlencode($selectedFamily) ?>&taxonomy=<?= urlencode($selectedTaxonomy) ?>&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>">Next</a>
            <?php endif; ?>
        </div>
    </main>

    <?php include('includes/footer.php'); ?>
</body>

</html>