<?php
session_start();
require('includes/db_connect.php');

$category_id = $_GET['id'] ?? null;
if (!$category_id || !ctype_digit((string)$category_id)) {
    header("Location: category_list_user.php");
    exit;
}

$stmt = $db->prepare("SELECT * FROM categories WHERE category_id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    echo "Category not found.";
    exit;
}

$selectedFamily = $_GET['family'] ?? '';
$selectedStatus = $_GET['status'] ?? '';
$searchName = $_GET['name'] ?? '';

$sortable_columns = ['species_id', 'family', 'scientific_name', 'common_name', 'status'];
$sort_by = in_array($_GET['sort_by'] ?? '', $sortable_columns) ? $_GET['sort_by'] : 'species_id';
$sort_order = ($_GET['sort_order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

$conditions = ["category_id = ?"];
$params = [$category_id];

if ($selectedFamily !== '') {
    $conditions[] = "family = ?";
    $params[] = $selectedFamily;
}

if ($selectedStatus !== '') {
    $conditions[] = "status = ?";
    $params[] = $selectedStatus;
}

if ($searchName !== '') {
    $conditions[] = "(scientific_name LIKE ? OR common_name LIKE ?)";
    $params[] = "%$searchName%";
    $params[] = "%$searchName%";
}

$where = implode(" AND ", $conditions);
$stmt2 = $db->prepare("
    SELECT *
    FROM species
    WHERE $where
    ORDER BY $sort_by $sort_order
");
$stmt2->execute($params);
$species_list = $stmt2->fetchAll(PDO::FETCH_ASSOC);
$family_stmt = $db->prepare("
    SELECT DISTINCT family 
    FROM species 
    WHERE category_id = ?
    ORDER BY family ASC
");
$family_stmt->execute([$category_id]);
$family_list = $family_stmt->fetchAll(PDO::FETCH_COLUMN);

$status_stmt = $db->prepare("
    SELECT DISTINCT status 
    FROM species 
    WHERE category_id = ?
    ORDER BY status ASC
");
$status_stmt->execute([$category_id]);
$status_list = $status_stmt->fetchAll(PDO::FETCH_COLUMN);

$logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($category['name']) ?></title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <?php include('includes/header.php'); ?>

    <main id="main" class="main-content">

        <div class="grid-row">
            <div class="grid-col">

                <h1><?= htmlspecialchars($category['name']) ?></h1>
                <div class="table-filters">

                    <label for="filter-name">Name:</label>
                    <form method="GET" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $category_id ?>">
                        <input type="hidden" name="family" value="<?= htmlspecialchars($selectedFamily) ?>">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($selectedStatus) ?>">
                        <input type="text" id="filter-name" name="name" value="<?= htmlspecialchars($searchName) ?>"
                            placeholder="Search name...">
                        <button type="submit" class="btn-search">Search</button>
                    </form>

                    <label>Family:</label>
                    <select
                        onchange="location='?id=<?= $category_id ?>&family=' + encodeURIComponent(this.value) + '&status=<?= urlencode($selectedStatus) ?>&name=<?= urlencode($searchName) ?>'">
                        <option value="">-All-</option>
                        <?php foreach ($family_list as $fam): ?>
                            <option value="<?= htmlspecialchars($fam) ?>" <?= $selectedFamily == $fam ? 'selected' : '' ?>>
                                <?= htmlspecialchars($fam) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label>Status:</label>
                    <select
                        onchange="location='?id=<?= $category_id ?>&family=<?= urlencode($selectedFamily) ?>&status=' + encodeURIComponent(this.value) + '&name=<?= urlencode($searchName) ?>'">
                        <option value="">-All-</option>
                        <?php foreach ($status_list as $st): ?>
                            <option value="<?= htmlspecialchars($st) ?>" <?= $selectedStatus == $st ? 'selected' : '' ?>>
                                <?= htmlspecialchars($st) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if ($logged_in): ?>
                        <a href="species_create.php?category_id=<?= $category_id ?>" class="btn-action">+ Add Species</a>
                    <?php endif; ?>
                </div>
                <div class="resp-scroll">
                    <table class="data-table sticky-header block-wide">
                        <thead>
                            <tr>
                                <?php
                                $columns = [
                                    'species_id' => '#',
                                    'family' => 'Family',
                                    'scientific_name' => 'Scientific Name',
                                    'common_name' => 'Common Name',
                                    'status' => 'Status',
                                ];

                                if ($logged_in) {
                                    $columns['edit'] = 'Edit';
                                }

                                foreach ($columns as $col => $label):
                                    $sorted_class = ($sort_by === $col) ? 'sorted-' . $sort_order : '';

                                    if ($col === 'edit') {
                                        $url = '#';
                                    } else {
                                        $url = "?id=$category_id"
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
                            <?php foreach ($species_list as $sp): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td><?= htmlspecialchars($sp['family']) ?></td>
                                    <td><?= htmlspecialchars($sp['scientific_name']) ?></td>
                                    <td>
                                        <a href="species_details.php?id=<?= $sp['species_id'] ?>">
                                            <?= htmlspecialchars($sp['common_name']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($sp['status']) ?></td>

                                    <?php if ($logged_in): ?>
                                        <td>
                                            <a href="species_edit.php?id=<?= $sp['species_id'] ?>" class="edit-btn">Edit</a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (!$species_list): ?>
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
