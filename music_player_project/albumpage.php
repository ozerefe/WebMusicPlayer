<!-- Özer Efe Engin 20210702033 -->
<?php
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "ozer_efe_engin";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$albumTitle = isset($_GET['name']) ? $conn->real_escape_string($_GET['name']) : '';
$sql = "SELECT A.*, AR.name AS artist_name FROM ALBUMS A 
        JOIN ARTISTS AR ON A.artist_id = AR.artist_id 
        WHERE A.title = '$albumTitle'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $album = $result->fetch_assoc();
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title><?php echo htmlspecialchars($album['title']); ?> - Album Page</title>
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
                margin: 20px; 
                background-color: var(--bg-color);
                color: var(--text-color);
                line-height: 1.6;
            }
            
            .container { 
                max-width: 800px; 
                margin: auto; 
                background-color: var(--card-color);
                padding: 25px;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            }
            
            .header { 
                display: flex; 
                align-items: center; 
                gap: 25px; 
                margin-bottom: 20px;
            }
            
            .header img { 
                width: 180px; 
                height: 180px; 
                object-fit: cover; 
                border-radius: 12px; 
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            }
            
            h1, h2 { 
                border-bottom: 1px solid rgba(255, 255, 255, 0.1); 
                padding-bottom: 10px; 
                margin-bottom: 15px;
                color: var(--primary-color);
            }
            
            .song { 
                margin: 15px 0; 
                padding: 10px;
                border-radius: 8px;
                background-color: rgba(255, 255, 255, 0.05);
                transition: all 0.3s ease;
            }
            
            .song:hover {
                background-color: var(--hover-color);
                transform: translateX(5px);
            }
            
            .song-item {
                display: flex;
                align-items: center;
                padding: 12px;
                margin: 12px 0;
                border-radius: 8px;
                background-color: rgba(255, 255, 255, 0.05);
                transition: all 0.3s ease;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .song-item:hover {
                background-color: var(--hover-color);
                transform: translateX(5px);
            }
            
            .song-image {
                width: 50px;
                height: 50px;
                object-fit: cover;
                border-radius: 8px;
                margin-right: 15px;
            }
            
            .song-info {
                flex: 1;
            }
            
            .song-title {
                font-weight: 600;
                color: var(--text-color);
                margin: 0;
                font-size: 16px;
            }
            
            .song-artist {
                color: var(--text-color);
                opacity: 0.7;
                font-size: 14px;
                margin: 3px 0 0 0;
            }
            
            .play-button {
                margin-right: 15px;
                background-color: var(--primary-color);
                color: white;
                border: none;
                border-radius: 20px;
                padding: 6px 12px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-family: 'Poppins', sans-serif;
                font-size: 14px;
            }
            
            .play-button:hover {
                background-color: #2980b9;
                transform: scale(1.05);
            }
            
            .song-duration {
                color: var(--text-color);
                opacity: 0.7;
                font-size: 14px;
            }
            
            a { 
                text-decoration: none; 
                color: var(--text-color); 
                display: block;
                transition: color 0.3s;
            }
            
            a:hover { 
                color: var(--primary-color); 
            }
            
            p {
                margin: 10px 0;
                opacity: 0.9;
            }
            
            strong {
                color: var(--primary-color);
                font-weight: 500;
            }
            
            .home-button {
                position: absolute;
                top: 20px;
                right: 20px;
                padding: 8px 15px;
                background-color: rgba(255, 255, 255, 0.2);
                color: var(--text-color);
                text-decoration: none;
                border-radius: 20px;
                font-size: 14px;
                transition: all 0.3s ease;
                font-family: 'Poppins', sans-serif;
            }
            
            .home-button:hover {
                background-color: rgba(255, 255, 255, 0.3);
                transform: translateY(-2px);
            }
            
            /* Özel scrollbar */
            ::-webkit-scrollbar {
                width: 6px;
            }
            
            ::-webkit-scrollbar-track {
                background: var(--bg-color);
            }
            
            ::-webkit-scrollbar-thumb {
                background: var(--primary-color);
                border-radius: 6px;
            }
        </style>
        <script>
            // Event listener for song play buttons
            document.addEventListener('DOMContentLoaded', function() {
                const playButtons = document.querySelectorAll('.play-button[data-song]');
                playButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const songName = this.getAttribute('data-song');
                        window.location.href = 'currentmusic.php?name=' + encodeURIComponent(songName);
                    });
                });
            });
        </script>
    </head>
    <body>
    <a href="homepage.php" class="home-button">🏠 Home</a>
    <div class="container">
        <div class="header">
            <img src="<?php echo htmlspecialchars($album['image']); ?>" alt="Album Image">
            <div>
                <h1><?php echo htmlspecialchars($album['title']); ?></h1>
                <p><strong>Artist:</strong> <a href="artistpage.php?name=<?php echo urlencode($album['artist_name']); ?>" style="display: inline;"><?php echo htmlspecialchars($album['artist_name']); ?></a></p>
                <p><strong>Genre:</strong> <?php echo htmlspecialchars($album['genre']); ?></p>
                <p><strong>Release Date:</strong> <?php echo htmlspecialchars($album['release_date']); ?></p>
                <?php
                // Şarkı sayısını hesapla
                $albumId = $album['album_id'];
                $songCountQuery = $conn->query("SELECT COUNT(*) as song_count FROM SONGS WHERE album_id = $albumId");
                $actualSongCount = $songCountQuery ? $songCountQuery->fetch_assoc()['song_count'] : 0;
                ?>
                <p><strong>Number of Songs:</strong> <?php echo $actualSongCount; ?></p>
            </div>
        </div>

        <h2>Song List</h2>
        <?php
        $albumId = $album['album_id'];
        $songs = $conn->query("SELECT * FROM SONGS WHERE album_id = $albumId ORDER BY song_rank ASC");
        
        // Gerçek şarkı sayısını hesapla
        $actualSongCount = $songs ? $songs->num_rows : 0;
        
        if ($songs && $songs->num_rows > 0) {
            while ($song = $songs->fetch_assoc()) {
                echo "
                <div class='song-item'>
                    <img src='" . htmlspecialchars($song['image']) . "' alt='Song Image' class='song-image'>
                    <div class='song-info'>
                        <p class='song-title'>" . htmlspecialchars($song['title']) . "</p>
                        <p class='song-artist'>" . htmlspecialchars($album['artist_name']) . "</p>
                    </div>
                    <button class='play-button' data-song='" . htmlspecialchars($song['title']) . "'>▶️ Play</button>
                    <div class='song-duration'>" . gmdate("i:s", $song['duration']) . "</div>
                </div>";
            }
        } else {
            echo "<p>No songs found in this album.</p>";
        }
        ?>
    </div>
    </body>
    </html>
    <?php
} else {
    echo "<p>Album not found.</p>";
}
$conn->close();
?>
