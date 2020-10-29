<?php

use Model\Sms;
use Model\Sms\Template;
use Carbon\Carbon;

if (!Core_Access::instance()->hasCapability(Core_Access::CRON)) {
    die('Access forbidden');
}

//Определение типа рассылки: за день или за пару часов
if (Core_Array::Get('type', 'day') == 'day') {
    $date = Carbon::tomorrow()->format('Y-m-d');
    $timeFrom = null;
    $timeTo = null;
    $templateTag = Template::TAG_LIDS_BEFORE_CONSULT_DAY;
} else {
    $date = Carbon::now()->format('Y-m-d');
    $timeFrom = Carbon::now()->addHours(2)->format('H:i:s');
    $timeTo = Carbon::now()->addHours(3)->format('H:i:s');
    $templateTag = Template::TAG_LIDS_BEFORE_CONSULT_HOUR;
}

//Поиск консультаций
$tomorrow = Carbon::tomorrow()->format('Y-m-d');
$consultsQuery = Schedule_Lesson::query()
    ->select(['client_id', 'type_id'])
    ->leftJoin((new Lid)->getTableName() . ' as l', 'client_id = l.id and type_id = ' . Schedule_Lesson::TYPE_CONSULT)
    ->where('lesson_type', '=', Schedule_Lesson::SCHEDULE_CURRENT)
    ->where('insert_date', '=', $date)
    ->open()
        ->where('type_id', '=', Schedule_Lesson::TYPE_CONSULT)
        ->orWhere('type_id', '=', Schedule_Lesson::TYPE_GROUP_CONSULT)
    ->close();

if (!is_null($timeFrom)) {
    $consultsQuery->where('time_from', '>=', $timeFrom)
        ->where('time_to', '<=', $timeTo);
}

$consults = $consultsQuery->get();

//Поиск идентификаторов лидов которые стоят в графике (в том числе и в составе групп) у которых включено смс уведомление
$lidsIds = collect([]);
/** @var Schedule_Lesson $consult */
foreach ($consults as $consult) {
    if ($consult->typeId() == Schedule_Lesson::TYPE_CONSULT) {
        $lidsIds->add($consult->clientId());
    } else {
        $lidsGroup = $consult->getGroup();
        $lidsIds = $lidsIds->merge(
            $lidsGroup->getClientsListQuery()
                ->select([(new Lid)->getTableName() . '.id'])
                ->where('sms_notification', '=', 1)
                ->get()
                ->pluck('id')
                ->toArray()
        );
    }
}

$lids = Lid::query()
    ->select(['id', 'number'])
    ->whereIn('id', $lidsIds->unique()->toArray())
    ->where('sms_notification', '=', 1)
    ->where('number', '<>', '')
    ->get();
$lidsPhoneNumbers = (clone $lids)->pluck('number');
//$lidsPhoneNumbers = collect(['89803782856']);

if ($lidsPhoneNumbers->count() !== 0) {
    try {
        //Отправка сообщений
        Sms::instance()->toNumbers($lidsPhoneNumbers->all());
        Sms::instance()->setTemplateByTag($templateTag);
        $response = Sms::instance()->send();

        //Добавление комментария при успешной отправке сообщения
        $successLids = collect([]);
        foreach ($response->data as $data) {
            if (($data->status ?? '') === 'sent') {
                $lids = Lid::query()
                    ->where('number', 'like', '%' . mb_substr($data->phone, 1))
                    ->get();
                if ($lids->count() > 0) {
                    $successLids = $successLids->merge($lids);
                }
            }
        }
        /** @var Lid $lid */
        foreach ($successLids as $lid) {
            $lid->addComment('Было отправлено СМС с напоминанием о консультации');
        }
    } catch (Exception $e) {
        Log::instance()->error(Log::TYPE_SMS, $e->getMessage());
    }
}