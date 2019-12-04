<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */


Orm::Debug(true);

Core::requireClass('User');
Core::requireClass('User_Controller');
Core::requireClass('Property_Controller');


Core::requireClass('Vk');


$token = '1677ccea4ae8498645725db24832c0c657fec5c5bc61c50e554a42e33de809d0697b32df400b79cf7f316';
//$token = '1677ccea40c657fec5c5bc61c50e554a42e33de809d0697b32df400b79cf7f316';
//$link = 'public187038781';
$link = 'kinolyapp';

$vk = new VK($token);
debug($vk->resolveScreenName($link));


exit;

Orm::execute('create table Vk_Group
(
	id int auto_increment,
	title varchar(255) null,
	link varchar(50) null,
	vk_id varchar(15) null,
	secret_key varchar(255) null,
	subordinated int null,
	constraint vk_group_pk
		primary key (id)
);');

$StructureIntegration = Core::factory('Structure')
    ->title('Интеграции')
    ->description('Раздел для итеграции с различными сторонними сервисами')
    ->parentId(0)
    ->path('integration')
    ->action('musadm/integration/index')
    ->templateId(10)
    ->menuId(0);
$StructureIntegration->save();

$StructureVk = Core::factory('Structure')
    ->title('Вк')
    ->description('Раздел настроек интеграции с контактом')
    ->parentId($StructureIntegration->getId())
    ->path('vk')
    ->action('musadm/integration/vk')
    ->menuId(0);
$StructureVk->save();


$StructureSenler = Core::factory('Structure')
    ->title('Сенлер')
    ->description('Раздел настроек интеграции с сервисом рассылок "Сенлер"')
    ->parentId($StructureIntegration->getId())
    ->path('senler')
    ->action('musadm/integration/senler')
    ->menuId(0);
$StructureSenler->save();


$IsEnable = Property_Controller::factory()
    ->type(PARAM_BOOL)
    ->title('Интеграция с сенлером')
    ->description('Указатель на то включена ли интеграция с сервисом рассылок "Сенлер"')
    ->tagName('integration_senler_enabled')
    ->defaultValue('0')
    ->active(1)
    ->dir(0)
    ->sorting(0);
$IsEnable->save();


$GroupDirector = Core::factory('Core_Access_Group', 1);
$GroupManager = Core::factory('Core_Access_Group', 2);
$GroupTeacher = Core::factory('Core_Access_Group', 3);
$GroupClient = Core::factory('Core_Access_Group', 4);

$GroupDirector->capabilityAllow('integration_vk');
$GroupDirector->capabilityAllow('integration_senler');
$GroupManager->capabilityForbidden('integration_vk');
$GroupManager->capabilityForbidden('integration_senler');
$GroupTeacher->capabilityForbidden('integration_vk');
$GroupTeacher->capabilityForbidden('integration_senler');
$GroupClient->capabilityForbidden('integration_vk');
$GroupClient->capabilityForbidden('integration_senler');


//$CronUser = (new User)
//    ->surname('Планировщик задач')
//    ->name('Cron')
//    ->active(1)
//    ->authToken(uniqidReal(User::getMaxAuthTokenLength()))
//    ->login('Cron')
//    ->password('cron')
//    ->groupId(0);
//$CronUser->save();
//
//$NewGroup = Core::factory('Core_Access_Group')
//    ->title('Сторонние сервисы и приложения')
//    ->parentId(0)
//    ->description('Группа прав доступа не для пользователей системы, я для приложений и сторонних сервисов')
//    ->subordinated(0)
//    ->save()
//    ->subordinated(0)
//    ->save();
//$NewGroup->appendUser($CronUser->getId());
//
//$Groups = Core::factory('Core_Access_Group')->findAll();
//array_pop($Groups);
//foreach ($Groups as $Group) {
//    $Group->capabilityForbidden(Core_Access::CRON);
//}
//$NewGroup->capabilityAllow(Core_Access::CRON);

/*
$User = User_Controller::factory(1226, false);

$factory = (new Factory())->withServiceAccount(ROOT . '/firebase.json');
$messaging = ($factory)->createMessaging();

//Пример отправки одного уведомления на несколько устройств сразу
$devicesTokens = [$User->pushId()];
$notification = Firebase\Messaging\Notification::create('Title', 'Body');

$message = CloudMessage::new()->withNotification($notification);
$sendReport = $messaging->sendMulticast($message, $devicesTokens);

echo 'Successful sends: '.$sendReport->successes()->count().PHP_EOL;
echo 'Failed sends: '.$sendReport->failures()->count().PHP_EOL;

if ($sendReport->hasFailures()) {
    foreach ($sendReport->failures()->getItems() as $failure) {
        echo $failure->error()->getMessage().PHP_EOL;
    }
}

//Пример отправки уведомления одному пользователю
$deviceToken = $User->pushId();
$message = CloudMessage::withTarget('token', $deviceToken)->withNotification($notification);
$messaging->send($message);
*/


exit;

//$dbh = new mysqli("37.140.192.32:3306", "u4834_root", "n1omY2_1", "u4834955_core");
//$dbh->query("SET NAMES utf8");
global $CFG;
Orm::Debug(false);
$Orm = new Orm();

//Добавление премиальных платежей
$NewPayment = Core::factory('Payment_Type');
$NewPayment1 = clone $NewPayment;
$NewPayment2 = clone $NewPayment;
$NewPayment1->title('Начисление премиальных')->subordinated(0)->isDeletable(0)->save();
$NewPayment2->title('Выплата премий')->subordinated(0)->isDeletable(0)->save();
