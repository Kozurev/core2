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

    const PRIORITY_NORMAl = 1;  //Обычный приоритет
    const PRIORITY_MEDIUM = 2;  //Средний приоритет
    const PRIORITY_HIGH = 3;    //Высокий приоритет


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
        return 'Задача закрыта';
    }


    /**
     * Геттер для текста комментария при восстановлении задачи для доработки
     *
     * @return string
     */
    public function doneRevertComment() : string
    {
        return 'Задача восстановлена на доработку';
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
                $Note->authorId(User::parentAuth()->getId());
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

        Core::notify([&$this], 'before.Task.markAsDone');

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


    /**
     * @return false|int
     */
    public function time()
    {
        return strtotime($this->date);
    }


    /**
     * @param null $obj
     * @return $this
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'before.Task.save');

        if (empty($this->date)) {
            $this->date = date('Y-m-d');
        }

        if (empty(parent::save())) {
            return null;
        }

        Core::notify([&$this], 'after.Task.save');
        return $this;
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'before.Task.delete');

        foreach ($this->getNotes() as $Note) {
            $Note->delete();
        }

        parent::delete();
        Core::notify([&$this], 'after.Task.delete');
    }

}