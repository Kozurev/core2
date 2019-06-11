<?php
/**
 * Страница менеджера 3 в 1
 *
 * @author BadWolf
 * @date 18.03.2018 21:21
 * @version 20190221
 * @version 20190412
 * @version 20190427
 * @version 20190526
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
    $accessClientsRead = Core_Access::instance()->hasCapability(Core_Access::USER_READ_CLIENTS);
    $accessLidRead =     Core_Access::instance()->hasCapability(Core_Access::LID_READ);
    $accessLidCreate =   Core_Access::instance()->hasCapability(Core_Access::LID_CREATE);
    $accessLidEdit =     Core_Access::instance()->hasCapability(Core_Access::LID_EDIT);
    $accessLidComment =  Core_Access::instance()->hasCapability(Core_Access::LID_APPEND_COMMENT);
    $accessTaskRead =    Core_Access::instance()->hasCapability(Core_Access::TASK_READ);
    $accessTaskCreate =  Core_Access::instance()->hasCapability(Core_Access::TASK_CREATE);
    $accessTaskEdit =    Core_Access::instance()->hasCapability(Core_Access::TASK_EDIT);
    $accessTaskComment = Core_Access::instance()->hasCapability(Core_Access::TASK_APPEND_COMMENT);

    $Director = $User->getDirector();
    $subordinated = $Director->getId();

    //Формирование столбца лидов
    Core::factory('Lid_Controller');
    $LidController = new Lid_Controller($User);
    $LidController->isShowPeriods(false);
    $LidController->isEnableCommonLids(false);
    $LidController->isWithAreasAssignments(true);

    //Формирование столбца Задач
    Core::factory('Task_Controller');
    $TaskController = new Task_Controller(User::current());
    $TaskController
        ->isWithAreasAssignments(true)
        ->isShowPeriods(false)
        ->isSubordinate(true)
        ->isLimitedAreasAccess(true)
        ->addSimpleEntity('taskAfterAction', 'tasks');
    ?>

    <div class="dynamic-fixed-row">
        <?php
        if ($accessClientsRead) {
            Core::factory('Core_Entity')
                ->addSimpleEntity(
                        'access_user_create_client',
                    (int)Core_Access::instance()->hasCapability(Core_Access::USER_CREATE_CLIENT)
                )
                ->xsl('musadm/users/search-form.xsl')
                ->show();
        }
        ?>
    </div>

    <section class="section-bordered">
        <div class="row">
            <?php
            if ($accessLidRead) {
                ?>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 lids">
                    <?$LidController->show();?>
                </div>
                <?php
            }
            ?>

            <?php
            if ($accessTaskRead) {
                ?>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 tasks">
                    <?$TaskController->show();?>
                </div>
                <?php
            }
            ?>
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