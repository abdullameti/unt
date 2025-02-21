<?php
session_start();

// Verifikimi i login-it
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Verifikimi i seksionit aktual që duhet të shfaqet
$section = isset($_GET['section']) ? $_GET['section'] : 'dashboard';

// Logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paneli Kryesor</title>
    <!-- Font Awesome për ikona -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* CSS i përbashkët */
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #eaf6ff;
            color: #333;
        }

        .dashboard {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #00509e;
            color: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            height: 100%;
        }

        .sidebar h2 {
            margin: 0 0 10px;
            font-size: 18px;
            display: flex;
            align-items: center;
        }

        .sidebar h2 i {
            margin-right: 10px;
            font-size: 22px;
        }

        .sidebar h3 {
            margin: 0 0 20px;
            font-size: 16px;
            color: #d1e7ff;
        }

        .sidebar nav ul {
            list-style: none;
            padding: 0;
        }

        .sidebar nav ul li {
            margin: 15px 0;
        }

        .sidebar nav ul li a {
            text-decoration: none;
            color: #d1e7ff;
            font-size: 16px;
            padding: 10px 15px;
            display: flex;
            align-items: center;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .sidebar nav ul li a i {
            margin-right: 10px;
            font-size: 18px;
        }

        .sidebar nav ul li a:hover {
            background-color: #fff;
            color: #00509e;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .main-content h1 {
            margin: 0 0 20px;
            font-size: 24px;
            color: #00509e;
        }

        .student-actions {
            margin-bottom: 20px;
        }

        .student-actions button {
            background-color: #00509e;
            color: #fff;
            border: none;
            padding: 10px 15px;
            margin-right: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .student-actions button:hover {
            background-color: #004080;
        }

        .form-container {
            display: none;
            margin-top: 20px;
        }

        .form-container.active {
            display: block;
        }

        .form-container input, .form-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .saved-students {
            margin-top: 20px;
        }

        .saved-students table {
            width: 100%;
            border-collapse: collapse;
        }

        .saved-students table th, .saved-students table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .saved-students table th {
            background-color: #00509e;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
        <h2><i class="fas fa-graduation-cap"></i> <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Përdorues i paidentifikuar'; ?></h2>
            <h3>Fakulteti i Shkencave të Informatikës</h3>
            <nav>
                <ul>
                    <li><a href="index.php?section=dashboard"><i class="fas fa-tachometer-alt"></i>Paneli Kryesor</a></li>
                    <li><a href="index.php?section=students"><i class="fas fa-user-graduate"></i> Studentët</a></li>
                    <li><a href="index.php?section=subjects"><i class="fas fa-book"></i> Lëndët</a></li>
                    <li><a href="index.php?section=create-certificate"><i class="fas fa-file-alt"></i> Krijo Çertifikatë</a></li>
                    <li><a href="index.php?action=logout"><i class="fas fa-sign-out-alt"></i> Dilni</a></li>
                </ul>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <?php
            // Lidhja me seksionet dhe përmbajtjen që do të shfaqet
            if ($section == 'dashboard') {
                echo '<h1>UNIVERSITETI "NËNË TEREZA" - Shkup</h1><p>Zgjidhni një opsion nga menuja në të majtë për të vazhduar.</p>';
            } elseif ($section == 'students') {
                include 'students.php'; // Këtu përfshini kodin që keni krijuar për studentët
            } elseif ($section == 'subjects') {
                include 'subjects.php';
            } elseif ($section == 'create-certificate') {
                include 'create_certificate.php';
            } else {
                echo '<h1>UNIVERSITETI "NËNË TEREZA" - Shkup</h1>';
            }
            ?>
        </div>
    </div>
</body>
</html>
