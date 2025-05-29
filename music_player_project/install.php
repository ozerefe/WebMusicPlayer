<!-- Özer Efe Engin 20210702033 -->
<?php

$servername = "localhost";
$username = "root";
$password = "mysql";

$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$dbname = "ozer_efe_engin";
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    $message = "Database created or already exists.";
} else {
    die("Database could not be created: " . $conn->error);
}

$conn->select_db($dbname);

// Creating tables 
$queries = [
    // COUNTRY
    "CREATE TABLE IF NOT EXISTS COUNTRY (
        country_id INT AUTO_INCREMENT PRIMARY KEY,
        country_name VARCHAR(100),
        country_code VARCHAR(10)
    )",

    // USERS
    "CREATE TABLE IF NOT EXISTS USERS (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        country_id INT,
        username VARCHAR(50),
        name VARCHAR(50),
        email VARCHAR(100),
        password VARCHAR(100),
        age INT,
        date_joined DATE,
        last_login DATE,
        follower_num INT,
        subscription_type VARCHAR(50),
        top_genre VARCHAR(50),
        num_songs_liked INT,
        most_played_artist VARCHAR(100),
        image VARCHAR(255),
        FOREIGN KEY (country_id) REFERENCES COUNTRY(country_id)
    )",

    // ARTISTS
    "CREATE TABLE IF NOT EXISTS ARTISTS (
        artist_id INT AUTO_INCREMENT PRIMARY KEY,
        country_id INT,
        name VARCHAR(100),
        genre VARCHAR(50),
        date_joined DATE,
        total_num_music INT,
        total_albums INT,
        listeners INT,
        bio TEXT,
        image VARCHAR(255),
        FOREIGN KEY (country_id) REFERENCES COUNTRY(country_id)
    )",

    // ALBUMS
    "CREATE TABLE IF NOT EXISTS ALBUMS (
        album_id INT AUTO_INCREMENT PRIMARY KEY,
        artist_id INT,
        title VARCHAR(100),
        release_date DATE,
        genre VARCHAR(50),
        music_number INT,
        image VARCHAR(255),
        FOREIGN KEY (artist_id) REFERENCES ARTISTS(artist_id)
    )",

    // SONGS
    "CREATE TABLE IF NOT EXISTS SONGS (
        song_id INT AUTO_INCREMENT PRIMARY KEY,
        album_id INT,
        title VARCHAR(100),
        duration INT,
        genre VARCHAR(50),
        release_date DATE,
        song_rank INT,
        image VARCHAR(255),
        FOREIGN KEY (album_id) REFERENCES ALBUMS(album_id)
    )",

    // PLAYLISTS
    "CREATE TABLE IF NOT EXISTS PLAYLISTS (
        playlist_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        title VARCHAR(100),
        description TEXT,
        date_created DATE,
        image VARCHAR(255),
        FOREIGN KEY (user_id) REFERENCES USERS(user_id)
    )",

    // PLAYLIST_SONGS
    "CREATE TABLE IF NOT EXISTS PLAYLIST_SONGS (
        playlistsong_id INT AUTO_INCREMENT PRIMARY KEY,
        playlist_id INT,
        song_id INT,
        date_added DATE,
        FOREIGN KEY (playlist_id) REFERENCES PLAYLISTS(playlist_id),
        FOREIGN KEY (song_id) REFERENCES SONGS(song_id)
    )",

    // PLAY_HISTORY
    "CREATE TABLE IF NOT EXISTS PLAY_HISTORY (
        play_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        song_id INT,
        playtime DATETIME,
        FOREIGN KEY (user_id) REFERENCES USERS(user_id),
        FOREIGN KEY (song_id) REFERENCES SONGS(song_id)
    )",

    // FOLLOWS
    "CREATE TABLE IF NOT EXISTS FOLLOWS (
        follow_id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        artist_id INT,
        followed_date DATETIME,
        FOREIGN KEY (user_id) REFERENCES USERS(user_id),
        FOREIGN KEY (artist_id) REFERENCES ARTISTS(artist_id),
        UNIQUE KEY (user_id, artist_id)
    )"
];

foreach ($queries as $q) {
    $conn->query($q);
}

$conn->close();
?>

<!--Tasarım kısmı -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Musically - Database Setup</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: "Segoe UI", sans-serif;
      background: linear-gradient(to bottom right, #2980b9, #020406);
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .container {
      background-color: #fff;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
      width: 90%;
      max-width: 500px;
      text-align: center;
    }

    h1 {
      margin-bottom: 20px;
      font-size: 1.8rem;
      color: #333;
    }

    p {
      font-size: 1rem;
      color: #555;
      margin-bottom: 20px;
    }

    button {
      width: 100%;
      padding: 14px;
      background-color: #1AA34A;
      border: none; 
      color: white;
      font-size: 1rem;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button:hover {
      background-color: #1AA34A;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Database Successfully Installed</h1>
    <p><?php echo $message; ?></p>

    <form action="generate_data.php" method="post">
      <button type="submit">Create output.sql file</button>
    </form>
  </div>
</body>
</html>
