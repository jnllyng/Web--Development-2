<?php
include('includes/db_connect.php');

$category = $_GET['category'] ?? 'Animal';
$selectedFamily = $_GET['family'] ?? '';
$selectedTaxonomy = $_GET['taxonomy'] ?? '';
$selectedStatus = $_GET['status'] ?? '';
$searchName = $_GET['name'] ?? ''; 

$conditions = ["category = ?"];
$params = [$category];

if ($selectedFamily) {
    $conditions[] = "family = ?";
    $params[] = $selectedFamily;
}
if ($selectedTaxonomy) {
    $conditions[] = "taxonomy = ?";
    $params[] = $selectedTaxonomy;
}
if ($selectedStatus) {
    $conditions[] = "status = ?";
    $params[] = $selectedStatus;
}
if ($searchName) {
    $conditions[] = "(scientific_name LIKE ? OR common_name LIKE ?)";
    $params[] = "%$searchName%";
    $params[] = "%$searchName%";
}

$where = implode(" AND ", $conditions);
$stmt = $db->prepare("SELECT * FROM species WHERE $where ORDER BY species_id ASC");
$stmt->execute($params);
$species_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$taxonomy_list = $db->prepare("SELECT DISTINCT taxonomy FROM species WHERE category = ? ORDER BY taxonomy ASC");
$taxonomy_list->execute([$category]);
$taxonomy_list = $taxonomy_list->fetchAll(PDO::FETCH_COLUMN);

$family_list = $db->prepare("SELECT DISTINCT family FROM species WHERE category = ? ORDER BY family ASC");
$family_list->execute([$category]);
$family_list = $family_list->fetchAll(PDO::FETCH_COLUMN);

$status_list = $db->prepare("SELECT DISTINCT status FROM species WHERE category = ? ORDER BY status ASC");
$status_list->execute([$category]);
$status_list = $status_list->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category) ?> List</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include('includes/header.php'); ?>

<main id="main" class="main-content">
    <div class="grid-row">
        <div class="grid-col">

            <div class="table-filters">
                <label for="filter-name">Name:</label>
                <form method="GET" style="display:inline;">
                    <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                    <input type="hidden" name="family" value="<?= htmlspecialchars($selectedFamily) ?>">
                    <input type="hidden" name="taxonomy" value="<?= htmlspecialchars($selectedTaxonomy) ?>">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($selectedStatus) ?>">
                    <input type="text" id="filter-name" name="name" value="<?= htmlspecialchars($searchName) ?>" placeholder="Search name...">
                    <button type="submit" class="btn-search">Search</button>
                </form>
                <label>Family:</label>
                <select onchange="location='?category=<?= urlencode($category) ?>&family=' + this.value + '&taxonomy=<?= urlencode($selectedTaxonomy) ?>&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>'">
                    <option value="">-All-</option>
                    <?php foreach ($family_list as $fam): ?>
                        <option value="<?= htmlspecialchars($fam) ?>" <?= $selectedFamily == $fam ? 'selected' : '' ?>>
                            <?= htmlspecialchars($fam) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Taxonomy:</label>
                <select onchange="location='?category=<?= urlencode($category) ?>&family=<?= urlencode($selectedFamily) ?>&taxonomy=' + this.value + '&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>'">
                    <option value="">-All-</option>
                    <?php foreach ($taxonomy_list as $tax): ?>
                        <option value="<?= htmlspecialchars($tax) ?>" <?= $selectedTaxonomy == $tax ? 'selected' : '' ?>>
                            <?= htmlspecialchars($tax) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label>Status:</label>
                <select onchange="location='?category=<?= urlencode($category) ?>&family=<?= urlencode($selectedFamily) ?>&taxonomy=<?= urlencode($selectedTaxonomy) ?>&status=' + this.value + '&name=<?= urlencode($searchName) ?>'">
                    <option value="">-All-</option>
                    <?php foreach ($status_list as $st): ?>
                        <option value="<?= htmlspecialchars($st) ?>" <?= $selectedStatus == $st ? 'selected' : '' ?>>
                            <?= htmlspecialchars($st) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="resp-scroll">
                <table class="data-table sticky-header block-wide">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Family</th>
                            <th>Taxonomy</th>
                            <th>Scientific Name</th>
                            <th>Common Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($species_list as $sp): ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= htmlspecialchars($sp['category']) ?></td>
                                <td><?= htmlspecialchars($sp['family']) ?></td>
                                <td><?= htmlspecialchars($sp['taxonomy']) ?></td>
                                <td><?= htmlspecialchars($sp['scientific_name']) ?></td>
                                <td><?= htmlspecialchars($sp['common_name']) ?></td>
                                <td><?= htmlspecialchars($sp['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$species_list): ?>
                            <tr><td colspan="7">No species found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</main>

<?php include('includes/footer.php'); ?>
</body>
</html>
