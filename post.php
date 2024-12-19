<?php
// Start the session and include configuration
session_start();

include('config.php');

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['id'])) {
    echo "Error: User ID is not set in the session.";
    exit();
}

// Assign the user ID from session
$user_id = $_SESSION['id'];
// Initialize variables
$title = $content = $excerpt = $category = "";
$title_err = $content_err = $category_err = "";
$categories = ['Food', 'Travel', 'Technology', 'Lifestyle'];

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty(trim($_POST["title"]))) {
        $title_err = "Please enter a title.";
    } else {
        $title = trim($_POST["title"]);
    }

    // Validate content
    if (empty(trim($_POST["content"]))) {
        $content_err = "Please enter content.";
    } else {
        $content = trim($_POST["content"]);
    }

    // Validate category
    if (empty(trim($_POST["category"])) || !in_array($_POST["category"], $categories)) {
        $category_err = "Please select a valid category.";
    } else {
        $category = trim($_POST["category"]);
    }

    // Generate excerpt from content (first 150 characters)
    $excerpt = substr(strip_tags($content), 0, 150) . '...';

    // Check input errors before inserting into database
    if (empty($title_err) && empty($content_err) && empty($category_err)) {
        // Prepare an insert statement
        $sql = "INSERT INTO blog_posts (title, content, excerpt, category, author_id, user_id) VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssii", $title, $content, $excerpt, $category, $user_id, $user_id);
            if ($stmt->execute()) {
                // Redirect to index page
                header("location: index.php");
                exit();
            } else {
                echo "Database error: Could not execute the statement.";
            }
            $stmt->close();
        }
    }
}

// If ID is provided in URL, load existing post for viewing
if (isset($_GET['id'])) {
    $sql = "SELECT p.*, u.username FROM blog_posts p JOIN users u ON p.author_id = u.id WHERE p.id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($_GET['id']) ? "View Post" : "Create New Post" ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group textarea {
            height: 200px;
            resize: vertical;
        }

        .error {
            color: #dc3545;
            font-size: 0.9em;
            margin-top: 5px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
            margin-left: 10px;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }

        .post-content {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .post-meta {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_GET['id']) && $post): ?>
            <!-- View Post -->
            <h1><?= htmlspecialchars($post['title']) ?></h1>
            <div class="post-meta">
                Posted by <?= htmlspecialchars($post['username']) ?> on 
                <?= htmlspecialchars(date('F j, Y', strtotime($post['created_at']))) ?>
            </div>
            <div class="post-content">
                <?= nl2br(htmlspecialchars($post['content'])) ?>
            </div>
            <div style="margin-top: 20px;">
                <a href="index.php" class="btn btn-secondary">Back to Posts</a>
            </div>
        <?php else: ?>
            <!-- Create New Post Form -->
            <h1>Create New Post</h1>
            <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($title); ?>">
                    <span class="error"><?= $title_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" 
                                <?= ($category === $cat) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="error"><?= $category_err; ?></span>
                </div>
                
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content"><?= htmlspecialchars($content); ?></textarea>
                    <span class="error"><?= $content_err; ?></span>
                </div>
                
                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Create Post">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
