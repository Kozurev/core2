<?php
/**
 * Модель комментария
 *
 * @author BadWolf
 * @date 31.01.2019 10:26
 * @version 20190802
 * Class Comment_Model
 */
class Comment_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * Дата и время создания в формате DATETIME ('Y-m-d H:i:s')
     *
     * @var string
     */
    protected $datetime;


    /**
     * id пользователя-автора комментария
     *
     * @var int
     */
    protected $author_id;


    /**
     * Фамилия и имя автора на момент сохранения комментария
     *
     * @var string
     */
    protected $author_fullname;


    /**
     * Текст комментария
     *
     * @var string
     */
    protected $text;



    /**
     * @param string|null $datetime
     * @return $this|string
     */
    public function datetime(string $datetime = null)
    {
        if (is_null($datetime)) {
            return strval($this->datetime);
        } else {
            $this->datetime = strval($datetime);
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
     * @param string|null $authorFullname
     * @return $this|string
     */
    public function authorFullname(string $authorFullname = null)
    {
        if (is_null($authorFullname)) {
            return strval($this->author_fullname);
        } else {
            $this->author_fullname = $authorFullname;
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
            return strval($this->text);
        } else {
            $this->text = $text;
            return $this;
        }
    }

}