<?php
    $href  = "http://example.com";
    $title = "Snafu";
?>
<!DOCTYPE html public "intoxication">
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Make Your Instructor Cry</title>
</head>
<body>
    <p><span><?= "WAT?!?" ?></span></p>
    <p><?= "<a href='" . $href ."'>" . $title . "</a>" ?></p>   <!-- DON'T DO THIS   -->
    <p><a href="<?= $href ?>"><?= $title ?></a></p>     <!-- Good example of PHP in HTML -->
</body>
</html>

<!-- 
<p><a href="<?= $href ?>"><?= $title ?></a></p>
############^^^^^^^^^^^^##^^^^^^^^^^^^^######## 

The code above the hashes (#) is HTML. The code above the carets (^) is PHP. -->