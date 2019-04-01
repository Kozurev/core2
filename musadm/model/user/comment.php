<?php
/**
 * Модель комментария к пользователю в новом разделе клиента
 *
 * @author Kozurev Egor
 * @date 30.11.2018 13:43
 * @version 20190401
 * @Class User_Comment
 */
class User_Comment extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * В отличии от других подобных моделей комментариев к сертификату или лиду
     * время сохраняется в формате TIMESTAMP
     *
     * @var int
     */
    protected $time = 0;


    /**
     * id автора комментария
     *
     * @var int
     */
    protected $author_id = 0;


    /**
     * id пользователя к которому создается комментарий
     *
     * @var int
     */
    protected $user_id = 0;


    /**
     * Текст комментария
     *
     * @var string
     */
    protected $text = '';


    /**
     * @param int|null $time
     * @return $this|int
     */
    public function time(int $time = null)
    {
        if (is_null($time)) {
            return intval($this->time);
        } else {
            $this->time = $time;
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
     * @param int|null $userId
     * @return $this|int
     */
    public function userId(int $userId = null)
    {
        if (is_null($userId)) {
            return intval($this->user_id);
        } else {
            $this->user_id = $userId;
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
     * @param null $obj
     * @return $this|void
     */
    public function save($obj = null)
    {
        if ($this->authorId() === 0) {
            $this->authorId(User::parentAuth()->getId());
        }

        if ($this->time() === 0) {
            $this->time = time();
        }

        Core::notify([&$this], 'beforeUserCommentSave');
        parent::save();
        Core::notify([&$this], 'afterUserCommentSave');
    }


    /**
     * @return array
     */
    public function schema() : array
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'time' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'author_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'user_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'text' => [
                'required' => true,
                'type' => PARAM_STRING
            ]
        ];
    }


}