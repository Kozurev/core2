<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

//$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
//$dbh->query("SET NAMES utf8");
global $CFG;
Orm::Debug(false);
$Orm = new Orm();

Core::requireClass('Property');
Core::requireClass('Property_Controller');


/**
 * Рефакторинг связей объектов с доп. свойствами
 */
$GroupClient = Core::factory('User_Group', ROLE_CLIENT);
$GroupTeacher = Core::factory('User_Group', ROLE_TEACHER);

$Balance = Property_Controller::factoryByTag('balance');
$IndivLessons = Property_Controller::factoryByTag('indiv_lessons');
$GroupLessons = Property_Controller::factoryByTag('group_lessons');

$Orm->executeQuery('DELETE FROM Property_Int_Assigment WHERE property_id in (12, 13, 14)');

$Balance->makeAssignment($GroupClient);
$IndivLessons->makeAssignment($GroupClient);
$GroupLessons->makeAssignment($GroupClient);



exit;
$limit = 50;
$offset = Core_Array::Get('offset', 0, PARAM_INT);
$total = Core::factory('User')->getCount();

if ($offset >= $total) {
    $Orm->executeQuery('DELETE FROM Property_String_Assigment WHERE property_id = 22');
    $Orm->executeQuery('DELETE FROM Property_String WHERE property_id = 22');
    $Orm->executeQuery('DELETE FROM Property WHERE id = 22');
    exit('Работа скрипта заверщена');
}


if ($offset == 0) {
    $Orm->executeQuery('
        CREATE TABLE File
        (
            id int PRIMARY KEY AUTO_INCREMENT,
            name varchar(25),
            real_name varchar(255),
            type varchar(255),
            type_id int,
            timecreated datetime,
            timemodified datetime
        );
    ');

    $Orm->executeQuery('
        CREATE TABLE File_Assignment
        (
            id int PRIMARY KEY AUTO_INCREMENT,
            model_id int,
            object_id int,
            file_id int
        );
    ');

    $Orm->executeQuery('ALTER TABLE Schedule_Absent CHANGE client_id object_id int(11) NOT NULL;');
    $Orm->executeQuery('ALTER TABLE Schedule_Absent ADD time_to time NULL;');
    $Orm->executeQuery('ALTER TABLE Schedule_Absent ADD time_from time NULL;');
    $Orm->executeQuery('
    ALTER TABLE Schedule_Absent
      MODIFY COLUMN type_id int(11) NOT NULL AFTER time_to,
      MODIFY COLUMN object_id int(11) NOT NULL AFTER time_to,
      MODIFY COLUMN time_from time AFTER time_to;
    ');
    $Orm->executeQuery('UPDATE Schedule_Absent SET time_from = \'00:00:00\', time_to = \'00:00:00\'');
    $Orm->executeQuery('ALTER TABLE User CHANGE patronimyc patronymic varchar(255) NOT NULL DEFAULT \'\';');
    $Orm->executeQuery('ALTER TABLE User ADD auth_token varchar(50) DEFAULT \'\' NULL;');

    //Добавление структуры "Экспорт"
    $Structure = new Structure();
    $Structure1 = clone $Structure;
    $Structure1
        ->title('Экспорт лидов')
        ->parentId(28)
        ->path('musadm/lid/export')
        ->templateId(0)
        ->description('Раздел для экспорта лидов по выбранным фильтрам в exel')
        ->children_name('Structure_Item')
        ->active(1)
        ->menuId(1)
        ->sorting(0)
        ->save();

    $Structure2 = clone $Structure;
    $Structure2
        ->title('Раздел новых лидов')
        ->parentId(28)
        ->path('musadm/lid/new_lid')
        ->templateId(0)
        ->description('Раздел для отображения новых лидов')
        ->children_name('Structure_Item')
        ->active(1)
        ->menuId(1)
        ->sorting(0)
        ->save();


    $Type = new Event_Type();

    $Type1 = clone $Type;
    $Type1
        ->name('schedule_edit_absent_period')
        ->title('Редактирование периода отсутствия')
        ->parentId(1)
        ->save();

    $Type2 = clone $Type;
    $Type2
        ->name('schedule_append_consult')
        ->title('Назначение консультации')
        ->parentId(1)
        ->save();

    $Type3 = clone $Type;
    $Type3
        ->name('schedule_set_absent')
        ->title('Отмена занятия из актуального графика')
        ->parentId(1)
        ->save();

    $Orm->executeQuery('
        CREATE TABLE User_Auth_Log
        (
            id int PRIMARY KEY AUTO_INCREMENT,
            user_id int,
            datetime datetime,
            device_id int,
            system_id int,
            ip varchar(12)
        );
    ');

    Core::factory('Property')->addToPropertiesList(Core::factory('Lid'), 20);
}


$Users = Core::factory('User')
    ->queryBuilder()
    ->limit($limit)
    ->offset($offset)
    ->findAll();

foreach ($Users as $User) {
    if ($User->groupId() == ROLE_CLIENT) {
        $newPass = '1234';
    } elseif ($User->groupId() == ROLE_TEACHER) {
        $newPass = '4321';
    } elseif ($User->groupId() == ROLE_MANAGER) {
        $newPass = '135684';
    } elseif ($User->groupId() == ROLE_DIRECTOR) {
        $newPass = '070707a';
    } else {
        $newPass = '000000';
    }
    $User->password($newPass);
    $User->authToken(uniqidReal(User::getMaxAuthTokenLength()));
    $User->save();
}

$nextOffset = $limit + $offset;
echo 'Обработано ' . $nextOffset . ' пользователей из ' . $total;

?>

<script>
    setTimeout(function(){
        window.location.href = '/musadm/test?offset=<?=$nextOffset?>';
    }, 500);
</script>