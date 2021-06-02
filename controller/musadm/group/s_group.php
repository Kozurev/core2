<?php
/**
 * Настройки для раздела "Группы"
 *
 * @author: BadWolf
 * @date: 24.04.2018 19:46
 * @version 20190603
 * @version 20190405
 * @version 20190526
 */

authOrOut();

$User = User_Auth::current();

$Director = $User->getDirector();
$subordinated = $Director->getId();

$breadcumbs[0] = new stdClass();
$breadcumbs[0]->title = Core_Page_Show::instance()->Structure->title();
$breadcumbs[0]->active = 1;
Core_Page_Show::instance()->setParam('body-class', 'body-blue');
Core_Page_Show::instance()->setParam('title-first', 'СПИСОК');
Core_Page_Show::instance()->setParam('title-second', 'ГРУПП');
Core_Page_Show::instance()->setParam('breadcumbs', $breadcumbs);

$action = Core_Array::Get('action', null, PARAM_STRING);


//основные права доступа к разделу
$accessRead =   Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_GROUP_READ);
$accessCreate = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_GROUP_CREATE);
$accessEdit =   Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_GROUP_EDIT);
$accessDelete = Core_Access::instance()->hasCapability(Core_Access::SCHEDULE_GROUP_DELETE);

if ($action === 'show') {
    Core_Page_Show::instance()->execute();
    exit();
}

//Формирование содержания всплывающего окна состава группы
if ($action === 'getGroupComposition') {
    if (!$accessEdit) {
        Core_Page_Show::instance()->error(403);
    }

    $groupId = Core_Array::Get('group_id', null, PARAM_INT);
    if (is_null($groupId)) {
        Core_Page_Show::instance()->error(404);
    }

    $group = Core::factory('Schedule_Group', $groupId);
    if (is_null($group)) {
        Core_Page_Show::instance()->error(404);
    }

    $groupItems = $group->getClientList();
    $group->addEntities($groupItems);

    if ($group->type() === Schedule_Group::TYPE_CLIENTS) {
        $userController = new User_Controller(User_Auth::current());
        $users = $userController
            ->groupId(ROLE_CLIENT)
            ->isWithAreaAssignments(false);
        $users->queryBuilder()
            ->orderBy('surname', 'ASC')
            ->orderBy('name', 'ASC');
        $users = $users->getUsers();
    } elseif ($group->type() === Schedule_Group::TYPE_LIDS) {
        $lidsIds = [];
        foreach ($groupItems as $groupItem) {
            $lidsIds[] = $groupItem->getId();
        }
        $lids = (new Lid())->queryBuilder()
            ->whereIn('id', $lidsIds)
            ->findAll();
    }

    (new Core_Entity())
        ->addEntity($group, 'group')
        ->addEntities($users ?? [])
        ->addEntities($lids ?? [])
        ->xsl('musadm/groups/group_assignment.xsl')
        ->show();
    exit;
}


//Формирование всплывающего окна создания/редактирования
if ($action == 'updateForm') {
    $popupData = Core::factory('Core_Entity');
    $modelId = Core_Array::Get('group_id', 0, PARAM_INT);

    if (Core_Page_Show::instance()->Structure->path() == 'clients') {
        $type = Schedule_Group::TYPE_CLIENTS;
    } else {
        $type = Schedule_Group::TYPE_LIDS;
    }

    if ($modelId == 0 && !$accessCreate) {
        Core_Page_Show::instance()->error(403);
    }
    if ($modelId != 0 && !$accessEdit) {
        Core_Page_Show::instance()->error(403);
    }

    if ($modelId !== 0) {
        $Group = Core::factory('Schedule_Group', $modelId);
        if (is_null($Group)) {
            Core_Page_Show::instance()->error(404);
        }
        $Group->addEntity($Group->getTeacher());
        $Group->addEntities($Group->getClientList());
    } else {
        $Group = Core::factory('Schedule_Group');
    }

    $Users = User_Controller::factory()
        ->queryBuilder()
        ->open()
            ->where('group_id', '=', ROLE_TEACHER)
            ->orWhere('group_id', '=', ROLE_CLIENT)
        ->close()
        ->where('subordinated', '=', $subordinated)
        ->where('active', '=', 1)
        ->orderBy('surname')
        ->findAll();

    $Areas = (new Schedule_Area_Assignment)->getAreas(User_Auth::current());

    $popupData
        ->addEntity($Group)
        ->addEntities($Users)
        ->addEntities($Areas)
        ->addSimpleEntity('group_type', $type)
        ->xsl('musadm/groups/edit_group_popup.xsl')
        ->show();

    exit;
}

//Обработчик для фильтрации (поиска) клиентов по фио
if ($action === 'groupSearchClient') {
    $Users = new User_Controller(User::current());
    $Users
        ->groupId(ROLE_CLIENT)
        ->filterType(User_Controller::FILTER_NOT_STRICT)
        ->isWithAreaAssignments(false);
    $Users->queryBuilder()
        ->orderBy('surname', 'ASC')
        ->orderBy('name', 'ASC');

    $searchingQuery = Core_Array::Get('surname', '', PARAM_STRING);
    if (!empty($searchingQuery)) {
        $Users->appendFilter('surname', $searchingQuery);
    }

    $Users = $Users->getUsers();

    $outputJson = [];
    foreach ($Users as $User) {
        $jsonUser = new stdClass();
        $jsonUser->id = $User->getId();
        $jsonUser->fio = $User->surname() . ' ' . $User->name();
        $outputJson[] = $jsonUser;
    }

    echo json_encode($outputJson);
    exit;
}

//Создание связей клиентов и группы
if ($action === 'groupCreateAssignments') {
    if (!$accessEdit) {
        Core_Page_Show::instance()->error(403);
    }

    $userIds = Core_Array::Get('userIds', [], PARAM_ARRAY);
    $groupId = Core_Array::Get('groupId', null, PARAM_INT);

    if (is_null($groupId)) {
        Core_Page_Show::instance()->error(403);
    }

    $group = Core::factory('Schedule_Group')
        ->queryBuilder()
        ->where('id', '=', $groupId)
        ->where('subordinated', '=', $subordinated)
        ->find();

    if (is_null($group)) {
        Core_Page_Show::instance()->error(403);
    }

    $outputJson = [];
    foreach ($userIds as $id) {
        if ($group->type() == Schedule_Group::TYPE_CLIENTS) {
            $client = User_Controller::factory($id);
        } else {
            $client = Lid_Controller::factory($id);
        }
        if (!is_null($client)) {
            $group->appendClient($client->getId());
            $jsonUser = new stdClass();
            $jsonUser->id = $client->getId();
            $jsonUser->fio = $client->surname() . ' ' . $client->name();
            if ($group->type() === Schedule_Group::TYPE_LIDS) {
                $jsonUser->fio .= ' ' . $client->number();
            }
            $outputJson[] = $jsonUser;
        }
    }

    echo json_encode($outputJson);
    exit;
}

//Удаление существующих связей группы с клиентами
if ($action === 'groupDeleteAssignments') {
    if (!$accessEdit) {
        Core_Page_Show::instance()->error(403);
    }

    $userIds = Core_Array::Get('userIds', [], PARAM_ARRAY);
    $groupId = Core_Array::Get('groupId', null, PARAM_INT);

    if (is_null($groupId)) {
        Core_Page_Show::instance()->error(403);
    }

    $group = Core::factory('Schedule_Group')
        ->queryBuilder()
        ->where('id', '=', $groupId)
        ->where('subordinated', '=', $subordinated)
        ->find();

    if (is_null($group)) {
        Core_Page_Show::instance()->error(403);
    }

    $outputJson = [];

    foreach ($userIds as $id) {
        $client = Schedule_Group_Assignment::getObjectById($id, $group->type());
        if (!is_null($client)) {
            $group->removeClient($id);
            $jsonUser = new stdClass();
            $jsonUser->id = $client->getId();
            $jsonUser->fio = $client->surname() . ' ' . $client->name();
            $outputJson[] = $jsonUser;
        }
    }

    echo json_encode($outputJson);
    exit;
}



if (!$accessRead) {
    Core_Page_Show::instance()->error(403);
}


//Обновление содержимого страницы
if ($action == 'refreshGroupTable') {
    Core_Page_Show::instance()->execute();
    exit;
}