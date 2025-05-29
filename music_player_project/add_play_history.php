<!-- Özer Efe Engin 20210702033 -->
<?php

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "ozer_efe_engin";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// POST verilerini al ve güvenli hale getir
$userId = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$songId = isset($_POST['song_id']) ? intval($_POST['song_id']) : 0;

// Kayıt için kontrol
if ($userId > 0 && $songId > 0) {
    // Şarkı geçmişine ekle
    $sql = "INSERT INTO PLAY_HISTORY (user_id, song_id, playtime) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $userId, $songId);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "invalid parameters";
}

$conn->close();
?> 