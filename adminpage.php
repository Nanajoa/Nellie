<?php
// Only process API requests if it's a POST/GET request
if ($_SERVER['REQUEST_METHOD'] === 'POST' || 
    (isset($_GET['action']) && isset($_GET['entity']))) {
    
    // Database connection
    $host = 'localhost';
    $dbname = 'blog_db';
    $username = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]);
        exit;
    }

    // Handle requests
    $action = $_REQUEST['action'] ?? '';
    $entity = $_REQUEST['entity'] ?? '';

    switch($action) {
        case 'create':
            createEntity($pdo, $entity);
            break;
        case 'read':
            readEntities($pdo, $entity);
            break;
        case 'update':
            updateEntity($pdo, $entity);
            break;
        case 'delete':
            deleteEntity($pdo, $entity);
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
    exit;
}

function createEntity($pdo, $entity) {
    try {
        switch($entity) {
            case 'users':
                $stmt = $pdo->prepare("INSERT INTO users (username, password, email, user_type, is_active, created_at) 
                                     VALUES (?, ?, ?, ?, ?, NOW())");
                $stmt->execute([
                    $_POST['username'],
                    password_hash($_POST['password'], PASSWORD_DEFAULT),
                    $_POST['email'],
                    $_POST['userType'],
                    $_POST['isActive']
                ]);
                break;

            case 'blog_posts':
                $stmt = $pdo->prepare("INSERT INTO blog_posts (title, content, author, category, created_at, updated_at) 
                                     VALUES (?, ?, ?, ?, NOW(), NOW())");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['content'],
                    $_POST['author'],
                    $_POST['category']
                ]);
                break;
        }
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function readEntities($pdo, $entity) {
    try {
        switch($entity) {
            case 'users':
                $stmt = $pdo->query("SELECT id, username, email, user_type, is_active, created_at FROM users ORDER BY id DESC");
                break;
            case 'blog_posts':
                $stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY created_at DESC");
                break;
        }
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $items]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function updateEntity($pdo, $entity) {
    try {
        switch($entity) {
            case 'users':
                $sql = "UPDATE users SET username = ?, email = ?, user_type = ?, is_active = ?";
                $params = [
                    $_POST['username'],
                    $_POST['email'],
                    $_POST['userType'],
                    $_POST['isActive']
                ];
                
                if (!empty($_POST['password'])) {
                    $sql .= ", password = ?";
                    $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }
                
                $sql .= " WHERE id = ?";
                $params[] = $_POST['id'];
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                break;

            case 'blog_posts':
                $stmt = $pdo->prepare("UPDATE blog_posts SET title = ?, content = ?, 
                                     author = ?, category = ?, updated_at = NOW() 
                                     WHERE id = ?");
                $stmt->execute([
                    $_POST['title'],
                    $_POST['content'],
                    $_POST['author'],
                    $_POST['category'],
                    $_POST['id']
                ]);
                break;
        }
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function deleteEntity($pdo, $entity) {
    try {
        switch($entity) {
            case 'users':
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                break;
            case 'blog_posts':
                $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
                $stmt->execute([$_POST['id']]);
                break;
        }
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Admin Dashboard</title>
    
    <<style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-button {
            padding: 10px 20px;
            border: none;
            background-color: #f4f4f4;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
        }

        .tab-button.active {
            background-color: #4CAF50;
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .form-section {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        input, select {
            flex: 1 1 200px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            padding: 8px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .edit-btn {
            background-color: #2196F3;
        }

        .delete-btn {
            background-color: #f44336;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
            position: sticky;
            top: 0;
        }

        .table-container {
            overflow-x: auto;
            max-height: 600px;
            overflow-y: auto;
        }

        .hidden {
            display: none;
        }
        /* Navigation Styles */
nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #2c3e50;
    color: #ecf0f1;
    padding: 1rem 2rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.logo {
    display: flex;
    align-items: center;
    font-size: 1.5rem;
    font-weight: 600;
}

.logo i {
    color: #3498db;
    margin-right: 0.75rem;
}

.nav-links {
    display: flex;
    gap: 1.5rem;
}

.nav-links a {
    color: #ecf0f1;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
    position: relative;
}

.nav-links a:hover {
    color: #3498db;
}

.nav-links a::after {
    content: '';
    position: absolute;
    width: 0;
    height: 2px;
    bottom: -5px;
    left: 0;
    background-color: #3498db;
    transition: width 0.3s ease;
}

.nav-links a:hover::after {
    width: 100%;
}

    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <i class="fas fa-blog" style="margin-right: 0.5rem;"></i>
            Blog Admin Dashboard
        </div>
        
        <div class="tabs">
            <button class="tab-button active" onclick="showTab('users')">Users</button>
            <button class="tab-button" onclick="showTab('blog_posts')">Blog Posts</button>
        </div>

        <!-- Users Tab -->
        <div id="usersTab" class="tab-content active">
            <div class="form-section">
                <h2>Add New User</h2>
                <form id="addUserForm">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <select name="userType" required>
                        <option value="" disabled selected>User Type</option>
                        <option value="admin">Admin</option>
                        <option value="author">Author</option>
                        <option value="user">User</option>
                    </select>
                    <select name="isActive" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <button type="submit">Add User</button>
                </form>
            </div>
            <div class="table-container">
                <table id="usersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>User Type</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <!-- Blog Posts Tab -->
        <div id="blog_postsTab" class="tab-content">
            <div class="form-section">
                <h2>Add New Blog Post</h2>
                <form id="addBlogPostForm">
                    <input type="text" name="title" placeholder="Title" required>
                    <textarea name="content" placeholder="Content" required></textarea>
                    <input type="text" name="author" placeholder="Author" required>
                    <input type="text" name="category" placeholder="Category" required>
                    <button type="submit">Add Blog Post</button>
                </form>
            </div>
            <div class="table-container">
                <table id="blog_postsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Content</th>
                            <th>Author</th>
                            <th>Category</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
    // Initial load
    loadAllTables();

    // Tab switching functionality
    window.showTab = function(tabName) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
        });

        // Show selected tab
        document.getElementById(tabName + 'Tab').classList.add('active');
        document.querySelector(`button[onclick="showTab('${tabName}')"]`).classList.add('active');
    };

    // Load all tables data
    function loadAllTables() {
        loadTableData('users');
        loadTableData('blog_posts');
    }

    // Generic function to load table data
    function loadTableData(entity) {
        fetch(`?action=read&entity=${entity}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    populateTable(entity, data.data);
                } else {
                    console.error('Error loading data:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Populate tables with data
    function populateTable(entity, items) {
        const tbody = document.querySelector(`#${entity}Table tbody`);
        tbody.innerHTML = '';

        items.forEach(item => {
            const tr = document.createElement('tr');
            let html = '';

            switch(entity) {
                case 'users':
                    html = `
                        <td>${item.id}</td>
                        <td>${item.username}</td>
                        <td>${item.email}</td>
                        <td>${item.user_type}</td>
                        <td>${item.is_active === '1' ? 'Active' : 'Inactive'}</td>
                        <td>${formatDate(item.created_at)}</td>
                    `;
                    break;

                case 'blog_posts':
                    html = `
                        <td>${item.id}</td>
                        <td>${item.title}</td>
                        <td>${truncateText(item.content, 100)}</td>
                        <td>${item.author}</td>
                        <td>${item.category}</td>
                        <td>${formatDate(item.created_at)}</td>
                        <td>${formatDate(item.updated_at)}</td>
                    `;
                    break;
            }

            // Add action buttons
            html += `
                <td>
                    <button class="edit-btn" onclick="editItem('${entity}', ${JSON.stringify(item).replace(/"/g, '&quot;')})">Edit</button>
                    <button class="delete-btn" onclick="deleteItem('${entity}', ${JSON.stringify(item).replace(/"/g, '&quot;')})">Delete</button>
                </td>
            `;

            tr.innerHTML = html;
            tbody.appendChild(tr);
        });
    }

    // Helper function to format dates
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }

    // Helper function to truncate text
    function truncateText(text, maxLength) {
        if (!text) return '';
        return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
    }

    // Form submission handlers
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        submitForm('users', formData);
    });

    document.getElementById('addBlogPostForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        submitForm('blog_posts', formData);
    });

    // Generic form submission function
    function submitForm(entity, formData) {
        fetch('?action=create&entity=' + entity, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadTableData(entity);
                // Reset form
                document.querySelector(`#add${capitalizeFirstLetter(entity)}Form`).reset();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Helper function to capitalize first letter
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // Edit item function
    window.editItem = function(entity, item) {
        const form = document.querySelector(`#add${capitalizeFirstLetter(entity)}Form`);
        
        // Create a hidden input for the ID if it doesn't exist
        let idInput = form.querySelector('input[name="id"]');
        if (!idInput) {
            idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            form.appendChild(idInput);
        }
        
        // Populate form fields
        Object.keys(item).forEach(key => {
            const element = form.querySelector(`[name="${key}"]`);
            if (element && key !== 'password') { // Skip password field
                element.value = item[key];
            }
        });

        // Change submit button text
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.textContent = 'Update';
        
        // Add update event listener
        form.onsubmit = function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            
            fetch('?action=update&entity=' + entity, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadTableData(entity);
                    form.reset();
                    // Reset form to create mode
                    submitButton.textContent = 'Add';
                    form.onsubmit = null;
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        submitForm(entity, new FormData(e.target));
                    });
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        };
    };

    // Delete item function
    window.deleteItem = function(entity, item) {
        if (confirm('Are you sure you want to delete this item?')) {
            const formData = new FormData();
            formData.append('id', item.id);

            fetch('?action=delete&entity=' + entity, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadTableData(entity);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }
    };
});
    </script>
</body>
</html>