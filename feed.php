<?php include("includes/header.php"); ?>
<body class="profile-page sidebar-collapse">
<?php include("includes/navbarMain.php"); ?>
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

//выводим все его твиты
$alltweets = pg_query($con,
    "SELECT * FROM tweet JOIN user_t ON tweet.author = user_t.id");
$alltweets = pg_fetch_all($alltweets, PGSQL_NUM);
$arr = [];

foreach ($alltweets as $alltweet) {
    $alltweet = implode(",", $alltweet);
    array_push($arr, $alltweet);
}
if (!empty($_GET["tweet_id"])) {
    $tweet_id_insert = htmlspecialchars($_GET['tweet_id']);
    $insert_like = pg_query($con, "INSERT into tweet_like(tweet_id, user_id) values ('$tweet_id_insert','$user_id')");
}
for ($i = 0; $i < count($arr); $i++) {
    $infAboutIwit = explode(",", $arr[$i]);
    $tweet_id = $infAboutIwit[0];
    $user_id=$infAboutIwit[1];
    $text = $infAboutIwit[2];
    $dateCreatetweet = $infAboutIwit[3];
    $fullname = $infAboutIwit[6] . $infAboutIwit[7];
    $login=$infAboutIwit[5];
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

?>
