<?php
$config = [
   'gallery_name' => 'My Unsplash Gallery',
   'local_images' => [
        [
            'filename' => 'misty_mountain.jpg',
            'photographer' => 'Jakub B.',
            'url' => 'https://unsplash.com/@jakubb'
        ],
        [
            'filename' => 'forest_lake.jpg',
            'photographer' => 'Jannik L.',
            'url' => 'https://unsplash.com/@janniklingnau'
        ],
        [
            'filename' => 'sunrise_peak.jpg',
            'photographer' => 'Mateusz Ł.',
            'url' => 'https://unsplash.com/@mateuszlacek'
        ],
        [
            'filename' => 'green_valley.jpg',
            'photographer' => 'Dan C.',
            'url' => 'https://unsplash.com/@dancurtis'
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $config['gallery_name']; ?></title>
  <link rel="stylesheet" href="main.css">
</head>
<body>
  <h1><?php echo $config['gallery_name']; ?></h1>
  <h1><?php echo count($config['local_images']); ?> Large Images</h1>
  <div class="gallery">
    <?php foreach ($config['local_images'] as $img): ?>
      <div class="image">
        <img src="images/<?php echo $img['filename']; ?>" alt="<?php echo $img['photographer']; ?>">
        <p>
          Photo by <a href="<?php echo $img['url']; ?>" target="_blank">
            <?php echo $img['photographer']; ?>
          </a>
        </p>
      </div>
    <?php endforeach; ?>
  </div>
</body>
</html>
