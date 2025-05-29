<!-- Özer Efe Engin 20210702033 -->
<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "ozer_efe_engin";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Kullanıcı ID al
$userResult = $conn->query("SELECT user_id FROM USERS WHERE username = '{$_SESSION['username']}'");
$userId = ($userResult && $userResult->num_rows > 0) ? $userResult->fetch_assoc()['user_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $userId > 0) {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $image = $conn->real_escape_string($_POST['image']);
    $date = date('Y-m-d');

    $insert = "INSERT INTO PLAYLISTS (user_id, title, description, date_created, image) 
               VALUES ($userId, '$title', '$description', '$date', '$image')";
    if ($conn->query($insert)) {
        header("Location: homepage.php");
        exit();
    } else {
        $error = "Playlist could not be created: " . $conn->error;
    }
}
?>



<!--Tasarım kısmı  -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add New Playlist</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #121212;
            --text-color: #f5f5f5;
            --bg-color: #181818;
            --card-color: #282828;
            --hover-color: #333333;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            padding: 40px;
            line-height: 1.6;
        }
        
        .container {
            max-width: 600px;
            margin: auto;
            background: var(--card-color);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        
        h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 25px;
            font-size: 28px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
        }
        
        input, textarea {
            width: 100%;
            padding: 12px;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--text-color);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        input:focus, textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            background-color: rgba(255, 255, 255, 0.15);
        }
        
        button {
            display: block;
            width: 100%;
            padding: 14px;
            margin-top: 25px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        button:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .error {
            color: #e74c3c;
            background-color: rgba(231, 76, 60, 0.2);
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .home-button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 18px;
            background-color: rgba(255, 255, 255, 0.2);
            color: var(--text-color);
            text-decoration: none;
            border-radius: 30px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .home-button:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>
<body>
<a href="homepage.php" class="home-button">🏠 Home</a>

<div class="container">
    <h2>Create New Playlist</h2>
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label for="title">Playlist Title</label>
            <input type="text" name="title" id="title" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" id="description" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="image">Image URL</label>
            <input type="text" name="image" id="image" required>
        </div>

        <button type="submit">Create Playlist</button>
    </form>
</div>
</body>
</html>
