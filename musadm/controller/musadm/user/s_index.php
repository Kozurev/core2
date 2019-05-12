<?php
/**
 * Настройки страницы со списком пользователей различных групп
 *
 * @author Kozurev Egor
 * @date 11.04.2018 22:16
 * @version 20190414
 */


Core::factory('User_Controller');
Core::factory('Lid_Controller');
Core::factory('Schedule_Area_Controller');


$User = User::current();
$Director = $User->getDirector();
$subordinated = $Director->getId();
$accessRules = ['groups' => [ROLE_ADMIN, ROLE_DIRECTOR, ROLE_MANAGER]];

if (!User::checkUserAccess($accessRules, $User)) {
    Core_Page_Show::instance()->error(404);
}

$action = Core_Array::Get('action', null, PARAM_STRING);


//Форма редактирования клиента
if ($action === 'updateFormClient') {
    $userId = Core_Array::Get('userId', 0, PARAM_INT);
    $output = Core::factory('Core_Entity');

    if ($userId) {
        $Client = User_Controller::factory($userId);
        if (is_null($Client)) {
            exit(Core::getMessage('NOT_FOUND', ['Пользователь', $userId]));
        }
        if (!User::isSubordinate($Client, $User)) {
            exit(Core::getMessage('NOT_SUBORDINATE', ['Пользователь', $userId]));
        }

        $AreaAssignments = Core::factory('Schedule_Area_Assignment')->getAssignments($Client);
        if (count($AreaAssignments) > 0) {
            $Client->addSimpleEntity('area_id', $AreaAssignments[0]->areaId());
        }

        $Properties[] = Core::factory('Property', 16)->getPropertyValues($Client)[0]; //Доп. телефон
        $Properties[] = Core::factory('Property',  9)->getPropertyValues($Client)[0]; //Ссылка вк
        $Properties[] = Core::factory('Property', 17)->getPropertyValues($Client)[0]; //Длительность урока
        $Properties[] = Core::factory('Property', 18)->getPropertyValues($Client)[0]; //Соглашение подписано
        $Properties[] = Core::factory('Property', 20)->getPropertyValues($Client)[0]; //Учителя
        $Properties[] = Core::factory('Property', 28)->getPropertyValues($Client)[0]; //Год рождения
        $Properties = array_merge($Properties, Core::factory('Property', 21)->getPropertyValues($Client)); //Учителя
        $Properties = array_merge($Properties, Core::factory('Property', 20)->getPropertyValues($Client)); //Направление подготовки (инструмент)
    } else {
        $Client = User_Controller::factory();
        $Properties[] = Core::factory('Property_Int')
            ->value(
                Core::factory('Property', 17)->defaultValue()
            );
    }

    $Areas = Core::factory('Schedule_Area')->getList(true, false);

    $ListTeachers = Core::factory('Property')
        ->getByTagName('teachers')
        ->getList();
    $ListInstruments = Core::factory('Property')
        ->getByTagName('instrument')
        ->getList();

    $output
        ->addEntity($Client)
        ->addEntities($Areas, 'areas')
        ->addEntities($Properties, 'property_value')
        ->addEntities($ListTeachers, 'property_list')
        ->addEntities($ListInstruments, 'property_list')
        ->xsl('musadm/users/edit_client_popup.xsl')
        ->show();

    exit;
}


//Форма редактирования учителя
if ($action === 'updateFormTeacher') {
    $userId = Core_Array::Get('userId', 0, PARAM_INT);
    $output = Core::factory('Core_Entity');

    if ($userId != 0) {
        $Teacher = User_Controller::factory($userId);

        if (is_null($Teacher)) {
            exit (Core::getMessage('NOT_FOUND', ['Преподаватель', $userId]));
        }

        $Properties[] = Core::factory('Property', 20)->getPropertyValues($Teacher)[0];    //Инструмент
        $Properties[] = Core::factory('Property', 31)->getPropertyValues($Teacher)[0];    //Инструмент
        $output->addEntities($Properties, 'property_value');
    } else {
        $Teacher = User_Controller::factory();
    }

    $PropertyLists = Core::factory('Property')
        ->getByTagName('instrument')
        ->getList();

    $output
        ->addEntity($Teacher)
        ->addEntities($PropertyLists, 'property_list')
        ->xsl('musadm/users/edit_teacher_popup.xsl')
        ->show();
    exit;
}


//Форма редактирования директора
if ($action === 'updateFormDirector') {
    $userId = Core_Array::Get('userId', 0, PARAM_INT);
    $output = Core::factory('Core_Entity');

    if ($userId != 0) {
        $Director = User_Controller::factory($userId, false);

        if (is_null($Director)) {
            exit (Core::getMessage('NOT_FOUND', ['Директор', $userId]));
        }

        $City = Core::factory('Property', 29)->getPropertyValues($Director)[0]; //Город
        $Link = Core::factory('Property', 33)->getPropertyValues($Director)[0]; //Город
        $Organization = Core::factory('Property', 30)->getPropertyValues($Director)[0]; //Организация

        $output->addEntity($City, 'property_value');
        $output->addEntity($Link, 'property_value');
        $output->addEntity($Organization, 'property_value');
    } else {
        $Director = User_Controller::factory();
    }

    $output
        ->addEntity($Director)
        ->xsl('musadm/users/edit_director_popup.xsl')
        ->show();
    exit;
}


//Форма редактирования менеджера
if ($action === 'updateFormManager') {
    $userId = Core_Array::Get('userId', 0, PARAM_INT);
    $output = Core::factory('Core_Entity');

    if ($userId != 0) {
        $Manager = User_Controller::factory($userId);

        if (is_null($Director)) {
            exit (Core::getMessage('NOT_FOUND', ['Директор', $userId]));
        }
    } else {
        $Manager = User_Controller::factory();
    }

    $output
        ->addEntity($Manager)
        ->xsl('musadm/users/edit_manager_popup.xsl')
        ->show();

    exit;
}


//Обновление таблиц
if ($action === 'refreshTableUsers') {
    $this->execute();
    exit;
}


//Форма для создания платежа
if ($action === 'getPaymentPopup') {
    $userId = Core_Array::Get('userId', null, PARAM_INT);
    $User = User_Controller::factory($userId);
    if (is_null($userId) || is_null($User)) {
        exit (Core::getMessage('NOT_FOUND', ['Пользователь', $userId]));
    }

    Core::factory('Core_Entity')
        ->addEntity($User)
        ->addSimpleEntity('function', 'clients')
        ->xsl('musadm/users/balance/edit_payment_popup.xsl')
        ->show();
    exit;
}


//Сохранение платежа
if ($action == 'savePayment') {
    $userId =       Core_Array::Get('userid', 0, PARAM_INT);
    $value  =       Core_Array::Get('value', 0, PARAM_FLOAT);
    $description =  Core_Array::Get('description', '', PARAM_STRING);
    $type =         Core_Array::Get('type', 0, PARAM_INT);

    $Payment = Core::factory('Payment')
        ->user($userId)
        ->type($type)
        ->value($value)
        ->description($description);
    $Payment->save();

    //Корректировка баланса ученика
    $User = User_Controller::factory($userId);
    if (is_null($User)) {
        exit (Core::getMessage('NOT_FOUND', ['Пользователь', $userId]));
    }

    $UserBalance = Core::factory('Property', 12);
    $UserBalance = $UserBalance->getPropertyValues($User)[0];
    $balanceOld = intval($UserBalance->value());

    $type == 1
        ?   $balanceNew = $balanceOld + intval($value)
        :   $balanceNew = $balanceOld - intval($value);
    $UserBalance->value($balanceNew);
    $UserBalance->save();
    exit ('0');
}


//При сохранении пользователя идет проверка на дублирования логина
if ($action === 'checkLoginExists') {
    $userId = Core_Array::Get('userId', 0, PARAM_INT);
    $login = Core_Array::Get('login', '', PARAM_STRING);

    if ($login == '') {
        exit ('Логин не может быть пустым');
    }

    $User = Core::factory('User')
        ->queryBuilder()
        ->where('id', '<>', $userId)
        ->where('login', '=', $login)
        ->find();
    if (!is_null($User)) {
        exit ('Пользователь с таким логином уже существует');
    }
    exit;
}


//Экспорт пользователей в Excel
if ($action === 'export') {
    User::checkUserAccess(['groups' => [ROLE_DIRECTOR, ROLE_MANAGER]]);
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename=demo.xls');
    Core::factory('User_Controller');
    $ClientController = new User_Controller(User::current());
    $ClientController->properties([16]);
    $ClientController->isSubordinate(true);
    $ClientController->isWithAreaAssignments(true);
    $ClientController->groupId(ROLE_CLIENT);
    $ClientController->xsl('musadm/users/export.xsl');
    $ClientController->queryBuilder()->orderBy('surname', 'ASC');

    //Фильтры
    $ScheduleAssignment = Core::factory('Schedule_Area_Assignment');
    foreach ($_GET as $paramName => $values) {
        if ($paramName === 'areas') {
            foreach ($_GET['areas'] as $areaId) {
                if ($areaId > 0
                    && ($ScheduleAssignment->issetAssignment(User::current(), intval($areaId)) !== null)
                    || User::checkUserAccess(['groups' => [ROLE_DIRECTOR]])
                ) {
                    $Area = Schedule_Area_Controller::factory(intval($areaId));
                    if ($Area !== null) {
                        $ClientController->forAreas([$Area]);
                    }
                }
            }
            continue;
        }

        if (strpos($paramName, 'property_') !== false) {
            foreach ($_GET[$paramName] as $value) {
                $propId = explode('property_', $value)[0];
                $ClientController->appendFilter($paramName, $value);
            }
        }
    }

    Core::attachObserver('beforeUserControllerShow', function($args) {
        $pattern = '/[^0-9]/';
        $replacement = '';

        foreach ($args['users'] as $User) {
            $filteredNumber = preg_replace($pattern, $replacement, $User->phoneNumber());
            $User->phoneNumber($filteredNumber);
            foreach ($User->_childrenObjects() as $add) {
                if ($add instanceof Property_String) {
                    $addPhone = $add->value();
                    if ($addPhone == '') {
                        continue;
                    }
                    $addPhone = preg_replace($pattern, $replacement, $addPhone);
                    $add->value($addPhone);
                }
            }
        }
    });

    $ClientController->show();
    exit;
}


//Получение данных лида для заполнения формы создания клиента
if ($action === 'getLidData') {
    $lidId = Core_Array::Get('lidId', null, PARAM_INT);
    $Lid = Lid_Controller::factory($lidId);
    if (is_null($lidId) || is_null($Lid)) {
        exit;
    }

    $LidEncode = new stdClass();
    $LidEncode->name = $Lid->name();
    $LidEncode->surname = $Lid->surname();
    $LidEncode->phone = $Lid->number();
    $LidEncode->vk = $Lid->vk();
    echo json_encode($LidEncode);
    exit;
}


//Открытие всплывающего окна создания/удаления связей сущьности с филиалами для типа связи многие ко многим
if ($action === 'showAssignmentsPopup') {
    $modelId =   Core_Array::Get('model_id', 0, PARAM_INT);
    $modelName = Core_Array::Get('model_name', '', PARAM_STRING);
    if ($modelId <= 0 || $modelName == '') {
        Core_Page_Show::instance()->error(404);
    }

    $Object = Core::factory($modelName, $modelId);
    if (is_null($Object)) {
        exit (Core::getMessage('NOT_FOUND', [$modelName, $modelId]));
    }
    if (method_exists($Object, 'subordinated') && $Object->subordinated() != $subordinated) {
        exit (Core::getMessage('NOT_SUBORDINATE', [$modelName, $modelId]));
    }
    $AreasList = Core::factory('Schedule_Area')->getList(true, false);
    $AreaAssignments = Core::factory('Schedule_Area_Assignment')->getAssignments($Object);

    Core::factory('Core_Entity')
        ->addSimpleEntity('model-id', $modelId)
        ->addSimpleEntity('model-name', $modelName)
        ->addEntities($AreasList, 'areas')
        ->addEntities($AreaAssignments, 'assignments')
        ->xsl('musadm/schedule/assignments/areas_assignments_edit.xsl')
        ->show();
    exit;
}


//Обработчик для создания новой связи сущьности и филиала
if ($action === 'appendAreaAssignment') {
    $modelId = Core_Array::Get('model_id', 0, PARAM_INT);
    $modelName = Core_Array::Get('model_name', '', PARAM_STRING);
    $areaId = Core_Array::Get('area_id', 0, PARAM_INT);

    if ($modelId <= 0 || $modelName == '' || $areaId <= 0) {
        Core_Page_Show::instance()->error(404);
    }

    $Object = Core::factory($modelName, $modelId);
    if (is_null($Object)) {
        exit (Core::getMessage('NOT_FOUND', [$modelName, $modelId]));
    }
    if (method_exists($Object, 'subordinated') && $Object->subordinated() != $subordinated) {
        exit (Core::getMessage('NOT_SUBORDINATE', [$modelName, $modelId]));
    }
    $Area = Schedule_Area_Controller::factory($areaId);
    if (is_null($Area)) {
        exit (Core::getMessage('NOT_FOUND', ['Филиал', $areaId]));
    }
    $Assignment = Core::factory('Schedule_Area_Assignment')->createAssignment($Object, $areaId);

    $outputJson = new stdClass();
    $outputJson->id = $Assignment->getId();
    $outputJson->title = $Area->title();
    echo json_encode($outputJson);
    exit;
}


//Обработчик удаления связи объекта с филмалом
if ($action === 'deleteAreaAssignment') {
    $modelId = Core_Array::Get('model_id', 0, PARAM_INT);
    $modelName = Core_Array::Get('model_name', '', PARAM_STRING);
    $areaId = Core_Array::Get('area_id', 0, PARAM_INT);

    if ($modelId <= 0 || $modelName == '' || $areaId <= 0) {
        Core_Page_Show::instance()->error(404);
    }
    $Object = Core::factory($modelName, $modelId);
    if (is_null($Object)) {
        exit (Core::getMessage('NOT_FOUND', [$modelName, $modelId]));
    }
    if (method_exists($Object, 'subordinated') && $Object->subordinated() != $subordinated) {
        exit (Core::getMessage('NOT_SUBORDINATE', [$modelName, $modelId]));
    }
    Core::factory('Schedule_Area_Assignment')->deleteAssignment($Object, $areaId);
    exit;
}


if ($action === 'applyUserFilter') {
    Core_Page_Show::instance()->execute();
    exit;
}


if (Core_Page_Show::instance()->StructureItem->getId() == ROLE_CLIENT) {
    $title2 = 'КЛИЕНТОВ';
    $breadcumb = 'клиентов';
} else {
    $title2 = 'СОТРУДНИКОВ';
    $breadcumb = 'сотрудников';
}

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->title;
$breadcumbs[0]->active = 1;

Core_Page_Show::instance()->setParam('body-class', 'body-primary');
Core_Page_Show::instance()->setParam('title-first', 'СПИСОК');
Core_Page_Show::instance()->setParam('title-second', $title2);
Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);

$title[] = Core_Page_Show::instance()->Structure->title();
if (get_class(Core_Page_Show::instance()->StructureItem) == 'User_Group') {
    $title[] = $this->StructureItem->title();
}
if (get_class(Core_Page_Show::instance()->StructureItem) == 'User') {
    $title[] = $this->StructureItem->surname() . ' ' . $this->StructureItem->name();
}
$this->title = array_pop($title);