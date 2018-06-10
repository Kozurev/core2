<?
$this->css("/templates/template7/css/style.css");
?>

<?php

    $oCurentUser = Core::factory("User")->getCurrent();
    $pageUserId = Core_Array::getValue($_GET, "userid", 0);
    if($pageUserId > 0)
    {
        $oUser = Core::factory("User", $pageUserId);
    }
    else
    {
        $oUser = $oCurentUser;
    }
?>

    <div class="calendar_small">
        <input type="date" class="form-control schedule_calendar" />
    </div>

    <div style="text-align: right">
        <button class="btn btn-success schedule_task_create">Написать администратору</button>
    </div>

    <input type="hidden" id="userid" value="<?=$oUser->getId()?>">
    <input type="hidden" id="areaid" value="<?=$_GET['area']?>">

    <div class="schedule">
    <?$this->execute();?>
    </div>