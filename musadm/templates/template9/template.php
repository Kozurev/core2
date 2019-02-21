<?php
/**
 * Макет для раздела "Статистика"
 *
 * @author Bad Wolf
 * @date 03.06.2018 12:53
 * @version 20190221
 */

$calendarRow = Core::factory( 'Core_Entity' )
    ->xsl( 'musadm/statistic/calendar.xsl' )
    ->show( false );

$areasSelectRow = Core::factory( 'Core_Entity' )
    ->addEntities(
        Core::factory( 'Schedule_Area_Assignment' )
            ->getAreas( User::current() )
    )
    ->xsl( 'musadm/statistic/areas_select.xsl' )
    ->show( false );
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
