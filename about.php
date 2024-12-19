<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About</title>
    <style>
        /* General Body Styling */
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom right, #d9e2ec, #f7fafc), 
                        url('blog_back_image.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
        }

        /* Main Container */
        .about-container {
            text-align: center;
            background: rgba(255, 255, 255, 0.9); /* Slight transparency for the background */
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 600px;
            width: 90%;
        }

        /* Heading Styling */
        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #4a90e2; /* Soft blue color for emphasis */
        }

        /* Paragraph Styling */
        p {
            font-size: 1.2rem;
            line-height: 1.6;
            margin: 0 0 20px;
            color: #555;
        }

        /* Button Styling */
        .home-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: bold;
            color: white;
            background-color: #4a90e2; /* Soft blue */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .home-button:hover {
            background-color: #357abd; /* Darker blue on hover */
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }

            h1 {
                font-size: 2rem;
            }

            p {
                font-size: 1rem;
            }

            .home-button {
                font-size: 0.9rem;
                padding: 8px 16px;
            }
        }
    </style>
</head>
<body>
    <div class="about-container">
        <h1>About This Blog</h1>
        <p>
            Welcome to our blog! We cover a variety of topics including food, fashion, and weather. 
            Explore, read, and feel free to share your thoughts in the comments. Enjoy your time here!
        </p>
        <!-- Button to redirect to the homepage -->
        <a href="homepage.php" class="home-button">Go to Homepage</a>
    </div>
</body>
</html>
