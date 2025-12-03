<?php
session_start();
require('includes/db_connect.php');

$searchName = $_GET['name'] ?? '';
$filterType = $_GET['type'] ?? '';

$sortable_columns = ['category_id', 'name', 'type'];
$sort_by = in_array($_GET['sort_by'] ?? '', $sortable_columns) ? $_GET['sort_by'] : 'category_id';
$sort_order = ($_GET['sort_order'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

$conditions = [];
$params = [];

if ($searchName !== '') {
    $conditions[] = "c.name LIKE ?";
    $params[] = "%" . $searchName . "%";
}

// type filter
if ($filterType !== '') {
    $conditions[] = "EXISTS (
        SELECT 1 FROM species s 
        WHERE s.category_id = c.category_id
          AND s.type = ?
    )";
    $params[] = $filterType;
}

$where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

// Main Query
$stmt = $db->prepare("
    SELECT 
        c.category_id,
        c.name,

        (SELECT s.type FROM species s 
         WHERE s.category_id = c.category_id 
         LIMIT 1) AS type

    FROM categories c
    $where
    ORDER BY $sort_by $sort_order
");
$stmt->execute($params);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$logged_in = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Categories</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

    <?php include('includes/header.php'); ?>

    <main id="main" class="main-content">
        <div class="grid-row">
            <div class="grid-col">

                <h1>Categories</h1>

                <div class="table-filters">

                    <label for="filter-name">Name:</label>
                    <form method="GET" style="display:inline;">
                        <input type="text" id="filter-name" name="name" value="<?= htmlspecialchars($searchName) ?>"
                            placeholder="Search category name...">
                        <button type="submit" class="btn-search">Search</button>
                        <label for="filter-type" style="margin-left:20px;">Type:</label>
                        <select name="type" id="filter-type" onchange="this.form.submit()">
                            <option value="">- All -</option>
                            <option value="Animal" <?= $filterType === "Animal" ? "selected" : "" ?>>Animal</option>
                            <option value="Plant" <?= $filterType === "Plant" ? "selected" : "" ?>>Plant</option>
                        </select>
                    </form>

                    <?php if ($logged_in): ?>
                        <a href="category_create_user.php" class="btn-action" style="margin-left:20px;">
                            + Add Category
                        </a>
                    <?php endif; ?>

                </div>

                <div class="resp-scroll">
                    <table class="data-table sticky-header">
                        <thead>
                            <tr>
                                <?php
                                $columns = [
                                    'category_id' => '#',
                                    'name' => 'Category',
                                    'type' => 'Type'
                                ];

                                if ($logged_in) {
                                    $columns['edit'] = 'Manage';
                                }

                                foreach ($columns as $col => $label):
                                    $sorted_class = ($sort_by === $col) ? 'sorted-' . $sort_order : '';

                                    if ($col === 'edit') {
                                        $url = '#';
                                    } else {
                                        $url = "?name=" . urlencode($searchName)
                                            . "&type=" . urlencode($filterType)
                                            . "&sort_by=$col&sort_order="
                                            . ($sort_by === $col && $sort_order === 'asc' ? 'desc' : 'asc');
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
                            <?php foreach ($categories as $c): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <a href="category_detail.php?id=<?= $c['category_id'] ?>">
                                            <?= htmlspecialchars($c['name']) ?>
                                        </a>
                                    </td>
                                    <td><?= $c['type'] ? htmlspecialchars($c['type']) : '—' ?></td> 
                                    <?php if ($logged_in): ?>
                                        <td>
                                            <a class="edit-btn"
                                                href="category_edit_user.php?id=<?= $c['category_id'] ?>">Edit</a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (!$categories): ?>
                                <tr>
                                    <td colspan="<?= count($columns) ?>">No categories found.</td>
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