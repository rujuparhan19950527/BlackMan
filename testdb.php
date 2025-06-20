<?php
$link = mysqli_connect('localhost', 'your_db_user', 'your_db_password', 'your_db_name');
if (!$link) {
    die('Could not connect: ' . mysqli_connect_error());
}
echo 'Connected successfully';
?>
