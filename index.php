<?php

$link = mysqli_connect('localhost', 'root', '', 'test');
mysqli_set_charset($link, 'utf8');

//устанавливаем стиль для input при неправильном вводе
$style='style="background: rgba(250,210,209,0.56)"';
$name_d = '';
$surname_d = '';
$age_d = '';
$city_d = '';
$language_d = '';
$login_d = '';
$email_d = '';
$password_d = '';


$checkbox_save = '';
$login_save = '';

//принимает логин, пароль и соль для пользователя, а возвращает соленый пароль
function salt($login, $password, $salt)
{
    return md5(trim($password).$salt);
}

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
            session_start();
            $_SESSION['auth'] = true;
            $_SESSION['login'] = $user['login'];
            $_SESSION['id'] = $user['id'];
            if (!empty($_POST['remember']) && $_POST['remember'] === '1') {
                $ikey = generatorSalt();
                setcookie('login', $user['login'], time() + 3600);
                setcookie('ikey', $ikey, time() + 3600);
                $ikey = mysqli_real_escape_string($link, $ikey);
                $query = sprintf("UPDATE authoriz SET ikey='%s' WHERE login='%s'", $ikey, $user['login']);
                mysqli_query($link, $query);

            }
            echo "Добро пожаловать";
        } else {
            if (!empty($_POST['remember']) && $_POST['remember'] === '1') {
                $checkbox_save = 'checked="checked"';
            }
            $login_save = "value=\"{$_POST['login']}\"";
            echo "Неправильный логин или пароль.";
        }
    } else {
        //на самом деле такого логина нет, но пишем для введения в заблуждение злоумышленника
        if (!empty($_POST['remember']) && $_POST['remember'] === '1') {
            $checkbox_save = 'checked="checked"';
        }
        $login_save = "value=\"{$_POST['login']}\"";
        echo "Неправильный логин или пароль."; 
    }
} else {
    session_start();
    //var_dump($_SESSION);

//начало входа на сайт
    if (empty($_SESSION['auth']) || $_SESSION['auth'] == false) {
        if (!empty($_COOKIE['login']) && !empty($_COOKIE['ikey'])) {
            $ikey = mysqli_real_escape_string($link, $_COOKIE['ikey']);
            $query = sprintf("SELECT * FROM authoriz WHERE login='%s' AND ikey='%s'", $_COOKIE['login'], $ikey);
            //flush();
            $result = mysqli_query($link, $query);
            $user = mysqli_fetch_assoc($result);
            if (!empty($user)) {
                //Пользователь с таким cookie существует
                $_SESSION['auth'] = true;
                $_SESSION['login'] = $user['login'];
                $_SESSION['id'] = $user['id'];
                //перезаписываем cookie
                $ikey = generatorSalt();
                setcookie('login', $user['login'], time() + 3600);
                setcookie('ikey', $ikey, time() + 3600);
                $ikey = mysqli_real_escape_string($link, $ikey);
                $query = sprintf("UPDATE authoriz SET ikey='%s' WHERE login='%s'", $ikey, $user['login']);
                mysqli_query($link, $query);
                //пользователь авторизован, выполняем нужный код
                echo 'пользователь авторизован через куки, куки перезаписаны';
            }
        }
    } else {
        //пользователь авторизован по сессии, выполняем нужный код
        echo 'пользователь авторизован по сессии';
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
            $login_d = $style;
            $password_d = $style;
            echo "Введите пароль длиной от 6 до 10 символов, логин - от 6 до 12.";
        } else {
            if ($_POST['password'] !== $_POST['password2']) {
                $password_d = $style;
                echo "Пароли не совпадают.";
            } else {
                //пароли совпадают, проверяем логин и email
                $email = mysqli_real_escape_string($link, trim($_POST['email']));
                $query = sprintf('SELECT email FROM authoriz WHERE email="%s"', $email);
                $r = mysqli_query($link, $query);
                if (mysqli_num_rows($r)) {
                    $email_d = $style;
                    $mes_email = "email";
                }

                $login = mysqli_real_escape_string($link, trim($_POST['login']));
                $query = sprintf('SELECT email FROM authoriz WHERE login="%s"', $login);
                $r = mysqli_query($link, $query);
                if (mysqli_num_rows($r)) {
                    $login_d = $style;
                    $mes_log = "логин";
                }

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
        if (empty($_POST['name'])) $name_d = $style;
        if (empty($_POST['surname'])) $surname_d = $style;
        if (empty($_POST['age'])) $age_d = $style;
        if (empty($_POST['city'])) $city_d = $style;
        if (empty($_POST['language'])) $language_d = $style;
        if (empty($_POST['login'])) $login_d = $style;
        if (empty($_POST['email'])) $email_d = $style;
        if (empty($_POST['password'])) $password_d = $style;
        if (empty($_POST['password2'])) $password_d = $style;
        echo "Вы не заполнили все поля";
    }
}

?>
<h1>Регистрация пользователя</h1>
<form action="" method="POST">
    <p>Введите имя <input type="text" name="name" <?=$name_d; ?> value="<?php if (isset($_POST['name'])) echo "{$_POST['name']}"; ?>"></p>
    <p>Введите фамилию <input type="text" name="surname" <?=$surname_d; ?> value="<?php if (isset($_POST['surname'])) echo "{$_POST['surname']}"; ?>"></p>
    <p>Введите  дату рождения <input type="date" name="age" <?=$age_d; ?>value="<?php if (isset($_POST['age'])) echo "{$_POST['age']}"; ?>"></p>
    <p>Введите город <input type="text" name="city" <?=$city_d; ?> value="<?php if (isset($_POST['city'])) echo "{$_POST['city']}"; ?>"></p>
    <p>Выберите Ваш язык </p><select name="language" <?=$language_d; ?>>
        <option value="Русский" <?php if(isset($_POST['language']) && $_POST['language'] == 'Русский') echo 'selected'; ?>>Русский</option>
        <option value="Английский" <?php if(isset($_POST['language']) && $_POST['language'] == 'Английский') echo 'selected'; ?>>Английский</option>
        <option value="Немецкий" <?php if(isset($_POST['language']) && $_POST['language'] == 'Немецкий') echo 'selected'; ?>>Немецкий</option>
        <option value="Французский" <?php if(isset($_POST['language']) && $_POST['language'] == 'Французский') echo 'selected'; ?>>Французский</option>
    </select>
    <p>Введите email <input type="text" name="email" <?=$email_d; ?>value="<?php if (isset($_POST['email'])) echo "{$_POST['email']}"; ?>"></p>
    <p>Введите желаемый логин <input type="text" name="login" <?=$login_d; ?> value="<?php if (isset($_POST['login'])) echo "{$_POST['login']}"; ?>"></p>
    <p>Введите пароль <input type="password" name="password"  <?=$password_d; ?>></p>
    <p>Повторите пароль <input type="password" name="password2" <?=$password_d; ?>></p>
    <p>Если желаете сгенерировать пароль, отметьте: <input type="checkbox" name="checkbox"></p>
    <input type="submit" name="submit">
</form>

<h1>Авторизация пользователя</h1>
<form action="" method="POST">
    <p>Введите логин <input type="text" name="login" <?=$login_save; ?>></p>
    <p>Введите пароль <input type="password" name="password"></p>
    <p>Запомнить меня <input type="checkbox" name="remember" value="1" <?=$checkbox_save; ?>></p>
    <input type="submit" name="authoriz">
</form>

<h3 ><a href="logout.php">Выйти.</a></h3>
<a href="in.php">Следующая страница</a>