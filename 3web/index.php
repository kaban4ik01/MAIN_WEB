<?php
// Подключение к базе данных
$host = 'localhost';
$dbname = 'u68527'; 
$username = 'u68527'; 
$password = '5678625'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Валидация данных
$errors = [];

if (empty($_POST['fullname'])) {
    $errors[] = "ФИО обязательно для заполнения.";
} elseif (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s]+$/u', $_POST['fullname'])) {
    $errors[] = "ФИО должно содержать только буквы и пробелы.";
}

if (empty($_POST['phone'])) {
    $errors[] = "Телефон обязателен для заполнения.";
}

if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Введите корректный email.";
}

if (empty($_POST['birthdate'])) {
    $errors[] = "Дата рождения обязательна.";
}

if (empty($_POST['gender'])) {
    $errors[] = "Укажите пол.";
}

if (empty($_POST['languages'])) {
    $errors[] = "Выберите хотя бы один язык программирования.";
}

if (empty($_POST['contract_'])) {
    $errors[] = "Необходимо подтвердить ознакомление с контрактом.";
}

// Если есть ошибки, выводим их
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<div class='error'>$error</div>";
    }
    exit;
}

// Сохранение данных в БД
try {
    // Вставка основной информации
    $stmt = $pdo->prepare("
        INSERT INTO applications 
        (fullname, phone, email, birthdate, gender, bio, contract_) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $_POST['fullname'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['birthdate'],
        $_POST['gender'],
        $_POST['bio'],
        (int)$_POST['contract_']
    ]);

    $applicationId = $pdo->lastInsertId();

    // Вставка выбранных языков программирования
    $stmt = $pdo->prepare("
        INSERT INTO application_languages (application_id, language_id) 
        VALUES (?, ?)
    ");

    foreach ($_POST['languages'] as $languageId) {
        $stmt->execute([$applicationId, $languageId]);
    }

    echo "<div style='
    background-color: #5CDB95;
    color: #05386B;
    padding: 12px 20px;
    border-radius: 5px;
    font-weight: bold;
    margin: 20px 0;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    '>Данные успешно сохранены!</div>";
} catch (PDOException $e) {
    die("Ошибка при сохранении данных: " . $e->getMessage());
}
?>