<?php

/*******w******** 
    
    Name: Gurshan Singh Sekhon
    Date: October 01, 2025
    Description: View full post or create new post

****************/

require('connect.php');

$errors = [];
$title = '';
$content = '';
$post = null;

// Check if viewing a specific post
if (isset($_GET['id'])) {
    // Validate ID is an integer
    if (filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
        $id = (int)$_GET['id'];
        
        // Fetch the post
        $query = "SELECT id, title, content, timestamp FROM posts WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindParam(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $post = $statement->fetch(PDO::FETCH_ASSOC);
        
        if (!$post) {
            $errors[] = "Post not found.";
        }
    } else {
        $errors[] = "Invalid post ID.";
    }
}

// Handle new post creation (requires authentication)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require('authenticate.php');
    
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
    
    // If no errors, insert into database
    if (empty($errors)) {
        $query = "INSERT INTO posts (title, content) VALUES (:title, :content)";
        $statement = $db->prepare($query);
        $statement->bindParam(':title', $title);
        $statement->bindParam(':content', $content);
        
        if ($statement->execute()) {
            // Redirect to home page after successful creation
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Failed to create post.";
        }
    }
}

// If creating a new post, require authentication
$isNewPost = !isset($_GET['id']);
if ($isNewPost) {
    require('authenticate.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title><?= $isNewPost ? 'New Post' : 'My Blog Post!' ?></title>
</head>
<body>
    <header class="<?= $isNewPost ? 'nav-head_edit' : 'nav-head' ?>">
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
        
        <?php if ($isNewPost): ?>
            <!-- New Post Form -->
            <h2>Create New Post</h2>
            <form action="post.php" method="post" class="post-form">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="content">Content:</label>
                    <textarea id="content" name="content" rows="10" required><?= htmlspecialchars($content) ?></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="submit">Create Post</button>
                    <a href="index.php" class="cancel-link">Cancel</a>
                </div>
            </form>
        <?php elseif ($post): ?>
            <!-- Display Full Post -->
            <article class="post-full">
                <h2><?= htmlspecialchars($post['title']) ?></h2>
                <p class="post-date">
                    <?= date('F d, Y, h:i a', strtotime($post['timestamp'])) ?>
                </p>
                <div class="post-content">
                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                </div>
                <div class="post-actions">
                    <a href="edit.php?id=<?= $post['id'] ?>" class="edit-link">Edit</a>
                    <a href="index.php">Back to Home</a>
                </div>
            </article>
        <?php endif; ?>
    </main>
</body>
</html>