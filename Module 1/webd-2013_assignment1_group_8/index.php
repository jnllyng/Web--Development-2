<?php

/*******w******** 

    Name: Group 8 - Jueun Yang, Gurshan Singh Sekhon, Princepal Singh
    Date: 2025-09-09
    Description: Updates $config data with gallery name, filenames of the images,
                 Displays thoses images in the page with photographer names and links.

****************/

$config = [

    'gallery_name' => 'Group 8\'s Gallery',

    'unsplash_categories' => [
        [
            'category' => 'Water',
            'link' => 'https://source.unsplash.com/300x200/?water'
        ],
        [
            'category' => 'Urban',
            'link' => 'https://source.unsplash.com/300x200/?urban'
        ],
        [
            'category' => 'Nature',
            'link' => 'https://source.unsplash.com/300x200/?nature'
        ],
        [
            'category' => 'Candy',
            'link' => 'https://source.unsplash.com/300x200/?candy'
        ],
        [
            'category' => 'Bears',
            'link' => 'https://source.unsplash.com/300x200/?bears'
        ],
        [
            'category' => 'Beer',
            'link' => 'https://source.unsplash.com/300x200/?beer'
        ],
        [
            'category' => 'Food',
            'link' => 'https://source.unsplash.com/300x200/?food'
        ],
        [
            'category' => 'Graffiti',
            'link' => 'https://source.unsplash.com/300x200/?graffiti'
        ]
    ],

    'local_images' => [
        [
            'image_path' => 'images/image_1.jpg',
            'photographer_name' => 'Ján Jakub Naništa',
            'unsplash_link' => 'https://unsplash.com/@janjakubnanista'
        ],
        [
            'image_path' => 'images/image_2.jpg',
            'photographer_name' => 'Wengang Zhai',
            'unsplash_link' => 'https://unsplash.com/@wgzhai'
        ],
        [
            'image_path' => 'images/image_3.jpg',
            'photographer_name' => 'Nick Fewings',
            'unsplash_link' => 'https://unsplash.com/@jannerboy62'
        ],
        [
            'image_path' => 'images/image_4.jpg',
            'photographer_name' => 'Viktor Talashuk',
            'unsplash_link' => 'https://unsplash.com/@viktortalashuk'
        ]
    ]

];

$name = $config['gallery_name'];
$count = count($config['local_images']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Alegreya" rel="stylesheet">
    <link rel="stylesheet" href="main.css">
    <title><?= $name; ?></title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
    <h1><?= $name; ?></h1>
    <div id="gallery">
        <?php foreach ($config['unsplash_categories'] as $gallery): ?>
            <div>
                <h2><?= $gallery['category']; ?></h2>
                <img src="<?= $gallery['link'] ?>" alt="<?= $gallery['category'] ?>">
            </div>
        <?php endforeach ?>
    </div>
    <h1><?= $count; ?> Large Images</h1>
    <div id="large-images">
        <?php foreach ($config['local_images'] as $image): ?>
            <img src="<?= $image['image_path'] ?>" alt="<?= $image['photographer_name'] ?>">
            <h3 class="photographer"><a href="<?= $image['unsplash_link'] ?>"><?= $image['photographer_name'] ?></a></h3>
        <?php endforeach ?>
    </div>
</body>
</html>