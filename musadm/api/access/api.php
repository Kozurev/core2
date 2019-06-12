<?php
/**
 * API обработчик для работы с группами прав доступа
 *
 * @author BadWolf
 * @date 12.05.2019 21:00
 */

$action = Core_Array::Request('action', null, PARAM_STRING);


$CurrentUser = User::current();
if (is_null($CurrentUser) || $CurrentUser->groupId() !== ROLE_DIRECTOR) {
    die(REST::error(0, 'Вы не авторизованы как директор'));
}

/**
 * Получения информации о группе для создания/редактирования
 *
 * @INPUT_GET:      id          int     required    идентификатор группы по которой запрашивается информация
 * @INPUT_GET:      parentId    int     required    id родительской группы необходим при создании группы
 *
 * @OUTPUT:         json
 *
 * @OUTPUT_DATA:    id          int
 * @OUTPUT_DATA:    title       string
 * @OUTPUT_DATA:    description string
 * @OUTPUT_DATA:    parentId    int
 * @OUTPUT_DATA:    countUsers  int
 */
if ($action === 'edit') {
    $id = Core_Array::Get('id', 0, PARAM_INT);
    $parentId = Core_Array::Get('parentId', 0, PARAM_INT);

    $Group = Core::factory('Core_Access_Group', $id);
    if (is_null($Group)) {
        die(REST::error(1, 'Неверно передан идентификатор группы'));
    }
    $Group->parentId($parentId);

    $output = new stdClass();
    $output->id = $id;
    $output->title = is_null($Group->title()) ? '' : $Group->title();
    $output->description = is_null($Group->description()) ? '' : $Group->description();
    $output->parentId = $parentId;

    try {
        $output->countUsers = $Group->getCountUsers();
    } catch (Exception $e) {
        die(REST::error(3, $e->getMessage()));
    }

    echo json_encode($output);
    exit;
}


/**
 * Сохранение данных группы
 *
 * @INPUT_GET:      id          int                 идентификатор сохраняемой группы
 * @INPUT_GET:      title       string              название группы (обязательно при создании новой группы)
 * @INPUT_GET:      description string              описание группы
 * @INPUT_GET:      parentId    int                 идентификатор родительской группы
 *
 * @OUTPUT:         json
 *
 * @OUTPUT_DATA:    id          int
 * @OUTPUT_DATA:    title       string
 * @OUTPUT_DATA:    description string
 * @OUTPUT_DATA:    parentId    int
 * @OUTPUT_DATA:    countUsers  int
 */
if ($action === 'save') {
    $id = Core_Array::Get('id', 0, PARAM_INT);

    $Group = Core::factory('Core_Access_Group', $id);
    if (is_null($Group)) {
        die(REST::error(1, 'Неверно передан идентификатор группы'));
    }

    unset($_GET['id']);
    unset($_GET['action']);

    foreach ($_GET as $setter => $newVal) {
        if (method_exists($Group, $setter)) {
            $Group->$setter($newVal);
        }
    }

    $Group->save();
    $output = new stdClass();
    $output->id = $Group->getId();
    $output->title = is_null($Group->title()) ? '' : $Group->title();
    $output->description = is_null($Group->description()) ? '' : $Group->description();
    $output->parentId = $Group->parentId();

    try {
        $output->countUsers = $Group->getCountUsers();
    } catch (Exception $e) {
        die(REST::error(3, $e->getMessage()));
    }

    echo json_encode($output);
    exit;
}


/**
 * Удаление группы
 *
 * @INPUT_GET:      id          int     идентификатор удаляемой группы
 *
 * @OUTPUT:         json
 *
 * @OUTPUT_DATA:    id          int
 * @OUTPUT_DATA:    title       string
 * @OUTPUT_DATA:    description string
 * @OUTPUT_DATA:    parentId    int
 */
if ($action === 'delete') {
    $id = Core_Array::Get('id', 0, PARAM_INT);

    $Group = Core::factory('Core_Access_Group', $id);
    if ($id === 0 ||is_null($Group)) {
        die(REST::error(1, 'Неверно передан идентификатор группы'));
    } else {
        $Group->delete();
    }

    $output = new stdClass();
    $output->id = $Group->getId();
    $output->title = $Group->title();
    $output->description = is_null($Group->description()) ? '' : $Group->description();
    $output->parentId = $Group->parentId();
    echo json_encode($output);
    exit;
}


/**
 * Изменение прав доступа
 *
 * @INPUT_GET:      groupId         int     required    идентификатор группы для которой изменяются права доступа
 * @INPUT_GET:      capabilityName  string  required    название права доступа
 * @INPUT_GET:      value           int     required    значение: 1 - разрешить; 0 - запретить; -1 - как у родителя
 *
 * @OUTPUT          json
 *
 * @OUTPUT_DATA:    capability      string      название (русское) редактируемого права
 * @OUTPUT_DATA:    group           object      информация о группе
 *                    ->id          int
 *                    ->title       string
 *                    ->description string
 *                    ->parentId    int
 *                    ->countUsers  int
 */
if ($action === 'setCapability') {
    $groupId = Core_Array::Get('groupId', 0, PARAM_INT);
    $capabilityName = Core_Array::Get('capabilityName', '', PARAM_STRING);
    $value = Core_Array::Get('value', 100, PARAM_INT);

    $Group = Core::factory('Core_Access_Group', $groupId);
    if ($groupId === 0 ||is_null($Group) || $capabilityName === '') {
        Core_Page_Show::instance()->error(404);
    }

    if ($value === 1) {
        $Group->capabilityAllow($capabilityName);
    } elseif ($value === 0) {
        $Group->capabilityForbidden($capabilityName);
    } elseif ($value === -1) {
        $Group->capabilityAsParent($capabilityName);
    } else {
        Core_Page_Show::instance()->error(404);
    }

    $stdGroup = new stdClass();
    $stdGroup->id = $Group->getId();
    $stdGroup->title = $Group->title();
    $stdGroup->description = is_null($Group->description()) ? '' : $Group->description();
    $stdGroup->parentId = $Group->parentId();

    try {
        $stdGroup->countUsers = $Group->getCountUsers();
    } catch (Exception $e) {
        die(REST::error(3, $e->getMessage()));
    }

    $output = new stdClass();
    $output->capability = Core_Array::getValue(Core_Access::instance()->capabilities, $capabilityName, '', PARAM_STRING);
    $output->group = $stdGroup;
    echo json_encode($output);
    exit;
}


/**
 * Получение бщего списка пользователей и пользователей, принадлежащих данной группе
 *
 * @INPUT_GET:      groupId         int     required    идентификатор группы
 * @INPUT_GET:      params          array               массив дополнительных параметров
 *
 * @OUTPUT:         json
 *
 * @OUTPUT_DATA:    group           object              объект, содержащий информацию о группе
 *                    ->id          int
 *                    ->title       string
 *                    ->description string
 *                    ->parentId    int
 *                    ->countUsers  int
 * @OUTPUT_DATA:    users           array               массив пользователей группы
 *                    []->id        int
 *                    []->surname   string
 *                    []->name      string
 *                    []->active    int
 *                    []->groupId   int
 */
if ($action === 'getList') {
    $groupId = Core_Array::Get('groupId', 0, PARAM_INT);
    $params = Core_Array::Get('params', [], PARAM_ARRAY);

    $Group = Core::factory('Core_Access_Group', $groupId);
    if ($groupId === 0 ||is_null($Group)) {
        die(REST::error(1, 'Неверно передан идентификатор группы'));
    }

    $result['group'] = new stdClass();
    $result['group']->id = $Group->getId();
    $result['group']->title = $Group->title();
    $result['group']->parentId = $Group->parentId();
    $result['group']->description = $Group->description();

    try {
        $result['group']->countUsers = $Group->getCountUsers();
        $Users = $Group->getUserList($params);
    } catch (Exception $e) {
        die(REST::error(3, $e->getMessage()));
    }

    $result['users'] = [];
    foreach ($Users as $user) {
        $stdUser = new stdClass();
        $stdUser->id = $user->getId();
        $stdUser->surname = $user->surname();
        $stdUser->name = $user->name();
        $stdUser->active = $user->active();
        $stdUser->groupId = $user->groupId();
        $result['users'][] = $stdUser;
    }

    echo json_encode($result);
    exit;
}


/**
 * Добавление/удаление пользователя в/из список пользователей группы
 *
 * @INPUT_GET:      groupId         int     required    идентификатор группы
 * @INPUT_GET:      userId          array   required    массив дополнительных параметров
 *
 * @OUTPUT:         json
 *
 * @OUTPUT_DATA:    group           object              объект, содержащий информацию о группе
 *                    ->id          int
 *                    ->title       string
 *                    ->description string
 *                    ->parentId    int
 *                    ->countUsers  int
 * @OUTPUT_DATA:    user                                объект, содержащий информацию о пользователе
 *                    ->id          int
 *                    ->surname     string
 *                    ->name        string
 *                    ->active      int
 *                    ->groupId     int
 */
if ($action === 'appendUser' || $action === 'removeUser') {
    $groupId = Core_Array::Get('groupId', 0, PARAM_INT);
    $userId = Core_Array::Get('userId', 0, PARAM_INT);

    $Group = Core::factory('Core_Access_Group', $groupId);
    if ($groupId === 0 ||is_null($Group)) {
        die(REST::error(1, 'Неверно передан идентификатор группы'));
    }

    $User = Core::factory('User', $userId);
    if ($userId === 0 || is_null($User)) {
        die(REST::error(1, 'Неверно передан идентификатор пользователя'));
    }

    if ($action === 'appendUser') {
        $Group->appendUser($User->getId());
    } else {
        $Group->removeUser($User->getId());
    }

    $output['group'] = new stdClass();
    $output['group']->id = $Group->getId();
    $output['group']->title = $Group->title();
    $output['group']->parentId = $Group->parentId();
    $output['group']->description = $Group->description();

    $output['user'] = new stdClass();
    $output['user']->id = $User->getId();
    $output['user']->surname = $User->surname();
    $output['user']->name = $User->name();
    $output['user']->groupId = $User->groupId();
    $output['user']->active = $User->active();

    echo json_encode($output);
    exit;
}