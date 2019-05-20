<?php
include('includes/connection.php');
session_start();
?>
<?php include("includes/header.php"); ?>
<body class="profile-page sidebar-collapse">
<?php include("includes/navbarMain.php"); ?>

<div class="page-header page-header-xs" data-parallax="true"
     style="background-image: url('./assets/img/fabio-mangione.jpg'); min-height: 14vh !important;">
    <div class="filter"></div>
</div>

<?php
//We check if the user is logged
if(isset($_SESSION['session_login']))
{
//We check if the ID of the discussion is defined
    if(isset($_GET['id']))
    {
        $id = intval($_GET['id']);
//We GET the title and the narators of the discussion
        $req1 = pg_query("select title, user1, user2 from pm where id=$id and id2=1");
        $dn1 = pg_fetch_array($req1);
//We check if the discussion exists
        if(pg_num_rows($req1)>0)
        {
//We check if the user have the right to read this discussion
            if($dn1['user1']==$_SESSION['userid'] or $dn1['user2']==$_SESSION['userid'])
            {
//The discussion will be placed in read messages
                if($dn1['user1']==$_SESSION['userid'])
                {
                    pg_query("update pm set user1read='yes' where id='$id' and id2=1");
                    $user_partic = 2;
                }
                else
                {
                    pg_query("update pm set user2read='yes' where id='$id' and id2=1");
                    $user_partic = 1;
                }
//We GET the list of the messages
                $req2 = pg_query('select pm.timestamp, pm.message, user_t.id as userid, user_t.login, user_t.photo from pm, user_t where pm.id= '.$id.' and user_t.id=pm.user1 order by pm.id2');
//We check if the form has been sent
                if(isset($_POST['message']) and $_POST['message']!='')
                {
                    $message = $_POST['message'];
                    //We remove slashes depending on the configuration
                    if(GET_magic_quotes_gpc())
                    {
                        $message = stripslashes($message);
                    }
                    //We protect the variables
                    $message = pg_escape_string(nl2br(htmlentities($message, ENT_QUOTES, 'UTF-8')));
                    //We send the message and we change the status of the discussion to unread for the recipient
                    $value = (intval(pg_num_rows($req2))+1);
                    if(pg_query("insert into pm (id, id2,  user1, message, timestamp)values('$id', '$value', ".$_SESSION['userid'].", '$message', ".time().")") and pg_query("update pm set user".$user_partic."read='yes' where id='$id' and id2=1"))
                    {
                        header("location:read_pm.php?id=$id");
                    }
                    else
                    {
                        ?>
                        <div class="message">An error occurred while sending the message.<br />
                            <a href="read_pm.php?id=<?php echo $id; ?>">Go to the discussion</a></div>
                        <?php
                    }
                }
                else
                {
//We display the messages
                    ?>
                    <div class="section profile-content">
                        <div class="container">
                            <div class="nav-tabs-navigation">
                                <div class="nav-tabs-wrapper">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#follows" role="tab">Диалоги</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- Tab panes -->
                            <div class="tab-content following">
                                <div class="tab-pane active" id="follows" role="tabpanel">
                                    <div class="row">
                                        <daiv class="col-md-12 ml-auto mr-auto">
                                            <div class="card1" style="padding: 3% 5% 5% 5%; text-align: center">
                                                <h3 style="margin-top: 0px;"><?php echo $dn1['title']; ?></h3><br>
                                                <table class="table">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col" style="width: 25%">User</th>
                                                        <th scope="col">Message</th>
                                                        <th scope="col" style="width: 25%">Time</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    while($dn2 = pg_fetch_array($req2))
                                                    {
                                                        ?>
                                                        <tr>
                                                            <td style="width: 25%;"><?php
                                                                if($dn2['photo']!='')
                                                                {
                                                                    echo '<img src="'.htmlentities($dn2['photo']).'" alt="Image Perso" class="img-circle img-no-padding img-responsive" />';
                                                                }
                                                                ?><br /><p><?php echo $dn2['login']; ?></p></td>
                                                            <td class="left">
                                                                <?php echo $dn2['message']; ?>
                                                            </td>
                                                            <td style="width: 25%;">
                                                                <div><?php echo date('m/d/Y H:i:s' ,$dn2['timestamp']); ?></div>
                                                            </td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    //We display the reply form
                                                    ?>
                                                    </tbody>
                                                </table>
                                                <form class="form-group" action="read_pm.php?id=<?php echo $id; ?>" method="POST">
                                                        <label for="message">Введите сообщение:</label>
                                                        <textarea class="form-control" name="message" id="message" rows="5"></textarea>
                                                        <input type="submit" class="btn btn-danger btn-sm" value="Отправить">
                                            </div>
                                        </daiv>
                                    </div>
                                </div>
                                <div class="tab-pane text-center" id="following" role="tabpanel">
                                    <h3 class="text-muted">Not following anyone yet :(</h3>
                                    <button class="btn btn-warning btn-round">Find artists</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            else
            {
                $error = "You dont have the rights to access this page.";
            }
        }
        else
        {
            $error = "This discussion does not exists.";
        }
    }
    else
    {
        $error = "The discussion ID is not defined.";
    }
}
else
{
    $error ="You must be logged to access this page. Please <a href='login.php'>Login</a>";
}
if(isset($error))
{
    echo '<div class="form-control" style="color: red; text-align: center; font-size: 20px; ">'.$error.'</div>';
}
?>
<?php include("includes/footer.php"); ?>
</body>
</html>
    