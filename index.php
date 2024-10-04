<?php
include 'db.php'; // Include database connection

// Base URL for your site
$base_url = 'http://vw-aus-cf-004.bmc.com/apps/'; // Replace with your actual domain

// Fetch applications from the database based on search and platform filters
$platform = isset($_GET['platform']) ? $_GET['platform'] : '';
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Prepare SQL query based on filters
$sql = "SELECT * FROM applications WHERE 1=1";
if ($platform != '') {
    $sql .= " AND platform = '$platform'";
}
if ($query != '') {
    $sql .= " AND name LIKE '%$query%'";
}
$result = $conn->query($sql);
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
        /* Sidebar Styles */
        .sidebar {
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 200px;
            background-color: #343a40;
            padding-top: 20px;
            color: white;
            z-index: 1000;
        }
        .sidebar .logo {
            display: block;
            margin: 0 auto;
            width: 150px;
        }
        .sidebar .platform-link {
            text-align: center;
            display: block;
            padding: 10px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .sidebar .platform-link:hover {
            background-color: #495057;
        }

        /* Adjust Content Wrapper to Account for Sidebar */
        .content-wrapper {
            margin-left: 210px;
            padding: 20px;
            margin-top: 60px; /* To adjust below the header */
        }

        /* Fixed Header */
        .header {
            position: fixed;
            top: 0;
            left: 200px;
            right: 0;
            background-color: #f8f9fa;
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
            z-index: 1000;
            display: flex;
            align-items: center;
        }

        /* Search Bar Container */
        .search-container {
            margin-left: auto;
            width: 300px;
        }

        .search-container input {
            width: 100%;
            border-radius: 20px;
            padding: 5px 10px;
        }

        /* Application Cards */
        .card {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .card-body {
            padding: 15px;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .platform-logo {
            height: 30px;
        }

        .btn-download {
            background-color: #007bff;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
        }

        .btn-download:hover {
            background-color: #0056b3;
        }

        /* Toast Notifications */
        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #343a40;
            color: white;
            border-radius: 8px;
            padding: 10px;
            z-index: 1050;
        }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="index.php">
            <img src="images/logo-light.svg" alt="Logo" class="logo">
        </a>
        <br>
        <a href="index.php?platform=windows" class="platform-link" data-platform="windows">
            <i class="fab fa-windows"></i> Windows
        </a>
        <a href="index.php?platform=linux" class="platform-link" data-platform="linux">
            <i class="fab fa-linux"></i> Linux
        </a>
        <a href="index.php" class="platform-link" data-platform="">
            <i class="fas fa-filter"></i> All Platforms
        </a>
    </div>

    <!-- Header -->
    <div class="header">
        <span class="ml-2" style="font-size: 1.2rem;">ARL Manager</span>
        <div class="search-container ml-auto">
            <input type="text" id="searchInput" class="form-control" placeholder="Search applications...">
        </div>
        <i class="fas fa-info-circle ml-3" data-toggle="modal" data-target="#infoModal" style="cursor: pointer;"></i>
        <a href="upload.php">
            <i class="fas fa-user ml-3" style="cursor: pointer;"></i>
        </a>
    </div>

    <!-- Main content -->
    <div class="content-wrapper">
        <h2 class="text-center mb-4">Apps, Repo & Library Manager</h2>
        <hr>
        <div class="row" id="applicationsContainer">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-download" data-clipboard-text="<?php echo $base_url . htmlspecialchars($row['file_path']); ?>">
                                Download Now
                            </button>
                            <img src="icon/<?php echo strtolower($row['platform']); ?>-logo.png" alt="<?php echo $row['platform']; ?> Logo" class="platform-logo">
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
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

    <!-- Toast Notification -->
    <div class="toast" id="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-body">
            Link copied to clipboard!
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
    <script>
        // Live search and platform filter using AJAX
        function fetchApplications(platform = '', query = '') {
            $.ajax({
                url: 'index.php',
                type: 'GET',
                data: {
                    platform: platform,
                    query: query
                },
                success: function(data) {
                    $('#applicationsContainer').html($(data).find('#applicationsContainer').html());
                }
            });
        }

        // Initial fetch
        fetchApplications();

        // Platform link click handling
        $('.platform-link').click(function(e) {
            e.preventDefault(); // Prevent default link behavior
            const platform = $(this).data('platform');
            const query = $('#searchInput').val();
            fetchApplications(platform, query);
        });

        // Search input handling
        $('#searchInput').on('input', function() {
            const query = $(this).val();
            const platform = $('.platform-link.active').data('platform') || '';
            fetchApplications(platform, query);
        });

        // Clipboard functionality for copying download links
        new ClipboardJS('.btn-download').on('success', function() {
            $('#toast').toast('show');
        });

        // Info Modal Date Setup
        const today = new Date().toISOString().split('T')[0]; // Format today's date
        $('#todayDate').text(today);
    </script>
</body>
</html>
