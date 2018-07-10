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

    if( $oUser->groupId() < 5 )
        $this->css("/templates/template7/css/style.css");

    if( $oUser->groupId() < 5 ) { ?>
        <div class="calendar_small">
            <input type="date" class="form-control schedule_calendar" />
            <span class="day_name"></span>
        </div>

        <div style="text-align: right">
            <a class="btn btn-green schedule_task_create">Написать администратору</a>
        </div>
    <? }else { ?>
        <script>
            $(function(){
                var date = new Date();
                var year = date.getFullYear();
                var month = date.getMonth() + 1;

                if( month < 10 ) month = "0" + month;

                $("#year").val(year);
                $("#month").val(month);
            });
        </script>
    <? } ?>

    <div class="schedule">
        <?php $this->execute();?>
    </div>

    <input type="hidden" id="userid" value="<?=$oUser->getId()?>" />
    <input type="hidden" id="areaid" value="<?=$this->oStructureItem->getId()?>" />

