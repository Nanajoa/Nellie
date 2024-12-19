<?php
session_start();

//Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: signup.php');
    exit();
}

// Include database configuration
require_once('config.php');

// Fetch user details
$user_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - <?php echo htmlspecialchars($user['username']); ?></title>
    <style>
        /* General Body Styling */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom, #f0f4f8, #d9e2ec);
            color: #333;
            line-height: 1.6;
        }

        .homepage-container {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        /* Header Styling */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            border-radius: 8px 8px 0 0;
        }

        header h1 {
            margin: 0;
        }

        .logout-button {
            text-decoration: none;
            color: white;
            background-color: #d9534f;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .logout-button:hover {
            background-color: #c9302c;
        }

        /* Main Section */
        main {
            padding: 20px;
        }

        .user-profile, .quick-actions {
            margin-bottom: 30px;
        }

        .user-profile h2, .quick-actions h2 {
            border-bottom: 2px solid #4a90e2;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .profile-details p {
            margin: 10px 0;
            font-size: 16px;
            color: #555;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .action-button {
            display: inline-block;
            text-decoration: none;
            background-color: #4a90e2;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .action-button:hover {
            background-color: #357abd;
        }

        /* Footer Styling */
        footer {
            text-align: center;
            margin-top: 20px;
            color: #777;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .homepage-container {
                margin: 10px;
                padding: 15px;
            }

            header {
                flex-direction: column;
                text-align: center;
            }

            .action-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <div class="homepage-container">
        <header>
            <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            <nav>
                <a href="logout.php" class="logout-button">Logout</a>
            </nav>
        </header>

        <main>
            <section class="user-profile">
                <h2>Your Profile</h2>
                <div class="profile-details">
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                </div>
            </section>

            <section class="quick-actions">
                <h2>Quick Actions</h2>
                <div class="action-buttons">
                    <a href="#" class="action-button">Edit Profile</a>
                    <a href="#" class="action-button">Change Password</a>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Your Website Name. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
