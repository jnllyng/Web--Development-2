<?php

/*******w******** 
    
    Name: Jueun Yang
    Date: 2025-10-28
    Description: File upload and resize challenge.

****************/

require 'C:\xampp\htdocs\Web-Development-2\C7 Starting Files\php-image-resize-master\lib\ImageResize.php';
require 'C:\xampp\htdocs\Web-Development-2\C7 Starting Files\php-image-resize-master\lib\ImageResizeException.php';
use \Gumlet\ImageResize;
use \Gumlet\ImageResizeException;
function file_upload_path($original_filename, $upload_subfolder_name = 'uploads')
{
    $current_folder = dirname(__FILE__);
    $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
    return join(DIRECTORY_SEPARATOR, $path_segments);
}

function file_is_an_image($temporary_path, $new_path)
{
    $allowed_mime_types = ['image/gif', 'image/jpeg', 'image/png'];
    $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];

    $actual_file_extension = pathinfo($new_path, PATHINFO_EXTENSION);
    $image_info = getimagesize($temporary_path);
    $actual_mime_type = $image_info ? $image_info['mime'] : '';

    $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
    $mime_type_is_valid = in_array($actual_mime_type, $allowed_mime_types);

    return $file_extension_is_valid && $mime_type_is_valid;
}

$image_upload_detected = isset($_FILES['image']) && ($_FILES['image']['error'] === 0);
$upload_error_detected = isset($_FILES['image']) && ($_FILES['image']['error'] > 0);
$message = '';

if ($image_upload_detected) {
    $image_filename = $_FILES['image']['name'];
    $temporary_image_path = $_FILES['image']['tmp_name'];
    $new_image_path = file_upload_path($image_filename);

    if (file_is_an_image($temporary_image_path, $new_image_path)) {
        if (move_uploaded_file($temporary_image_path, $new_image_path)) {
            try {
                $upload_dir = dirname($new_image_path);

                $resize_image = new ImageResize($new_image_path);
                $resize_image->resizeToWidth(400);
                $resize_image->save($upload_dir . DIRECTORY_SEPARATOR . pathinfo($image_filename, PATHINFO_FILENAME) . '_medium.' . pathinfo($image_filename, PATHINFO_EXTENSION));

                $thumbnail_image = new ImageResize($new_image_path);
                $thumbnail_image->resizeToWidth(50);
                $thumbnail_image->save($upload_dir . DIRECTORY_SEPARATOR . pathinfo($image_filename, PATHINFO_FILENAME) . '_thumbnail.' . pathinfo($image_filename, PATHINFO_EXTENSION));

                $message = "File uploaded successfully.";
            } catch (ImageResizeException $e) {
                $message = "Resize failed: " . $e->getMessage();
            }
        } else {
            $message = "File upload failed.";
        }
    } else {
        $message = "Not a valid image file.";
    }
} elseif ($upload_error_detected) {
    $message = "Upload failed with error code: " . $_FILES['image']['error'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My File Upload Challenge</title>
</head>
<body>
    <h1>File Upload Challenge</h1>

    <form method="post" enctype="multipart/form-data" action="fileUpload.php">
        <label for="image">Choose an image to upload:</label>
        <input type="file" name="image" id="image" required>
        <input type="submit" value="Upload Image">
    </form>

    <?php if (!empty($message)): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
</body>
</html>
