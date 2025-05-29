<!-- Özer Efe Engin 20210702033 -->
<?php
// Veritabanı bağlantı bilgileri
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "ozer_efe_engin";

try {
    // PDO ile veritabanına bağlan
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Veritabanını sil
    $conn->exec("DROP DATABASE IF EXISTS `$dbname`");

    echo "<h2>Veritabanı başarıyla silindi: $dbname</h2>";
    echo "<a href='index.html'>Ana Sayfaya Dön</a>";
} catch(PDOException $e) {
    echo "Hata: " . $e->getMessage();
}


$conn = null;
?>
