<?php
require('includes/db_connect.php');
session_start();

$type = $_GET['type'] ?? 'Animal';
$selectedCategory = $_GET['category'] ?? '';
$selectedFamily = $_GET['family'] ?? '';
$selectedStatus = $_GET['status'] ?? '';
$searchName = $_GET['name'] ?? '';

$sortable_columns = ['species_id', 'type', 'category_name', 'family', 'scientific_name', 'common_name', 'status'];
$sort_by = in_array($_GET['sort_by'] ?? '', $sortable_columns) ? $_GET['sort_by'] : 'species_id';
$sort_order = ($_GET['sort_order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

$conditions = ["s.type = ?"];
$params = [$type];

if ($selectedFamily) {
    $conditions[] = "s.family = ?";
    $params[] = $selectedFamily;
}

if ($selectedStatus) {
    $conditions[] = "s.status = ?";
    $params[] = $selectedStatus;
}

if ($selectedCategory) {
    $conditions[] = "s.category_id = ?";
    $params[] = $selectedCategory;
}

if ($searchName) {
    $conditions[] = "(s.scientific_name LIKE ? OR s.common_name LIKE ?)";
    $params[] = "%$searchName%";
    $params[] = "%$searchName%";
}

$where = implode(" AND ", $conditions);

$stmt = $db->prepare("
    SELECT s.*, c.name AS category_name 
    FROM species s
    LEFT JOIN categories c ON s.category_id = c.category_id
    WHERE $where
    ORDER BY $sort_by $sort_order
");
$stmt->execute($params);
$species = $stmt->fetchAll(PDO::FETCH_ASSOC);

$category_stmt = $db->prepare("SELECT * FROM categories ORDER BY name ASC");
$category_stmt->execute();
$category_list = $category_stmt->fetchAll(PDO::FETCH_ASSOC);

$family_stmt = $db->prepare("SELECT DISTINCT family FROM species WHERE type = ? ORDER BY family ASC");
$family_stmt->execute([$type]);
$family_list = $family_stmt->fetchAll(PDO::FETCH_COLUMN);

$status_stmt = $db->prepare("SELECT DISTINCT status FROM species WHERE type = ? ORDER BY status ASC");
$status_stmt->execute([$type]);
$status_list = $status_stmt->fetchAll(PDO::FETCH_COLUMN);

$showEditColumn = false;
foreach ($species as $sp) {
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $sp['user_id']) {
        $showEditColumn = true;
        break;
    }
}
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
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="page-actions">
                <a href="species_create.php?type=<?= $type ?>" class="btn-action">
                    + Add New <?= htmlspecialchars($type) ?>
                </a>

                <a href="category_create_user.php" class="btn-action">
                    + Add New Category
                </a>
            </div>
        <?php endif; ?>

        <div class="grid-row">
            <div class="grid-col">

                <div class="table-filters">

                    <label for="filter-name">Name:</label>
                    <form method="GET" style="display:inline;">
                        <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                        <input type="hidden" name="category" value="<?= htmlspecialchars($selectedCategory) ?>">
                        <input type="hidden" name="family" value="<?= htmlspecialchars($selectedFamily) ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($selectedStatus) ?>">
                        <input type="text" id="filter-name" name="name" value="<?= htmlspecialchars($searchName) ?>"
                            placeholder="Search name...">
                        <button type="submit" class="btn-search">Search</button>
                    </form>
                    <label>Category:</label>
                    <select
                        onchange="location='?type=<?= urlencode($type) ?>&family=<?= urlencode($selectedFamily) ?>&status=<?= urlencode($selectedStatus) ?>&category=' + encodeURIComponent(this.value) + '&name=<?= urlencode($searchName) ?>'">
                        <option value="">-All-</option>
                        <?php foreach ($category_list as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>" <?= $selectedCategory == $cat['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label>Family:</label>
                    <select
                        onchange="location='?type=<?= urlencode($type) ?>&family=' + encodeURIComponent(this.value) + '&status=<?= urlencode($selectedStatus) ?>&category=<?= urlencode($selectedCategory) ?>&name=<?= urlencode($searchName) ?>'">
                        <option value="">-All-</option>
                        <?php foreach ($family_list as $fam): ?>
                            <option value="<?= htmlspecialchars($fam) ?>" <?= $selectedFamily == $fam ? 'selected' : '' ?>>
                                <?= htmlspecialchars($fam) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Status:</label>
                    <select
                        onchange="location='?type=<?= urlencode($type) ?>&family=<?= urlencode($selectedFamily) ?>&status=' + encodeURIComponent(this.value) + '&category=<?= urlencode($selectedCategory) ?>&name=<?= urlencode($searchName) ?>'">
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
                                    'species_id' => '#',
                                    'type' => 'Type',
                                    'category_name' => 'Category',
                                    'family' => 'Family',
                                    'scientific_name' => 'Scientific Name',
                                    'common_name' => 'Common Name',
                                    'status' => 'Status',
                                ];

                                if ($showEditColumn) {
                                    $columns['edit'] = 'Edit';
                                }

                                foreach ($columns as $col => $label):
                                    $sorted_class = ($sort_by === $col) ? 'sorted-' . $sort_order : '';
                                    if ($col === 'edit') {
                                        $url = '#';
                                    } else {
                                        $url = "?type=" . urlencode($type)
                                            . "&category=" . urlencode($selectedCategory)
                                            . "&family=" . urlencode($selectedFamily)
                                            . "&status=" . urlencode($selectedStatus)
                                            . "&name=" . urlencode($searchName)
                                            . "&sort_by=$col&sort_order=" . ($sort_by === $col && $sort_order === 'asc' ? 'desc' : 'asc');
                                    }
                                    ?>
                                    <th class="sorting <?= $sorted_class ?>">
                                        <?php if ($col === 'edit'): ?>
                                            <?= $label ?>
                                        <?php else: ?>
                                            <a href="<?= $url ?>">
                                                <div class="sortwrap"><?= $label ?></div>
                                            </a>
                                        <?php endif; ?>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($species as $sp): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($sp['type']) ?></td>
                                    <td><?= htmlspecialchars($sp['category_name'] ?: '—') ?></td>
                                    <td><?= htmlspecialchars($sp['family']) ?></td>
                                    <td><?= htmlspecialchars($sp['scientific_name']) ?></td>
                                    <td>
                                        <a href="species_details.php?id=<?= $sp['species_id'] ?>">
                                            <?= htmlspecialchars($sp['common_name']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($sp['status']) ?></td>

                                    <?php if ($showEditColumn): ?>
                                        <td>
                                            <?php if ($_SESSION['user_id'] == $sp['user_id']): ?>
                                                <a href="species_edit.php?id=<?= $sp['species_id'] ?>" class="edit-btn">Edit</a>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (!$species): ?>
                                <tr>
                                    <td colspan="<?= count($columns) ?>">No species found.</td>
                                </tr>
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