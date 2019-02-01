<?php

Core_Page_Show::instance()->css( "/templates/template6/css/style.css" );

//echo "<input type='hidden' id='taskAfterAction' value='balance' />";

echo "<div class='users'>";
Core_Page_Show::instance()->execute();
echo "</div>";
?>
