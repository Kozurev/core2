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
//Orm::Debug(true);
$Orm = new Orm();

//$Orm->executeQuery('ALTER TABLE Payment ADD author_id int DEFAULT 0 NULL');
//$Orm->executeQuery('ALTER TABLE Payment ADD author_fio varchar(255) DEFAULT \'\' NULL;');
//$Orm->executeQuery('ALTER TABLE Lid ADD priority_id int DEFAULT 0 NULL');
//$Orm->executeQuery('UPDATE Lid SET priority_id = 1 WHERE 1');
//
//$LidMarker = Core::factory('Property')
//    ->tagName('lid_marker')
//    ->title('Маркер лида')
//    ->description('Особый маркер лида, необходимый для аналитики')
//    ->type('list')
//    ->defaultValue(0)
//    ->dir(0)
//    ->sorting(0)
//    ->save();
//
//$LidMarker->addToPropertiesList(Core::factory('Lid'), $LidMarker->getId());
//
////Core::factory('Property_List_Values')
////    ->propertyId(54)
////    ->value('Маркер 1')
////    ->subordinated(516)
////    ->sorting(0)
////    ->save();
////
////Core::factory('Property_List_Values')
////    ->propertyId(54)
////    ->value('Маркер 2')
////    ->subordinated(516)
////    ->sorting(0)
////    ->save();
//
//Core::factory('Structure')
//    ->title('Аналитика лидов')
//    ->parentId(28)
//    ->path('statistic')
//    ->action('musadm/lid/statistic')
//    ->description('Раздел аналитики лидов, подведения итогов')
//    ->save();
//
//Core::factory('Structure')
//    ->title('Консультации лидов')
//    ->parentId(28)
//    ->path('consults')
//    ->action('musadm/lid/consults')
//    ->description('Раздел с лидами, учавствующих в расписании')
//    ->save();
//
//Core::factory('Core_Access_Group', 1)->capabilityAllow(Core_Access::LID_STATISTIC);
//Core::factory('Core_Access_Group', 2)->capabilityForbidden(Core_Access::LID_STATISTIC);
//Core::factory('Core_Access_Group', 3)->capabilityForbidden(Core_Access::LID_STATISTIC);
//Core::factory('Core_Access_Group', 4)->capabilityForbidden(Core_Access::LID_STATISTIC);
//Core::factory('Core_Access_Group', 5)->capabilityAllow(Core_Access::LID_STATISTIC);
//Core::factory('Core_Access_Group', 6)->capabilityAllow(Core_Access::LID_STATISTIC);

//$LidStatusClient = Core::factory('Property')
//    ->tagName('lid_status_client')
//    ->title('Статус: стал клиентом')
//    ->description('Статус, присваевымый лиду после того как он стал клиентом')
//    ->type('int')
//    ->defaultValue(0)
//    ->dir(0)
//    ->sorting(0)
//    ->save();
//
//$Director = Core::factory('User', 516);
//$LidStatusClient->addNewValue($Director, 4);




//Core::requireClass('Lid_Controller');
//$Lid = Lid_Controller::factory(2849);
//
//try {
//    $Lid->addComment('Тестовый комментарий 1');
//    $Lid->addComment('Коммент номер 2');
//    debug($Lid->getComments());
//} catch(Exception $e) {
//    echo $e->getMessage();
//}



//$totalCount = Core_Array::Get('totalCount', Core::factory('Lid')->getCount(), PARAM_INT);
//$doneLids = Core_Array::Get('doneLids', 0, PARAM_INT);
//$doneComments = Core_Array::Get('doneComments', 0, PARAM_INT);
//$limit = 50;
//
//
//if ($doneLids == 0) {
//    Orm::execute('ALTER TABLE Comment MODIFY author_fullname varchar(255)');
//    Orm::execute('CREATE TABLE Lid_Comment_Assignment
//                        (
//                            id int PRIMARY KEY AUTO_INCREMENT,
//                            object_id int,
//                            comment_id int
//                        )');
//}
//
//if ($doneLids >= $totalCount) {
//    exit('Скрипт закончил работу. Обработано <b>' . $doneLids . '</b> лидов и ' . $doneComments);
//}
//
//$Lids = Core::factory('Lid')
//    ->queryBuilder()
//    ->select('id')
//    ->limit($limit)
//    ->offset($doneLids)
//    ->findAll();
//
//$lidIds = [];
//$LidsAssoc = [];
//foreach ($Lids as $Lid) {
//    $lidIds[] = $Lid->getId();
//    $LidsAssoc[$Lid->getId()] = $Lid;
//}
//
//$Comments = Core::factory('Lid_Comment')
//    ->queryBuilder()
//    ->whereIn('lid_id', $lidIds)
//    ->findAll();
//
//foreach ($Comments as $Comment) {
//    $NewComment = new Comment();
//    $NewComment->datetime($Comment->datetime());
//    $NewComment->text($Comment->text());
//    $NewComment->authorId($Comment->authorId());
//    $Author = Core::factory('User', $Comment->authorId());
//    if (!is_null($Author)) {
//        $NewComment->authorFullname($Author->surname() . ' ' . $Author->name());
//    }
//    $NewComment->save();
//    try {
//        $NewComment->makeAssignment($LidsAssoc[$Comment->lidId()]);
//    } catch (Exception $e) {
//        die($e->getMessage());
//    }
//}
//$doneLids += count($lidIds);
//$doneComments += count($Comments);
//echo 'Обработано <b>' . $doneLids . '</b> лидов из <b>' . $totalCount . '</b> и <b>' . $doneComments . '</b> комментариев';
?>

<script>
//setTimeout(function(){
//document.location.href = '<?//=$CFG->wwwroot?>///test?totalCount=<?//=$totalCount?>//&doneLids=<?//=$doneLids?>//&doneComments=<?//=$doneComments?>//';
//}, 200);
</script>

<?php

$Directors = Core::factory('Core_Access_Group', 1);
$Managers = Core::factory('Core_Access_Group', 2);
$Teachers = Core::factory('Core_Access_Group', 3);
$Clients = Core::factory('Core_Access_Group', 4);

$Directors->capabilityAllow(Core_Access::USER_LC_CLIENT);
$Directors->capabilityAllow(Core_Access::USER_LC_TEACHER);
$Directors->capabilityAllow(Core_Access::SCHEDULE_READ_USER);

$Managers->capabilityAllow(Core_Access::USER_LC_CLIENT);
$Managers->capabilityAllow(Core_Access::USER_LC_TEACHER);
$Managers->capabilityAllow(Core_Access::SCHEDULE_READ_USER);

$Teachers->capabilityForbidden(Core_Access::USER_LC_CLIENT);
$Teachers->capabilityAllow(Core_Access::USER_LC_TEACHER);
$Teachers->capabilityForbidden(Core_Access::SCHEDULE_READ);
$Teachers->capabilityAllow(Core_Access::SCHEDULE_READ_USER);

$Clients->capabilityAllow(Core_Access::USER_LC_CLIENT);
$Clients->capabilityForbidden(Core_Access::USER_LC_TEACHER);
$Clients->capabilityForbidden(Core_Access::USER_READ_CLIENTS);
$Clients->capabilityForbidden(Core_Access::SCHEDULE_READ);
$Clients->capabilityAllow(Core_Access::SCHEDULE_READ_USER);