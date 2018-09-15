<?php

$this->css( "templates/template6/css/style.css" );

/**
 * Вывод панели с заметками пользователя и датой последней авторизации 
 */
$oCurentUser = Core::factory("User")->getCurrent();
//$pageUserId = Core_Array::getValue($_GET, "userid", 0);
User::isAuthAs() ? $isAdmin = 1 : $isAdmin = 0;

//if($oCurentUser->groupId() < 4 && $pageUserId > 0)
//    $oUser = Core::factory("User", $pageUserId);
//else
    $oUser = $oCurentUser;

/**
 * Пользовательские примечания и дата последней авторизации
 */
//if($oCurentUser->groupId() < 4 && $oUser->groupId() == 5)
if( $isAdmin && $oCurentUser->groupId() == 5 )
{
    $oPropertyNotes = Core::factory("Property", 19);
    $clienNotes = $oPropertyNotes->getPropertyValues($oUser);

    $oPropertyLastEntry = Core::factory("Property", 22);
    $lastEntry = $oPropertyLastEntry->getPropertyValues($oUser);

    Core::factory("Core_Entity")
        ->addEntities($clienNotes, "note")
        ->addEntities($lastEntry, "entry")
        ->xsl("musadm/client_notes.xsl")
        ->show();
}

echo "<div class='users'>";
$this->execute();
echo "</div>";
?>
