<?php include("includes/header.php");
session_start();
$_SESSION=array();
?>
<body class="register-page sidebar-collapse">
<?php include("includes/navbar.php"); ?>
<div class="page-header" style="background-image: url('assets/img/login-image.jpg');">
    <div class="filter"></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-4 ml-auto mr-auto">
                <div class="card card-register">
                    <h3 class="title mx-auto" style="font-weight: bold; margin-bottom: 0px;">reTwitter</h3>
                    <h3 class="mx-auto">Вход</h3>
                    <form class="register-form" method="get">
                        <label>Логин</label>
                        <input type="text" name=login class="form-control" placeholder="Логин">
                        <label>Пароль</label>
                        <input type="password" name=password class="form-control" placeholder="Пароль">
                        <button class="btn btn-danger btn-block btn-round">Войти</button>
                    </form>
                    <div class="forgot">
                        <a href="register.php" class="btn btn-link btn-danger">Нет аккаунта? <b style="text-decoration: underline">Зарегистрироваться</b></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>
<?php require_once("includes/connection.php");




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
                $query=pg_query($con,"SELECT user_t.id from user_t where login ='$login' and password='$password' ");
                // старое место расположения
                //  session_start();
                $query=pg_fetch_array(  $query);
                $_SESSION['session_login']=$login;
                $_SESSION['userid']=$query['id'];
                /* Перенаправление браузера */
                header("Location: account.php");
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
