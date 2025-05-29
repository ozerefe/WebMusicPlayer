<!-- Özer Efe Engin 20210702033 -->
<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "ozer_efe_engin";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$playlistName = isset($_GET['name']) ? $_GET['name'] : '';
$res = $conn->query("SELECT playlist_id, description, image FROM PLAYLISTS WHERE title='" . $conn->real_escape_string($playlistName) . "'");

if ($res && $res->num_rows > 0) {
    $playlistData = $res->fetch_assoc();
    $playlistId = $playlistData['playlist_id'];
    $playlistDesc = $playlistData['description'];
    $playlistImage = $playlistData['image'];
} else {
    echo "Playlist not found!";
    exit();
}

// Şarkı ekleme işlemi
$message = '';
if (isset($_POST['add_song']) && isset($_POST['song_id'])) {
    $songId = intval($_POST['song_id']);
    
    // Şarkı zaten playliste ekli mi?
    $checkDuplicate = $conn->query("SELECT * FROM PLAYLIST_SONGS WHERE playlist_id = $playlistId AND song_id = $songId");
    
    if ($checkDuplicate && $checkDuplicate->num_rows == 0) {
        $addResult = $conn->query("INSERT INTO PLAYLIST_SONGS (playlist_id, song_id, date_added) VALUES ($playlistId, $songId, NOW())");
        if ($addResult) {
            $message = '<div style="color: green; padding: 10px; background-color: #f0fff0; border-radius: 5px; margin-bottom: 20px;">Şarkı başarıyla playliste eklendi!</div>';
        } else {
            $message = '<div style="color: red; padding: 10px; background-color: #fff0f0; border-radius: 5px; margin-bottom: 20px;">Şarkı eklenirken bir hata oluştu: ' . $conn->error . '</div>';
        }
    } else {
        $message = '<div style="color: #856404; padding: 10px; background-color: #fff3cd; border-radius: 5px; margin-bottom: 20px;">Bu şarkı zaten bu playliste eklenmiş!</div>';
    }
}

// Şarkı silme işlemi
if (isset($_POST['remove_song']) && isset($_POST['song_id'])) {
    $songIdToRemove = intval($_POST['song_id']);
    
    // Şarkıyı playlistten çıkar
    $removeQuery = "DELETE FROM PLAYLIST_SONGS WHERE playlist_id = $playlistId AND song_id = $songIdToRemove";
    $removeResult = $conn->query($removeQuery);
    
    if ($removeResult) {
        $message = '<div style="color: #3c763d; padding: 10px; background-color: #dff0d8; border-radius: 5px; margin-bottom: 20px;">Şarkı playlistten çıkarıldı!</div>';
    } else {
        $message = '<div style="color: red; padding: 10px; background-color: #fff0f0; border-radius: 5px; margin-bottom: 20px;">Şarkı çıkarılırken bir hata oluştu: ' . $conn->error . '</div>';
    }
}

// Playlist silme işlemi
if (isset($_POST['delete_playlist'])) {
    // İlk önce playlist'te bulunan şarkıları sil
    $deletePlaylistSongsQuery = "DELETE FROM PLAYLIST_SONGS WHERE playlist_id = $playlistId";
    $conn->query($deletePlaylistSongsQuery);
    
    // Sonra playlist'i sil
    $deletePlaylistQuery = "DELETE FROM PLAYLISTS WHERE playlist_id = $playlistId";
    $deleteResult = $conn->query($deletePlaylistQuery);
    
    if ($deleteResult) {
        // Playlist silindiyse ana sayfaya yönlendir
        header("Location: homepage.php");
        exit();
    } else {
        $message = '<div style="color: red; padding: 10px; background-color: #fff0f0; border-radius: 5px; margin-bottom: 20px;">Playlist silinirken bir hata oluştu: ' . $conn->error . '</div>';
    }
}

// Şarkı arama işlemi
$searchResults = [];
if (isset($_GET['song_search']) && !empty($_GET['song_search'])) {
    $searchTerm = $conn->real_escape_string($_GET['song_search']);
    $searchQuery = "SELECT S.song_id, S.title AS song_title, A.name AS artist_name, C.country_name, S.image 
                   FROM SONGS S 
                   JOIN ALBUMS AL ON S.album_id = AL.album_id 
                   JOIN ARTISTS A ON AL.artist_id = A.artist_id 
                   JOIN COUNTRY C ON A.country_id = C.country_id 
                   WHERE S.title LIKE '%$searchTerm%'
                   LIMIT 10";
    
    $searchResult = $conn->query($searchQuery);
    if ($searchResult && $searchResult->num_rows > 0) {
        while ($row = $searchResult->fetch_assoc()) {
            $searchResults[] = $row;
        }
    }
}

// Playlist şarkılarını getir
$sql = "SELECT S.song_id, S.title, S.image, A.name AS artist_name, C.country_name, S.duration 
        FROM PLAYLIST_SONGS PS 
        JOIN SONGS S ON PS.song_id = S.song_id 
        JOIN ALBUMS AL ON S.album_id = AL.album_id 
        JOIN ARTISTS A ON AL.artist_id = A.artist_id 
        JOIN COUNTRY C ON A.country_id = C.country_id 
        WHERE PS.playlist_id = $playlistId
        ORDER BY PS.date_added DESC";

$result = $conn->query($sql);

// Playlist bilgilerini düzenleme işlemi
if (isset($_POST['save_playlist_info'])) {
    $newTitle = $conn->real_escape_string($_POST['new_title']);
    $newDescription = $conn->real_escape_string($_POST['new_description']);
    
    // Boş başlık kontrolü
    if (empty($newTitle)) {
        $message = '<div style="color: #a94442; padding: 10px; background-color: #f2dede; border-radius: 5px; margin-bottom: 20px;">Playlist adı boş olamaz!</div>';
    } else {
        $updateQuery = "UPDATE PLAYLISTS SET title = '$newTitle', description = '$newDescription' WHERE playlist_id = $playlistId";
        $updateResult = $conn->query($updateQuery);
        
        if ($updateResult) {
            $message = '<div style="color: #3c763d; padding: 10px; background-color: #dff0d8; border-radius: 5px; margin-bottom: 20px;">Playlist bilgileri güncellendi!</div>';
            $playlistName = $newTitle; // Mevcut sayfada güncel adı göster
            $playlistDesc = $newDescription; // Mevcut sayfada güncel açıklamayı göster
            
            // URL'i güncelle
            echo "<script>
                  window.history.replaceState({}, '', 'playlistpage.php?name=" . urlencode($newTitle) . "');
                  </script>";
        } else {
            $message = '<div style="color: red; padding: 10px; background-color: #fff0f0; border-radius: 5px; margin-bottom: 20px;">Bilgiler güncellenirken bir hata oluştu: ' . $conn->error . '</div>';
        }
    }
}


include 'playlistpage.html';
?>
