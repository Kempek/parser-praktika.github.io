<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ошибки</title>
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
        <a href="error_log.txt" download>Скачать файл с некорректными строками</a>
    </div>
</body>
</html>
<?
ini_set('max_execution_time', 120);
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"])) {
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["fileToUpload"]["name"]);
    $fileType = pathinfo($targetFile, PATHINFO_EXTENSION);

    if ($fileType === "txt") {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
            $file = $targetFile;
            $mysql = new mysqli("localhost", "root", "", "php-mysql");

            if ($mysql->connect_error) {
                die('Ошибка подключения (' . $mysql->connect_errno . ') ' . $mysql->connect_error);
            }

            $errorLog = '';
            $d = [];

            if (file_exists($file)) {
                $handle = fopen($file, "r");

                if ($handle) {
                    $mysql->query("SET NAMES 'utf8'");
                    $logError = fopen("error_log.txt", "a");

                    while (($line = fgets($handle)) !== false) {
                        $data = mb_convert_encoding($line, 'UTF-8', 'Windows-1251');
                        $line = mb_convert_encoding($line, 'UTF-8', 'Windows-1251');//для логирования
                        $data = explode(';', $data);
                        $data = array_map('trim', $data);

                        if (count($data) < 4) {
                            $errorLog .= "Ошибка меньше 4 элементов данных в строке: " . $line . "\n";
                            continue;
                        }

                        $firstFiveElements = array_slice($data, 0, 5);
                        $remainingElements = array_slice($data, 5);

                        $escapedData = array_map(function ($item) use ($mysql) {
                            return $mysql->real_escape_string($item);
                        }, $firstFiveElements);

                        $query = "INSERT INTO pars (lic_chet, FIO, adres, data_month, arrears, value_chet) 
                            VALUES ('{$escapedData[0]}', '{$escapedData[1]}', '{$escapedData[2]}', '{$escapedData[3]}', '{$escapedData[4]}', '{$mysql->real_escape_string(implode(';', $remainingElements))}')";

                        try {
                            if (!$mysql->query($query)) {
                                $errorLog .= "Ошибка в значениях или их формате в строке: " . $line . "\n";
                            }
                        } catch (mysqli_sql_exception $e) {
                            // обработка исключения, например, добавление информации об ошибке в $errorLog
                            $errorLog .= "Ошибка в значениях или их формате в строке: " . $line . "\n";
                        }

                        $adres = explode(',', $data[2]);
                        $k = count($adres);
                        // Добавление в словарь
                        if ($k <= 3) {                                
                            if ($k == 3 && ctype_alpha($adres[1])) {
                                $adres[] = '0';
                            }
                            if ($k == 3 && !ctype_alpha($adres[1])) {
                                array_splice($adres, 1, 0, 'Нет улицы');
                            }
                            if ($k == 2 && preg_match('/\d/', $adres[1])) {
                                $adres[] = '0';
                                array_splice($adres, 1, 0, 'Нет улицы');
                            }
                        }
                        if (count($adres) < 4){
                            $errorLog .= "Ошибка в адресе в строке: " . $line . "\n";
                            continue;
                        }

                        $punkt = $adres[0];
                        $street = $adres[1];
                        $dom = $adres[2];
                        $kv = $adres[3];

                        // Добавляем первый элемент в массив $d
                        if (!array_key_exists($punkt, $d)) {
                            $d[$punkt] = [$street => [$dom => [$kv]]];
                        } else {
                            if (!array_key_exists($street, $d[$punkt])) {
                                $d[$punkt][$street] = [$dom => [$kv]];
                            } else {
                                if (!array_key_exists($dom, $d[$punkt][$street])) {
                                    $d[$punkt][$street][$dom] = [$kv];
                                } else {
                                    array_push($d[$punkt][$street][$dom], $kv);
                                }
                            }
                        }
                    }

                    fclose($logError);
                    fclose($handle);

                    if (!empty($errorLog)) {
                        file_put_contents("error_log.txt", $errorLog, FILE_APPEND);
                    }

                    file_put_contents('dictionary.php', '<?php $d = ' . var_export($d, true) . ';');
                } else {
                    echo 'Не удалось открыть файл';
                }
            } else {
                echo 'Файл не найден';
            }

            $mysql->close();
        } else {
            echo "Произошла ошибка при загрузке файла.";
        }
    } else {
        echo "Недопустимый формат файла. Пожалуйста, загрузите файл с расширением .txt.";
    }
}
?>