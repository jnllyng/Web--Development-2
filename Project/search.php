<?php
session_start();
require('includes/db_connect.php');

$q = trim($_GET['q'] ?? '');
$category_id = $_GET['category_id'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 20; // adjust N as needed
$offset = ($page - 1) * $per_page;

// fetch categories for dropdown
$cat_stmt = $db->query("SELECT category_id, name FROM categories ORDER BY name ASC");
$categories = $cat_stmt->fetchAll(PDO::FETCH_ASSOC);

$conditions = [];
$params = [];

if ($q !== '') {
    $conditions[] = "(s.common_name LIKE ? OR s.scientific_name LIKE ? OR s.family LIKE ?)";
    $like = "%" . $q . "%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if ($category_id !== '') {
    $conditions[] = "s.category_id = ?";
    $params[] = $category_id;
}

$where = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

// total count for pagination
$count_stmt = $db->prepare("
    SELECT COUNT(*) 
    FROM species s
    LEFT JOIN categories c ON s.category_id = c.category_id
    $where
");
$count_stmt->execute($params);
$total_results = (int) $count_stmt->fetchColumn();
$total_pages = $total_results ? (int) ceil($total_results / $per_page) : 1;
$block_size = 10;
$block_start = (int) (floor(($page - 1) / $block_size) * $block_size) + 1;
$block_end = min($block_start + $block_size - 1, $total_pages);
$prev_block_page = $block_start > 1 ? $block_start - 1 : null;
$next_block_page = $block_end < $total_pages ? $block_end + 1 : null;

// fetch paginated results
$query_stmt = $db->prepare("
    SELECT s.species_id, s.common_name, s.scientific_name, s.family, s.status, c.name AS category_name
    FROM species s
    LEFT JOIN categories c ON s.category_id = c.category_id
    $where
    ORDER BY s.common_name ASC
    LIMIT $per_page OFFSET $offset
");
$query_stmt->execute($params);
$results = $query_stmt->fetchAll(PDO::FETCH_ASSOC);

function build_page_link($page, $q, $category_id) {
    $query = http_build_query([
        'q' => $q,
        'category_id' => $category_id,
        'page' => $page
    ]);
    return "search.php?$query";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php include('includes/header.php'); ?>

<main id="main" class="main-content">
    <div class="grid-container">
        <h1>Search</h1>
        <form method="get" action="search.php" class="sitesearch-form" style="margin-bottom:20px;">
            <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search keyword(s)" required>
            <select name="category_id">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>" <?= $category_id == $cat['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Search</button>
        </form>

        <?php if ($total_results === 0): ?>
            <p>No results found.</p>
        <?php else: ?>
            <p><?= $total_results ?> result<?= $total_results === 1 ? '' : 's' ?> found.</p>
            <div class="resp-scroll">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Common Name</th>
                            <th>Scientific Name</th>
                            <th>Family</th>
                            <th>Status</th>
                            <th>Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $row): ?>
                            <tr>
                                <td>
                                    <a href="species_details.php?id=<?= $row['species_id'] ?>">
                                        <?= htmlspecialchars($row['common_name']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($row['scientific_name']) ?></td>
                                <td><?= htmlspecialchars($row['family']) ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><?= htmlspecialchars($row['category_name'] ?? 'Uncategorized') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($prev_block_page): ?>
                        <a href="<?= build_page_link($prev_block_page, $q, $category_id) ?>">&laquo; Prev</a>
                    <?php endif; ?>

                    <?php for ($p = $block_start; $p <= $block_end; $p++): ?>
                        <?php if ($p == $page): ?>
                            <strong><?= $p ?></strong>
                        <?php else: ?>
                            <a href="<?= build_page_link($p, $q, $category_id) ?>"><?= $p ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($next_block_page): ?>
                        <a href="<?= build_page_link($next_block_page, $q, $category_id) ?>">Next &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php include('includes/footer.php'); ?>
</body>
</html>
