<?php include("includes/header.php"); ?>
<link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
<div class="tweetEntry-tweetHolder">
    <div>
        <form action="account.php" class="tweetEntry" method="get">
            <img src="img/5dfe981827a52329f4bf9e0fad48e45d.png" style="width: 25px;height: 25px;margin-left: 20px">
            <input class="button" style="margin-left: 20px" type="submit" value="Send">
            <label for="tweet">What's happening?
                <textarea name="tweet" class="inputTweet"></textarea>
            </label>
        </form>
    </div>
    <div class="tweetEntry-tweetHolder">
    </div>
</div>
<?php

if (isset($_SESSION['session_login'])) {
    header("Location:login.php");
}
if (!isset($_GET["account"])) { //Альбертик измени чтоб работало без !
    if (!empty($_GET['tweet'])) {
        $tweet = htmlspecialchars($_GET['tweet']);
        $date = date('d-m-y');
        $date = (string)$date;
        $insert_tweet = pg_query($con, "INSERT into tweet(author,text,date) values ('$user_id','$tweet','$date')");
        print '<script type="javascript">location.reload();</script>';
    }

};

require 'includes/connection.php';
session_start();
$login = $_SESSION['session_login'];
//находим айдишник зашедшего пользователя
$result = pg_query($con, "SELECT id FROM user_t WHERE login='" . $login . "'");
$arr = pg_fetch_array($result, 0, PGSQL_NUM);
$user_id = $arr[0];
//выводим все его твиты
$alltweets = pg_query($con,
    "SELECT tweet.id,name,surname,login,date,text FROM tweet JOIN user_t ON tweet.author = user_t.id AND author='" . $user_id . "'");
$arr = [];
if (!empty($_GET["tweet_id"])) {
    $tweet_id = htmlspecialchars($_GET['tweet_id']);
    $insert_like = pg_query($con, "INSERT into tweet_like(tweet_id, user_id) values ('$tweet_id','$user_id')");
}else{
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
        var tweetEntry_tweetHolder=document.getElementsByClassName("tweetEntry-tweetHolder");
        
        
        var tweetEntry=document.createElement(\'div\');
        var tweetEntry_content=document.createElement(\'div\');
        var divAction=document.createElement(\'div\');
       
      
        
        tweetEntry_content.innerHTML=\'<a class="tweetEntry-account-group" href="[userURL]">\' +
         \'<img class="tweetEntry-avatar" src="https://img.tsn.ua/cached/1518092914/tsn-e596772b039de3f9cc99cecfb6e26c38/thumbs/315x210/85/a1/bf4178308ac255c99f6aa164121fa185.jpg">\' +
         \'<strong class="tweetEntry-fullname"> ' . $fullname . '</strong>\' +
         \'<span class="tweetEntry-username"> @<b>' . $nameUser . '</b></span>\' +
         \'<span class="tweetEntry-timestamp">-   ' . $dateCreatetweet . '</span></a>\' +
         \'<div class="tweetEntry-text-container">' . $text . '</div>\';
  
        divAction.innerHTML=\' <i class="fa fa-reply" style="width: 80px;"></i>\' +
         \'<i class="fa fa-retweet" style="width: 80px"></i>\' +
         \'<i class="fa fa-heart" style="width: 80px">'.$count.'</i>\'; 

        tweetEntry.className=\'tweetEntry\';
        tweetEntry_content.className=\'tweetEntry-content\';
        divAction.className=\'tweetEntry-action-list\';

        tweetEntry.id=' . $tweet_id . ';

        tweetEntry.appendChild(tweetEntry_content);
        tweetEntry_tweetHolder[0].appendChild(tweetEntry);
        tweetEntry.appendChild(divAction);

        var like =document.getElementsByClassName(\'fa fa-heart\');
        
        for(var i = 0; i <like.length ; i++) {
          like[i].onclick=function(e) {
                let tweet_id=e.target.parentNode.parentElement.id;
                e.target.parentNode.parentElement.getElementsByClassName("fa fa-heart")[0].innerText='.$count.';
                location.href = "http://localhost:63342/ReTwitter/account.php?tweet_id="+tweet_id;
          };
        }
      </script>';
    }
}


?>
<?php include("includes/footer.php"); ?>
