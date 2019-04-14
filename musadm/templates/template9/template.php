<?php
/**
 * Макет для раздела "Статистика"
 *
 * @author Bad Wolf
 * @date 03.06.2018 12:53
 * @version 20190221
 * @version 20190414
 */

$dateFormat = 'Y-m-d';
$date = date($dateFormat);
$dateFrom = Core_Array::Get('date_from', $date, PARAM_DATE);
$dateTo =   Core_Array::Get('date_to', $date, PARAM_DATE);
$calendarRow = Core::factory('Core_Entity')
    ->addSimpleEntity('date_from', $dateFrom)
    ->addSimpleEntity('date_to', $dateTo)
    ->xsl('musadm/statistic/calendar.xsl')
    ->show(false);

$areasSelectRow = Core::factory('Core_Entity')
    ->addEntities(
        Core::factory('Schedule_Area_Assignment')
            ->getAreas(User::current())
    )
    ->xsl('musadm/statistic/areas_select.xsl')
    ->show(false);
?>

<section>
    <?=$calendarRow?>
    <?=$areasSelectRow?>
</section>

<section class="statistic">
    <?php
        Core_Page_Show::instance()->execute();
    ?>
</section>
