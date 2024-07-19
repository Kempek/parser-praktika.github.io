<?php
// Установка соединения с базой данных
$conn = new mysqli("localhost","root","","php-mysql");

// Проверяем соединение
if ($conn->connect_error) {
    die("Ошибка соединения: " . $conn->connect_error);
}

// Получаем показатель из формы
$search_term = $_POST["search_term"];

// Выполняем запрос к базе данных
$sql = "SELECT * FROM pars WHERE lic_chet = '$search_term'";
$result = $conn->query($sql);

// Выводим результаты

// Закрываем соединение
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
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "Задолжность: " . $row["arrears"]. "<br>";
                $parts = explode(';', $row['value_chet']);
                for ($i = 0; $i < count($parts); $i += 2) {
                    $counter = isset($parts[$i]) ? $parts[$i] : '';
                    $value = isset($parts[$i + 1]) ? $parts[$i + 1] : '';
                    echo "Счётчик - $counter Показатели - $value<br>";
                }
            }
        } else {
            echo "Ничего не найдено для показателя: " . $search_term;
        }
        
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  </body>
</html>