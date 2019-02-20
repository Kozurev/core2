<?php

Core_Page_Show::instance()->css( "/templates/template6/css/style.css" );

/**
 * Фильтры для страницы клиентов
 */
if ( Core_Page_Show::instance()->StructureItem !== null && Core_Page_Show::instance()->StructureItem->getId() == 5 )
{
    $Areas =        Core::factory( 'Schedule_Area_Assignment' )->getAreas( User::current() );
    $Instruments =  Core::factory( 'Property' )->getByTagName( 'instrument' )->getList();
    $Teachers =     Core::factory( 'Property' )->getByTagName( 'teachers' )->getList();

    global $CFG;

    Core::factory( 'Core_Entity' )
        ->addSimpleEntity( 'wwwroot', $CFG->rootdir )
        ->addEntities( $Areas )
        ->addEntities( $Instruments, 'property_value' )
        ->addEntities( $Teachers, 'property_value' )
        ->xsl( 'musadm/users/client_filter.xsl' )
        ->show();
}

echo "<div class='users'>";
Core_Page_Show::instance()->execute();
echo "</div>";
?>