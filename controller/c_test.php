<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 20.04.2018
 * Time: 1:02
 */

use Kreait\Firebase;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Factory;

Orm::Debug(true);

Core::requireClass('User');
Core::requireClass('User_Controller');


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
