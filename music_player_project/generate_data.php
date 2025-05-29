<!-- Özer Efe Engin 20210702033 -->
<?php
ob_start();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Veri Oluşturma</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      box-sizing: border-box;
      text-align: center;
    }
    .container h3 {
      color: #333;
      margin-bottom: 10px;
    }
    .container p {
      color: #555;
      margin-bottom: 20px;
    }
    .container button {
      width: 100%;
      padding: 14px;
      background-color: #1DB954;
      border: none;
      color: white;
      font-size: 1rem;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .container button:hover {
      background-color: #1AA34A;
    }
  </style>
</head>
<body>
  <div class="container">
    <?php
    $user_names = file('input_names.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $artist_names = file('artist_names.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $album_titles = file('album_titles.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $song_titles = file('song_titles.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $artist_images = file('artist_images.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $album_images = file('album_images.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $playlist_images = file('playlist_images.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $all_countries = file('all_countries_with_codes.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if (!$user_names || count($user_names) < 80) die("<p>Hata: input_names.txt en az 80 isim içermeli!</p>");
    if (!$artist_names || count($artist_names) < 100) die("<p>Hata: artist_names.txt en az 100 isim içermeli!</p>");
    if (!$album_titles || count($album_titles) < 200) die("<p>Hata: album_titles.txt en az 200 isim içermeli!</p>");
    if (!$song_titles || count($song_titles) < 1000) die("<p>Hata: song_titles.txt en az 1000 isim içermeli!</p>");
    if (!$all_countries || count($all_countries) < 10) die("<p>Hata: all_countries_with_codes.txt en az 10 ülke içermeli!</p>");

    $outputFile = fopen("output.sql", "w");
    if (!$outputFile) die("<p>output.sql dosyası oluşturulamadı!</p>");

    // Ülkeleri txt dosyasından oku ve işle
    $countries = [];
    $country_id = 1;
    
    // Rastgele 50 ülke seç
    shuffle($all_countries);
    $selected_countries = array_slice($all_countries, 0, 25);
    
    foreach ($selected_countries as $country_entry) {
        // Ülke adını ve kodunu parantezlere göre ayır
        preg_match('/(.+) \(([A-Z]{2})\)/', $country_entry, $matches);
        
        if (count($matches) === 3) {
            $country_name = trim($matches[1]);
            $country_code = $matches[2];
            
            $countries[] = [$country_name, $country_code];
            
            // SQL kaydını oluştur
            $escaped_name = str_replace("'", "''", $country_name);
            fwrite($outputFile, "INSERT INTO COUNTRY (country_id, country_name, country_code) VALUES ($country_id, '{$escaped_name}', '{$country_code}');\n");
            $country_id++;
        }
    }

    $imageMap = [];
    foreach ($artist_images as $line) {
        list($artist, $url) = explode('|', trim($line));
        $imageMap[$artist] = $url;
    }

    for ($i = 1; $i <= 100; $i++) { // Her artist'e 1 albüm ve 1 şarkı ataması
        $cid = rand(1, count($countries));
        $name = $user_names[array_rand($user_names)];
        $username = "user$i";
        $email = "user$i@example.com";
        $password = "password$i";
        $age = rand(18, 50);
        $date = date('Y-m-d');
        $follower = rand(0, 500);
        $sub = (rand(0,1) ? 'Premium' : 'Free');
        $genres = ['Pop', 'Rap', 'Arabesk'];
        $genre = $genres[array_rand($genres)];
        $num_liked = rand(0, 100);
        $most_artist = $artist_names[array_rand($artist_names)];
        $image = "https://picsum.photos/seed/user$i/100";

        fwrite($outputFile, "INSERT INTO USERS (user_id, country_id, username, name, email, password, age, date_joined, last_login, follower_num, subscription_type, top_genre, num_songs_liked, most_played_artist, image) VALUES ($i, $cid, '$username', '$name', '$email', '$password', $age, '$date', '$date', $follower, '$sub', '$genre', $num_liked, '$most_artist', '$image');\n");
    }

    //  ARTISTS tablosu: Albümlere referans verilmeden önce oluşturulmalı
for ($i = 1; $i <= 100; $i++) {
    $cid = rand(1, count($countries));
    $name = $artist_names[$i - 1];
    $genres = ['Pop', 'Rap', 'Arabesk'];
    $genre = $genres[array_rand($genres)];
    $date = date('Y-m-d');
    $music_num = rand(10, 100);
    $album_num = rand(1, 10);
    $listeners = rand(1000, 100000);
    $bio = "Bio of $name";
    $image = isset($imageMap[$name]) ? $imageMap[$name] : "https://picsum.photos/seed/artist$i/300";

    fwrite($outputFile, "INSERT INTO ARTISTS (artist_id, country_id, name, genre, date_joined, total_num_music, total_albums, listeners, bio, image) VALUES ($i, $cid, '$name', '$genre', '$date', $music_num, $album_num, $listeners, '$bio', '$image');
");
}

// Her artist için 1 albüm ve o albümde 1 şarkı oluşturuluyor
    $albumImageMap = []; // Albüm ID'lerine göre görsel eşlemesi için kullanılıyor
    $albumCounter = 1; // Albüm ID sayaç
    $songCounter = 1; // Şarkı ID sayaç
    for ($i = 1; $i <= 100; $i++) {
        $title = $album_titles[$albumCounter - 1];
        $date = date('Y-m-d');
        $genres = ['Pop', 'Rap', 'Arabesk'];
        $genre = $genres[array_rand($genres)];
        $music_num = 1;
        $image = isset($album_images[$albumCounter - 1]) ? trim($album_images[$albumCounter - 1]) : "https://picsum.photos/seed/album$albumCounter/300";
        $albumImageMap[$albumCounter] = $image;

        fwrite($outputFile, "INSERT INTO ALBUMS (album_id, artist_id, title, release_date, genre, music_number, image) VALUES ($albumCounter, $i, '$title', '$date', '$genre', $music_num, '$image');\n");

        $songTitle = $song_titles[$songCounter - 1];
        $duration = rand(180, 300);
        $song_rank = rand(1, 1000);
        $songImage = $image;

        fwrite($outputFile, "INSERT INTO SONGS (song_id, album_id, title, duration, genre, release_date, song_rank, image) VALUES ($songCounter, $albumCounter, '$songTitle', $duration, '$genre', '$date', $song_rank, '$songImage');\n");

        $albumCounter++;
        $songCounter++;
    }

    for ($i = 101; $i <= 200; $i++) { // 🔁 Geriye kalan albümler rastgele artistlere atanıyor
        $artist_id = rand(1, 100);
        $title = $album_titles[$i - 1];
        $date = date('Y-m-d');
        $genres = ['Pop', 'Rap', 'Arabesk'];
        $genre = $genres[array_rand($genres)];
        $music_num = rand(5, 15);
        $image = isset($album_images[$i - 1]) ? trim($album_images[$i - 1]) : "https://picsum.photos/seed/album$i/300";
        $albumImageMap[$i] = $image;

        fwrite($outputFile, "INSERT INTO ALBUMS (album_id, artist_id, title, release_date, genre, music_number, image) VALUES ($i, $artist_id, '$title', '$date', '$genre', $music_num, '$image');\n");
    }

    shuffle($song_titles); // ✅ Aynı şarkı başkasına verilmesin diye karıştır
for ($i = $songCounter; $i <= 1000; $i++) { // 🔁 Geriye kalan şarkılar rastgele albümlere atanıyor
    $album_id = rand(1, 200);
    $title = $song_titles[$i - 1];
    $duration = rand(180, 300);
    $genres = ['Pop', 'Rap', 'Arabesk'];
    $genre = $genres[array_rand($genres)];
    $date = date('Y-m-d');
    $song_rank = rand(1, 1000);
    $image = isset($albumImageMap[$album_id]) ? $albumImageMap[$album_id] : "https://picsum.photos/seed/album$album_id/300";

    fwrite($outputFile, "INSERT INTO SONGS (song_id, album_id, title, duration, genre, release_date, song_rank, image) VALUES ($i, $album_id, '$title', $duration, '$genre', '$date', $song_rank, '$image');
");
}

    for ($i = 1; $i <= 500; $i++) {
        $user_id = rand(1, 100);
        $title = "Playlist $i";
        $desc = "Description $i";
        $date = date('Y-m-d');
        $image = isset($playlist_images[$i - 1]) ? trim($playlist_images[$i - 1]) : "https://picsum.photos/seed/playlist$i/200";

        fwrite($outputFile, "INSERT INTO PLAYLISTS (playlist_id, user_id, title, description, date_created, image) VALUES ($i, $user_id, '$title', '$desc', '$date', '$image');\n");
    }

    for ($i = 1; $i <= 500; $i++) {
        $playlist_id = rand(1, 500);
        $song_id = rand(1, 1000);
        $date = date('Y-m-d');

        fwrite($outputFile, "INSERT INTO PLAYLIST_SONGS (playlistsong_id, playlist_id, song_id, date_added) VALUES ($i, $playlist_id, $song_id, '$date');\n");
    }

    for ($i = 1; $i <= 100; $i++) {
        $user_id = rand(1, 100);
        $song_id = rand(1, 1000);
        $playtime = date('Y-m-d H:i:s');

        fwrite($outputFile, "INSERT INTO PLAY_HISTORY (play_id, user_id, song_id, playtime) VALUES ($i, $user_id, $song_id, '$playtime');\n");
    }

    fclose($outputFile);
    echo "<h3>✅ output.sql başarıyla oluşturuldu!</h3>";
    echo "<p>PhpMyAdmin → <strong>Import</strong> sekmesinden output.sql dosyasını içe aktarabilirsiniz.</p>";
    ?>
    <form action='login.html' method='get'>
      <button type='submit'>Login Sayfasına Git</button>
    </form>
  </div>
</body>
</html>
<?php
ob_end_flush(); ?>
