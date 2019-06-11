<?php
/**
 * Настройки раздела "Лиды"
 *
 * @author Bad Wolf
 * @date 26.04.2018 14:23
 * @version 20190221
 * @version 20190526
 */

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


/**
 * Открытие всплывающего окна создания комментария к лиду
 */
if ($action === 'add_note_popup') {
    if (!$accessComment) {
        Core_Page_Show::instance()->error(403);
    }

    $modelId = Core_Array::Get('model_id', 0, PARAM_INT);
    $Lid = Lid_Controller::factory($modelId);
    if (is_null($Lid)) {
        Core_Page_Show::instance()->error(404);
    }
    Core::factory('Core_Entity')
        ->addEntity($Lid)
        ->xsl('musadm/lids/add_lid_comment.xsl')
        ->show();
    exit;
}


/**
 * Обработчик сохранения лида
 */
if ($action === 'save_lid') {
    if (!$accessCreate) {
        Core_Page_Show::instance()->error(403);
    }

    $surname =  Core_Array::Get('surname', '', PARAM_STRING);
    $name =     Core_Array::Get('name', '', PARAM_STRING);
    $sourceSel= Core_Array::Get('source_select', 0, PARAM_INT);
    $sourceInp= Core_Array::Get('source_input', '', PARAM_STRING);
    $number =   Core_Array::Get('number', '', PARAM_STRING);
    $vk =       Core_Array::Get('vk', '', PARAM_STRING);
    $date =     Core_Array::Get('control_date', date('Y-m-d'), PARAM_STRING);
    $statusId = Core_Array::Get('status_id', 0, PARAM_INT);
    $areaId =   Core_Array::Get('area_id', 0, PARAM_INT);
    $comment =  Core_Array::Get('comment', '', PARAM_STRING);

    $Lid = Lid_Controller::factory()
        ->surname($surname)
        ->name($name)
        ->number($number)
        ->vk($vk)
        ->controlDate($date)
        ->statusId($statusId)
        ->areaId($areaId);

    //источник (значение, вводимое с клавиатуры)
    if ($sourceSel == 0 && $sourceInp != '') {
        $Lid->source($sourceInp);
    }

    $Lid->save();

    //создание комментария
    if ($comment != '') {
        $Lid->addComment($comment, false);
    }

    //источник (значение, выбранное из списка источников лидов)
    if ($sourceSel > 0 && $sourceInp == '') {
        Core::factory('Property')
            ->getByTagName('lid_source')
            ->addNewValue($Lid, $sourceSel);
    }
    exit;
}


/**
 * Обработчик изменения статуса лида
 */
if ($action === 'changeStatus') {
    if (!$accessEdit) {
        Core_Page_Show::instance()->error(403);
    }

    $modelId =  Core_Array::Get('model_id', 0, PARAM_INT);
    $statusId = Core_Array::Get('status_id', 0, PARAM_INT);

    $Lid = Lid_Controller::factory($modelId);
    if (is_null($Lid)) {
        Core_Page_Show::instance()->error(404);
    }
    $Lid->changeStatus($statusId);
    exit;
}


/**
 * Обработчик изменени даты контроля лида
 */
if ($action === 'changeDate') {
    if (!$accessEdit) {
        Core_Page_Show::instance()->error(403);
    }

    $modelId =  Core_Array::Get('model_id', 0, PARAM_INT);
    $date =     Core_Array::Get('date', '', PARAM_STRING);

    if ($modelId == 0 || $date == '') {
        Core_Page_Show::instance()->error(404);
    }

    $Lid = Lid_Controller::factory($modelId);
    if (is_null($Lid)) {
        Core_Page_Show::instance()->error(404);
    }
    $Lid->changeDate($date);
    exit;
}


/**
 * Изменение принадлежности лида к филиалу
 */
if ($action === 'updateLidArea') {
    if (!$accessEdit) {
        Core_Page_Show::instance()->error(403);
    }

    $lidId =  Core_Array::Get('lid_id', 0, PARAM_INT);
    $areaId = Core_Array::Get('area_id', 0, PARAM_INT);

    if ($lidId == 0) {
        Core_Page_Show::instance()->error(404);
    }
    $Lid = Lid_Controller::factory($lidId);
    if (is_null($Lid)) {
        Core_Page_Show::instance()->error(404);
    }
    Core::factory('Schedule_Area_Assignment')->createAssignment($Lid, $areaId);
    exit;
}


/**
 * Обработчик для создания / редактирования лида
 * TODO: пока что реализован лишь механизм создания лида но с заделом и под редактирование
 */
if ($action === 'editLidPopup') {
    if (!$accessCreate) {
        Core_Page_Show::instance()->error(403);
    }

    $lidId = Core_Array::Get('lid_id', null, PARAM_INT);
    $Lid = Lid_Controller::factory($lidId);
    if (is_null($Lid)) {
        Core_Page_Show::instance()->error(404);
    }

    $Areas = Core::factory('Schedule_Area')->getList();
    $Statuses = $Lid->getStatusList();
    $Sources = Core::factory('Property')->getByTagName('lid_source')->getList();

    Core::factory('Core_Entity')
        ->addEntities($Areas)
        ->addEntities($Statuses)
        ->addEntities($Sources, 'source')
        ->addSimpleEntity('today', date('Y-m-d'))
        ->xsl('musadm/lids/edit_lid_popup.xsl')
        ->show();
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