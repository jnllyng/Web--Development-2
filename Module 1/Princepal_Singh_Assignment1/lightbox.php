<?php
$config = [
   'gallery_name' => 'My Unsplash Lightbox',
   'local_images' => [
        'misty_mountain',
        'forest_lake',
        'sunrise_peak',
        'green_valley'
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $config['gallery_name']; ?></title>
  <link rel="stylesheet" href="main.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/luminous-lightbox/2.0.1/luminous-basic.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/luminous-lightbox/2.0.1/Luminous.min.js"></script>
</head>
<body>
  <h1><?php echo $config['gallery_name']; ?></h1>
  <div class="gallery image">
    <?php foreach ($config['local_images'] as $img): ?>
      <a href="images/<?php echo $img; ?>.jpg">
        <img src="images/<?php echo $img; ?>_thumbnail.jpg" alt="<?php echo $img; ?>">
      </a>
    <?php endforeach; ?>
  </div>
  <script>
    new LuminousGallery(document.querySelectorAll(".image a"));
  </script>
</body>
</html>
