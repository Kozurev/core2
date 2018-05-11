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

    <input type="hidden" id="userid" value="<?=$oUser->getId()?>">
    <input type="hidden" id="areaid" value="<?=$_GET['area']?>">

    <div class="schedule">
    <?$this->execute();?>
    </div>