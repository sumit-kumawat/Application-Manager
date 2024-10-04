<?php
session_start(); // Start the session

// Check if already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: upload.php'); // Redirect to upload.php if already logged in
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check the credentials (for demonstration purposes)
    if ($username === 'admin' && $password === 'bmcAdm1n') {
        $_SESSION['loggedin'] = true; // Set session variable
        header('Location: upload.php'); // Redirect to upload.php
        exit();
    } else {
        $error_message = 'Invalid credentials. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auth</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #e9ecef; /* Light background color */
        }
        .card {
            border-radius: 8px; /* Rounded corners for the card */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow for card */
        }
        .card-header {
            background-color: #f5f5f5; /* Light gray background for header */
            color: #333; /* Dark text color */
            text-align: center;
            font-size: 1.25rem; /* Slightly larger font size for header */
        }
        .form-group label {
            font-weight: bold; /* Bold labels for form fields */
        }
        .btn-primary {
            background-color: #007bff; /* Blue button color */
            border: none; /* Remove border */
            border-radius: 5px; /* Rounded corners for button */
        }
        .btn-primary:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        .alert {
            border-radius: 5px; /* Rounded corners for alerts */
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 15px;
        }
        .back-link a {
            color: #007bff;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .vl {
        border-left: 1.8px solid gray;
        height: 40px;
        }
    </style>
</head>
<body>
    <!-- Header from upload.php -->
    <header class="header d-flex align-items-center" style="background-color: #343a40; color: white; padding: 10px 20px;">
        <a href="index.php">
            <img src="images/logo-light.svg" alt="Logo" class="logo"> 
        </a>
        &nbsp&nbsp
        <!-- Vertical Line -->
        <div class="vl"></div>
        <span class="ml-2" style="font-size: 1.2rem;">Apps Manager</span>
        <div class="ml-auto">
            <a href="index.php" class="text-white"><- go back</a>
        </div>
    </header>

    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card w-100 w-md-25">
            <div class="card-header">
                Auth - AppManager
            </div>
            <div class="card-body">
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form id="loginForm" method="post">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
                <div class="back-link">
                    <a href="index.php">Back to Home</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
