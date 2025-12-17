<?php
$password_baru = "admin123"; 
$hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);

echo "Copy kode ini ke database: <br><br>";
echo $hash_baru;
?>