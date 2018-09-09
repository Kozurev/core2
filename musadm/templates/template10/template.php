<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$this->title;?></title>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <?
    $this
        ->css( "/templates/template10/assets/plugins/bootstrap/css/bootstrap.min.css" )
        ->css( "/templates/template10/assets/plugins/font-awesome/css/font-awesome.css" )
        ->css( "/templates/template10/assets/plugins/elegant_font/css/style.css" )
        ->css( "/templates/template10/assets/css/popup.css" )
        ->css( "/templates/template10/assets/css/styles.css" )
        ->css( "/templates/template10/assets/css/custom.css" )
        ->js( "/templates/template10/assets/plugins/jquery.min.js" );

        global $CFG;
        $oUser = Core::factory("User")->getCurrent();
        $oUserGroup  = $oUser->getParent();
        $rootdir = "/" . $CFG->rootdir;
        $disauthorizeLink = $rootdir . "authorize?auth_revert=1";

        $name = $oUser->name();
        $surname = $oUser->surname();
        $isAdmin = $oUser->groupId() <= 3;

        $Director = User::current()->getDirector();
        if( !$Director )    die( Core::getMessage("NOT_DIRECTOR") );
        $subordinated = $Director->getId();
    ?>
</head>

<body class="<?=$this->getParam( "body-class", "" )?>">

    <div class="loader" style="display: none"></div>
    <div class="popup"></div>
    <div class="overlay"></div>


    <div id="fb-root"></div>
        <div class="page-wrapper">
            <header id="header" class="header">
                <nav class="navbar navbar-inverse">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <a class="navbar-brand" href="<?=$rootdir?>" >Musicmetod</a>
                        </div>
                        <ul class="nav navbar-nav">
                            <?
                            if( $oUser->groupId() == 2 )
                            { ?>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="<?=$rootdir?>user">Расписание
                                    <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <?
                                    $aoAreas = Core::factory("Schedule_Area")
                                        ->where( "active", "=", 1 )
                                        ->where( "subordinated", "=", $subordinated )
                                        ->orderBy("sorting")
                                        ->findAll();
                                    foreach ($aoAreas as $area)
                                    {
                                        $href = $rootdir . "schedule/" . $area->path();
                                        echo "<li><a href='".$href."'>";
                                        echo $area->title();
                                        echo "</a></li>";
                                    }
                                    ?>
                                </ul>
                            </li>
                            <? } elseif( $oUser->groupId() == 4 || $oUser->groupId() == 6 ) {?>
                                <li><a href="<?=$rootdir?>schedule">Расписание</a></li>
                            <? }

                            //Пункты только для клиентов
                            if( $oUser->groupId() == 5 )
                            {
                                ?>
                                <li><a href="<?=$rootdir?>user/balance<?=$sUserId?>" >Баланс</a></li>
                                <li><a href="<?=$rootdir?>user/changelogin" >Сменить логин или пароль</a></li>
                                <?
                            }

                            //Пункты только для администратора, директора или менеджера
                            if( $oUser->groupId() == 2 || $oUser->groupId() == 6 )
                            {
                                ?>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="<?=$rootdir?>user">Пользователи
                                        <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?=$rootdir?>user/teacher" >Штат</a></li>
                                        <li><a href="<?=$rootdir?>user/client" >Клиенты</a></li>
                                        <li><a href="<?=$rootdir?>user/archive">Архив</a></li>
                                    </ul>
                                </li>
                                <li><a href="<?=$rootdir?>groups">Группы</a></li>
                                <li><a href="<?=$rootdir?>lids">Лиды</a></li>
                                <li><a href="<?=$rootdir?>certificates">Сертификаты</a></li>
                                <li><a href="<?=$rootdir?>finances">Финансы</a></li>
                                <li><a href="<?=$rootdir?>tasks">Задачи</a></li>
                                <?
                                if( User::checkUserAccess(["groups" => [6]]) )
                                {
                                    echo "<li><a href='".$rootdir."tarif'>Тарифы</a></li>";
                                    echo "<li><a href='".$rootdir."statistic'>Статистика</a></li>";
                                }
                            }
                            ?>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <?
                            if( User::isAuthAs() && User::checkUserAccess(["groups" => [4, 5]]) )
                            {
                                echo "<li><a>". $oUser->phoneNumber()."</a></li>";
                            }
                            ?>
                            <li><a><?echo $surname . " " . $name; ?></a></li>
                            <li><a href="<?=$disauthorizeLink?>">Выйти</a></li>
                        </ul>
                    </div>
                </nav>

                <div class="container">
                    <?php
                        Core::factory( "Core_Entity" )
                            ->addSimpleEntity( "title-first", $this->getParam( "title-first" ) )
                            ->addSimpleEntity( "title-second", $this->getParam( "title-second" ) )
                            ->addEntities( $this->getParam( "breadcumbs" ), "breadcumb" )
                            ->xsl( "musadm/header.xsl" )
                            ->show();
                    ?>
                </div><!--//container-->
            </header>

            <div class="container page">
                <?php $this->execute();?>
            </div>

            <div id="ekkoLightbox-640" class="ekko-lightbox modal fade in" tabindex="-1" style="padding-right: 17px;">
                <div class="modal-dialog" style="width: auto; max-width: 1032px;">
                    <div class="modal-content"><div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title"></h4>
                        </div>
                        <div class="modal-body">
                            <div class="ekko-lightbox-container">
                                <div class="content"></div>
                            </div>
                        </div>
                        <div class="modal-footer" style="display:none">null</div>
                    </div>
                </div>
            </div>

        </div><!--//page-wrapper-->

<footer class="footer text-center">
    <div class="container">
        <small class="copyright"><a href="http://musicmetod.ru/" target="_blank">ООО"Мьюзикметод"</a></small>
        <small class="copyright">Щорса 54, оф.307 37-42-11, +79092012550</small>
    </div><!--//container-->
</footer><!--//footer-->


<?php
$this
    ->js( "/templates/template10/assets/plugins/bootstrap/js/bootstrap.min.js" )
    ->js( "/templates/template10/assets/js/jquery.validate.min.js" )
    ->js( "/templates/template4/lib/tablesorter/js/jquery.tablesorter.js" )
    ->js( "/templates/template4/lib/tablesorter/js/jquery.tablesorter.widgets.js" )
    ->js( "/templates/template4/lib/tablesorter/addons/pager/jquery.tablesorter.pager.js" )
    ->js( "/templates/template4/lib/tablesorter/beta-testing/pager-custom-controls.js" )
    ->js( "/templates/template10/assets/plugins/prism/prism.js" )
    ->js( "/templates/template10/assets/plugins/jquery-scrollTo/jquery.scrollTo.min.js" )
    ->js( "/templates/template10/assets/plugins/lightbox/dist/ekko-lightbox.min.js" )
    ->js( "/templates/template10/assets/plugins/jquery-match-height/jquery.matchHeight-min.js" )
    ->js( "/templates/template10/assets/js/main.js" )
    ->js("/templates/template4/js/bootstrap.min.js")
    ->js( "/templates/template4/js/jquery.validate.min.js" )
    ->js( "/templates/template4/js/bootstrap.min.js" )
    ->js( "/templates/template4/lib/tablesorter/js/jquery.tablesorter.js" )
    ->js( "/templates/template4/lib/tablesorter/js/jquery.tablesorter.widgets.js" )
    ->js( "/templates/template4/lib/tablesorter/addons/pager/jquery.tablesorter.pager.js" )
    ->js( "/templates/template4/lib/tablesorter/beta-testing/pager-custom-controls.js" )
    ->js( "/templates/template4/js/main.js" )
    ->js( "/templates/template4/js/users.js" )
    ->js( "/templates/template4/js/payments.js" )
    ->js( "/templates/template4/js/groups.js" )
    ->js( "/templates/template4/js/lids.js" )
    ->js( "/templates/template4/js/schedule.js" )
    ->js( "/templates/template4/js/tasks.js" )
    ->js( "/templates/template4/js/certificates.js" )
    ->js( "/templates/template4/js/finances.js" )
    ->js( "/templates/template4/js/statistic.js" )
    ->js( "/templates/template4/js/js.js" );
?>

</body>
</html>
