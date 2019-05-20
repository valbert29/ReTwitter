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
<div class="section profile-content">
    <?php
    //We check if the user is logged
    if (isset($_SESSION['session_login'])) {
    //We list his messages in a table
    //Two queries are executes, one for the unread messages and another for read messages
    $req1 = pg_query($con,
        "select * from pm  join user_t on pm.user1=user_t.id where pm.user2=" . $_SESSION['userid'] . " and pm.user2read='no'");
    $req2 = pg_query($con,
        "select * from pm join user_t on pm.user2=user_t.id where pm.user1=" . $_SESSION['userid'] . "
union
select * from pm join user_t on pm.user1=user_t.id where pm.user2=" . $_SESSION['userid'] . " and pm.user2read='yes'");
    ?>
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
                        <div class="card1" style="padding: 5%">

                            <a href="new_pm.php" class="btn btn-danger btn-round" >
                                <i class="nc-icon nc-simple-add"></i> Начать диалог
                            </a>


                            <h3>Новые Диалоги(<?php echo intval(pg_num_rows($req1)); ?>):</h3><br>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">Тема Диалога</th>
                                    <th scope="col">Собеседник</th>
                                    <th scope="col">Дата отправления</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                //We display the list of unread messages
                                while ($dn1 = pg_fetch_array($req1)) {
                                    ?>
                                    <tr>
                                        <td class="left"><a
                                                    href="read_pm.php?id=<?php echo $dn1['dialogId']; ?>"><?php echo htmlentities($dn1['title'],
                                                    ENT_QUOTES, 'UTF-8'); ?></a></td>

                                        <td><p><?php echo htmlentities($dn1['name'],
                                                    ENT_QUOTES, 'UTF-8'); ?></p></td>
                                        <td><?php echo date('Y/m/d H:i:s', $dn1['timestamp']); ?></td>
                                    </tr>
                                    <?php
                                }
                                //If there is no unread message we notice it
                                if (intval(pg_num_rows($req1)) == 0) {
                                    ?>
                                    <tr>
                                        <td colspan="4" class="center">You have no unread message.</td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <h3>Открытые Диалоги(<?php echo intval(pg_num_rows($req2)); ?>):</h3><br>
                            <table class="table">
                                <thead>
                                <tr>
                                    <th scope="col">Тема Диалога</th>
                                    <th scope="col">Собеседник</th>
                                    <th scope="col">Дата отправления</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                //We display the list of read messages
                                while ($dn2 = pg_fetch_array($req2)) {
                                    ?>
                                    <tr>
                                        <td class="left"><a
                                                    href="read_pm.php?id=<?php echo $dn2['dialogId']; ?>"><?php echo htmlentities($dn2['title'],
                                                    ENT_QUOTES, 'UTF-8'); ?></a></td>
                                        <!--                    <td>--><?php //echo $dn2['reps']-1;
                                        ?><!--</td>-->
                                        <td><p><?php echo htmlentities($dn2['name'],
                                                    ENT_QUOTES, 'UTF-8'); ?></p></td>
                                        <td><?php echo date('Y/m/d H:i:s', $dn2['timestamp']); ?></td>
                                    </tr>
                                    <?php
                                }
                                //If there is no read message we notice it
                                if (intval(pg_num_rows($req2)) == 0) {
                                    ?>
                                    <tr>
                                        <td colspan="4" class="center">You have no read message.</td>
                                    </tr>
                                    <?php
                                }}
                                ?>
                                </tbody>
                            </table>
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
<?php include("includes/footer.php"); ?>