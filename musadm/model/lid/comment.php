<?php
/**
 * Класс-модель комментария к лиду (необходимо удалить и заменить одной единой таблицей комментариев)
 *
 * @author BadWolf
 * @date 24.04.2018 22:19
 * @version 20190328
 * Class Lid_Comment
 */
class Lid_Comment extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * id автора комментария
     *
     * @var int
     */
    protected $author_id = 0;


    /**
     * id лида с которым связан комментарий
     *
     * @var int
     */
    protected $lid_id = 0;


    /**
     * Текст комментария
     *
     * @var string
     */
    protected $text;


    /**
     * Дата и время создания комментария
     *
     * @var string
     */
    protected $datetime;


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
     * @param int|null $lidId
     * @return $this|int
     */
    public function lidId(int $lidId = null)
    {
        if (is_null($lidId)) {
            return intval($this->lid_id);
        } else {
            $this->lid_id = $lidId;
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
     * @param string|null $datetime
     * @return $this|string
     */
    public function datetime(string $datetime = null)
    {
        if (is_null($datetime)) {
            return $this->datetime;
        } else {
            $this->datetime = $datetime;
            return $this;
        }
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'beforeLidCommentSave');

        $User = User::current();
        if ($this->author_id === 0 && !is_null($User)) {
            $this->author_id = $User->getId();
        }

        if (is_null($this->datetime)) {
            $this->datetime = date('Y-m-d H:i:s');
        }

        parent::save();

        Core::notify([&$this], 'afterLidCommentSave');
    }


    //Параметры валидации при сохранении таблицы
    public function schema()
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'author_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'lid_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'datetime' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'text' => [
                'required' => true,
                'type' => PARAM_STRING
            ]
        ];
    }

}