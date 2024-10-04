<?php
session_start(); // Start the session

// Include database connection
include 'db.php';

// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: auth.php'); // Redirect to login page
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $platform = $_POST['platform'];
    $file = $_FILES['file'];

    // Check if file upload was successful
    if ($file['error'] == UPLOAD_ERR_OK) {
        // Define the upload directory and file path
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($file['name']);

        // Ensure the upload directory exists and is writable
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move the file to the uploads directory
        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            // Prepare and execute the database insertion
            $stmt = $conn->prepare("INSERT INTO applications (name, description, platform, file_path) VALUES (?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ssss", $name, $description, $platform, $uploadFile);
                if ($stmt->execute()) {
                    $stmt->close();
                    echo json_encode(['status' => 'success', 'message' => 'File uploaded and data inserted successfully.']);
                    exit();
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Database insertion failed: ' . $conn->error]);
                    exit();
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Database prepare statement failed: ' . $conn->error]);
                exit();
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'File upload failed.']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'File upload error: ' . $file['error']]);
        exit();
    }
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM applications WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $stmt->close();
            echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully.']);
            exit();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database deletion failed: ' . $conn->error]);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database prepare statement failed: ' . $conn->error]);
        exit();
    }
}

// Fetch records for display
$records = $conn->query("SELECT * FROM applications")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Manager</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .header {
            background-color: #343a40;
            color: white;
            padding: 10px 20px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .header .logo {
            height: 40px;
        }

        .header .search-container {
            margin-left: auto;
        }

        .search-container input {
            width: 300px;
        }

        .container {
            margin-top: 80px; /* Adjust margin to avoid overlap with fixed header */
        }

        .progress {
            height: 30px;
        }

        .progress-bar {
            line-height: 30px; /* Align text vertically in the progress bar */
            color: white;
            text-align: center;
        }

        .vl {
            border-left: 1.8px solid gray;
            height: 40px;
        }

        .app-item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .app-item .delete-btn {
            color: red;
            cursor: pointer;
        }

        .modal-dialog {
            max-width: 800px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header d-flex align-items-center">
    <a href="index.php">
            <img src="images/logo-light.svg" alt="Logo" class="logo"> 
        </a>
        &nbsp&nbsp
        <!-- Vertical Line -->
        <div class="vl"></div>
        <span class="ml-2" style="font-size: 1.2rem;">Apps Manager</span>
        <div class="search-container ml-auto">
            <input type="text" id="searchInput" class="form-control" placeholder="Search applications...">
        </div>
        <button class="btn btn-primary ml-3" data-toggle="modal" data-target="#uploadModal">Upload Application</button>
        <i class="fas fa-info-circle ml-3" data-toggle="modal" data-target="#infoModal" style="cursor: pointer;"></i>
        <a href="logout.php">
            <i class="fas fa-arrow-right ml-3" style="cursor: pointer;"></i>
        </a>
    </div>

    <!-- Main content -->
    <div class="container mt-5">
        <h1 class="text-center mb-4">Application List</h1>
        <hr>
        <div id="appList">
            <?php foreach ($records as $record): ?>
                <div class="app-item" data-name="<?php echo htmlspecialchars($record['name']); ?>">
                    <h4><?php echo htmlspecialchars($record['name']); ?></h4>
                    <p><?php echo htmlspecialchars($record['description']); ?></p>
                    <p><strong>Platform:</strong> <?php echo htmlspecialchars($record['platform']); ?></p>
                    <a href="<?php echo htmlspecialchars($record['file_path']); ?>" download>Download File</a>
                    <span class="delete-btn" data-id="<?php echo $record['id']; ?>"><i class="fas fa-trash-alt"></i> Delete</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Application</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="platform">Platform</label>
                            <select class="form-control" id="platform" name="platform" required>
                                <option value="Windows">Windows</option>
                                <option value="Linux">Linux</option>
                                <option value="Unix">Unix</option>
                                <option value="Android">Android</option>
                                <option value="iOS">iOS</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="file">File</label>
                            <input type="file" class="form-control-file" id="file" name="file" required>
                        </div>
                        <div class="progress mb-3">
                            <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Modal -->
    <div class="modal fade" id="infoModal" tabindex="-1" role="dialog" aria-labelledby="infoModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="infoModalLabel">Application Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Application:</strong> App Manager</p>
                    <p><strong>Version:</strong> 2.0</p>
                    <p><strong>Last Updated:</strong> <span id="todayDate"></span></p>
                    <p><strong>Developed by:</strong> Sumit_Kumawat@bmc.com</p>
                    <p><strong>Support Group:</strong> CloudFrontiers@bmc.com</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery, Bootstrap JS, and AJAX handling -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            // Handle form submission
            $('#uploadForm').on('submit', function (e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    xhr: function () {
                        var xhr = new XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function (e) {
                            if (e.lengthComputable) {
                                var percentComplete = Math.round((e.loaded / e.total) * 100);
                                $('#progressBar').css('width', percentComplete + '%').text(percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    type: 'POST',
                    url: '',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        response = JSON.parse(response);
                        if (response.status === 'success') {
                            alert(response.message);
                            $('#uploadModal').modal('hide');
                            location.reload(); // Reload the page to update the list
                        } else {
                            alert(response.message);
                        }
                    }
                });
            });

            $(document).ready(function() {
            // Set the date of 2 days ago in the info modal
            var today = new Date();
            var twoDaysAgo = new Date(today);
            twoDaysAgo.setDate(today.getDate() - 2); // Subtract 2 days

            var dateString = twoDaysAgo.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            $('#todayDate').text(dateString);
            });

            // Handle delete functionality
            $(document).on('click', '.delete-btn', function () {
                var id = $(this).data('id');
                if (confirm('Are you sure you want to delete this record?')) {
                    $.ajax({
                        type: 'GET',
                        url: '',
                        data: { delete_id: id },
                        success: function (response) {
                            response = JSON.parse(response);
                            if (response.status === 'success') {
                                alert(response.message);
                                location.reload(); // Reload the page to update the list
                            } else {
                                alert(response.message);
                            }
                        }
                    });
                }
            });

            // Handle live search
            $('#searchInput').on('keyup', function () {
                var searchValue = $(this).val().toLowerCase();
                $('.app-item').each(function () {
                    var itemName = $(this).data('name').toLowerCase();
                    $(this).toggle(itemName.indexOf(searchValue) > -1);
                });
            });
        });
    </script>
</body>
</html>