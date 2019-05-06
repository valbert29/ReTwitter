<?php include("includes/header.php"); ?>
    <div class="container">
        <div id="account">
            <h1>Создать твит</h1>
            <form action="account.php" method="get" >
                <textarea name="twit" id="" cols="30" rows="10"></textarea>
                <p class="submit"><input class="button" type="submit" value="Send"></p>
            </form>
        </div>
    </div>
<?php include("includes/footer.php"); ?>
<?php
session_start();
require 'includes/connection.php';
$login= $_SESSION['session_login'];
$result=pg_query($con,"SELECT id FROM user_t WHERE login='".$login."'");
$arr = pg_fetch_array($result, 0, PGSQL_NUM);
$user_id=$arr[0];
$allTwits=pg_query($con,"SELECT login,date,text FROM twit JOIN user_t ON twit.author = user_t.id AND author='".$user_id."'");
$numrows=pg_num_rows($allTwits);
$arrFor = pg_fetch_all($allTwits,PGSQL_NUM);
foreach ($arrFor as $value){
    $infa=implode(" ",$value);
    print "<div class='twit'>".$infa."</div>";
}

if(!isset($_GET["account"])){ //Альбертик измени чтоб работало без !
    if(!empty($_GET['twit'])) {
        $twit = htmlspecialchars($_GET['twit']);
        $date=date('d-m-y');
        $date=(string)$date;
        $insert_twit = pg_query($con, "INSERT into twit(author,text,date) values ('$user_id','$twit','$date')");
    }
}
