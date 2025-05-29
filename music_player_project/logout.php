<!-- Özer Efe Engin 20210702033 -->
<?php
session_start();
session_destroy();
header("Location: login.html");
exit();
?>
