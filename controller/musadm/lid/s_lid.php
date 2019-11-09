<?php
/**
 * Настройки раздела "Лиды"
 *
 * @author Bad Wolf
 * @date 26.04.2018 14:23
 * @version 20190221
 * @version 20190526
 */

authOrOut();

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;

Core_Page_Show::instance()->setParam('body-class', 'body-purple');
Core_Page_Show::instance()->setParam('title-first', 'СПИСОК');
Core_Page_Show::instance()->setParam('title-second', 'ЛИДОВ');
Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);

$User = User::current();
$accessRules = ['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]];

if (!User::checkUserAccess($accessRules, $User)) {
    Core_Page_Show::instance()->error(403);
}

Core::factory('Lid_Controller');
$subordinated = $User->getDirector()->getId();
$action = Core_Array::Get('action', '',PARAM_STRING);

//основные права доступа к разделу
$accessRead =    Core_Access::instance()->hasCapability(Core_Access::LID_READ);
$accessCreate =  Core_Access::instance()->hasCapability(Core_Access::LID_CREATE);
$accessEdit =    Core_Access::instance()->hasCapability(Core_Access::LID_EDIT);
$accessComment = Core_Access::instance()->hasCapability(Core_Access::LID_APPEND_COMMENT);


/**
 * Открытие всплывающего окна создание/редактирование статуса лида
 */
if ($action === 'getLidStatusPopup') {
    if (!User::checkUserAccess(['groups' => [ROLE_DIRECTOR]])) {
        Core_Page_Show::instance()->error(403);
    }

    $id = Core_Array::Get('id', 0, PARAM_INT);
    if ($id !== 0) {
        $Status = Core::factory('Lid_Status')
            ->queryBuilder()
            ->where('id', '=', $id)
            ->where('subordinated', '=', $subordinated)
            ->find();

        if (is_null($Status)) {
            Core_Page_Show::instance()->error(404);
        }
    } else {
        $Status = Core::factory('Lid_Status');
    }

    Core::factory('Core_Entity')
        ->addEntity(User::current())
        ->addEntity($Status)
        ->addEntities(Lid_Status::getColors(), 'color')
        ->xsl('musadm/lids/edit_lid_status_popup.xsl')
        ->show();
    exit;
}


/**
 * Сохранение данных статуса лида
 */
if ($action === 'saveLidStatus') {
    if (!User::checkUserAccess(['groups' => [ROLE_DIRECTOR]])) {
        Core_Page_Show::instance()->error(403);
    }

    $id =    Core_Array::Get('id', null, PARAM_INT);
    $title = Core_Array::Get('title', '', PARAM_STRING);
    $class = Core_Array::Get('item_class', '', PARAM_STRING);

    if (!is_null($id)) {
        $Status = Core::factory('Lid_Status')
            ->queryBuilder()
            ->where('id', '=', $id)
            ->where('subordinated', '=', $subordinated)
            ->find();

        if (is_null($Status)) {
            Core_Page_Show::instance()->error(404);
        }
    } else {
        $Status = Core::factory('Lid_Status');
    }

    $jsonData = new stdClass();
    $jsonData->itemClass = $class;
    $jsonData->title = $title;

    if ($Status->getId() > 0) {
        $jsonData->oldItemClass = $Status->itemClass();
    }

    $Status
        ->title($title)
        ->itemClass($class)
        ->save();

    $jsonData->id = $Status->getId();
    $jsonData->colorName = Lid_Status::getColor($class);

    echo json_encode($jsonData);
    exit;
}


/**
 * Удаление статуса лида
 */
if ($action === 'deleteLidStatus') {
    if (!User::checkUserAccess(['groups' => [ROLE_DIRECTOR]])) {
        Core_Page_Show::instance()->error(403);
    }

    $id = Core_Array::Get('id', null, PARAM_INT);
    if (is_null($id)) {
        Core_Page_Show::instance()->error(404);
    }

    $Status = Core::factory('Lid_Status')
        ->queryBuilder()
        ->where('id', '=', $id)
        ->where('subordinated', '=', $subordinated)
        ->find();

    if (is_null($Status)) {
        Core_Page_Show::instance()->error(404);
    }

    $colorName = Lid_Status::getColor($Status->itemClass());

    $jsonData = new stdClass();
    $jsonData->id = $Status->getId();
    $jsonData->title = $Status->title();
    $jsonData->itemClass = $Status->itemClass();
    $jsonData->colorName = $colorName;
    echo json_encode($jsonData);
    $Status->delete();
    exit;
}


//проверка прав доступа
if (!$accessRead) {
    Core_Page_Show::instance()->error(403);
}


if ($action === 'refreshLidTable') {
    Core_Page_Show::instance()->execute();
    exit;
}