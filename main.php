<?php

// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "php-mysql";

// Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}

// очистка база данных
if (isset($_POST['delete_records'])) {
    $sql = "DELETE FROM pars LIMIT 1000000";
    if ($conn->query($sql) === TRUE) {
        echo "База данных очищена";
    } else {
        echo "Ошибка при удалении строк: " . $conn->error;
    }
}

$conn->close();
?>
<!doctype html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Загрузка</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  </head>
  <body>
  <header class="d-flex justify-content-center py-3">
            <ul class="nav nav-pills">
            <li class="nav-item"><a href="index.php" class="nav-link active" aria-current="page">Поиск</a></li>
            <li class="nav-item"><a href="main.php" class="nav-link">Загрузка</a></li>
            </ul>
        </header>
    <div class="container">
        <h1>Загрузка файла с данными</h1>

        <form action="upload1.php" method="post" enctype="multipart/form-data">
            <input type="file" name="fileToUpload" id="fileToUpload" accept=".txt">
            <input type="submit" value="Upload File" name="submit">
        </form>
        <br>
        <br>
        <form method="post">
            <button type="submit" name="delete_records">Очистка база данных</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  </body>
</html>