<?php
/**
 * Макет для раздела "Расписание"
 *
 * @author BadWolf
 * @version 20190322
 */

$pageUserId = Core_Array::Get('userid', null, PARAM_INT);
$subordinated = User::current()->getDirector()->getId();

if (is_null($pageUserId)) {
    $User = User::current();
} else {
    Core::factory('User_Controller');
    $User = User_Controller::factory($pageUserId);
}

is_object(Core_Page_Show::instance()->StructureItem)
    ?   $areaId = Core_Page_Show::instance()->StructureItem->getId()
    :   $areaId = 0;

    if (User::checkUserAccess(['groups' => [ROLE_MANAGER]], $User)
        || (User::checkUserAccess(['groups' => [ROLE_DIRECTOR]], $User) && is_object(Core_Page_Show::instance()->StructureItem))
    )
    {
        Core_Page_Show::instance()->css('/templates/template7/css/style.css');
    }

    if (User::checkUserAccess(['groups' => [ROLE_MANAGER, ROLE_TEACHER]], $User)
        || (User::checkUserAccess(['groups' => [ROLE_DIRECTOR]], $User) && is_object(Core_Page_Show::instance()->StructureItem))
    ) { ?>
    <section>
        <div class="row calendar_small">
            <div>
                <input type="date" class="form-control schedule_calendar" value="<?=date('Y-m-d')?>" />
            </div>
            <div>
                <span class="day_name"></span>
            </div>
            <div>
                <a class="btn btn-green" onclick="newScheduleTaskPopup()">Написать администратору</a>
            </div>
        </div>
    </section>
<? }else { ?>
    <script>
        $(function(){
            var date = new Date();
            var year = date.getFullYear();
            var month = date.getMonth() + 1;

            if( month < 10 ) month = '0' + month;

            $('#year').val(year);
            $('#month').val(month);
        });
    </script>
<? } ?>

<section class="schedule">
    <?php
    Core_Page_Show::instance()->execute();
    ?>
</section>

<input type="hidden" id="userid" value="<?=$User->getId()?>" />
<input type="hidden" id="areaid" value="<?=$areaId?>" />

