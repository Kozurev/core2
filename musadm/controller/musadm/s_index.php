<?php
/**
 *
 *
 * @author Bad Wolf
 * @date 18.03.2018 21:21
 * @version 20190222
 * @version 20190427
 */


$User = User::current();
$access = ['groups' => [ROLE_ADMIN, ROLE_MANAGER, ROLE_DIRECTOR]];

if (is_null($User)) {
    $host  = Core_Array::Server('HTTP_HOST', '');
    $uri   = rtrim(dirname(Core_Array::Server('PHP_SELF', '', PARAM_STRING)), '/\\');
    header("Location: http://$host$uri/authorize?back=$host$uri/");
    exit;
}

$host  = Core_Array::Server('HTTP_HOST', '', PARAM_STRING);
$uri   = rtrim(dirname(Core_Array::Server('PHP_SELF', '', PARAM_STRING)), '/\\');

Core_Page_Show::instance()->setParam('body-class', 'body-green');
Core_Page_Show::instance()->setParam('title-first', 'ГЛАВНАЯ');
Core_Page_Show::instance()->setParam('title-second', 'СТРАНИЦА');

if (Core_Array::Get('ajax', null, PARAM_STRING) === null) {
    if (!User::checkUserAccess($access, $User)) {
        header("Location: http://$host$uri/authorize?back=/$uri");
    }
    if ($User->groupId() == ROLE_DIRECTOR) {
        header("Location: http://$host$uri/user/client");
    }
    if ($User->groupId() == ROLE_CLIENT) {
        header("Location: http://$host$uri/balance");
    }
    if ($User->groupId() == ROLE_TEACHER) {
        header("Location: http://$host$uri/schedule");
    }
}


$action = Core_Array::Get('action', null, PARAM_STRING);

$Director = $User->getDirector();
$subordinated = $Director->getId();

Core::factory('User_Controller');
Core::factory('Lid_Controller');
Core::factory('Task_Controller');
Core::factory('Property_Controller');


/**
 * Обработчик для открытия всплывающего окна редактирования списка дополнительного свойства
 */
if ($action === 'getPropertyListPopup') {
    $propId = Core_Array::Get('prop_id', null, PARAM_INT);
    if (is_null($propId)) {
        Core_Page_Show::instance()->error(404);
    }

    $Property = Property_Controller::factory($propId);
    if (is_null($Property)) {
        Core_Page_Show::instance()->error(404);
    }

    Core::factory('Core_Entity')
        ->addEntity($Property)
        ->addEntities($Property->getList())
        ->xsl('musadm/edit_property_list.xsl')
        ->show();

    exit;
}


/**
 * Сохранение элемента списка дополнительного свойства
 */
if ($action === 'savePropertyListValue') {
    $id =     Core_Array::Get('id', null, PARAM_INT);
    $propId = Core_Array::Get('prop_id', null, PARAM_INT);
    $value =  Core_Array::Get('value', null, PARAM_STRING);

    if (is_null($propId) || is_null($value) || $propId <= 0 || $value == '') {
        Core_Page_Show::instance()->error(404);
    }

    $NewValue = Property_Controller::factoryListValue($id);
    if (is_null($NewValue)) {
        Core_Page_Show::instance()->error(404);
    }

    $NewValue
        ->propertyId($propId)
        ->value($value)
        ->sorting(0);
    $NewValue->save();

    $returnJson = new stdClass();
    $returnJson->id = $NewValue->getId();
    $returnJson->propertyId = $propId;
    $returnJson->value = $value;
    echo json_encode($returnJson);
    exit;
}


/**
 * Удаление элемента списка дополнения свйоства
 */
if ($action === 'deletePropertyListValue') {
    $id = Core_Array::Get('id', null, PARAM_INT);
    if (is_null($id)) {
        Core_Page_Show::instance()->error(404);
    }

    $PropertyListValue = Property_Controller::factoryListValue($id);
    if (is_null($PropertyListValue)) {
        Core_Page_Show::instance()->error(404);
    }

    $PropertyListValue->delete();
    exit;
}


/**
 * Обработчик для сохранения значения доп. свойства
 */
if ($action === 'savePropertyValue') {
    $propertyName = Core_Array::Get('prop_name', null, PARAM_STRING);
    $propertyValue= Core_Array::Get('value', null, PARAM_FLOAT);
    $modelId =      Core_Array::Get('model_id', null, PARAM_INT);
    $modelName =    Core_Array::Get('model_name', null, PARAM_STRING);

    $Object = Core::factory($modelName);
    $Property = Core::factory('Property')->getByTagName($propertyName);

    if (is_null($Property) || is_null($Object)) {
        Core_Page_Show::instance()->error(404);
    }

    if (method_exists($Object, 'subordinated')) {
        $Object->queryBuilder()
            ->open()
            ->where('subordinated', '=', $subordinated)
            ->orWhere('subordinated', '=', 0)
            ->close();
    }

    $Object = $Object->queryBuilder()
        ->where('id', '=', $modelId)
        ->find();

    if (is_null($Object)) {
        Core_Page_Show::instance()->error(404);
    }

    $Value = $Property->getPropertyValues($Object)[0];
    $Value->value($propertyValue)->save();
    exit;
}


/**
 * Обновление таблицы лидов
 */
if ($action === 'refreshLidTable') {
    $LidController = new Lid_Controller($User);

    $areaId = Core_Array::Get('area_id', 0, PARAM_INT);
    if ($areaId !== 0) {
        $forArea = Core::factory('Schedule_Area', $areaId);
        $LidController->forAreas([$forArea]);
        $LidController->isEnableCommonLids(false);
    }

    $phone = Core_Array::Get('phone', null, PARAM_STRING);
    if (!is_null($phone)) {
        $LidController->appendFilter('number', $phone);
        $LidController->addSimpleEntity('number', $phone);
        $LidController->isPeriodControl(false);
    }

    $LidController
        ->lidId(Core_Array::Get('lidid', null, PARAM_INT))
        ->isEnableCommonLids(false)
        ->isWithAreasAssignments(true)
        ->isShowPeriods(false)
        ->show();
    exit;
}


/**
 * Обновление таблицы
 */
if ($action === 'refreshTasksTable') {
    $TaskController = new Task_Controller(User::current());

    $areaId = Core_Array::Get('areaId', 0, PARAM_INT);
    if ($areaId !== 0) {
        $forArea = Core::factory('Schedule_Area', $areaId);
        $TaskController->forAreas([$forArea]);
        $TaskController->isEnableCommonTasks(false);
    }

    $TaskController
        ->isWithAreasAssignments(true)
        ->isShowPeriods(false)
        ->isSubordinate(true)
        ->isLimitedAreasAccess(true)
        ->show();
    exit;
}


if ($action === 'search_client') {
    $surname = Core_Array::Get('surname', null, PARAM_STRING);
    $name    = Core_Array::Get('name', null, PARAM_STRING);
    $phone   = Core_Array::Get('phone', null, PARAM_STRING);

    $ClientController = new User_Controller(User::current());
    $ClientController
        ->isSubordinate(true)
        ->filterType(User_Controller::FILTER_NOT_STRICT)
        ->isActiveBtnPanel(false)
        ->groupId(ROLE_CLIENT)
        ->properties(true)
        ->isLimitedAreasAccess(true)
        ->xsl('musadm/users/clients.xsl');

    if (!is_null($surname)) {
        $ClientController->appendFilter('surname', $surname);
    }
    if (!is_null($name)) {
        $ClientController->appendFilter('name', $name);
    }
    if (!is_null($phone)) {
        $ClientController->appendFilter('phone_number', $phone);
    }

    $SearchingClientsHtml = $ClientController->show(false);
    if (count($ClientController->getUserIds()) > 0) {
        echo "<div class='users'>";
        echo $SearchingClientsHtml;
        echo "</div>";
    }
    exit;
}


if ($action === 'getObjectInfoPopup') {
    $id =     Core_Array::Get('id', 0, PARAM_INT);
    $model =  Core_Array::Get('model', '', PARAM_STRING);

    $Object = Core::factory($model)
        ->queryBuilder()
        ->where('id', '=', $id)
        ->where('subordinated', '=', $subordinated)
        ->find();

    if (is_null($Object)) {
        exit("<h2>Объект с переданными данными не найден или был удален</h2>");
    }

    $Output = Core::factory('Core_Entity')
        ->xsl('musadm/object.xsl');

    switch ($model)
    {
        case 'Task' :
            $TaskController = new Task_Controller( User::current() );
            $TaskController
                ->isShowPeriods( false )
                ->isShowButtons( false )
                ->isPeriodControl( false )
                ->taskId( $id )
                ->xsl( 'musadm/tasks/all.xsl' )
                ->show();
            exit;

        case 'Lid' :
            $LidController = new Lid_Controller(User::current());
            $LidController
                ->isShowPeriods(false)
                ->isShowButtons(false )
                ->isPeriodControl(false)
                ->lidId($id)
                ->xsl('musadm/lids/lids.xsl')
                ->show();
            exit;

        case 'Certificate' :
            $Object->sellDate(refactorDateFormat($Object->sellDate()));
            $Object->activeTo(refactorDateFormat($Object->activeTo()));

            $Notes = Core::factory( 'Certificate_Note' )->queryBuilder()
                ->select(['certificate_id', 'author_id', 'date', 'text', 'surname', 'name'])
                ->where('certificate_id', '=', $id)
                ->leftJoin('User AS u', 'u.id = author_id')
                ->orderBy('date', 'DESC')
                ->orderBy('Certificate_Note.id', 'DESC')
                ->findAll();

            foreach ($Notes as $Note) {
                $Note->date(refactorDateFormat($Note->date()));
            }

            $Output->addEntities($Notes, 'note');
            break;

        default: echo "<h2>Ошибка: отсутствует обработчик для модели '". $model ."'</h2>";
    }

    $Output->addEntity($Object)->show();
    exit;
}


if ($action === 'refreshTableUsers') {
    Core_Page_Show::instance()->execute();
    exit;
}