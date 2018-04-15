<!DOCTYPE html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?=$this->title;?></title>

    <?$this
        ->showCss()
        ->js("/templates/template4/js/jquery.min.js")
        ->showJs();
    ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</head>
<body>
    <?
    global $CFG;
    $oUser = Core::factory("User")->getCurent();
    $name = $oUser->name();
    $surname = $oUser->surname();
    $isAdmin = $oUser->groupId() <= 3;
    $rootdir = "/" . $CFG->rootdir;
    $back = $_SERVER['HTTP_HOST'] . $rootdir;
    ?>
    <div class="container">

        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="<?=$rootdir?>">Musicmethod</a>
                </div>
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="<?=$rootdir?>user">Расписание
                            <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="#">Мичурина</a></li>
                            <li><a href="#">Щорса</a></li>
                        </ul>
                    </li>
                    <?
                    if(!$isAdmin)
                    {
                        ?>
                        <li><a href="#">Сменить логин или пароль</a></li>
                        <li><a href="#">Договор оферты</a></li>
                        <?
                    }
                    if($isAdmin)
                    {
                        ?>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="<?=$rootdir?>user">Пользователи
                                <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?=$rootdir?>user/teacher">Штат</a></li>
                                <li><a href="<?=$rootdir?>user/client">Клиенты</a></li>
                            </ul>
                        </li>
                        <li><a href="#">Группы</a></li>
                        <li><a href="#">Архив</a></li>
                        <li><a href="#">Лиды</a></li>
                        <li><a href="#">Сертификаты</a></li>
                        <li><a href="#">Задачи</a></li>
                        <?
                    }
                    ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li><a><?echo $surname . " " . $name; ?></a></li>
                    <li><a href="<?=$rootdir?>authorize?disauthorize=1&back=<?=$back?>">Выйти</a></li>
                </ul>
            </div>
        </nav>

        <div class="page">
            <?$this->execute();?>
        </div>
    </div>


</body>
</html>





