<?php
// Получить значения из POST
$punkt = $_POST['punkt'];
$street = $_POST['street'];
$house = $_POST['house'];
$apartment = $_POST['apartment'];

// Соединить адрес в одну строку
$fullAddress = implode(', ', array_map('trim', array($punkt, $street, $house, $apartment)));

// Подключение к базе данных
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "php-mysql";

// Создание подключения
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$stmt = $conn->prepare("SELECT * FROM `pars` WHERE `adres` = ?");
$stmt->bind_param("s", $fullAddress);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc(); // Получаем результаты запроса здесь

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
        if ($row) {
          $inputString = $row['value_chet'];
          $inputArrears = $row['arrears'];          // Разбиваем строку по запятым
          // Обрабатываем каждую часть
          $parts = explode(';', $inputString);
          // Обрабатываем каждую часть
          echo "Задолжность - $inputArrears";
          echo "<br>";
          for ($i = 0; $i < count($parts); $i += 2) {
              $counter = isset($parts[$i]) ? $parts[$i] : '';
              $value = isset($parts[$i + 1]) ? $parts[$i + 1] : '';
              echo "Счётчик - $counter Показатели - $value<br>";
          }
      } else {
          echo "Нет результатов";
      }
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  </body>
</html>