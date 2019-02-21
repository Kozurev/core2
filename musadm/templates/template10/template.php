<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=Core_Page_Show::instance()->title;?></title>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Bitter' rel='stylesheet'>
    <?
    Core_Page_Show::instance()
        ->css( '/templates/template10/assets/plugins/bootstrap/css/bootstrap.min.css' )
        ->css( '/templates/template10/assets/plugins/font-awesome/css/font-awesome.css' )
        ->css( '/templates/template10/assets/plugins/elegant_font/css/style.css' )
        ->css( '/templates/template10/assets/css/popup.css' )
        ->css( '/templates/template10/assets/css/styles.css' )
        ->css( '/templates/template10/assets/css/custom.css' )
        ->css( '/templates/template10/assets/css/balance.css' )
        ->css( '/templates/template10/assets/css/lids.css' )
        ->css( '/templates/template10/assets/css/finances.css' )
        ->css( '/templates/template10/assets/css/tasks.css' )
        ->css( '/templates/template10/assets/css/checkbox.css' )
        ->css( '/templates/template10/assets/css/tooltip.css' )
        ->css( '/templates/template10/assets/css/scroll.css' )
        ->css( '/templates/template10/assets/css/radiobutton.css' )
        ->js( '/templates/template10/assets/plugins/jquery.min.js' );

        global $CFG;
        Core::factory( 'User_Controller' );
        $Director = User::current()->getDirector();
        $subordinated = $Director->getId();
        $pageUserId = Core_Array::Get( 'userid', null, PARAM_INT );

        is_null( $pageUserId )
            ?   $User = User::current()
            :   $User = User_Controller::factory( $pageUserId );

        if ( is_null( $pageUserId ) )
        {
            $disauthorizeLink = 'href="' . $CFG->rootdir . '/authorize?auth_revert=1' . '"';
        }
        else
        {
            $UserGroup = Core::factory( 'User_Group', $User->groupId() );
            $disauthorizeLink = 'href="' . $CFG->rootdir . '/user/' . $UserGroup->path() . '/"';
        }

        $name =     $User->name();
        $surname =  $User->surname();
        $isAdmin =  $User->groupId() <= ROLE_MANAGER;

        switch ( Core_Page_Show::instance()->getParam( 'body-class', '' ) )
        {
            case 'body-orange':     $hover = '#F88C30';     break;
            case 'body-primary':    $hover = '#40babd';     break;
            case 'body-blue':       $hover = '#58bbee';     break;
            case 'body-red':        $hover = '#f77b6b';     break;
            case 'body-pink':       $hover = '#EA5395';     break;
            case 'body-green':      $hover = '#75c181';     break;
            default:                $hover = 'black';
        }
    ?>
</head>

<body class="<?=$this->getParam( 'body-class', '' )?>">

    <style>
        :root {
            --hover-color: <?=$hover?>;
        }
    </style>

    <div class="loader" style="display: none"></div>
    <div class="popup"></div>
    <div class="overlay"></div>

    <input type="hidden" id="rootdir" value="<?=$CFG->rootdir?>"/>

    <div id="fb-root"></div>
        <div class="page-wrapper">
            <header id="header" class="header">
                <nav class="navbar navbar-inverse">
                    <div class="container-fluid">
                        <div class="navbar-header">
                            <a class="navbar-brand" href="<?=$CFG->rootdir?>" >Musicmetod</a>
                        </div>
                        <ul class="nav navbar-nav">
                            <?php
                            if ( User::checkUserAccess( ['groups' => [ROLE_MANAGER]] ) )
                            {
                            ?>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="<?=$CFG->rootdir?>/user">Расписание
                                    <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <?php
                                    $Areas = Core::factory( 'Schedule_Area_Assignment' )->getAreas( $User, true );

                                    foreach ( $Areas as $area )
                                    {
                                        $href = $CFG->rootdir . '/schedule/' . $area->path();
                                        echo "<li><a href='" . $href . "'>";
                                        echo $area->title();
                                        echo "</a></li>";
                                    }
                                    ?>
                                </ul>
                            </li>
                            <?php
                            }
                            elseif ( User::checkUserAccess( ['groups' => [ROLE_DIRECTOR, ROLE_TEACHER]] ) )
                            {
                            ?>
                                <li><a href="<?=$CFG->rootdir?>/schedule">Расписание</a></li>
                            <?php
                            }

                            //Пункты только для клиентов
                            if ( User::checkUserAccess( ['groups' => [ROLE_CLIENT]] ) )
                            {

                                $linkBalance = $CFG->rootdir . '/balance';
                                $linkChangelogin = $CFG->rootdir . '/changelogin';

                                if ( $pageUserId !== null )
                                {
                                    $linkBalance .= '?userid=' . $pageUserId;
                                    $linkChangelogin .= '?userid=' . $pageUserId;
                                }

                            ?>
                                <li><a href="<?=$linkBalance?>" >Баланс</a></li>
                                <li><a href="<?=$linkChangelogin?>" >Сменить логин или пароль</a></li>
                            <?php
                            }

                            //Пункты только для администратора, директора или менеджера
                            if ( User::checkUserAccess( ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]] ) )
                            {
                            ?>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="<?=$CFG->rootdir?>/user">Пользователи
                                        <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="<?=$CFG->rootdir?>/user/teacher" >Штат</a></li>
                                        <li><a href="<?=$CFG->rootdir?>/user/client" >Клиенты</a></li>
                                        <li><a href="<?=$CFG->rootdir?>/user/archive">Архив</a></li>
                                    </ul>
                                </li>
                                <li><a href="<?=$CFG->rootdir?>/groups">Группы</a></li>
                                <li><a href="<?=$CFG->rootdir?>/lids">Лиды</a></li>
                                <li><a href="<?=$CFG->rootdir?>/certificates">Сертификаты</a></li>
                                <li><a href="<?=$CFG->rootdir?>/tasks">Задачи</a></li>
                                <?php
                                if ( User::checkUserAccess( ['groups' => [ROLE_DIRECTOR]] ) )
                                {
                                    echo "<li><a href='" . $CFG->rootdir . "/finances'>Финансы</a></li>";
                                    echo "<li><a href='".$CFG->rootdir."/statistic'>Статистика</a></li>";
                                }
                                ?>
                            <?php
                            }
                            ?>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <?
                            if ( !is_null( $pageUserId ) )
                            {
                                echo "<li><a>". $User->phoneNumber()."</a></li>";
                            }
                            ?>
                            <li><a><?=$surname . ' ' . $name?></a></li>
                            <li><a><?=$User->getOrganizationName()?></a></li>
                            <li><a <?=$disauthorizeLink?>>Выйти</a></li>
                        </ul>
                    </div>
                </nav>

                <div class="container">
                    <?php
                        Core::factory( 'Core_Entity' )
                            ->addSimpleEntity( 'rootdir', $CFG->rootdir )
                            ->addSimpleEntity( 'title-first', Core_Page_Show::instance()->getParam( 'title-first' ) )
                            ->addSimpleEntity( 'title-second', Core_Page_Show::instance()->getParam( 'title-second' ) )
                            ->addEntities( Core_Page_Show::instance()->getParam( 'breadcumbs' ), 'breadcumb' )
                            ->xsl( 'musadm/header.xsl' )
                            ->show();
                    ?>
                </div><!--//container-->
            </header>

            <div class="container page">
                <?php
                Core_Page_Show::instance()->execute();
                ?>
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
        <small class="copyright">Щорса 54, оф.307 тел. 37-42-11, моб. +7 909 201 25 50</small>
    </div><!--//container-->
</footer><!--//footer-->


<?php
Core_Page_Show::instance()
    ->js( '/templates/template10/assets/plugins/bootstrap/js/bootstrap.min.js' )
    ->js( '/templates/template10/assets/js/jquery.validate.min.js' )
    ->js( '/templates/template10/assets/js/jquery.maskedinput.min.js' )
    ->js( '/templates/template4/lib/tablesorter/js/jquery.tablesorter.js' )
    ->js( '/templates/template4/lib/tablesorter/js/jquery.tablesorter.widgets.js' )
    ->js( '/templates/template4/lib/tablesorter/addons/pager/jquery.tablesorter.pager.js' )
    ->js( '/templates/template4/lib/tablesorter/beta-testing/pager-custom-controls.js' )
    ->js( '/templates/template10/assets/plugins/prism/prism.js' )
    ->js( '/templates/template10/assets/plugins/jquery-scrollTo/jquery.scrollTo.min.js' )
    ->js( '/templates/template10/assets/plugins/lightbox/dist/ekko-lightbox.min.js' )
    ->js( '/templates/template10/assets/plugins/jquery-match-height/jquery.matchHeight-min.js' )
    ->js( '/templates/template10/assets/js/main.js' )
    ->js( '/templates/template4/js/bootstrap.min.js' )
    ->js( '/templates/template4/js/jquery.validate.min.js' )
    ->js( '/templates/template4/js/bootstrap.min.js' )
    ->js( '/templates/template4/lib/tablesorter/js/jquery.tablesorter.js' )
    ->js( '/templates/template4/lib/tablesorter/js/jquery.tablesorter.widgets.js' )
    ->js( '/templates/template4/lib/tablesorter/addons/pager/jquery.tablesorter.pager.js' )
    ->js( '/templates/template4/lib/tablesorter/beta-testing/pager-custom-controls.js' )
    ->js( '/templates/template4/js/main.js' )
    ->js( '/templates/template4/js/users.js' )
    ->js( '/templates/template4/js/payments.js' )
    ->js( '/templates/template4/js/groups.js' )
    ->js( '/templates/template4/js/lids.js' )
    ->js( '/templates/template4/js/schedule.js' )
    ->js( '/templates/template4/js/tasks.js' )
    ->js( '/templates/template4/js/certificates.js' )
    ->js( '/templates/template4/js/finances.js' )
    ->js( '/templates/template4/js/statistic.js' )
    ->js( '/templates/template4/js/areas_assignments.js' )
    ->js( '/templates/template4/js/property_list.js' )
    ->js( '/templates/template4/js/js.js' );
?>

</body>
</html>
