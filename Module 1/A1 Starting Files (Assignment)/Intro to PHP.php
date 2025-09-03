<?php

/*******w******** 
    
    Name: Jueun Yang
    Date: 2025-08-28
    Description:

****************/

$config = [

    'gallery_name' => 'Elly\'s Gallery',
 
    'unsplash_categories' => ['array','of','category','keywords'],
 
    'local_images' => [ [
            'image_path' => 'images/image 1.jpg',
            'photographer_name' => 'Samuel Larocque',
            'unsplash_link' => 'https://unsplash.com/@samuellarocque'
        ],
        [
            'image_path' => 'images/image 2.jpg',
            'photographer_name' => 'Gus',
            'unsplash_link' => 'https://unsplash.com/@vrid_is'
        ],
        [
            'image_path' => 'images/image 3.jpg',
            'photographer_name' => 'Edgar',
            'unsplash_link' => 'https://unsplash.com/@snapshot_journey'
        ],
        [
            'image_path' => 'images/image 4.jpg',
            'photographer_name' => 'Matthieu Lemarchal',
            'unsplash_link' => 'https://unsplash.com/@tamieuh'
        ]
    ]
 
];

$name = $config['gallery_name'];
$count = count($config['local_images']);
$galleries = [['category'=>'Water',
            'link' =>'https://source.unsplash.com/300x200/?water'],
            ['category'=>'Urban',
            'link' =>'https://source.unsplash.com/300x200/?urban'],
            ['category'=>'Nature',
            'link' =>'https://source.unsplash.com/300x200/?nature'],
            ['category'=>'Candy',
            'link' =>'https://source.unsplash.com/300x200/?candy'],
            ['category'=>'Bears',
            'link' =>'https://source.unsplash.com/300x200/?bears'],
            ['category'=>'Beer',
            'link' =>'https://source.unsplash.com/300x200/?beer'],
            ['category'=>'Food',
            'link' =>'https://source.unsplash.com/300x200/?food'],
            ['category'=>'Graffiti',
            'link' =>'https://source.unsplash.com/300x200/?graffiti'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Assignment 1</title>
</head>
<body>
    <!-- Remember that alternative syntax is good and html inside php is bad -->
     <h1><?= $name; ?></h1>
     <div id="gallery">
        <?php foreach ($galleries as $gallery): ?>
        <div>
            <h2><?= $gallery['category']; ?></h2>
            <img src="<?=$gallery['link']?>" alt="<?= $gallery['category'] ?>">
        <?php endforeach ?>
     </div>
     <h1><?= $count; ?> Large Images</h1>
     <div id="large-images">
        <?php foreach($config['local_images'] as $image): ?>
        <img src="<?=$image['image_path']?>" alt="<?= $image['photographer_name'] ?>">
        <h3 class="photographer"><a href="<?= $image['unsplash_link'] ?>"><?= $image['photographer_name'] ?></a></h3>
        <?php endforeach ?>
     </div>

</body>
</html>