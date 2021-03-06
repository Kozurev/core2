<!DOCTYPE html>
<head>
    <title><?echo $this->title;?></title>
    <meta charset="utf-8">

    <?$this
        ->css('/templates/template3/css/bootstrap.min.css')
        ->css('/templates/template3/css/left_bar.css')
        ->css('/templates/template3/css/table.css')
        ->css('/templates/template3/css/pagination.css')
        ->showCss();
    ?>
</head>

<body class="body">
<div class="loader" style="display: none"></div>
<?php

/*
*	Верхняя панель
*/
if($this->oStructure->path() == "admin")
{
    $oUser = Core::factory("User")->getCurrent();
    Core::factory("Core_Entity")
        ->addEntity($oUser)
        ->xsl("admin/top_bar.xsl")
        ->show();
}

global $CFG;
$rootdir = $CFG->rootdir;
?>

<input type="hidden" id="rootdir" value="<?=$rootdir?>" />

<div class="container">
    <div class="row middle">
        <?php
        /*
        *	Вывод левого меню
        */
        if($this->oStructure->path() == "admin")
        {
            $aoMenuItems = Core::factory("Admin_Menu")
                ->where("active", "=", 1)
                ->orderBy("sorting")
                ->findAll();

            Core::factory("Core_Entity")
                ->addEntities($aoMenuItems)
                ->xsl("admin/left_bar.xsl")
                ->show();
        }
        ?>
        <div class="col-lg-9 main">
            <?
                $this->execute();
            ?>
        </div>
    </div>

    <div class="row bottom"></div>
</div>

<?
$this
    ->js('/templates/template3/js/jquery.min.js')
    ->js('/templates/template3/js/jquery.validate.min.js');

if($this->oStructure->path() == "admin")
    $this
        ->js('/templates/template3/js/admin.js')
        ->js('/templates/template3/js/left_bar.js');

$this->showJs();
?>

</body>
</html>