<?php
/**
 * Класс-модель для примечания к сертификату (надо удалить и объединить все комментарии в одну таблицу)
 *
 * @author BadWolf
 * @date 09.07.2018 10:52
 * @version 20190328
 * Class Certificate_Note
 */
class Certificate_Note extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Дата создания примечания формата: date('Y-m-d H:i:s')
     *
     * @var string
     */
    protected $date;


    /**
     * id сертификата, которому принадлежит примечание
     *
     * @var int
     */
    protected $certificate_id = 0;


    /**
     * id пользователя (автора) комментария
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
            return $this->author_id;
        } else {
            $this->author_id = $authorId;
            return $this;
        }
    }


    /**
     * @param int|null $certificateId
     * @return $this|int
     */
    public function certificateId(int $certificateId = null)
    {
        if (is_null($certificateId)) {
            return $this->certificate_id;
        } else {
            $this->certificate_id = $certificateId;
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


    //Параметры валидации при сохранении таблицы
    public function schema()
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'date' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'certificate_id' => [
                'required' => true,
                'type' => PARAM_INT,
            ],
            'author_id' => [
                'required' => true,
                'type' => PARAM_INT,
            ],
            'text' => [
                'required' => true,
                'type' => PARAM_STRING
            ]
        ];
    }


    /**
     * @return User|null
     */
    public function getAuthor()
    {
        return Core::factory('User', $this->author_id);
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'beforeCertificateNoteSave');

        if (is_null($this->date)) {
            $this->date = date('Y-m-d H:i:s');
        }
        if ($this->author_id == 0) {
            $AuthUser = User::current();
            if (!is_null($AuthUser)) {
                $this->author_id = $AuthUser->getId();
            }
        }

        parent::save();
        Core::notify([&$this], 'afterCertificateNoteSave');
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'beforeCertificateNoteDelete');
        parent::delete();
        Core::notify([&$this], 'afterCertificateNoteDelete');
    }
}