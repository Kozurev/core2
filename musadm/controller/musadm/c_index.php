<?php
/**
 * Created by PhpStorm.
 *
 * @author Kozurev Egor
 * @date 18.03.2018 21:21
 * @version 20190221
 * @version 20190412
 */

global $CFG;
$User = User::current();
Core_Page_Show::instance()->css('/templates/template6/css/style.css');
Core::factory('User_Controller');

//Список директоров
if (User::checkUserAccess(['groups' => [ROLE_ADMIN]], $User )) {
    $DirectorController = new User_Controller(User::current());
    $DirectorController
        ->properties([29, 30, 33])
        ->groupId(6)
        ->isSubordinate(false)
        ->addSimpleEntity('page-theme-color', 'green')
        ->xsl('musadm/users/directors.xsl');

    $DirectorController->queryBuilder()
        ->orderBy('User.id', 'ASC');

    echo "<div class='users'>";
    $DirectorController->show();
    echo "</div>";
}

//Страница для менеджера
if (User::checkUserAccess(['groups' => [ROLE_MANAGER]], $User)) {
    $Director = $User->getDirector();
    $subordinated = $Director->getId();

    //Формирование столбца лидов
    Core::factory('Lid_Controller');
    $LidController = new Lid_Controller($User);
    $LidController->isShowPeriods(false);

    //Формирование столбца Задач
    Core::factory('Task_Controller');
    $TaskController = new Task_Controller(User::current());
    $TaskController
        ->isShowPeriods(false)
        ->isSubordinate(true)
        ->isLimitedAreasAccess(true)
        ->addSimpleEntity('taskAfterAction', 'tasks');
    ?>

    <div class="dynamic-fixed-row">
        <?php
        Core::factory('Core_Entity')
            ->xsl('musadm/users/search-form.xsl')
            ->show();
        ?>
    </div>

    <section class="section-bordered">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 lids">
                <?$LidController->show();?>
            </div>

            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 tasks">
                <?$TaskController->show();?>
            </div>
        </div>
    </section>


    <?php
    //Список действий менеджера
    $LIMIT_STEP = 10;    //Лимит кол-ва отображаемых/подгружаемых событий
    $limit = Core_Array::Get('limit', $LIMIT_STEP);
    $bEnableLoadButton = 1;  //Флаг активности кнопки подгрузки
    $dateFrom = Core_Array::Get('event_date_from', null, PARAM_DATE); //Начала временного периода
    $dateTo = Core_Array::Get('event_date_to', null, PARAM_DATE);     //Конец временного периода

    $Events = Core::factory('Event');
    $Events
        ->queryBuilder()
        ->where('author_id', '=', $User->getId())
        ->orderBy('time', 'DESC');

    if (is_null($dateFrom) && is_null($dateTo)) {
        $totalCount = clone $Events;
        $totalCount = $totalCount->getCount();
        $Events->queryBuilder()->limit($limit);
        if ($totalCount <= $limit) {
            $bEnableLoadButton = 0;
        }
    } else {
        $bEnableLoadButton = 0;
        if (!is_null($dateFrom)) {
            $Events->queryBuilder()->where('time', '>=', strtotime($dateFrom));
        }
        if (!is_null($dateTo)) {
            $Events->queryBuilder()->where('time', '<=', strtotime($dateTo . ' + 1 day'));
        }
    }

    $Events = $Events->findAll();
    foreach ($Events as $Event) {
        $Event->date = date('d.m.Y H:i', $Event->time());
        $Event->text = $Event->getTemplateString();
    }

    global $CFG;

    echo "<div class='events'>";
    Core::factory( 'Core_Entity' )
        ->addEntity( $User )
        ->addEntities( $Events )
        ->addSimpleEntity( 'limit', $limit += $LIMIT_STEP )
        ->addSimpleEntity( 'date_from', $dateFrom )
        ->addSimpleEntity( 'date_to', $dateTo )
        ->addSimpleEntity( 'enable_load_button', $bEnableLoadButton )
        ->xsl( 'musadm/users/events.xsl' )
        ->show();
    echo "</div>";


}