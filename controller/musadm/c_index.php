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
 * @version 20200401
 */

global $CFG;
$User = User_Auth::current();
Core_Page_Show::instance()->css('/templates/template6/css/style.css');
Core::factory('User_Controller');

//Список директоров
if (User::checkUserAccess(['groups' => [ROLE_ADMIN]], $User )) {
    $DirectorController = new User_Controller(User_Auth::current());
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
    //Список действий менеджера
    $LIMIT_STEP = 25;    //Лимит кол-ва отображаемых/подгружаемых событий
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
    (new Core_Entity())
        ->addEntity($User)
        ->addEntities($Events)
        ->addSimpleEntity('limit', $limit += $LIMIT_STEP)
        ->addSimpleEntity('date_from', $dateFrom)
        ->addSimpleEntity('date_to', $dateTo)
        ->addSimpleEntity('enable_load_button', $bEnableLoadButton)
        ->xsl('musadm/users/events.xsl')
        ->show();
    echo "</div>";
}