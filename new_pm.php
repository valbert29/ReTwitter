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
    $form = true;
    $otitle = '';
    $orecip = '';
    $omessage = '';
//We check if the form has been sent
    if(isset($_POST['title'], $_POST['recip'], $_POST['message']))
    {
        $otitle = $_POST['title'];
        $orecip = $_POST['recip'];
        $omessage = $_POST['message'];
        //We remove slashes depending on the configuration
        if(get_magic_quotes_gpc())
        {
            $otitle = stripslashes($otitle);
            $orecip = stripslashes($orecip);
            $omessage = stripslashes($omessage);
        }
        //We check if all the fields are filled
        if($_POST['title']!='' and $_POST['recip']!='' and $_POST['message']!='')
        {
            //We protect the variables
            $title = pg_escape_string($otitle);
            $recip = pg_escape_string($orecip);
            $s = pg_query($con, "select id from user_t where user_t.login='$recip'");
            $row = pg_fetch_array($s);
            $id = $row['id'];
            $message = pg_escape_string(nl2br(htmlentities($omessage, ENT_QUOTES, 'UTF-8')));
            //We check if the recipient exists
            $dn1 = pg_fetch_array(pg_query("select 1 as recip, id as recipid, (select count(*) from pm) as npm from user_t where id='$id'"));
            if($dn1['recip']==1)
            {
                //We check if the recipient is not the actual user
                if($dn1['recipid']!=$_SESSION['userid'])
                {
                    $id = $dn1['npm']+1;
                    //We send the message
                   //if(pg_query($con,'insert into pm (id, id2, title, user1, user2, message, timestamp, user1read, user2read)values('.$id.', 1, "'.$title.'", "'.$_SESSION['userid'].'", "'.$dn1['recipid'].'", "'.$message.'", "'.time().'", "yes", "no")'))
                    if(pg_query($con, "insert into pm (id,id2, title, user1, user2, message, timestamp, user1read, user2read)values('$id', 1 , '$title', ".$_SESSION['userid'].", ".$dn1['recipid'].", '$message', ".time().", 'yes', 'no')"))
                    {
                        header("Location:list_pm.php");

                        $form = false;
                    }
                    else
                    {
                        //Otherwise, we say that an error occured
                        $error = 'An error occurred while sending the message';
                    }
                }
                else
                {
                    //Otherwise, we say the user cannot send a message to himself
                    $error = 'You cannot send a message to yourself.';
                }
            }
            else
            {
                //Otherwise, we say the recipient does not exists
                $error = 'The recipient does not exists.';
            }
        }
        else
        {
            //Otherwise, we say a field is empty
            $error = 'A field is empty. Please fill of the fields.';
        }
    }
    elseif(isset($_GET['recip']))
    {
        //We get the username for the recipient if available
        $orecip = $_GET['recip'];
    }
    if($form)
    {
//We display a message if necessary
        if(isset($error))
        {
            echo '<div class="form-control" style="color: red; text-align: center; font-size: 20px; ">'.$error.'</div>';
        }
//We display the form
        ?><div class="section profile-content">
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
                                <h3 style="margin-top: 0px;">Новое сообщение</h3><br>
                                <form action="new_pm.php" method="post">
                                    <div class="form-group">
                                        <label for="title">Тема:</label>
                                        <input type="text" class="form-control" value="<?php echo htmlentities($otitle, ENT_QUOTES, 'UTF-8'); ?>" id="title" name="title"
                                               placeholder="Тема сообщения">
                                    </div>
                                    <div class="form-group">
                                        <label for="recip" >Кому:</label>
                                        <input type="text" class="form-control" value="<?php echo htmlentities($orecip, ENT_QUOTES, 'UTF-8'); ?>" id="recip" name="recip" placeholder="Кому"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="message">Message</label>
                                        <textarea class="form-control"  rows="5" id="message" name="message"><?php echo htmlentities($omessage, ENT_QUOTES, 'UTF-8'); ?></textarea>
                                    </div>
                                    <input type="submit" class="btn btn-danger btn-sm" value="Отправить">
                                </form>
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
    echo '<div class="message">You must be logged to access this page.</div>';
}
?>
<?php include("includes/footer.php"); ?>