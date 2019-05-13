<?php include("includes/header.php"); ?>
<body class="register-page sidebar-collapse">
<?php include("includes/navbar.php"); ?>
<div class="page-header" style="background-image: url('assets/img/login-image.jpg');">
    <div class="filter"></div>
    <div class="container">
        <div class="row">
            <div class="col-lg-4 ml-auto mr-auto">
                <div class="card card-register">
                    <h3 class="title mx-auto" style="font-weight: bold; margin-bottom: 0px;">reTwitter</h3>
                    <h3 class="mx-auto">Регистрация</h3>
                    <form class="register-form" method="get">
                        <label>Логин</label>
                        <input type="text" name=login class="form-control" placeholder="Логин">
                        <label>Имя</label>
                        <input type="text" name=name class="form-control" placeholder="Имя">
                        <label>Фамилия</label>
                        <input type="text" name=surname class="form-control" placeholder="Фамилия">
                        <label>День рождения</label>
                        <input type="date" name=birth class="form-control" placeholder="Фамилия">
                        <label>Пароль</label>
                        <input type="password" name=password class="form-control" placeholder="Пароль">
                        <button class="btn btn-danger btn-block btn-round">Зарегистрироваться</button>
                    </form>
                    <div class="forgot">
                        <a href="login.php" class="btn btn-link btn-danger">Есть аккаунт? <b style="text-decoration: underline">Войти</b></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>
<?php require_once("includes/connection.php");
require 'includes/connection.php';

if (!isset($_GET["register"])) {
    if (!empty($_GET['login']) && !empty($_GET['name']) && !empty($_GET['surname']) && !empty($_GET['password']) && !empty($_GET['birth'])) {
        $login = htmlspecialchars($_GET['login']);
        $name = htmlspecialchars($_GET['name']);
        $surname = htmlspecialchars($_GET['surname']);
        $birth = htmlspecialchars($_GET['birth']);
        $password = htmlspecialchars($_GET['password']);
        $query = pg_query($con, "SELECT * FROM user_t WHERE login='" . $login . "'");
        $numrows = pg_numrows($query);
        print $numrows;
        if ($numrows == 0) {
            $sql = "INSERT INTO user_t(login, name, surname,password,birth_date) VALUES('$login','$name', '$surname', '$password','$birth')";
            $result = pg_query($con, $sql);
            if($result){

            }
        }
        header("Location:login.php");
    }
}
?>



