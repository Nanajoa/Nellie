<?php
// Start the session and include configuration
session_start();
include('config.php');

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit();
}

// Get the category from URL parameter, default to 'Food'
$category = isset($_GET['category']) ? $_GET['category'] : 'Food';

// Prepare and execute the query to get blog posts
$sql = "SELECT * FROM blog_posts WHERE category = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $category);
$stmt->execute();
$posts = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - <?= htmlspecialchars($category) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .post {
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .post h2 {
            margin-top: 0;
            color: #333;
        }

        .post-meta {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        .header-buttons {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }

        .button {
            padding: 8px 16px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .create-button {
            background-color: #28a745;
        }

        .create-button:hover {
            background-color: #218838;
        }

        .logout-button {
            background-color: #dc3545;
        }

        .logout-button:hover {
            background-color: #c82333;
        }

        .welcome-message {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #6c757d;
        }

        .category-nav {
            margin: 20px 0;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .category-nav a {
            margin-right: 15px;
            text-decoration: none;
            color: #333;
            padding: 5px 10px;
            border-radius: 3px;
        }

        .category-nav a:hover {
            background-color: #f0f0f0;
        }

        .category-nav a.active {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="welcome-message">
        Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!
    </div>
    
    <div class="header-buttons">
        <a href="post.php" class="button create-button">Create New Post</a>
        <a href="logout.php" class="button logout-button">Logout</a>
    </div>

    <div class="container">
        <div class="category-nav">
            <?php
            $categories = ['Food', 'Travel', 'Technology', 'Lifestyle'];
            foreach ($categories as $cat) {
                $activeClass = ($category === $cat) ? 'active' : '';
                echo '<a href="?category=' . htmlspecialchars($cat) . '" class="' . $activeClass . '">' 
                    . htmlspecialchars($cat) . '</a>';
            }
            ?>
        </div>

        <h1><?= htmlspecialchars($category) ?> Blog Posts</h1>

        <?php while ($post = $posts->fetch_assoc()): ?>
            <div class="post">
                <h2><?= htmlspecialchars($post['title']) ?></h2>
                <div class="post-meta">
                    Posted on <?= htmlspecialchars(date('F j, Y', strtotime($post['created_at']))) ?>
                </div>
                <p><?= htmlspecialchars($post['excerpt']) ?></p>
                <a href="post.php?id=<?= htmlspecialchars($post['id']) ?>">Read More</a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>