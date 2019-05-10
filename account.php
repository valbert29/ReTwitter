<?php include("includes/header.php"); ?>
<body class="profile-page sidebar-collapse">
<!-- Navbar -->
<?php include("includes/navbarMain.php"); ?>
<!-- End Navbar -->
<div class="page-header page-header-xs" data-parallax="true"
     style="background-image: url('assets/img/fabio-mangione.jpg');">
    <div class="filter"></div>
</div>
<div class="section profile-content">
    <div class="container">
        <div class="owner">
            <div class="avatar">
                <img src="assets/img/faces/joe-gardner-2.jpg" alt="Circle Image"
                     class="img-circle img-no-padding img-responsive">
            </div>
        </div>
        <br/>
        <div class="nav-tabs-navigation">
            <div class="nav-tabs-wrapper">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#follows" role="tab">Лента</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Tab panes -->
        <div class="tab-content following">
            <div class="tab-pane active" id="follows" role="tabpanel">
                <div class="row">
                    <div class="col-md-12 ml-auto mr-auto">
                        <ul class="list-unstyled follows">
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tab-pane text-center" id="following" role="tabpanel">
                <h3 class="text-muted">Not following anyone yet :(</h3>
                <button class="btn btn-warning btn-round">Find artists</button>
            </div>
        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>
<?php
require_once("includes/connection.php");
session_start();

if (!empty($_GET["login"])) {
    $login=$_SESSION['session_login'];
    $login_add = htmlspecialchars($_GET['login']);
    if($login!==$login_add){
        $findUser = pg_query($con, "SELECT id FROM user_t WHERE login='" . $login . "'");
        $findUser = pg_fetch_array($findUser, 0, PGSQL_NUM);
        $findUser = $findUser[0];

        $findLogin = pg_query($con, "SELECT id FROM user_t WHERE login='" . $login_add . "'");
        $findLogin = pg_fetch_array($findLogin, 0, PGSQL_NUM);
        $findLogin=$findLogin[0];
        print $findLogin;
        $search_follower=pg_query($con,"SELECT * FROM followers where user_id='$findLogin' AND follower_id='$findUser'");
        print pg_num_rows($search_follower);
        if(pg_num_rows($search_follower)==0) {
            print '<script type="text/javascript">
          var div=document.getElementsByClassName("owner");
          var para = document.createElement("div");
          para.innerHTML=\'<button class="btn btn-danger btn-round" onclick="addFollower()"><i class="nc-icon nc-tile-56"></i><a style="color:white" href="account.php?add_follow_user=' . $login_add . '">Подписаться</a></button>\';
          var node = document.createTextNode("");
          para.appendChild(node);
          div[0].appendChild(para);
        </script>';
        }else{
            print '<script type="text/javascript">
          var div=document.getElementsByClassName("owner");
          var para = document.createElement("div");
          para.innerHTML=\'<button class="btn btn-danger btn-round" ><i class="nc-icon nc-tile-56"></i>Подписан</button>\';
          var node = document.createTextNode("");
          para.appendChild(node);
          div[0].appendChild(para);
        </script>';
        }
    }

}else{
    $login_add=$_SESSION['session_login'];
}
if (!empty($_GET["add_follow_user"])) {
    $follower = $_SESSION['session_login'];
    $user = htmlspecialchars($_GET['add_follow_user']);

    $findUser = pg_query($con, "SELECT id FROM user_t WHERE login='" . $user . "'");
    $findUser = pg_fetch_array($findUser, 0, PGSQL_NUM);
    $findUser = $findUser[0];

    $findFollower = pg_query($con, "SELECT id FROM user_t WHERE login='" . $follower . "'");
    $findFollower = pg_fetch_array($findFollower, 0, PGSQL_NUM);
    $findFollower=$findFollower[0];

    $insert_folower = pg_query($con,
        "INSERT into followers(user_id, follower_id) VALUES('$findUser','$findFollower'); ");
}

$result = pg_query($con, "SELECT * FROM user_t WHERE login='" . $login_add . "'");
$arr = pg_fetch_array($result, 0, PGSQL_NUM);
$user_id = $arr[0];
$login = $arr[1];
$fullname = $arr[2] . $arr[3];

print '<script type="text/javascript">
var div=document.getElementsByClassName("owner");
var para = document.createElement("div");
para.innerHTML=\'<h4 class="title">' . $fullname . ' <br/><b>@' . $login . '</b></h4>\';
var node = document.createTextNode("");
para.className="name";
para.appendChild(node);
div[0].appendChild(para);
</script>';

if (!isset($_GET["account"])) {
    if (!empty($_GET['tweet'])) {
        $tweet = htmlspecialchars($_GET['tweet']);
        $date = date('d-m-y');
        $date = (string)$date;
        $insert_tweet = pg_query($con, "INSERT into tweet(author,text,date) values ('$user_id','$tweet','$date')");
        print '<script type="javascript">location.reload();</script>';
    }

};
//выводим все его твиты
$alltweets = pg_query($con,
    "SELECT tweet.id,name,surname,login,date,text FROM tweet JOIN user_t ON tweet.author = user_t.id AND author='" . $user_id . "'");
$arr = [];
if (!empty($_GET["tweet_id"])) {
    $tweet_id = htmlspecialchars($_GET['tweet_id']);
    $insert_like = pg_query($con, "INSERT into tweet_like(tweet_id, user_id) values ('$tweet_id','$user_id')");
}
$numrows = pg_num_rows($alltweets);
if ($numrows != 0) {
    $alltweets = pg_fetch_all($alltweets, PGSQL_NUM);
    foreach ($alltweets as $alltweet) {
        $alltweet = implode(",", $alltweet);
        array_push($arr, $alltweet);
    }

    for ($i = count($arr) - 1; $i >= 0; $i--) {
        $infAboutIwit = explode(",", $arr[$i]);
        $tweet_id = $infAboutIwit[0];
        $fullname = $infAboutIwit[1] . $infAboutIwit[2];
        $nameUser = $infAboutIwit[3];
        $dateCreatetweet = $infAboutIwit[4];
        $text = $infAboutIwit[5];
        $count_like = pg_query($con, "SELECT count(user_id) FROM tweet_like WHERE tweet_id='$tweet_id'");
        $count_like = pg_fetch_array($count_like, null, PGSQL_ASSOC);
        $count = $count_like['count'];
        print '<script type=\'text/javascript\'>
        var mainDiv=document.getElementsByClassName("list-unstyled follows");
        
        var divCard=document.createElement(\'div\');
        var li=document.createElement(\'li\');
      
        divCard.innerHTML=\'<img style="margin: 25px 0px 0px 25px"class="img-circle img-no-padding img-responsive"src="assets/img/faces/joe-gardner-2.jpg" alt="Card image cap"><div class="card-body"><h4 class="card-title" style="font-weight: bold">' . $login . '</h4><p class="card-text">' . $text . '</p><button class="btn btn-danger btn-round btn-sm"><i class="fa fa-heart"></i> ' . $count . '</button></div>\';
        divCard.id=' . $tweet_id . ';
        divCard.className=\'card\';
    
        li.appendChild(divCard);
        mainDiv[0].appendChild(li);

        var like =document.getElementsByClassName(\'btn btn-danger btn-round btn-sm\');
        
        for(var i = 0; i <like.length ; i++) {
          like[i].onclick=function(e) {
                let tweet_id=e.target.parentNode.parentElement.id;
                e.target.parentNode.parentElement.getElementsByClassName("btn btn-danger btn-round btn-sm")[0].innerText=' . $count . ';
                location.href = "http://localhost:63342/ReTwitter/account.php?tweet_id="+tweet_id;
          };
        }
      </script>';
    }
}


?>
<?php include("includes/footer.php"); ?>
