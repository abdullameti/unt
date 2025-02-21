<?php
session_start();

// Kontrollo nëse është dërguar formulari i login-it
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lexo të dhënat nga JSON file
    $users = json_decode(file_get_contents('users.json'), true);

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Verifikimi i username dhe password
    foreach ($users as $user) {
        if ($user['username'] == $username && $user['password'] == $password) {
            // Ruaj emrin në session
            $_SESSION['logged_in'] = true;
            $_SESSION['user_name'] = $user['name']; // Ruaj emrin në session
            header("Location: index.php"); // Redirekto në panelin kryesor
            exit;
        }
    }
    echo "Emri i përdoruesit ose fjalëkalimi janë të pasakta!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UNT - SHKUP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f2f7ff;
            margin: 0;
        }
        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            width: 350px;
        .logo {
            width: 300px;
        }
            
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .input-group {
            display: flex;
            width: 100%;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .input-group input {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            outline: none;
            font-size: 16px;
        }
        .input-group span {
            background-color: #f5f5f5;
            padding: 12px;
            font-size: 16px;
            color: #333;
            border-right: 1px solid #ccc;
            border-radius: 5px 0 0 5px;
            display: flex;
            align-items: center;
        }
        .input-group input:focus {
            border-color: #00509e;
        }
        .login-button {
            background-color: #00509e;
            color: white;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }
        .login-button:hover {
            background-color: #003d73;
        }
        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
            font-size: 14px;
        }
        .logo {
        display: block;
        margin: 0 auto 20px; /* Qendron në mes dhe ka një distancë poshtë */
        width: 150px; /* Mund ta ndryshosh në përmasat që dëshiron */
}
    </style>
</head>
<body>
    <div class="login-container">
        <img src="IMG/logo_cover.png" alt="Logo e Universitetit" class="logo">
        <h2>Hyrje</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="POST">
            <div class="input-group">
                <input type="text" name="username" class="input-field" placeholder="emri.mbiemri" required>
                <span>@unt.edu.mk</span>
            </div>
            <div class="input-group">
                <input type="password" name="password" class="input-field" placeholder="fjalëkalimi" required>
            </div>
            <button type="submit" class="login-button">Hyr</button>
        </form>
    </div>
</body>
</html>
