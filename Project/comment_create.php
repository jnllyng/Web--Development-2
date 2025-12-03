<?php
session_start();
require('includes/db_connect.php');

$species_id = $_POST['species_id'] ?? null;
$content = trim($_POST['content'] ?? '');
$user_id = $_SESSION['user_id'] ?? null;
$guest_name = trim($_POST['guest_name'] ?? '');
$captcha_input = trim($_POST['captcha'] ?? '');
$captcha_session = $_SESSION['captcha_text'] ?? '';

if (!$species_id || !ctype_digit((string)$species_id) || !$content) {
    $_SESSION['comment_error'] = "Invalid comment data.";
    $_SESSION['comment_content'] = $content;
    $_SESSION['comment_guest_name'] = $guest_name;
    header("Location: species_details.php?id=$species_id");
    exit;
}

if (!$captcha_session || strcasecmp($captcha_input, $captcha_session) !== 0) {
    $_SESSION['comment_error'] = "Incorrect CAPTCHA. Please try again.";
    $_SESSION['comment_content'] = $content;
    $_SESSION['comment_guest_name'] = $guest_name;
    header("Location: species_details.php?id=$species_id");
    exit;
}

if ($user_id) {
    $stmt = $db->prepare("INSERT INTO comments (species_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$species_id, $user_id, $content]);
} else {
    $stmt = $db->prepare("INSERT INTO comments (species_id, guest_name, content) VALUES (?, ?, ?)");
    $stmt->execute([$species_id, $guest_name, $content]);
}

unset($_SESSION['comment_error'], $_SESSION['comment_content'], $_SESSION['comment_guest_name'], $_SESSION['captcha_text']);
header("Location: species_details.php?id=$species_id");
exit;
?>
