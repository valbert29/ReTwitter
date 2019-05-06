<!--<div id="welcome">
    <h2>Добро пожаловать, <span> USER </span></h2>!
    <p><a href="logout.php">Выйти</a> из системы</p>
</div>-->
<?php

session_start();

if(!isset($_SESSION["session_login"])):
    header("location:login.php");
else:
    ?>

    <?php include("includes/header.php"); ?>
    <div id="welcome">
        <h2>Добро пожаловать, <span><?php echo $_SESSION['session_login'];?>! </span></h2>
        <p><a href="logout.php">Выйти</a> из системы</p>
    </div>

    <?php include("includes/footer.php"); ?>

<?php endif; ?>

