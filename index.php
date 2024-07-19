<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script>
        $(document).ready(function() {
            var data = <?php echo json_encode($d); ?>;

            // Заполнение выпадающего списка для улиц на основе выбранного пункта
            $('#punkt').change(function() {
                var selectedPunkt = $(this).val();
                var streets = data[selectedPunkt];
                var streetsDropdown = $('#street');
                streetsDropdown.empty();
                $.each(streets, function(street, houses) {
                    streetsDropdown.append($('<option></option>').attr('value', street).text(street));
                });
                streetsDropdown.change();
            });

            // Заполнение выпадающего списка для домов на основе выбранной улицы
            $('#street').change(function() {
                var selectedPunkt = $('#punkt').val();
                var selectedStreet = $(this).val();
                var houses = data[selectedPunkt][selectedStreet];
                var housesDropdown = $('#house');
                housesDropdown.empty();
                $.each(houses, function(house, apartments) {
                    housesDropdown.append($('<option></option>').attr('value', house).text(house));
                });
                housesDropdown.change();
            });

            // Заполнение выпадающего списка для квартир на основе выбранного дома
            $('#house').change(function() {
                var selectedPunkt = $('#punkt').val();
                var selectedStreet = $('#street').val();
                var selectedHouse = $(this).val();
                var apartments = data[selectedPunkt][selectedStreet][selectedHouse];
                var apartmentsDropdown = $('#apartment');
                apartmentsDropdown.empty();
                $.each(apartments, function(index, apartment) {
                    apartmentsDropdown.append($('<option></option>').attr('value', apartment).text(apartment));
                });
            });
        });
    </script>
</head>
<body>
    <header class="d-flex justify-content-center py-3">
            <ul class="nav nav-pills">
            <li class="nav-item"><a href="index.php" class="nav-link active" aria-current="page">Поиск</a></li>
            <li class="nav-item"><a href="main.php" class="nav-link">Загрузка</a></li>
            </ul>
    </header>
<?php
//Создаём словарь d для выпадающих форм
include 'dictionary.php';
?>
<?php

// Обработка отправленной формы
if(isset($_POST['submit'])) {
  $option1 = $_POST['select1'];
  $option2 = $_POST['select2'];
  $option3 = $_POST['select3'];
  $option4 = $_POST['select4'];

  // Выполнение запроса к базе данных
  $sql = "SELECT * FROM your_table WHERE column1 = '$option1' AND column2 = '$option2' AND column3 = '$option3' AND column4 = '$option4'";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    // Вывод данных
    while($row = $result->fetch_assoc()) {
      echo "Значение: " . $row["column_name1"]. " - Значение: " . $row["column_name2"]. "<br>";
      // Вывод остальных полей по необходимости
    }
  } else {
    echo "0 результатов";
  }
}


// Закрытие подключения
?>

    <div class="container">
        <form action="search_results.php" method="post">
        Введите показатель для поиска: <input type="text" name="search_term">
        <input type="submit">
        </form>
        <br>
        <h2>ИЛИ</h2>
        <form method="post" action="process.php">
                <label for="punkt">Выберите пункт:</label>
                <select class="js-example-basic-single" id="punkt" name="punkt">
                    <?php
                    foreach ($d as $punkt => $streets) {
                        echo "<option value='$punkt'>$punkt</option>";
                    }
                    ?>
                </select>
                
                <label for="street">Выберите улицу:</label>
                <select class="js-example-basic-single" id="street" name="street">
                    <!-- Первоначально не будет содержать никаких значений -->
                </select>

                <label for="house">Выберите дом:</label>
                <select class="js-example-basic-single" id="house" name="house">
                    <!-- Первоначально не будет содержать никаких значений -->
                </select>

                <label for="apartment">Выберите квартиру:</label>
                <select class="js-example-basic-single" id="apartment" name="apartment">
                    <!-- Первоначально не будет содержать никаких значений -->
                </select>
                <br>
                Даю согласие на <a href="http://eric33.ru/Zachita_personal_data/" target="_blank">обработку</a> персональных данных
                <input type="checkbox" id="soglasie" name="soglasie" value="yes" style="position:relative;top:2px;">
                <br>
                <br>
                <input type="submit" name="submit" value="Отправить">
        </form>
                    
    </div>
        
    <script>
        const streetsByPunkt = <?php echo json_encode($d); ?>;
        const streetSelect = document.getElementById('street');
        const houseSelect = document.getElementById('house');
        const apartmentSelect = document.getElementById('apartment');
        const updateStreets = () => {
            // Очистить список улиц
            streetSelect.innerHTML = '';
            // Получить список улиц для выбранного пункта
            const selectedPunkt = document.getElementById('punkt').value;
            const streets = streetsByPunkt[selectedPunkt];
            // Добавить опции улиц
            for (const street in streets) {
                streetSelect.innerHTML += `<option value='${street}'>${street}</option>`;
            }
            updateHouses();
        };
        const updateHouses = () => {
            // Очистить список домов
            houseSelect.innerHTML = '';
            // Получить список домов для выбранной улицы
            const selectedPunkt = document.getElementById('punkt').value;
            const selectedStreet = document.getElementById('street').value;
            const houses = streetsByPunkt[selectedPunkt][selectedStreet];
            // Добавить опции домов
            for (const house in houses) {
                houseSelect.innerHTML += `<option value='${house}'>${house}</option>`;
            }
            updateApartments();
        };
        const updateApartments = () => {
            // Очистить список квартир
            apartmentSelect.innerHTML = '';
            // Получить список квартир для выбранного дома
            const selectedPunkt = document.getElementById('punkt').value;
            const selectedStreet = document.getElementById('street').value;
            const selectedHouse = document.getElementById('house').value;
            const apartments = streetsByPunkt[selectedPunkt][selectedStreet][selectedHouse];
            // Добавить опции квартир
            for (const apartment of apartments) {
                apartmentSelect.innerHTML += `<option value='${apartment}'>${apartment}</option>`;
            }
        };
        // Обновить список улиц при изменении выбранного пункта
        document.getElementById('punkt').onchange = updateStreets;
        document.getElementById('street').onchange = updateHouses;
        document.getElementById('house').onchange = updateApartments;
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
</body>
</html>