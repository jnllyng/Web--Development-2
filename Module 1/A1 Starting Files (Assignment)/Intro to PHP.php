<?php

/*******w******** 
    
    Name: Jueun Yang
    Date: 2025-08-28
    Description:

****************/

$config = [

    'gallery_name' => 'Elly\'s Gallery',
 
    'unsplash_categories' => ['array','of','category','keywords'],
 
    'local_images' => [['image_path'=> 'images/image 1.jpg','images/image 2.jpg','images/image 3.jpg','images/image 4.jpg'],
                        ['photographer_name'=>'Samuel Larocque', 'gus', 'Edgar', 'Matthieu Lemarchal'],
                        ['unsplash_link' => 'https://unsplash.com/@samuellarocque', 'https://unsplash.com/@vrid_is', 'https://unsplash.com/@snapshot_journey', 'https://unsplash.com/@tamieuh']
    ]
 
];

$name = $config['gallery_name'];
$count = count($config['slocal_images']['image_path']);

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
     <h1><?= $name ?></h1>
     <h1><?= $count ?> Large Images</h1>
     <div id="large-images">
        <?php foreach($config['local_images']['image_path'] as $path): ?>
        <img src="<?=$path?>">
        <?php endforeach ?>
     </div>

</body>
</html>