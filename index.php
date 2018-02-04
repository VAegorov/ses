<?php

$link = mysqli_connect('localhost', 'root', '', 'test');
mysqli_set_charset($link, 'utf8');

//генератор паролей
function setPassword()
{
    $a = md5(mt_rand(1,100));
    $b = md5(mt_rand(1,100));
    $c = str_shuffle($a . $b);
    return $d = substr($c, 2, 8);
}

//генератор соли
function generatorSalt()
{
   $salt = '';
   $saltlength = 8;
   for ($i = 0; $i < $saltlength; $i++) {
       $salt .= chr(mt_rand(33, 126));
   }
   return $salt;
}

//авторизация
if (isset($_POST['authoriz'])) {
    $login = mysqli_real_escape_string($link, trim($_POST['login']));
    $query = sprintf("SELECT * FROM authoriz WHERE login='%s'", $login);
    $r = mysqli_query($link, $query);
    $user = mysqli_fetch_assoc($r);
    if (!empty($user)) {
        if ($user['password'] === md5(trim($_POST['password']).$user['salt'])) {
            //стартуем сессию и делаем авторизацию
            echo "Добро пожаловать";
        } else echo "Неправильный логин или пароль.";
    } else {
        //на самом деле такого логина нет, но пишем для введения в заблуждение злоумышленника
        echo "Неправильный логин или пароль.";
    }
}

//регистрация
if (isset($_POST['submit'])) {
    if (isset($_POST['checkbox'])) {
        $pass = $_POST['password'] = $_POST['password2'] = setPassword();
    }
    if (!empty($_POST['name']) && !empty($_POST['surname']) && !empty($_POST['age']) && !empty($_POST['city'])
        && !empty($_POST['language']) && !empty($_POST['email']) && !empty($_POST['login']) && !empty($_POST['password'])
        && !empty($_POST['password2'])) {
        //все поля заполнены
        if ((strlen($_POST['password']) < 6 || strlen($_POST['password']) > 10) ||
            (strlen($_POST['login']) < 4 || strlen($_POST['login']) > 12)) {
            echo "Введите пароль длиной от 6 до 10 символов, логин - от 6 до 12.";
        } else {
            if ($_POST['password'] !== $_POST['password2']) {
                echo "Пароли не совпадают.";
            } else {
                //пароли совпадают, проверяем логин и email
                $email = mysqli_real_escape_string($link, trim($_POST['email']));
                $query = sprintf('SELECT email FROM authoriz WHERE email="%s"', $email);
                $r = mysqli_query($link, $query);
                if (mysqli_num_rows($r)) $mes_email = "email";

                $login = mysqli_real_escape_string($link, trim($_POST['login']));
                $query = sprintf('SELECT email FROM authoriz WHERE login="%s"', $login);
                $r = mysqli_query($link, $query);
                if (mysqli_num_rows($r)) $mes_log = "логин";

                if (isset($mes_email)) echo "Указанный $mes_email занят, выберите другой.<br>";
                if (isset($mes_log)) echo "Указанный $mes_log занят, выберите другой.<br>";

                if (!isset($mes_email) && !isset($mes_log)) {
                    //логин и email свободны
                    $name = mysqli_real_escape_string($link, trim($_POST['name']));
                    $surname = mysqli_real_escape_string($link, trim($_POST['surname']));
                    $age = mysqli_real_escape_string($link, trim($_POST['age']));
                    $city = mysqli_real_escape_string($link, trim($_POST['city']));
                    $language = mysqli_real_escape_string($link, trim($_POST['language']));
                    $salt = generatorSalt();
                    $password = md5(trim($_POST['password']).$salt);
                    $date = date('Y-m-d H:i:s');
                    $query = sprintf('INSERT INTO authoriz (name, surname, age, city, language, password, email, login,
                          date, salt) VALUES ("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s")', $name, $surname,
                        $age, $city, $language, $password, $email, $login, $date, $salt);
                    $r = mysqli_query($link, $query);
                    if (mysqli_affected_rows($link)) {
                        //регистрация прошла успешно
                        echo "Вы успешно зарегистрировались.";
                        if (isset($pass)) echo "Ваш пароль: $pass";
                    }
                }
            }
        }



    } else {
        echo "Вы не заполнили все поля";
    }
}

?>
<h1>Регистрация пользователя</h1>
<form action="" method="POST">
    <p>Введите имя <input type="text" name="name"></p>
    <p>Введите фамилию <input type="text" name="surname"></p>
    <p>Введите  дату рождения <input type="date" name="age"></p>
    <p>Введите город <input type="text" name="city"></p>
    <p>Выберите Ваш язык </p><select name="language">
        <option value="Русский">Русский</option>
        <option value="Английский">Английский</option>
        <option value="Немецкий">Немецкий</option>
        <option value="Французский">Французский</option>
    </select>
    <p>Введите email <input type="text" name="email"></p>
    <p>Введите желаемый логин <input type="text" name="login"></p>
    <p>Введите пароль <input type="password" name="password"></p>
    <p>Повторите пароль <input type="password" name="password2"></p>
    <p>Если желаете сгенерировать пароль, отметьте: <input type="checkbox" name="checkbox"></p>
    <input type="submit" name="submit">
</form>

<h1>Авторизация пользователя</h1>
<form action="" method="POST">
    <p>Введите логин <input type="text" name="login"></p>
    <p>Введите пароль <input type="password" name="password"></p>
    <input type="submit" name="authoriz">
</form>