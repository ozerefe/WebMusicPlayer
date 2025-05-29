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

// Varsayılan sorgular
$defaultQueries = [
    "Top 5 Most Listened Artists" => 
        "SELECT A.name, A.listeners, C.country_name, A.image 
         FROM ARTISTS A 
         JOIN COUNTRY C ON A.country_id = C.country_id 
         ORDER BY A.listeners DESC 
         LIMIT 5",
    
    "Number of Artists by Country" => 
        "SELECT C.country_name, COUNT(A.artist_id) as artist_count 
         FROM COUNTRY C 
         JOIN ARTISTS A ON C.country_id = A.country_id 
         GROUP BY C.country_name 
         ORDER BY artist_count DESC 
         LIMIT 5",
    
    "Most Played Songs" => 
        "SELECT S.title, COUNT(PH.play_id) as play_count, A.name as artist_name, S.image 
         FROM SONGS S 
         JOIN PLAY_HISTORY PH ON S.song_id = PH.song_id 
         JOIN ALBUMS AL ON S.album_id = AL.album_id 
         JOIN ARTISTS A ON AL.artist_id = A.artist_id 
         GROUP BY S.song_id 
         ORDER BY play_count DESC 
         LIMIT 5",
    
    "Users with Most Playlists" => 
        "SELECT U.name, COUNT(P.playlist_id) as playlist_count, C.country_name 
         FROM USERS U 
         JOIN PLAYLISTS P ON U.user_id = P.user_id 
         JOIN COUNTRY C ON U.country_id = C.country_id 
         GROUP BY U.user_id 
         ORDER BY playlist_count DESC 
         LIMIT 5",
    
    "Number of Users by Country" => 
        "SELECT C.country_name, COUNT(U.user_id) as user_count 
         FROM COUNTRY C 
         JOIN USERS U ON C.country_id = U.country_id 
         GROUP BY C.country_name 
         ORDER BY user_count DESC 
         LIMIT 5"
];

// Seçili sorgu veya kullanıcı tanımlı SQL
$results = [];
$columns = [];
$executeStatus = "";
$selectedQuery = isset($_GET['query']) ? $_GET['query'] : null;
$customSql = isset($_POST['custom_sql']) ? trim($_POST['custom_sql']) : "";

// Sorgu çalıştırılıyor
if (!empty($customSql)) {
    try {
        $result = $conn->query($customSql);
        
        if ($result) {
            // Sütun başlıklarını al
            $columns = [];
            $metadata = $result->fetch_fields();
            foreach ($metadata as $field) {
                $columns[] = $field->name;
            }
            
            // Sonuçları al (maksimum 5 satır)
            $counter = 0;
            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
                $counter++;
                if ($counter >= 5) break;
            }
            
            if (count($results) > 0) {
                $executeStatus = "<div class='success'>Query executed successfully.</div>";
            } else {
                $executeStatus = "<div class='warning'>Query executed, but no results found.</div>";
            }
        } else {
            $executeStatus = "<div class='error'>Query error: " . $conn->error . "</div>";
        }
    } catch (Exception $e) {
        $executeStatus = "<div class='error'>Error: " . $e->getMessage() . "</div>";
    }
} else if ($selectedQuery && isset($defaultQueries[$selectedQuery])) {
    $sql = $defaultQueries[$selectedQuery];
    
    $result = $conn->query($sql);
    if ($result) {
        // Sütun başlıklarını al
        $columns = [];
        $metadata = $result->fetch_fields();
        foreach ($metadata as $field) {
            $columns[] = $field->name;
        }
        
        // Sonuçları al
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
    }
}


include 'generalSQL.html';

$conn->close();
?> 