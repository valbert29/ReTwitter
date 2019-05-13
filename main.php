<?php include("includes/header.php");
session_start(); ?>
<body class="profile-page sidebar-collapse">
<div class="page-header page-header-xs" data-parallax="true"
     style="background-image: url('assets/img/fabio-mangione.jpg'); min-height: 14vh !important;">
    <div class="filter"></div>
</div>
<div class="section profile-content">
    <div class="container">
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
        </div>
        <div class="tab-pane text-center" id="following" role="tabpanel">
        </div>
    </div>
</div>
<?php include("includes/navbarMain.php"); ?>
<?php include("includes/footer.php"); ?>
<?php
require_once("includes/connection.php");

$login = $_SESSION['session_login'];

$user_id = pg_query($con,
    "SELECT id FROM user_t WHERE login='$login'");
$user_id = pg_fetch_all($user_id, PGSQL_NUM);
$user_id = $user_id[0][0];

$all_tweets_id_follower = pg_query($con, "SELECT user_id FROM followers WHERE follower_id='$user_id'");
$all_tweets_id_follower = pg_fetch_all($all_tweets_id_follower, PGSQL_NUM);

if (!($all_tweets_id_follower)) {
    print '<script type="text/javascript">
        var tab=document.getElementsByClassName("tab-pane text-center");
        var divH=document.createElement(\'div\');
        divH.innerHTML=\'<h3 class="text-muted">Not following anyone yet :(</h3><button class="btn btn-warning btn-round"><a style="color:white" href="feed.php">Find artists</a></button>\';
        var node=document.createTextNode("");
        divH.appendChild(node);
        tab[0].appendChild(divH);
    </script>';
}
$arr = [];
if (!empty($_GET["tweet_id"])) {
    $tweet_id_insert = htmlspecialchars($_GET['tweet_id']);
    $insert_like = pg_query($con, "INSERT into tweet_like(tweet_id, user_id) values ('$tweet_id_insert','$user_id')");
}
if ($all_tweets_id_follower) {
    foreach ($all_tweets_id_follower as $alltweet) {
        foreach ($alltweet as $item) {
            $alltweets = pg_query($con,
                "SELECT * FROM tweet JOIN user_t ON tweet.author = user_t.id AND user_t.id='$item'");
            $alltweets = pg_fetch_all($alltweets, PGSQL_NUM);
            $arr = [];

            foreach ($alltweets as $alltweet) {
                $alltweet = implode(",", $alltweet);
                array_push($arr, $alltweet);
            }
            for ($i = 0; $i < count($arr); $i++) {
                $infAboutIwit = explode(",", $arr[$i]);
                $tweet_id = $infAboutIwit[0];
                $user_id = $infAboutIwit[1];
                $text = $infAboutIwit[2];

                $fullname = $infAboutIwit[5] . $infAboutIwit[6];
                $login = $infAboutIwit[4];
                $count_retweet = pg_query($con, "SELECT count(user_id) FROM tweet_retweet WHERE tweet_id='$tweet_id'");
                $count_retweet = pg_fetch_array($count_retweet, null, PGSQL_ASSOC);
                $count_retweet = $count_retweet['count'];
                $count_like = pg_query($con, "SELECT count(user_id) FROM tweet_like WHERE tweet_id='$tweet_id'");
                $count_like = pg_fetch_array($count_like, null, PGSQL_ASSOC);
                $count = $count_like['count'];
                print '<script type=\'text/javascript\'>
        var mainDiv=document.getElementsByClassName("list-unstyled follows");
        
        var divCard=document.createElement(\'div\');
        var li=document.createElement(\'li\');
      
        divCard.innerHTML=\'<img style="margin: 25px 0px 0px 25px;cursor:pointer"class="img-circle img-no-padding img-responsive"src="assets/img/faces/joe-gardner-2.jpg" alt="Card image cap"><div class="card-body">\'+
        \'<h4 class="card-title" style="font-weight: bold">' . $login . '</h4><p class="card-text">' . $text . '</p><button class="btn btn-danger btn-round btn-sm like"><i class="fa fa-heart"></i> ' . $count . '</button>\'+
        \'<button type="button" class="btn btn-danger btn-round btn-sm retweet" ><i class="fa fa-retweet" aria-hidden="true"></i>' . $count_retweet . '</button></div>\';
        divCard.id=' . $tweet_id . ';
        divCard.className=\'card\';
    
        li.appendChild(divCard);
        mainDiv[0].appendChild(li);
        
        var retweet=document.getElementsByClassName(\'btn btn-danger btn-round btn-sm retweet\');
        var like =document.getElementsByClassName(\'btn btn-danger btn-round btn-sm like\');
        var accounts=document.getElementsByClassName("img-circle img-no-padding img-responsive");
        
        for(var i = 0; i <like.length ; i++) {
          like[i].onclick=function(e) {
                let tweet_id=e.target.parentNode.parentElement.id;
                location.href = "http://localhost:63342/ReTwitter/main.php?tweet_id="+tweet_id;
          };
        }
         for(var j = 0; j < accounts.length; j++) {
          accounts[j].onclick=function(e) {
          let login=e.target.parentNode.parentElement.getElementsByClassName("card-title")[0].innerText;
                location.href = "http://localhost:63342/ReTwitter/account.php?login="+login;
          };
          
          for(let k = 0; k <retweet.length ; k++) {
            retweet[k].onclick=function(e) {
                 let tweet_id=e.target.parentNode.parentElement.id;
                 location.href = "http://localhost:63342/ReTwitter/account.php?tweet_retweet="+tweet_id;
          }
        }
        }
      </script>';
            }
        }
    }
}

?>
