<?php include("includes/header.php"); ?>
<div class="container">
    <div id="account">
        <h1>Создать твит</h1>
        <form action="account.php" method="get">
            <textarea name="twit" id="" cols="30" rows="10"></textarea>
            <p class="submit"><input class="button" type="submit" value="Send"></p>
        </form>
    </div>

</div>
<div class="commnets-area">
</div>


<?php
require 'includes/connection.php';
session_start();
//если мы хз кто зашел,посылаем на вход
if (!isset($_SESSION["session_login"])) {
    header("location:login.php");
} else {
    $login = $_SESSION['session_login'];
    //находим айдишник зашедшего пользователя
    $result = pg_query($con, "SELECT id FROM user_t WHERE login='" . $login . "'");
    $arr = pg_fetch_array($result, 0, PGSQL_NUM);
    $user_id = $arr[0];
    //выводим все его твиты
    $allTwits = pg_query($con,
        "SELECT login,date,text FROM twit JOIN user_t ON twit.author = user_t.id AND author='" . $user_id . "'");
    $arr = [];
    $allTwits = pg_fetch_all($allTwits, PGSQL_NUM);
    foreach ($allTwits as $allTwit) {
        $allTwit = implode(",", $allTwit);
        array_push($arr, $allTwit);
    }

    for ($i = count($arr) - 1; $i >= 0; $i--) {
        $infAboutIwit = explode(",", $arr[$i]);
        $nameUser = $infAboutIwit[0];
        $dateCreateTwit = $infAboutIwit[1];
        $text = $infAboutIwit[2];
        print "<script type='text/javascript'>

              var div=document.getElementsByClassName(\"commnets-area\");
              
              var commentDiv = document.createElement('div');
              var postInfoDiv=document.createElement('div');
              var leftArea=document.createElement('div');
              var middleArea=document.createElement('div');
              var p=document.createElement('p');
              
              p.innerText='$text';
              leftArea.innerHTML=' <a class=\"avatar\" href=\"#\"><img src=\"\" alt=\"Profile Image\"></a>';
              middleArea.innerHTML=' <a class=\"name\" href=\"#\"><b>$nameUser </b></a><h6 class=\"date\">  on $dateCreateTwit</h6>';
              
              middleArea.className='middle-area';
              leftArea.className='left-area';
              postInfoDiv.className='post-info';
              commentDiv.className='comment';
              
              postInfoDiv.appendChild(leftArea);
              postInfoDiv.appendChild(middleArea);
              commentDiv.appendChild(postInfoDiv);
              commentDiv.appendChild(p);
              div[0].appendChild(commentDiv);
              
        </script>";
    }

    if (!isset($_GET["account"])) { //Альбертик измени чтоб работало без !
        if (!empty($_GET['twit'])) {
            $twit = htmlspecialchars($_GET['twit']);
            $date = date('d-m-y');
            $date = (string)$date;
            $insert_twit = pg_query($con, "INSERT into twit(author,text,date) values ('$user_id','$twit','$date')");


        }

    };
}
?>
<?php include("includes/footer.php"); ?>
