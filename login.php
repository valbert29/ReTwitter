<?php include("includes/header.php"); ?>
    <div class="container mlogin">
        <div id="login">
            <h1>Вход</h1>
            <form action="login.php" id="loginform" method="get" name="loginform">
                <p><label for="user_login">Имя пользователя<br>
                        <input class="input" id="login" name="login" size="20"
                               type="text" value=""></label></p>
                <p><label for="user_pass">Пароль<br>
                        <input class="input" id="password" name="password" size="20"
                               type="password" value=""></label></p>
                <p class="submit"><input class="button" type="submit" value="Log In"></p>
                <p class="regtext">Еще не зарегистрированы?<a href="register.php">Регистрация</a>!</p>
            </form>
        </div>
    </div>
<?php include("includes/footer.php"); ?>
<?php require_once("includes/connection.php");
session_start();


if (isset($_SESSION["session_login"])) {
    // вывод "Session is set"; // в целях проверки
    header("Location: intropage.php");
}


if(isset($_GET["login"])){

    if(!empty($_GET['login']) && !empty($_GET['password'])) {
        $login=htmlspecialchars($_GET['login']);
        $password=htmlspecialchars($_GET['password']);

        $query =pg_query($con, "SELECT * FROM user_t WHERE login='".$login."' AND password='".$password."'");
        $numrows=pg_num_rows($query);
        if($numrows!=0)
        {
            while($row=pg_fetch_assoc($query))
            {
                $dblogin=$row['login'];
                $dbpassword=$row['password'];
            }
            if($login == $dblogin && $password == $dbpassword)
            {
                // старое место расположения
                //  session_start();
                $_SESSION['session_login']=$login;
                /* Перенаправление браузера */
                header("Location: intropage.php");
            }
        } else {
            //  $message = "Invalid login or password!";

            $message=  "Invalid login or password!";
        }
    } else {
        $message = "All fields are required!";
    }
}
?>

<?php if (!empty($message)) {echo "<p class=\"error\">" . "MESSAGE: ". $message . "</p>";} ?>
