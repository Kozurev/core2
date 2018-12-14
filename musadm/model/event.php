<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 26.11.2018
 * Time: 14:59
 */

class Event extends Event_Model
{

    /**
     * Формирование строки текста события
     * Вероятно придется разделять формирование строки для клиета и менеджера
     *
     * @return string
     */
    public function getTemplateString()
    {
        switch ( $this->type_id )
        {
            case 2:
                Core::factory( "Schedule_Lesson" );
                $str = $this->user_assignment_fio . ". ";
                $this->data()->Lesson->lessonType() == 1
                    ?   $str .= "Основной график с "
                    :   $str .= "Актуальный график на " ;
                $str .= refactorDateFormat($this->data()->Lesson->insertDate()) . " в " . $this->data()->Lesson->timeFrom();
                return $str;
                break;

            case 3:
                Core::factory( "Schedule_Lesson" );
                $str = $this->user_assignment_fio . " ";
                $this->data()->Lesson->lessonType() == 1
                    ?   $str .= "Удален(а) из основного графика с " . refactorDateFormat($this->data()->date)
                    :   $str .= "Отсутствует " . refactorDateFormat($this->data()->date);
                return $str;
                break;

            case 4:
                Core::factory( "Schedule_Absent" );
                return $this->user_assignment_fio . ". Отсутствует с " . refactorDateFormat($this->data()->Period->dateFrom())
                    . " по " . refactorDateFormat($this->data()->Period->dateTo());
                break;

            case 5:
                Core::factory( "Schedule_Lesson" );
                return $this->user_assignment_fio . ". Актуальный график изменился на " . refactorDateFormat($this->data()->date)
                    . "; старое время " . refactorTimeFormat($this->data()->Lesson->timeFrom())
                    . ", новое время " . refactorTimeFormat($this->data()->new_time_from) . ".";
                break;

            case 27:
                Core::factory( "Schedule_Lesson" );
                Core::factory( "Lid" );
                $Lesson = $this->data()->Lesson;
                $str =  "Добавил(а) консультацию c " . refactorTimeFormat( $Lesson->timeFrom() ) . " по " . refactorTimeFormat( $Lesson->timeTo() ) . ". ";

                if( $Lesson->clientId() )
                {
                    $lidId = $this->data()->Lid->getId();
                    $str .= "Лид <a href='#' class='info-by-id' data-model='Lid' data-id='".$lidId."'>№" . $lidId . "</a>";
                }
                return $str;
                break;

            case 7:
                return $this->user_assignment_fio . ". Добавлен(а) в архив";
                break;

            case 8:
                return $this->user_assignment_fio . ". Восстановлен(а) из архива";
                break;

            case 9:
                Core::factory( "User_Comment" );
                return $this->user_assignment_fio . ". Добавлен комментарий с текстом: " . $this->data()->Comment->text();

            case 11:
                Core::factory( "Payment" );
                return $this->user_assignment_fio . ". Внесение оплаты пользователю на сумму " . $this->data()->Payment->value() . " руб.";
                break;

            case 12:
                Core::factory( "Payment" );
                return "Внесение хозрасходов на сумму " . $this->data()->Payment->value() . " руб.";
                break;

            case 13:
                Core::factory( "Payment" );
                return "преп. " . $this->user_assignment_fio . ". Выплата на сумму " . $this->data()->Payment->value() . " руб.";
                break;

            case 14:
                Core::factory( "Property_String" );
                return "Добавил(а) комментарий к платежу с текстом: '". $this->data()->Comment->value() ."'";
                break;

            case 16: 
                Core::factory( "Task_Note" );
                $id = $this->data()->Note->taskId();
                $text = $this->data()->Note->text();
                return "Добавил(а) задачу <a href='#' class='info-by-id' data-model='Task' data-id='".$id."'>№" . $id . "</a> с комментарием: '" . $text . "'";
                break;

            case 17:
                $id = $this->data()->task_id->getId();
                return "Закрыл(а) задачу <a href='#' class='info-by-id' data-model='Task' data-id='" . $id ."'>№" . $id . "</a>";
                break;

            case 18:
                Core::factory( "Task_Note" );
                $id = $this->data()->Note->taskId();
                $text = $this->data()->Note->text();
                return "Добавил(а) комментарий к задаче <a href='#' class='info-by-id' data-model='Task' data-id='".$id."'>№" . $id . "</a> с текстом: '" . $text . "'";
                break;

            case 19:
                $id = $this->data()->task_id;
                $newDate = refactorDateFormat($this->data()->new_date);
                $oldDate = refactorDateFormat($this->data()->old_date);
                return "Задача <a href='#' class='info-by-id' data-model='Task' data-id='".$id."'>№" . $id . "</a>. Изменение даты с " . $oldDate . " на " . $newDate;
                break;

            case 21:
                Core::factory( "Lid" );
                $id = $this->data()->Lid->getId();
                return "Добавил(а) лида <a href='#' class='info-by-id' data-model='Lid' data-id='".$id."'>№" . $id . "</a> " . $this->data()->Lid->surname() . " " . $this->data()->Lid->name();
                break;

            case 22:
                Core::factory( "Lid_Comment" );
                $id = $this->data()->Comment->lidId();
                return "Добавил(а) комментарий к лиду <a href='#' class='info-by-id' data-model='Lid' data-id='".$id."'>№" . $id . "</a> с текстом '" . $this->data()->Comment->text() . "'";
                break; 

            case 23:
                Core::factory( "Lid" );
                $id = $this->data()->Lid->getId();
                $oldDate = refactorDateFormat( $this->data()->old_date );
                $newDate = refactorDateFormat( $this->data()->new_date );
                return "Лид <a href='#' class='info-by-id' data-model='Lid' data-id='".$id."'>№" . $id . "</a>. Изменение даты с " . $oldDate . " на " . $newDate;
                break;

            case 25: 
                Core::factory( "Certificate" );
                $id = $this->data()->Certificate->getId();
                $num = $this->data()->Certificate->number();
                return "Добавил(а) сертификат <a href='#' class='info-by-id' data-model='Certificate' data-id='".$id."'>№" . $num . "</a>";
                break;

            case 26:
                //Core::factory( "Certificate" );
                Core::factory( "Certificate_Note" );
                $id = $this->data()->Note->certificateId();
                $num = Core::factory( "Certificate", $id )->number();
                return "Сертификат <a href='#' class='info-by-id' data-model='Certificate' data-id='".$id."'>№" . $num . "</a>. " . $this->data()->Note->text();
                break;

            default: return "Шаблон формирования сообщения для события типа $this->type_id отсутствует.";
        }
    }



    public function save( $obj = null )
    {
        Core::notify( array( &$this ), "beforeEventSave" );

        //Задание значение времени события
        if( $this->time === 0 ) $this->time = time();

        //Задание значений связанных с автором события - author_id & author_fio
        if( $this->author_id === 0 )
        {
            $CurrentUser = User::parentAuth();

            if( $CurrentUser !== false )
            {
                $this->author_id = $CurrentUser->getId();
                $this->author_fio = $CurrentUser->surname() . " " . $CurrentUser->name();

                if( $CurrentUser->patronimyc() != "" )  $this->author_fio .= " " . $CurrentUser->patronimyc();
            }
        }

        //Конвертация дополнительных данных события в строку
        if( is_array( $this->data ) || is_object( $this->data ) )
        {
            try
            {
                $this->data = serialize( $this->data );
            }
            catch( Exception $e )
            {
                echo "<h2>" . $e->getMessage() . "</h2>";
                return;
            }
        }

        parent::save();

        Core::notify( array( &$this ), "afterEventSave" );
    }


    public function delete( $obj = null )
    {
        Core::notify( array( &$this ), "beforeEventDelete" );
        parent::delete();
        Core::notify( array( &$this ), "afterEventDelete" );
    }


}