<?php
    global $CFG;
?>
<h3>Настройка интеграции</h3>
<ul class="list">
    <?php
    foreach (Core_Page_Show::instance()->Structure->getChildren() as $structure) {
        echo '<li><a href="'.$CFG->rootdir.'/'.Core_Page_Show::instance()->Structure->path().'/'.$structure->path().'">'.$structure->title().'</a></li>';
    }
    ?>
</ul>