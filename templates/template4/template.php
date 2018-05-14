<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?=$this->title;?></title>

    <?$this
        ->css("/templates/template4/lib/tablesorter/css/theme.default.css")
        ->css("/templates/template4/css/bootstrap.min.css")
        ->css("/templates/template4/css/bootstrap-theme.min.css")
        ->css("/templates/template4/css/table.css")
        ->css("/templates/template4/css/buttons.css")
        ->css("/templates/template4/css/users.css")
        ->css("/templates/template4/css/popup.css")
        ->css("/templates/template4/css/lids.css")
        ->showCss()
        ->js("/templates/template4/js/jquery.min.js")
        ->js("/templates/template4/js/jquery.validate.min.js")
        ->js("/templates/template4/js/bootstrap.min.js")
        ->js("/templates/template4/lib/tablesorter/js/jquery.tablesorter.js")
        ->js("/templates/template4/lib/tablesorter/js/jquery.tablesorter.widgets.js")
        ->js("/templates/template4/lib/tablesorter/addons/pager/jquery.tablesorter.pager.js")
        ->js("/templates/template4/lib/tablesorter/beta-testing/pager-custom-controls.js")
        ->js("/templates/template4/js/main.js")
        ->js("/templates/template4/js/users.js")
        ->js("/templates/template4/js/payments.js")
        ->js("/templates/template4/js/groups.js")
        ->js("/templates/template4/js/lids.js")
        ->js("/templates/template4/js/schedule.js")
        ->showJs();
    ?>

</head>
<body>
    <div class="loader" style="display: none"></div>

    <div class="popup"></div>
    <div class="overlay"></div>

        <?
        global $CFG;
        $oCurentUser = Core::factory("User")->getCurrent();
        $pageUserId = Core_Array::getValue($_GET, "userid", 0); //id просматриваемого пользователя администратором
        $rootdir = "/" . $CFG->rootdir;
        $back = $_SERVER['HTTP_HOST'] . $rootdir;
        $disauthorizeLink = $rootdir . "authorize?disauthorize=1&back=" . $back;


        //Если администратор авторизован под учетной записью пользователя
        if($oCurentUser->groupId() < 4 && $pageUserId > 0)
        {
            $oUser = Core::factory("User", $pageUserId);
            $oUserGroup = Core::factory("User_Group", $oUser->groupId());
            $disauthorizeLink = $rootdir . "user/" . $oUserGroup->path();
            $sUserId = "?userid=" . $oUser->getId();
            $iUserId = $oUser->getId();
        }
        else
        {
            $oUser = $oCurentUser;
            $oUserGroup = Core::factory("User_Group", $oUser->groupId());
            $disauthorizeLink = $rootdir . "authorize?disauthorize=1&back=" . $back;
            $sUserId = "";
            $iUserId = 0;
        }


        $name = $oUser->name();
        $surname = $oUser->surname();
        $isAdmin = $oUser->groupId() <= 3;
        ?>
        <div class="container">

            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="<?=$rootdir?>" >Musicmetod</a>
                    </div>
                    <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="<?=$rootdir?>user">Расписание
                                <span class="caret"></span></a>
                            <ul class="dropdown-menu">
<!--                                <li><a href="--><?//=$rootdir?><!--schedule?area=1" >Мичурина</a></li>-->
<!--                                <li><a href="--><?//=$rootdir?><!--schedule?area=2" >Щорса</a></li>-->
                                <?
                                $aoAreas = Core::factory("Schedule_Area")->orderBy("sorting")->findAll();
                                foreach ($aoAreas as $area)
                                {
                                    $href = $rootdir . "schedule/" . $area->path();
                                    if($iUserId != 0)   $href .= "?userid=" . $iUserId;
                                    echo "<li><a href='".$href."'>";
                                    echo $area->title();
                                    echo "</a></li>";
                                }
                                ?>
                            </ul>
                        </li>
                        <?
                        //Пункты только для клиентов
                        if(!$isAdmin && $oUserGroup->getId() == 5)
                        {
                            ?>
                            <li><a href="<?=$rootdir?>user/balance<?=$sUserId?>" >Баланс</a></li>
                            <li><a href="#" >Сменить логин или пароль</a></li>
                            <?
                        }
                        //Пункты только для администратора, директора или менеджера
                        if($isAdmin)
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
<!--                            <li><a href="--><?//=$rootdir?><!--tarif" >Тарифы</a></li>-->
                            <li><a href="<?=$rootdir?>lids">Лиды</a></li>
                            <li><a href="#">Сертификаты</a></li>
                            <li><a href="#">Задачи</a></li>
                            <?
                        }
                        ?>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <?
                        if($oCurentUser->groupId() < 4 && $oUserGroup->getId() == 5)
                        {
                            echo "<li><a>". $oUser->phoneNumber()."</a></li>";
                        }
                        ?>
                        <li><a><?echo $surname . " " . $name; ?></a></li>
                        <li><a href="<?=$disauthorizeLink?>">Выйти</a></li>
                    </ul>
                </div>
            </nav>

            <div class="page">
                <?$this->execute();?>
            </div>
        </div>

</body>
</html>





