<?php
/**
 * Класс реализующий методы для работы с событиями ('Журнал событий')
 *
 * @author Kozurev Egor
 * @date 26.11.2018 14:59
 * @version 20190328
 * @version 20190412
 * Class Event
 */
class Event extends Event_Model
{

    /**
     * Список констант идентификаторов типов событий
     * Примечание: при создании нового типа в таблице Event_Type необходимо создать аналогичную новой записи константу
     */
    const SCHEDULE_APPEND_USER =            2;
    const SCHEDULE_REMOVE_USER =            3;
    const SCHEDULE_CREATE_ABSENT_PERIOD =   4;
    const SCHEDULE_EDIT_ABSENT_PERIOD =     27;
    const SCHEDULE_CHANGE_TIME =            5;
    const SCHEDULE_APPEND_CONSULT =         28;
    const SCHEDULE_SET_ABSENT =             29;

    const CLIENT_ARCHIVE =                  7;
    const CLIENT_UNARCHIVE =                8;
    const CLIENT_APPEND_COMMENT =           9;

    const PAYMENT_CHANGE_BALANCE =          11;
    const PAYMENT_HOST_COSTS =              12;
    const PAYMENT_TEACHER_PAYMENT =         13;
    const PAYMENT_APPEND_COMMENT =          14;

    const TASK_CREATE =                     16;
    const TASK_DONE =                       17;
    const TASK_APPEND_COMMENT =             18;
    const TASK_CHANGE_DATE =                19;

    const LID_CREATE =                      21;
    const LID_APPEND_COMMENT =              22;
    const LID_CHANGE_DATE =                 23;

    const CERTIFICATE_CREATE =              25;
    const CERTIFICATE_APPEND_COMMENT =      26;


    /**
     * Тип формирования шаблона строки
     */
    const STRING_FULL =     'full';     //Строка начинается с фамилии и имени клиента/преподавателя
    const STRING_SHORT =    'short';    //Строка НЕ начинается с фамилии и имени клиента/преподавателя


    public function getTypeName() : string
    {
        if ($this->typeId() > 0) {
            $EventType = Core::factory('Event_Type', $this->typeId());
            if (is_null($EventType)) {
                return 'неизвестно';
            } else {
                return lcfirst($EventType->title());
            }
        } else {
            return 'неизвестно';
        }
    }


    /**
     * Формирование строки текста события
     * Вероятно придется разделять формирование строки для клиета и менеджера
     *
     * @param string $type - принимает значение одной из констант с префиксом STRING_
     * @return string
     */
    public function getTemplateString(string $type = self::STRING_FULL) : string
    {
        if (!in_array($type, [self::STRING_SHORT, self::STRING_FULL])) {
            return 'Неверно указан тип формирования шаблона строки - ' . $type;
        }

        //Пока что всего 2 типа формирования шаблона и обходится конструкцией if/elseif
        //но на будущее лучше использовать тут конструкцию switch
        if ($type === self::STRING_FULL) {
            $str = $this->user_assignment_fio . '. ';
        } elseif ($type === self::STRING_SHORT) {
            $str = '';
        }

        if (is_null($this->data())) {
            return 'При сохранении события типа \'' . $this->getTypeName() . '\' произошла неизвестная ошибка';
        }

        switch ($this->type_id)
        {
            case self::SCHEDULE_APPEND_USER:
                Core::factory('Schedule_Lesson');
                $this->data()->Lesson->lessonType() == 1
                    ?   $str .= 'Основной график с '
                    :   $str .= 'Актуальный график на ' ;
                $insertDate = refactorDateFormat($this->data()->Lesson->insertDate());
                $timeStart = $this->data()->Lesson->timeFrom();
                return $str . $insertDate . ' в ' . $timeStart;
                break;

            case self::SCHEDULE_REMOVE_USER:
                Core::factory('Schedule_Lesson');
                $date = refactorDateFormat($this->data()->date);
                $this->data()->Lesson->lessonType() == 1
                    ?   $str .= 'Удален(а) из основного графика с ' . $date
                    :   $str .= 'Удален(а) из актуального графика ' . $date;
                return $str;
                break;

            case self::SCHEDULE_SET_ABSENT:
                Core::factory('Schedule_Lesson');
                $date = refactorDateFormat($this->data()->date);
                return 'Отсутствует ' . $date;
                break;

            case self::SCHEDULE_CREATE_ABSENT_PERIOD:
                Core::factory('Schedule_Absent');
                $dateFrom = refactorDateFormat($this->data()->Period->dateFrom());
                $dateTo =   refactorDateFormat($this->data()->Period->dateTo());
                return $str . 'Отсутствует с ' . $dateFrom . ' по ' . $dateTo;
                break;

            case self::SCHEDULE_CHANGE_TIME:
                Core::factory('Schedule_Lesson');
                $date =         refactorDateFormat($this->data()->date);
                $oldTimeFrom =  refactorTimeFormat($this->data()->Lesson->timeFrom());
                $newTimeFrom =  refactorTimeFormat($this->data()->new_time_from);
                return $str . ' Актуальный график изменился на ' . $date
                            . '; старое время ' . $oldTimeFrom
                            . ', новое время ' . $newTimeFrom . '.';
                break;

            case self::SCHEDULE_EDIT_ABSENT_PERIOD:
                Core::factory('Schedule_Absent');
                $oldDateFrom =  refactorDateFormat($this->data()->old_Period->dateFrom());
                $oldDateTo =    refactorDateFormat($this->data()->old_Period->dateTo());
                $newDateFrom =  refactorDateFormat($this->data()->new_Period->dateFrom());
                $newDateTo =    refactorDateFormat($this->data()->new_Period->dateTo());
                return $str . 'Период отсутствия изменен с: ' . $oldDateFrom . ' - ' . $oldDateTo
                                                    . ' на: ' . $newDateFrom . ' - ' . $newDateTo;
                break;

            case self::SCHEDULE_APPEND_CONSULT:
                Core::factory('Schedule_Lesson');
                Core::factory('Lid');
                $Lesson =   $this->data()->Lesson;
                $timeFrom = refactorTimeFormat($Lesson->timeFrom());
                $timeTo =   refactorTimeFormat($Lesson->timeTo());
                $str = 'Добавил(а) консультацию c ' . $timeFrom . ' по ' . $timeTo . '. ';
                if ($Lesson->clientId()) {
                    $lidId = $this->data()->Lid->getId();
                    $str .= "Лид <a href='#' class='info-by-id' data-model='Lid' data-id='".$lidId."'>№".$lidId."</a>";
                }
                return $str;
                break;

            case self::CLIENT_ARCHIVE:
                return $str . 'Добавлен(а) в архив';
                break;

            case self::CLIENT_UNARCHIVE:
                return $str . 'Восстановлен(а) из архива';
                break;

            case self::CLIENT_APPEND_COMMENT:
                Core::factory('User_Comment');
                return $str . 'Добавлен комментарий с текстом: ' . $this->data()->Comment->text();

            case self::PAYMENT_CHANGE_BALANCE:
                Core::factory('Payment');
                return $str . 'Внесение оплаты пользователю на сумму ' . $this->data()->Payment->value() . ' руб.';
                break;

            case self::PAYMENT_HOST_COSTS:
                Core::factory('Payment');
                return 'Внесение хозрасходов на сумму ' . $this->data()->Payment->value() . ' руб.';
                break;

            case self::PAYMENT_TEACHER_PAYMENT:
                Core::factory('Payment');
                return 'преп. ' . $this->user_assignment_fio . '. Выплата на сумму ' . $this->data()->Payment->value() . ' руб.';
                break;

            case self::PAYMENT_APPEND_COMMENT:
                Core::factory('Property_String');
                return 'Добавил(а) комментарий к платежу с текстом: \''. $this->data()->Comment->value() .'\'';
                break;

            case self::TASK_CREATE:
                Core::factory('Task_Note');
                $id =   $this->data()->Note->taskId();
                $text = $this->data()->Note->text();
                return "Добавил(а) задачу 
                        <a href='#' class='info-by-id' data-model='Task' data-id='" . $id . "'>№" . $id . "</a> 
                        с комментарием: '" . $text . "'";
                break;

            case self::TASK_DONE:
                $id = $this->data()->task_id->getId();
                return "Закрыл(а) задачу 
                        <a href='#' class='info-by-id' data-model='Task' data-id='" . $id ."'>№" . $id . "</a>";
                break;

            case self::TASK_APPEND_COMMENT:
                Core::factory('Task_Note');
                $id =   $this->data()->Note->taskId();
                $text = $this->data()->Note->text();
                return "Добавил(а) комментарий к задаче 
                        <a href='#' class='info-by-id' data-model='Task' data-id='" . $id . "'>№" . $id . "</a> 
                        с текстом: '" . $text . "'";
                break;

            case self::TASK_CHANGE_DATE:
                $id =       $this->data()->task_id;
                $newDate =  refactorDateFormat($this->data()->new_date);
                $oldDate =  refactorDateFormat($this->data()->old_date);
                return "Задача 
                        <a href='#' class='info-by-id' data-model='Task' data-id='" . $id . "'>№" . $id . "</a>. 
                        Изменение даты с " . $oldDate . " на " . $newDate;
                break;

            case self::LID_CREATE:
                Core::factory('Lid');
                $id =           $this->data()->Lid->getId();
                $lidSurname =   $this->data()->Lid->surname();
                $lidName =      $this->data()->Lid->name();
                return "Добавил(а) лида 
                        <a href='#' class='info-by-id' data-model='Lid' data-id='" . $id . "'>№$id</a> $lidSurname $lidName";
                break;

            case self::LID_APPEND_COMMENT:
                Core::factory('Lid_Comment');
                $id = $this->data()->Comment->lidId();
                return "Добавил(а) комментарий к лиду 
                <a href='#' class='info-by-id' data-model='Lid' data-id='" . $id . "'>№$id</a> 
                с текстом '" . $this->data()->Comment->text() . "'";
                break; 

            case self::LID_CHANGE_DATE:
                Core::factory('Lid');
                $id =       $this->data()->Lid->getId();
                $oldDate =  refactorDateFormat($this->data()->old_date);
                $newDate =  refactorDateFormat($this->data()->new_date);
                return "Лид 
                        <a href='#' class='info-by-id' data-model='Lid' data-id='" . $id . "'>№$id</a>. 
                        Изменение даты с " . $oldDate . " на " . $newDate;
                break;

            case self::CERTIFICATE_CREATE:
                Core::factory('Certificate');
                $id =   $this->data()->Certificate->getId();
                $num =  $this->data()->Certificate->number();
                return "Добавил(а) сертификат 
                        <a href='#' class='info-by-id' data-model='Certificate' data-id='" . $id . "'>№" . $num . "</a>";
                break;

            case self::CERTIFICATE_APPEND_COMMENT:
                Core::factory('Certificate_Note');
                $id =   $this->data()->Note->certificateId();
                $num =  Core::factory( 'Certificate', $id )->number();
                return "Сертификат 
                        <a href='#' class='info-by-id' data-model='Certificate' data-id='" . $id . "'>№$num</a>. "
                        . $this->data()->Note->text();
                break;

            default: return 'Шаблон формирования сообщения для события типа ' . $this->type_id . ' отсутствует.';
        }
    }

    /**
     * @param null $obj
     * @return void
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'beforeEventSave');

        //Задание значение времени события
        if ($this->time === 0) {
            $this->time = time();
        }

        //Задание значений связанных с автором события - author_id & author_fio
        if ($this->authorId() === 0) {
            $CurrentUser = User::parentAuth();

            if (!is_null($CurrentUser)) {
                $this->author_id =  $CurrentUser->getId();
                $this->author_fio = $CurrentUser->surname() . ' ' . $CurrentUser->name();
                if($CurrentUser->patronimyc() != '') {
                    $this->author_fio .= ' ' . $CurrentUser->patronimyc();
                }
            }
        }

        //Конвертация дополнительных данных события в строку
        if (is_array($this->data) || is_object($this->data)) {
            try {
                $this->data = serialize($this->data);
            } catch (Exception $e) {
                echo "<h2>" . $e->getMessage() . "</h2>";
                return;
            }
        }

        parent::save();
        Core::notify([&$this], 'afterEventSave');
    }


    /**
     * @param null $obj
     * @return void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'beforeEventDelete');
        parent::delete();
        Core::notify([&$this], 'afterEventDelete');
    }

}