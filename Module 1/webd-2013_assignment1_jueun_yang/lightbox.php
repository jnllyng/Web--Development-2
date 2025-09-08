<?php

/*******w******** 

    Name: Jueun Yang
    Date: 2025-08-28
    Description: Use a lightbox gallery using luminous javascript library 
                 to display full-size images by clicking on image thumbnails.

****************/

$config = [
    'gallery_name' => 'Elly\'s Gallery',
    'local_images' => [
        ['image_path' => 'images/image_1.jpg', 
        'photographer_name' => 'Nadine Marfurt',
        'thumbnail' => 'images/image_1_thumbnail.jpg'],
        ['image_path' => 'images/image_2.jpg', 
        'photographer_name' => 'Andreas M',
        'thumbnail' => 'images/image_2_thumbnail.jpg'],
        ['image_path' => 'images/image_3.jpg', 
        'photographer_name' => 'Tyler Duston',
        'thumbnail' => 'images/image_3_thumbnail.jpg'],
        ['image_path' => 'images/image_4.jpg', 
        'photographer_name' => 'Jamie Davies',
        'thumbnail' => 'images/image_4_thumbnail.jpg'],
        ['image_path' => 'images/image_5.jpg', 
        'photographer_name' => 'Andy He',
        'thumbnail' => 'images/image_5_thumbnail.jpg'],
        ['image_path' => 'images/image_6.jpg', 
        'photographer_name' => 'Etienne Girardet',
        'thumbnail' => 'images/image_6_thumbnail.jpg'],
        ['image_path' => 'images/image_7.jpg', 
        'photographer_name' => 'Luca Bravo',
        'thumbnail' => 'images/image_7_thumbnail.jpg'],
        ['image_path' => 'images/image_8.jpg', 
        'photographer_name' => 'Toni Cuenca',
        'thumbnail' => 'images/image_8_thumbnail.jpg']
    ]
];
 
$name = $config['gallery_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $name; ?></title>
    <link href="https://fonts.googleapis.com/css?family=Alegreya" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/luminous-lightbox/2.0.0/luminous-basic.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/luminous-lightbox/2.0.0/Luminous.min.js"></script>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <h1><?= $name; ?></h1>
    <div id="gallery">
        <?php foreach ($config['local_images'] as $image): ?>
            <div class="image">
                <h2><?= $image['photographer_name']; ?></h2>
                <a href="<?= $image['image_path']; ?>">
                <img src="<?= $image['thumbnail'] ?>" alt="<?= $image['photographer_name'] ?>">
            </a>
            </div>
        <?php endforeach ?>
    </div>
    <script>
        new LuminousGallery(document.querySelectorAll(".image a"));
    </script>
</body>
</html>