<?php

    $pageUserId = Core_Array::Get( "userid", null );
    $subordinated = User::current()->getDirector()->getId();

    if( is_null( $pageUserId ) )
    {
        $User = Core::factory("User")->getCurrent();
    }
    else
    {
        $User = Core::factory( "User", $pageUserId );
    }

    /**
     * Проверка на принадлежность клиента, под которым происходит авторизация,
     * тому же директору, которому принадлежит и менеджер
     */
    if( $User->subordinated() !== $subordinated && !is_null( $pageUserId ) )
    {
        die( "Доступ к личному кабинету данного пользователя заблокирован, так как он принадлежит другой организации" );
    }


    is_object( Core_Page_Show::instance()->StructureItem )
        ?   $areaId = Core_Page_Show::instance()->StructureItem->getId()
        :   $areaId = 0;


        if(
            User::checkUserAccess( ["groups" => [2]], $User )
            || ( User::checkUserAccess( ["groups" => [6]], $User ) && is_object( Core_Page_Show::instance()->StructureItem ) )
        )   Core_Page_Show::instance()->css("/templates/template7/css/style.css");

        if(
            User::checkUserAccess( ["groups" => [2, 4]], $User )
            || ( User::checkUserAccess( ["groups" => [6]], $User ) && is_object( Core_Page_Show::instance()->StructureItem ) )
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
        <?php Core_Page_Show::instance()->execute();?>
    </div>

    <input type="hidden" id="userid" value="<?=$User->getId()?>" />
    <input type="hidden" id="areaid" value="<?=$areaId?>" />

