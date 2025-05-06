<?php
header('Content-Type: text/html; charset=UTF-8');


$db_host = 'localhost';
$db_name = 'u68527';
$db_user = 'u68527';
$db_pass = '5678625';


$messages = array();
$errors = array();
$values = array();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
   
    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', time() - 3600);
        $messages[] = '<div class="success">Результаты сохранены.</div>';
    }

   
    $error_fields = array(
        'fullname', 'phone', 'email', 'birthdate', 
        'gender', 'languages', 'contract_'
    );
    
    foreach ($error_fields as $field) {
        $errors[$field] = !empty($_COOKIE[$field.'_error']);
        if ($errors[$field]) {
            setcookie($field.'_error', '', time() - 3600);
        }
    }

   
    $value_fields = array(
        'fullname', 'phone', 'email', 'birthdate', 
        'gender', 'bio', 'contract_'
    );
    
    foreach ($value_fields as $field) {
        $values[$field] = empty($_COOKIE[$field.'_value']) ? '' : $_COOKIE[$field.'_value'];
    }

   
    $values['languages'] = empty($_COOKIE['languages_value']) ? array() : explode(',', $_COOKIE['languages_value']);
    $values['contract_'] = !empty($_COOKIE['contract_value']);

    
    include('form.php');
}
else {
   
    $validation_failed = false;

   
    if (empty($_POST['fullname']) || !preg_match('/^[а-яА-ЯёЁa-zA-Z\s\-]{2,150}$/u', $_POST['fullname'])) {
        setcookie('fullname_error', '1', 0); 
        $validation_failed = true;
    }
    setcookie('fullname_value', $_POST['fullname'], time() + 365 * 24 * 60 * 60); 

    if (empty($_POST['phone']) || !preg_match('/^\+?\d{10,15}$/', $_POST['phone'])) {
        setcookie('phone_error', '1', 0);
        $validation_failed = true;
    }
    setcookie('phone_value', $_POST['phone'], time() + 365 * 24 * 60 * 60);

    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        setcookie('email_error', '1', 0);
        $validation_failed = true;
    }
    setcookie('email_value', $_POST['email'], time() + 365 * 24 * 60 * 60);

    $today = new DateTime();
    $birthdate = DateTime::createFromFormat('Y-m-d', $_POST['birthdate']);
    if (empty($_POST['birthdate']) || !$birthdate || $birthdate > $today) {
        setcookie('birthdate_error', '1', 0);
        $validation_failed = true;
    }
    setcookie('birthdate_value', $_POST['birthdate'], time() + 365 * 24 * 60 * 60);

    if (empty($_POST['gender']) || !in_array($_POST['gender'], ['male', 'female'])) {
        setcookie('gender_error', '1', 0);
        $validation_failed = true;
    }
    setcookie('gender_value', $_POST['gender'], time() + 365 * 24 * 60 * 60);

    $allowedLanguages = range(1, 12);
    if (empty($_POST['languages'])) {
        setcookie('languages_error', '1', 0);
        $validation_failed = true;
    } else {
        foreach ($_POST['languages'] as $langId) {
            if (!in_array($langId, $allowedLanguages)) {
                setcookie('languages_error', '1', 0);
                $validation_failed = true;
                break;
            }
        }
    }
    setcookie('languages_value', implode(',', $_POST['languages']), time() + 365 * 24 * 60 * 60);

    if (empty($_POST['contract_'])) {
        setcookie('contract_error', '1', 0);
        $validation_failed = true;
    }
    setcookie('contract_value', isset($_POST['contract_']) ? '1' : '', time() + 365 * 24 * 60 * 60);

    if ($validation_failed) {
        header('Location: index.php');
        exit();
    }
    else {
       
        $error_cookies = array(
            'fullname_error', 'phone_error', 'email_error', 
            'birthdate_error', 'gender_error', 'languages_error', 
            'contract_error'
        );
        
        foreach ($error_cookies as $cookie) {
            setcookie($cookie, '', time() - 3600);
        }

        
        try {
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
           
            $stmt = $pdo->prepare("INSERT INTO applications (fullname, phone, email, birthdate, gender, bio, contract_) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['fullname'],
                $_POST['phone'],
                $_POST['email'],
                $_POST['birthdate'],
                $_POST['gender'],
                $_POST['bio'],
                isset($_POST['contract_']) ? 1 : 0
            ]);
            
            $appId = $pdo->lastInsertId();
            
           
            $stmt = $pdo->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
            foreach ($_POST['languages'] as $langId) {
                $stmt->execute([$appId, $langId]);
            }
            
            setcookie('save', '1', time() + 24 * 60 * 60);
            header('Location: index.php');
        } catch (PDOException $e) {
            setcookie('database_error', '1', time() + 24 * 60 * 60);
            header('Location: index.php');
            exit();
        }
    }
}
?>