<?php include("includes/header.php");
session_start();
if (!isset($_SESSION['session_login'])) {
    header("location:login.php");
}
$login_add = $_SESSION['session_login'];
?>
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
                <a href="">
                    <img src="assets/img/faces/joe-gardner-2.jpg" alt="Circle Image"
                         class="img-circle img-no-padding img-responsive"></a>

            </div>
        </div>
        <br/>
        <div class="nav-tabs-navigation">
            <div class="nav-tabs-wrapper">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#feeds" role="tab">Лента</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#follows" role="tab">Подписчики</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#following" role="tab">Подписки</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Tab panes -->
        <div class="tab-content following">
            <div class="tab-pane active" id="feeds" role="tabpanel">
                <div class="row">
                    <div class="col-md-12 ml-auto mr-auto">
                        <ul class="list-unstyled">
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tab-pane text-center follows" id="follows" role="tabpanel">
                <div class="row">
                    <div class="col-md-6 ml-auto mr-auto">
                        <ul class="list-unstyled follows">
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tab-pane text-center following" id="following" role="tabpanel">
                <div class="row">
                    <div class="col-md-6 ml-auto mr-auto">
                        <ul class="list-unstyled following">
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<?php include("includes/footer.php"); ?>
<?php
require_once("includes/connection.php");


if (!empty($_GET["login"])) {
    $login = $_SESSION['session_login'];
    $login_add = htmlspecialchars($_GET['login']);
    if ($login !== $login_add) {
        $findUser = pg_query($con, "SELECT id FROM user_t WHERE login='" . $login . "'");
        $findUser = pg_fetch_array($findUser, 0, PGSQL_NUM);
        $findUser = $findUser[0];

        $findLogin = pg_query($con, "SELECT id FROM user_t WHERE login='" . $login_add . "'");
        $findLogin = pg_fetch_array($findLogin, 0, PGSQL_NUM);
        $findLogin = $findLogin[0];
        $search_follower = pg_query($con,
            "SELECT * FROM followers where user_id='$findLogin' AND follower_id='$findUser'");

        if (pg_num_rows($search_follower) == 0) {
            print '<script type="text/javascript">
          var div=document.getElementsByClassName("owner");
          var para = document.createElement("div");
          para.innerHTML=\'<button class="btn btn-danger btn-round"><a style="color:white" href="account.php?add_follow_user=' . $login_add . '">Подписаться</a></button>\';
          var node = document.createTextNode("");
          para.appendChild(node);
          div[0].appendChild(para);
        </script>';
        } else {
            print '<script type="text/javascript">
          var div=document.getElementsByClassName("owner");
          var para = document.createElement("div");
          para.innerHTML=\'<button class="btn btn-danger btn-round" >Подписан</button>\';
          var node = document.createTextNode("");
          para.appendChild(node);
          div[0].appendChild(para);
        </script>';
        }
    }
} else {
    print '<script type="text/javascript">
          var mainDiv=document.getElementsByClassName("list-unstyled");
        
        var divCard=document.createElement(\'div\');
        var li=document.createElement(\'li\');
      
        divCard.innerHTML=\'<div class="card-body">\'+
                                        \'<form method="get" action="account.php">\'+
                                            \'<div class="form-group">\'+
                                                \'<label for="exampleFormControlTextarea1">У вас есть о чем рассказать?</label>\'+
                                                \'<textarea name="tweet" class="form-control" id="exampleFormControlTextarea1" rows="1"></textarea>\'+
                                            \'</div>\'+
                                            \'<input type="submit" class="btn btn-danger btn-sm send" value="Опубликовать">\'+
                                        \'</form>\'+
                                    \'</div>\';
        divCard.className=\'card1\';
    
        li.appendChild(divCard);
        mainDiv[0].appendChild(li);
        </script>';
}
$findLogin = pg_query($con, "SELECT id FROM user_t WHERE login='" . $login_add . "'");
$findLogin = pg_fetch_array($findLogin, 0, PGSQL_NUM);
$findLogin = $findLogin[0];
$search_followers = pg_query($con, "SELECT user_id FROM followers where follower_id='$findLogin'");
$num_fol = pg_numrows($search_followers);
$search_followers = pg_fetch_all($search_followers, PGSQL_NUM);

$findSubscriber = pg_query($con, "SELECT follower_id FROM followers where user_id='$findLogin'");
$num_sub = pg_numrows($findSubscriber);
$findSubscriber = pg_fetch_all($findSubscriber, PGSQL_NUM);
if ($num_sub == 0) {
    print '<script type="text/javascript">
        var tab=document.getElementsByClassName("tab-pane text-center follows");
        var divH=document.createElement(\'div\');
        divH.innerHTML=\'<h3 class="text-muted">Not followers  yet :(</h3>\';
         var node=document.createTextNode("");
        divH.appendChild(node);
        tab[0].appendChild(divH);
    </script>';
} else {
    foreach ($findSubscriber as $item) {
        foreach ($item as $follower) {
            $findFollower = pg_query($con, "SELECT * FROM user_t WHERE id='" . $follower . "'");
            $findFollower = pg_fetch_array($findFollower, 0, PGSQL_NUM);
            $fullname_follower = $findFollower[2] . " " . $findFollower[3];
            $login_follower = $findFollower[1];
            print '<script type="text/javascript">
            var tab=document.getElementsByClassName("list-unstyled follows");
            var li=document.createElement(\'li\');
            li.innerHTML=\'<div class="row"><div class="col-lg-2 col-md-4 col-4 ml-auto mr-auto">\' +
            \'<img src="assets/img/faces/clem-onojeghuo-2.jpg" alt="Circle Image" class="img-circle img-no-padding img-responsive">\' +
            \'</div><div class="col-lg-7 col-md-4 col-4  ml-auto mr-auto"><h6>' . $login_follower . '<br/><small>' . $fullname_follower . '</small></h6>\' +
            \'</div></div>\';
            var node=document.createTextNode("");
            li.appendChild(node);
            tab[0].appendChild(li);
         </script>';
        }
    }

}
if ($num_fol == 0) {
    print '<script type="text/javascript">
        var tab=document.getElementsByClassName("tab-pane text-center following");
        var divH=document.createElement(\'div\');
        divH.innerHTML=\'<h3 class="text-muted">Not following anyone yet :(</h3>\';
         var node=document.createTextNode("");
        divH.appendChild(node);
        tab[0].appendChild(divH);
    </script>';
} else {
    foreach ($search_followers as $item) {
        foreach ($item as $follower) {
            $findFollower = pg_query($con, "SELECT * FROM user_t WHERE id='" . $follower . "'");
            $findFollower = pg_fetch_array($findFollower, 0, PGSQL_NUM);
            $fullname_follower = $findFollower[2] . " " . $findFollower[3];
            $login_follower = $findFollower[1];
            print '<script type="text/javascript">
            var tab1=document.getElementsByClassName("list-unstyled following");
            var li=document.createElement(\'li\');
            li.innerHTML=\'<div class="row"><div class="col-lg-2 col-md-4 col-4 ml-auto mr-auto">\' +
            \'<img src="assets/img/faces/clem-onojeghuo-2.jpg" alt="Circle Image" class="img-circle img-no-padding img-responsive">\' +
            \'</div><div class="col-lg-7 col-md-4 col-4  ml-auto mr-auto"><h6>' . $login_follower . '<br/><small>' . $fullname_follower . '</small></h6>\' +
            \'</div></div>\';
            var node=document.createTextNode("");
            li.appendChild(node);
            tab1[0].appendChild(li);
         </script>';
        }
    }


}
if (!empty($_GET["add_follow_user"])) {
    $follower = $_SESSION['session_login'];
    $login_add = htmlspecialchars($_GET['add_follow_user']);

    $findUser = pg_query($con, "SELECT id FROM user_t WHERE login='" . $login_add . "'");
    $findUser = pg_fetch_array($findUser, 0, PGSQL_NUM);
    $findUser = $findUser[0];

    $findFollower = pg_query($con, "SELECT id FROM user_t WHERE login='" . $follower . "'");
    $findFollower = pg_fetch_array($findFollower, 0, PGSQL_NUM);
    $findFollower = $findFollower[0];

    $insert_folower = pg_query($con,
        "INSERT into followers(user_id, follower_id) VALUES('$findUser','$findFollower'); ");
    print '<script type="text/javascript">
          var div=document.getElementsByClassName("owner");
          var para = document.createElement("div");
          para.innerHTML=\'<button class="btn btn-danger btn-round" >Подписан</button>\';
          var node = document.createTextNode("");
          para.appendChild(node);
          div[0].appendChild(para);
        </script>';

}
if (!empty($_GET["tweet_retweet"])) {
    $tweet_retweet = htmlspecialchars($_GET['tweet_retweet']);
    $alltweets = pg_query($con,
        "SELECT * FROM tweet WHERE id='$tweet_retweet'");
    $alltweets = pg_fetch_all($alltweets, PGSQL_NUM);
    $alltweets = $alltweets[0];
    $user_id = $alltweets[1];
    $findUser = pg_query($con, "SELECT login FROM user_t WHERE id='" . $user_id . "'");
    $findUser = pg_fetch_array($findUser, 0, PGSQL_NUM);
    $login = $findUser[0];
    $text = $alltweets[2];
    $retweet = pg_query($con, "INSERT into tweet_retweet(tweet_id, user_id) VALUES ('$tweet_retweet','$findLogin')");
    pg_query($con, "INSERT into tweet(author, text) values ('$findLogin','$text')");

}

$result = pg_query($con, "SELECT * FROM user_t WHERE login='" . $login_add . "'");
$arr = pg_fetch_array($result, 0, PGSQL_NUM);
$user_id = $arr[0];
$login = $arr[1];
$fullname = $arr[2] . " " . $arr[3];
$birth = $arr[5];
print '<script type="text/javascript">
var div=document.getElementsByClassName("owner");
var para = document.createElement("div");
para.innerHTML=\'<h4 class="title"><strong>' . $fullname . ' </strong></h4><b>@' . $login . '</b><p><i class="fa fa-birthday-cake" aria-hidden="true"></i> Дата рождения  ' . $birth . '</p>\';
var node = document.createTextNode("");
para.className="name";
para.appendChild(node);
div[0].appendChild(para);
</script>';

if (!isset($_GET["account"])) {
    if (!empty($_GET['tweet'])) {
        $tweet = htmlspecialchars($_GET['tweet']);
        $insert_tweet = pg_query($con, "INSERT into tweet(author,text) values ('$user_id','$tweet')");
        print '<script type="javascript">location.reload();</script>';
    }

};
if (!empty($_GET['delete'])) {
    $delete = htmlspecialchars($_GET['delete']);
    $delete_retweet = pg_query($con, "DELETE from tweet_retweet WHERE tweet_id='$delete'");
    $delete_like = pg_query($con, "DELETE from tweet_like WHERE tweet_id='$delete'");
    $delete_tweet = pg_query($con, "DELETE from tweet WHERE id='$delete'");
    print '<script type="javascript">location.reload();</script>';
}
//выводим все его твиты
$alltweets = pg_query($con,
    "SELECT tweet.id,name,surname,login,text FROM tweet JOIN user_t ON tweet.author = user_t.id AND author='" . $user_id . "'");
$arr = [];
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
        $text = $infAboutIwit[4];

        $count_like = pg_query($con, "SELECT count(user_id) FROM tweet_like WHERE tweet_id='$tweet_id'");
        $count_like = pg_fetch_array($count_like, null, PGSQL_ASSOC);
        $count = $count_like['count'];
        $count_tweet = 0;
        print '<script type=\'text/javascript\'>
        var mainDiv=document.getElementsByClassName("list-unstyled");
        
        var divCard=document.createElement(\'div\');
        var li=document.createElement(\'li\');
      
        divCard.innerHTML=\'<div><img style="margin: 25px 0px 0px 25px"class="img-circle img-no-padding img-responsive"src="assets/img/faces/joe-gardner-2.jpg" alt="Card image cap"><a href="account.php?delete=' . $tweet_id . '"><i class="fa fa-times"  style="color:gray;float:right;margin-right:40px;margin-top:40px;cursor:pointer" style="flo"aria-hidden="true"></i></a></div><div class="card-body"><h4 class="card-title" style="font-weight: bold">' . $login . '</h4><p class="card-text">' . $text . '</p><button class="btn btn-danger btn-round btn-sm"><i class="fa fa-heart"></i> ' . $count . '</button><button style="margin-left:10px" class="btn btn-danger btn-round btn-sm retweet"  ><i class="fa fa-retweet" aria-hidden="true"></i>' . $count_tweet . '</button></div>\';
        divCard.id=' . $tweet_id . ';
        divCard.className=\'card\';
    
        li.appendChild(divCard);
        mainDiv[0].appendChild(li);
      </script>';

    }
//айди твита который ретвитнули(107)
    $search_retweet_id = pg_query($con, "SELECT tweet_id,text FROM tweet_retweet JOIN tweet 
                                                ON tweet_retweet.tweet_id = tweet.id AND user_id='$user_id'");
    $search_retweet_id = pg_fetch_all($search_retweet_id, PGSQL_NUM);
    if ($search_retweet_id) {
        foreach ($search_retweet_id as $value) {
            $search_id = $value[0];
            $text_bla = $value[1];
            //логин пользователя у которого сперли твит
            $login_tweeter = pg_query($con, "SELECT login FROM user_t JOIN tweet t on user_t.id = t.author
                                        WHERE t.id='$search_id'");
            $login_tweeter = pg_fetch_all($login_tweeter, PGSQL_NUM);
            $login_tweeter = $login_tweeter[0][0];

            //текст всех твитов пользователя
            $search_all_text = pg_query($con, "SELECT text FROM tweet WHERE author='$user_id'");
            $search_all_text = pg_fetch_all($search_all_text, PGSQL_NUM);

            //айди ретвита у вора
            $search_text_retweet = pg_query($con, "SELECT id FROM tweet WHERE text='$text_bla' AND author='$user_id'");
            $search_text_retweet = pg_fetch_all($search_text_retweet, PGSQL_NUM);
            $search_text_retweet = $search_text_retweet[0][0];


            foreach ($search_all_text as $item) {
                if ($item[0] === $text_bla) {
                    print '<script type="text/javascript">
var retweet=document.getElementsByClassName("card");
for(let i = 0; i < retweet.length; i++) {
    let id_retweet=' . $search_text_retweet . ';
     if(retweet[i].id==id_retweet){
        var fa=document.createElement(\'i\');
        fa.className="fa fa-retweet";
        fa.ariahidden="true";
        var node = document.createTextNode("         Вы ретвитнули");
        retweet[i].firstChild.appendChild(node);
         retweet[i].firstChild.appendChild(fa);
         retweet[i].getElementsByClassName("card-title")[0].innerText=\'' . $login_tweeter . '\';
      }
  }
</script>';
                }
            }
        }
    }
}


?>
<?php include("includes/footer.php"); ?>
