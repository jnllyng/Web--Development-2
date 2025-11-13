<?php
session_start();
require('includes/db_connect.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manitoba Nature Archive</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <?php include('includes/header.php'); ?>

    <main id="main" class="main-content">
        <section class="hero">
            <div class="grid-container">
                <h1>Welcome to the Manitoba Nature!</h1>
                <p>Our mission is to document and share information about the native plants and
                    animals of Manitoba.</p>
            </div>
        </section>

        <section class="highlights">
            <div class="grid-container">
                <h2>Featured Species</h2>
                <div class="species-grid">
                    <article class="species-card">
                        <img src="uploads/species/Arctic Fox.webp" alt="Sample Animal">
                        <h3>Arctic Fox</h3>
                        <p>Vulpes lagopus</p>
                        <a href="species_details.php?id=531">Learn More</a>
                    </article>
                    <article class="species-card">
                        <img src="uploads/species/Rose.jpg" alt="Sample Plant">
                        <h3>Rose</h3>
                        <p>Rosa x dulcissima</p>
                        <a href="species_details.php?id=3765">Learn More</a>
                    </article>
                </div>
            </div>
        </section>

        <section class="about">
            <div class="grid-container">
                <h2>About Manitoba Nature Archive</h2>
                <p>
                    Manitoba Nature Archive is a regional not-for-profit organization dedicated to documenting
                    information about native species. We provide educational resources to the public through our content
                    management system.
                </p>
            </div>
        </section>
    </main>

    <?php include('includes/footer.php'); ?>
</body>

</html>