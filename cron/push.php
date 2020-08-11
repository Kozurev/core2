<?php


Core::requireClass('Push');

if (!Core_Access::instance()->hasCapability(Core_Access::CRON)) {
    die('Access forbidden');
}

//$logFile = ROOT . '/log/cron/' . date('Y-m-d H:i') . '.txt';
$logData = '';
$logCountNotifies = 0;

$date = new DateTime(date('Y-m-d'));
$date->modify('+1 day');
$tomorrowDate = $date->format('Y-m-d');
$tomorrowDayName = $date->format('l');

$userTableName =    Core::factory('User')->getTableName();
$areaTableName =    Core::factory('Schedule_Area')->getTableName();
$lessonTableName =  Core::factory('Schedule_Lesson')->getTableName();

$lessons = (new Schedule_Lesson)
    ->queryBuilder()
    ->select([
        $lessonTableName . '.id',
        $lessonTableName . '.time_from',
        'CONCAT(teacher.surname, \' \', teacher.name) AS teacher',
        'u.push_id',
        'type_id',
        'client_id',
        'u.id AS user_id'
    ])
    ->where('u.subordinated', '=', 585)
    ->where('day_name', '=', $tomorrowDayName)
    ->where('type_id', '=', Schedule_Lesson::TYPE_INDIV)
    ->open()
        ->open()
            ->where('lesson_type', '=', Schedule_Lesson::SCHEDULE_CURRENT)
            ->where('insert_date', '=', $tomorrowDate)
        ->close()
        ->open()
            ->orWhere('lesson_type', '=', Schedule_Lesson::SCHEDULE_MAIN)
            ->where('insert_date', '<=', $tomorrowDate)
            ->open()
                ->where('delete_date', '>', $tomorrowDate)
                ->orWhere('delete_date', 'is', 'NULL')
            ->close()
        ->close()
    ->close()
    ->join($userTableName . ' AS u', 'u.id = ' . $lessonTableName . '.client_id AND u.push_id IS NOT NULL')
    ->join($userTableName . ' AS teacher', $lessonTableName . '.teacher_id = teacher.id')
    ->findAll();
$lessonsCount = count($lessons);

/** @var Schedule_Lesson[] $lessons */
foreach ($lessons as $lesson) {
    if ($lesson->isAbsent($tomorrowDate)) {
        $lessonsCount--;
        continue;
    }

    try {
        $logCountNotifies++;
        Push::instance()->notification([
            'title' => 'Напоминаем о предстоящем занятии',
            'body' => 'Ждем вас завтра в ' . refactorTimeFormat($lesson->timeFrom()) . ', преподаватель ' . $lesson->teacher
        ])->send($lesson->push_id);
    } catch (Kreait\Firebase\Exception\Messaging\NotFound $e) {
        $logCountNotifies--;
        $logData .= 'Ошибка: ' . $lesson->user_id . ' ' . $e->getMessage() . '; ';
    } catch (Kreait\Firebase\Exception\Messaging\InvalidMessage $e) {
        $logCountNotifies--;
        $logData .= 'Ошибка: ' . $lesson->user_id . ' ' . $e->getMessage() . '; ';
    } catch (Kreait\Firebase\Exception\InvalidArgumentException $e) {
        $logCountNotifies--;
        $logData .= 'Ошибка: ' . $lesson->user_id . ' ' . $e->getMessage() . '; ';
    }
}

Log::instance()->debug(Log::TYPE_CRON, 'Всего оповещений отправлено: ' . $logCountNotifies . ' из ' . $lessonsCount . '; ' . $logData);