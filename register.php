<?php include("includes/header.php"); ?>
    <div class="container mregister">
        <div id="login">
            <h1>Регистрация</h1>
            <form action="register.php" id="registerform" method="get" name="registerform">
                <p><label for="user_login">Login<br>
                        <input class="input" name="login" size="32" type="text" value=""></label></p>
                <p><label for="user_name">Имя<br>
                        <input class="input" name="name" size="32" value=""></label></p>
                <p><label for="user_surname">Фамилия<br>
                        <input class="input" name="surname" size="32" value=""></label></p>
                <p><label for="user_birth">День рождения<br>
                        <input class="input" name="birth" size="32" value="" type="date"></label></p>
                <p><label for="user_password">Пароль<br>
                        <input class="input" name="password" type="password" size="32" value=""></label></p>
                <p class="submit"><input class="button" id="register" name="register" type="submit"
                                         value="Зарегистрироваться"></p>
                <p class="regtext">Уже зарегистрированы? <a href="login.php">Введите имя пользователя</a>!</p>
            </form>
        </div>
    </div>
<?php include("includes/footer.php"); ?>
<?php require_once("includes/connection.php");
require 'includes/connection.php';

if(isset($_GET["register"])){

    if(!empty($_GET['login']) && !empty($_GET['name']) && !empty($_GET['surname']) && !empty($_GET['password']) && !empty($_GET['birth'])) {
        $login= htmlspecialchars($_GET['login']);
        $name=htmlspecialchars($_GET['name']);
        $surname=htmlspecialchars($_GET['surname']);
        $birth=htmlspecialchars($_GET['birth']);
        $password=htmlspecialchars($_GET['password']);
        $query=pg_query($con,"SELECT * FROM user_t WHERE login='".$login."'");
        if(!$query)echo 'durea';
        $numrows=pg_numrows($query);
        if($numrows==0)
        {
            $sql="INSERT INTO user_t(login, name, surname,password,birth_date) VALUES('$login','$name', '$surname', '$password','$birth')";
            $result=pg_query( $con,$sql);
            if($result){
                $message = "Account Successfully Created";
            } else {
                $message = "Failed to insert data information!";
            }
        } else {
            $message = "That surname already exists! Please try another one!";
        }
    } else {
        $message = "All fields are required!";
    }
}
?>
<?php if (!empty($message)) {echo "<p class=\"error\">" . "MESSAGE: ". $message . "</p>";} ?>


