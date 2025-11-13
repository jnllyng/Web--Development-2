<?php
require('includes/db_connect.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$type = $_GET['type'] ?? 'Plant';
$selectedFamily = $_GET['family'] ?? '';
$selectedTaxonomy = $_GET['taxonomy'] ?? '';
$selectedStatus = $_GET['status'] ?? '';
$searchName = $_GET['name'] ?? '';

$sortable_columns = ['category_id', 'type', 'family', 'taxonomy', 'scientific_name', 'common_name', 'status'];
$sort_by = in_array($_GET['sort_by'] ?? '', $sortable_columns) ? $_GET['sort_by'] : 'category_id';
$sort_order = ($_GET['sort_order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
$next_sort_order = $sort_order === 'asc' ? 'desc' : 'asc';

$conditions = ["type = ?"];
$params = [$type];

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
$stmt = $db->prepare("SELECT * FROM categories WHERE $where ORDER BY $sort_by $sort_order");
$stmt->execute($params);
$category_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

$taxonomy_list = $db->prepare("SELECT DISTINCT taxonomy FROM categories WHERE type = ? ORDER BY taxonomy ASC");
$taxonomy_list->execute([$type]);
$taxonomy_list = $taxonomy_list->fetchAll(PDO::FETCH_COLUMN);

$family_list = $db->prepare("SELECT DISTINCT family FROM categories WHERE type = ? ORDER BY family ASC");
$family_list->execute([$type]);
$family_list = $family_list->fetchAll(PDO::FETCH_COLUMN);

$status_list = $db->prepare("SELECT DISTINCT status FROM categories WHERE type = ? ORDER BY status ASC");
$status_list->execute([$type]);
$status_list = $status_list->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($type) ?> List</title>
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
                        <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                        <input type="hidden" name="family" value="<?= htmlspecialchars($selectedFamily) ?>">
                        <input type="hidden" name="taxonomy" value="<?= htmlspecialchars($selectedTaxonomy) ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($selectedStatus) ?>">
                        <input type="text" id="filter-name" name="name" value="<?= htmlspecialchars($searchName) ?>"
                            placeholder="Search name...">
                        <button type="submit" class="btn-search">Search</button>
                    </form>

                    <label>Family:</label>
                    <select
                        onchange="location='?type=<?= urlencode($type) ?>&family=' + this.value + '&taxonomy=<?= urlencode($selectedTaxonomy) ?>&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>'">
                        <option value="">-All-</option>
                        <?php foreach ($family_list as $fam): ?>
                            <option value="<?= htmlspecialchars($fam) ?>" <?= $selectedFamily == $fam ? 'selected' : '' ?>>
                                <?= htmlspecialchars($fam) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Taxonomy:</label>
                    <select
                        onchange="location='?type=<?= urlencode($type) ?>&family=<?= urlencode($selectedFamily) ?>&taxonomy=' + this.value + '&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>'">
                        <option value="">-All-</option>
                        <?php foreach ($taxonomy_list as $tax): ?>
                            <option value="<?= htmlspecialchars($tax) ?>" <?= $selectedTaxonomy == $tax ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tax) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Status:</label>
                    <select
                        onchange="location='?type=<?= urlencode($type) ?>&family=<?= urlencode($selectedFamily) ?>&taxonomy=<?= urlencode($selectedTaxonomy) ?>&status=' + this.value + '&name=<?= urlencode($searchName) ?>'">
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
                                <?php
                                $columns = [
                                    'category_id' => '#',
                                    'type' => 'Type',
                                    'family' => 'Family',
                                    'taxonomy' => 'Taxonomy',
                                    'scientific_name' => 'Scientific Name',
                                    'common_name' => 'Common Name',
                                    'status' => 'Status'
                                ];
                                foreach ($columns as $col => $label):
                                    $sorted_class = ($sort_by === $col) ? 'sorted-' . $sort_order : '';
                                    $url = "?type=" . urlencode($type) .
                                        "&family=" . urlencode($selectedFamily) .
                                        "&taxonomy=" . urlencode($selectedTaxonomy) .
                                        "&status=" . urlencode($selectedStatus) .
                                        "&name=" . urlencode($searchName) .
                                        "&sort_by=$col&sort_order=" . ($sort_by === $col && $sort_order === 'asc' ? 'desc' : 'asc');
                                    ?>
                                    <th class="sorting <?= $sorted_class ?>"
                                        data-sort-type="<?= $col === 'category_id' ? 'int' : 'string' ?>">
                                        <a href="<?= $url ?>">
                                            <div class="sortwrap"><?= $label ?></div>
                                        </a>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($category_list as $sp): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($sp['type']) ?></td>
                                    <td><?= htmlspecialchars($sp['family']) ?></td>
                                    <td><?= htmlspecialchars($sp['taxonomy']) ?></td>
                                    <td><?= htmlspecialchars($sp['scientific_name']) ?></td>
                                    <td><a
                                            href="species_details.php?id=<?= $sp['category_id'] ?>"><?= htmlspecialchars($sp['common_name']) ?></a>
                                    </td>
                                    <td><?= htmlspecialchars($sp['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (!$category_list): ?>
                                <tr>
                                    <td colspan="7">No species found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="species_create.php?type=<?= $type ?>" class="btn-add">+ Add New
                            <?= htmlspecialchars($type) ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php include('includes/footer.php'); ?>
</body>

</html>