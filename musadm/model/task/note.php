<?php
/**
 * Класс-модель примечания к задаче
 *
 * @author BadWolf
 * @date 16.05.2018 16:50
 * @version 20190401
 * Class Task_Note
 */
class Task_Note extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Дата и время создания примечания формата 'Y-m-d H:i:s'
     *
     * @var string
     */
    protected $date;


    /**
     * id задачи, которой принадлежит примечание
     *
     * @var int
     */
    protected $task_id = 0;


    /**
     * id пользователя-автора примечания
     *
     * @var int
     */
    protected $author_id = 0;


    /**
     * Текст примечания
     *
     * @var string
     */
    protected $text;


    /**
     * @param string|null $date
     * @return $this|string
     */
    public function date(string $date = null)
    {
        if (is_null($date)) {
            return $this->date;
        } else {
            $this->date = $date;
            return $this;
        }
    }


    /**
     * @param int|null $authorId
     * @return $this|int
     */
    public function authorId(int $authorId = null)
    {
        if (is_null($authorId)) {
            return intval($this->author_id);
        } else {
            $this->author_id = $authorId;
            return $this;
        }
    }


    /**
     * @param int|null $taskId
     * @return $this|int
     */
    public function taskId(int $taskId = null)
    {
        if (is_null($taskId)) {
            return intval($this->task_id);
        } else {
            $this->task_id = $taskId;
            return $this;
        }
    }


    /**
     * @param string|null $text
     * @return $this|string
     */
    public function text(string $text = null)
    {
        if (is_null($text)) {
            return $this->text;
        } else {
            $this->text = $text;
            return $this;
        }
    }


    /**
     * Поиск автора текста задачи
     *
     * @return User|null
     */
    public function getAuthor()
    {
        if ($this->authorId() === 0) {
            return null;
        }

        $Author = Core::factory('User', $this->author_id);
        return $Author;
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'beforeTaskNoteSave');

        if (is_null($this->date)) {
            $this->date = date('Y-m-d H:i:s');
        }
        if ($this->author_id === 0) {
            $this->author_id = User::parentAuth()->getId();
        }

        parent::save();
        Core::notify([&$this], 'afterTaskNoteSave');
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete( $obj = null )
    {
        Core::notify( [&$this], "beforeTaskNoteDelete" );

        parent::delete();

        Core::notify( [&$this], "afterTaskNoteDelete" );
    }

}