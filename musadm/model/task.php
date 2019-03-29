<?php
/**
 * Класс реализующий методы для работы с задачами
 *
 * @author BadWolf
 * @date 16.05.2018 16:47
 * @version 20190329
 * Class Task
 */
class Task extends Task_Model
{
    //Текст комментария при закрытии задачи
    const TASK_DONE_COMMENT = 'Задача закрыта';

    //Текст задачи при возвращения к работе
    const TASK_DONE_REVERT_COMMENT = 'Задача восстановлена на доработку';


    /**
     * Поиск комментариев задачи
     *
     * @return array
     */
    public function getNotes() : array
    {
        if (empty($this->id)) {
            return [];
        }

        return Core::factory('Task_Note')
            ->queryBuilder()
            ->where('task_id', '=', $this->getId())
            ->orderBy('date', 'DESC')
            ->findAll();
    }


    /**
     * Геттер для текста комментария при закрытии задачи
     *
     * @return string
     */
    public function doneComment() : string
    {
        return self::TASK_DONE_COMMENT;
    }


    /**
     * Геттер для текста комментария при восстановлении задачи для доработки
     *
     * @return string
     */
    public function doneRevertComment() : string
    {
        return self::TASK_DONE_REVERT_COMMENT;
    }


    /**
     * Добавление комментария к задаче
     *
     * @param string $text - текст комментария
     * @param int|null $authorId - id автора, по умолчанию береться id авторизованного пользователя
     * @param string|null $date - дата комментария, по умолчанию берется текущая
     * @return $this
     */
    public function addNote(string $text, int $authorId = null, string $date = null)
    {
        $Note = Core::factory('Task_Note');

        if (is_null($authorId)) {
            if (is_null(User::current())) {
                $Note->authorId(0);
            } else {
                $Note->authorId(User::current()->getId());
            }
        } else {
            $Note->authorId($authorId);
        }

        if (is_null($date)) {
            $Note->date(date('Y-m-d H:i:s'));
        } else {
            $Note->date($date);
        }

        $Note
            ->taskId($this->id)
            ->text($text)
            ->save();

        return $this;
    }


    /**
     * Пометка завершения задачи
     *
     * @return $this
     */
    public function markAsDone()
    {
        if (empty($this->id)) {
            return $this;
        }

        Core::notify([&$this], 'TaskMarkAsDone');

        $this
            ->doneDate(date('Y-m-d'))
            ->done(1)
            ->save();

        $this->addNote($this->doneComment());
        return $this;
    }


    /**
     * Возвращение задачи на выполнение
     *
     * @return $this
     */
    public function doneRevert()
    {
        if (empty($this->id)) {
            return $this;
        }

        Core::notify([&$this], 'TaskDoneRevert');

        $this->done_date = null;
        $this->done(0)->save();
        return $this;
    }


    public function time()
    {
        return strtotime( $this->date );
    }


    public function save( $obj = null )
    {
        Core::notify( [&$this], "beforeTaskSave" );

        if ( $this->date == "" )   $this->date = date( "Y-m-d" );

        parent::save();

        Core::notify( [&$this], "afterTaskSave" );

        return $this;
    }


    public function delete( $obj = null )
    {
        Core::notify( [&$this], "beforeTaskDelete" );

        parent::delete();

        Core::notify( [&$this], "afterTaskDelete" );
    }

}