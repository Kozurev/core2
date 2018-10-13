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

        <div class="row calendar_small">
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-7 col-lg-offset-3 col-md-offset-0 col-sm-offset-0">
                <input type="date" class="form-control schedule_calendar" />
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6 col-xs-5">
                <span class="day_name"></span>
            </div>
        </div>

        <div class="row buttons-panel">
            <div class="col-lg-3 col-md-6 col-sm-12">
                <a class="btn btn-green schedule_task_create">Написать администратору</a>
            </div>
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

