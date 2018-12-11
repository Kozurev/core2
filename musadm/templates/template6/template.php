<?php

$this->css( "/templates/template6/css/style.css" );

/**
 * Вывод панели с заметками пользователя и датой последней авторизации 
 */
$pageUserId = Core_Array::Get( "userid", null );

//User::isAuthAs() ? $isAdmin = 1 : $isAdmin = 0;
if( is_null( $pageUserId ) )
    $oUser = User::current();
else
    $oUser = Core::factory( "User", $pageUserId );

/**
 * Пользовательские примечания и дата последней авторизации
 */
if( !is_null( $pageUserId ) )
{
    $oPropertyNotes = Core::factory( "Property", 19 );
    $clienNotes = $oPropertyNotes->getPropertyValues( $oUser );

    $oPropertyPerLesson = Core::factory( "Property", 32 );
    $perLesson = $oPropertyPerLesson->getPropertyValues( $oUser );

    $oPropertyLastEntry = Core::factory( "Property", 22 );
    $lastEntry = $oPropertyLastEntry->getPropertyValues( $oUser );

    Core::factory("Core_Entity")
        ->addEntities($clienNotes, "note")
        ->addEntities($lastEntry, "entry")
        ->addEntities($perLesson, "per_lesson")
        ->xsl("musadm/client_notes.xsl")
        ->show();
}

echo "<div class='users'>";
$this->execute();
echo "</div>";
?>
