<?php
session_start();
require('includes/db_connect.php');

$species_id = $_GET['id'] ?? null;
if (!$species_id) {
    echo "Invalid species ID.";
    exit;
}

$stmt = $db->prepare("SELECT * FROM species WHERE species_id = ?");
$stmt->execute([$species_id]);
$species = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$species) {
    echo "Species not found.";
    exit;
}

$search_query = $species['common_name'] ?: $species['scientific_name'];
$api_url = "https://en.wikipedia.org/w/api.php?action=query&list=search&srsearch=" . urlencode($search_query) . "&utf8=&format=json";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
$response = curl_exec($ch);
curl_close($ch);

$wiki_page = null;
if ($response) {
    $data = json_decode($response, true);
    if (!empty($data['query']['search'][0]['title'])) {
        $wiki_page = $data['query']['search'][0]['title'];
    }
}
$wiki_url = $wiki_page ? "https://en.wikipedia.org/wiki/" . str_replace(' ', '_', $wiki_page) : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($species['common_name'] ?: $species['scientific_name']) ?> Details</title>
</head>
<body>

<?php include('includes/header.php'); ?>

<main class="main-content">
    <div class="grid-container">
        <h1><?= htmlspecialchars($species['common_name'] ?: $species['scientific_name']) ?></h1>
        <h2>About this Species</h2>

        <?php if ($wiki_url): ?>
            <iframe src="<?= $wiki_url ?>" width="100%" height="600px" style="border:1px solid #ccc;"></iframe>
        <?php else: ?>
            <p><em>No Wikipedia page found for this species.</em></p>
        <?php endif; ?>
    </div>
</main>

<?php include('includes/footer.php'); ?>

</body>
</html>
