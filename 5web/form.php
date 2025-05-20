<?php
header('Content-Type: text/html; charset=UTF-8');

$values = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['errors'] ?? [];
$generated_credentials = $_SESSION['generated_credentials'] ?? null;
$login = $_SESSION['login'] ?? null;

try {
    $db_host = 'localhost';
    $db_name = 'u68527';
    $db_user = 'u68527';
    $db_pass = '5678625';
    
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $stmt = $pdo->query("SELECT * FROM programming_languages");
    $languages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $languages = [];
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма</title>
    <link rel="stylesheet" href="../3web/style.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #EDF5E1;
        color: #05386B;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
    }

    .login-form {
        background-color: #379683;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        width: 80%;
        max-width: 400px;
        margin-bottom: 30px;
    }

    .login-form h2 {
        color: #EDF5E1;
        margin-top: 0;
        text-align: center;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: bold;
        color: #EDF5E1;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #5CDB95;
        border-radius: 5px;
        background-color: #EDF5E1;
        box-sizing: border-box;
    }

    button {
        padding: 12px 20px;
        background-color: #5CDB95;
        color: #05386B;
        border: none;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        font-size: 16px;
    }

    button:hover {
        background-color: #8EE4AF;
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    button:active {
        transform: translateY(0);
    }

    .error {
        color: #ff6b6b;
        background-color: #fff;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
        text-align: center;
        font-weight: bold;
    }

    footer {
        background-color: #379683;
        color: #EDF5E1;
        text-align: center;
        padding: 20px 0;
        width: 100%;
        margin-top: auto;
        box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1);
    }

    footer p {
        margin: 0;
        font-size: 14px;
    }
</style>
</head>
<body>
    <h1>Форма</h1>

    <div class="form-container">
        <?php if (!empty($login)): ?>
            <p>Вы вошли как: <?= htmlspecialchars($login) ?> (<a href="login.php?action=logout">Выйти</a>)</p>
        <?php else: ?>
            <p><a href="login.php">Войти</a></p>
        <?php endif; ?>

        <?php if (!empty($generated_credentials)): ?>
            <div class="credentials">
                <h3>Ваши данные для входа:</h3>
                <p><strong>Логин:</strong> <?= htmlspecialchars($generated_credentials['login']) ?></p>
                <p><strong>Пароль:</strong> <?= htmlspecialchars($generated_credentials['password']) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php">
            <div class="form-group">
                <label for="fullname">ФИО:</label>
                <input type="text" id="fullname" name="fullname" 
                       value="<?= htmlspecialchars($values['fullname'] ?? '') ?>"
                       class="<?= !empty($errors['fullname']) ? 'error' : '' ?>" required>
                <?php if (!empty($errors['fullname'])): ?>
                    <div class="error-message">Допустимы только буквы, пробелы и дефисы (2-150 символов)</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="tel" id="phone" name="phone" 
                       value="<?= htmlspecialchars($values['phone'] ?? '') ?>"
                       class="<?= !empty($errors['phone']) ? 'error' : '' ?>" required>
                <?php if (!empty($errors['phone'])): ?>
                    <div class="error-message">Введите 10-15 цифр, можно с + в начале</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" 
                       value="<?= htmlspecialchars($values['email'] ?? '') ?>"
                       class="<?= !empty($errors['email']) ? 'error' : '' ?>" required>
                <?php if (!empty($errors['email'])): ?>
                    <div class="error-message">Введите корректный email</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="birthdate">Дата рождения:</label>
                <input type="date" id="birthdate" name="birthdate" 
                       value="<?= htmlspecialchars($values['birthdate'] ?? '') ?>"
                       class="<?= !empty($errors['birthdate']) ? 'error' : '' ?>" required>
                <?php if (!empty($errors['birthdate'])): ?>
                    <div class="error-message">Дата должна быть в прошлом</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Пол:</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="male" name="gender" value="male"
                               <?= ($values['gender'] ?? '') === 'male' ? 'checked' : '' ?> required>
                        <label for="male">Мужской</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="female" name="gender" value="female"
                               <?= ($values['gender'] ?? '') === 'female' ? 'checked' : '' ?>>
                        <label for="female">Женский</label>
                    </div>
                </div>
                <?php if (!empty($errors['gender'])): ?>
                    <div class="error-message">Укажите пол</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="languages">Любимые языки программирования:</label>
                <select id="languages" name="languages[]" multiple 
                        class="<?= !empty($errors['languages']) ? 'error' : '' ?>" required>
                    <?php foreach ($languages as $lang): ?>
                        <option value="<?= $lang['id'] ?>"
                            <?= in_array($lang['id'], $values['languages'] ?? []) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($lang['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small>Для множественного выбора удерживайте Ctrl (Windows)</small>
                <?php if (!empty($errors['languages'])): ?>
                    <div class="error-message">Выберите хотя бы один язык</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="bio">Биография:</label>
                <textarea id="bio" name="bio"><?= htmlspecialchars($values['bio'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <div class="checkbox-container">
                    <input type="checkbox" id="contract_" name="contract_" value="1"
                           <?= ($values['contract_'] ?? false) ? 'checked' : '' ?> required>
                    <label for="contract_">С контрактом ознакомлен(а)</label>
                </div>
                <?php if (!empty($errors['contract_'])): ?>
                    <div class="error-message">Необходимо подтвердить ознакомление</div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <button type="submit">Сохранить</button>
            </div>
        </form>
    </div>
    <footer>
        <p>© Кулик Д.А. Группа 27/2</p>
    </footer>
</body>
</html>
<?php
unset($_SESSION['errors'], $_SESSION['generated_credentials']);
?>