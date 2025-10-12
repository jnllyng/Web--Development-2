<?php

/*******w******** 
    
    Name: Gurshan Singh Sekhon
    Date: October 01, 2025
    Description: Edit or delete existing blog posts

****************/

require('connect.php');
require('authenticate.php');

$errors = [];
$title = '';
$content = '';
$post = null;

// Validate GET parameter
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $errors[] = "Invalid or missing post ID.";
} else {
    $id = (int)$_GET['id'];
    
    // Fetch the post
    $query = "SELECT id, title, content, timestamp FROM posts WHERE id = :id";
    $statement = $db->prepare($query);
    $statement->bindParam(':id', $id, PDO::PARAM_INT);
    $statement->execute();
    $post = $statement->fetch(PDO::FETCH_ASSOC);
    
    if (!$post) {
        $errors[] = "Post not found.";
    } else {
        $title = $post['title'];
        $content = $post['content'];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $post) {
    // Check if delete button was clicked
    if (isset($_POST['delete'])) {
        $query = "DELETE FROM posts WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        
        if ($statement->execute()) {
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Failed to delete post.";
        }
    } 
    // Handle update
    elseif (isset($_POST['update'])) {
        // Sanitize and validate input
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        
        // Validation
        if (strlen($title) < 1) {
            $errors[] = "Title must be at least 1 character.";
        }
        if (strlen($content) < 1) {
            $errors[] = "Content must be at least 1 character.";
        }
        
        // If no errors, update database
        if (empty($errors)) {
            $query = "UPDATE posts SET title = :title, content = :content WHERE id = :id";
            $statement = $db->prepare($query);
            $statement->bindParam(':title', $title);
            $statement->bindParam(':content', $content);
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($statement->execute()) {
                header("Location: post.php?id=" . $id);
                exit;
            } else {
                $errors[] = "Failed to update post.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Edit this Post!</title>
</head>
<body>
    <header class="nav-head_edit">
        <a href="index.php"><img src="blog_logo.png" alt="logo"></a>
        <h1><a href="index.php">My Blog</a></h1>
    </header>
    
    <main>
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <?php foreach ($errors as $error): ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($post): ?>
            <h2>Edit Post</h2>
            <form action="edit.php?id=<?= $post['id'] ?>" method="post" class="post-form">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" rows="10" required><?= htmlspecialchars($content) ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="update">Update Post</button>
                    <button type="submit" name="delete" class="delete-btn" onclick="return confirm('Are you sure you want to delete this post?')">Delete Post</button>
                    <a href="post.php?id=<?= $post['id'] ?>" class="cancel-link">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>