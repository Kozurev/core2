<?php

    $oUser = Core::factory("User")->getCurrent();
    //$pageUserId = Core_Array::getValue($_GET, "userid", 0);

    is_object( $this->oStructureItem )
        ?   $areaId = $this->oStructureItem->getId()
        :   $areaId = 0;


        if(
            User::checkUserAccess( ["groups" => [2]], $oUser )
            || ( User::checkUserAccess( ["groups" => [6]], $oUser ) && is_object( $this->oStructureItem ) )
        )   $this->css("/templates/template7/css/style.css");

        if(
            User::checkUserAccess( ["groups" => [2, 4]], $oUser )
            || ( User::checkUserAccess( ["groups" => [6]], $oUser ) && is_object( $this->oStructureItem ) )
        ) { ?>
        <div class="calendar_small">
            <input type="date" class="form-control schedule_calendar" />
            <span class="day_name"></span>
        </div>

        <div style="float: right; margin: 20px 0px">
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
    <input type="hidden" id="areaid" value="<?=$areaId?>" />

