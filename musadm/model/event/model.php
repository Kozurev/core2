<?php
/**
 * Класс-модель "события"
 *
 * @author BadWolf
 * @date 26.11.2018 15:26
 * @version 20190328
 * @version 20190412
 * Class Event_Model
 */
class Event_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Время события (TIMESTAMP)
     *
     * @var int
     */
    protected $time = 0;


    /**
     * id пользователя (автора)
     *
     * @var int
     */
    protected $author_id = 0;


    /**
     * ФИО автора, на случай если пользоватеь был удален
     *
     * @var string
     */
    protected $author_fio = '';


    /**
     * id пользователя (связь с каким-либо пользователем)
     *
     * @var int
     */
    protected $user_assignment_id = 0;


    /**
     * ФИО связанного пользователя на случай если он удален
     *
     * @var string
     */
    protected $user_assignment_fio = '';


    /**
     * id типа события из таблицы Event_Type
     *
     * @var int
     */
    protected $type_id = 0;


    /**
     * Дополнительная информация события
     * при сохранении значение данного свойства сериализуется
     *
     * @var string
     */
    protected $data = '';


    /**
     * @param int|null $time
     * @return $this|int
     */
    public function time(int $time = null)
    {
        if (is_null($time)) {
            return $this->time;
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
     * @param string|null $authorFio
     * @return $this|string
     */
    public function authorFio(string $authorFio = null)
    {
        if (is_null($authorFio) ) {
            return $this->author_fio;
        } else {
            $this->author_fio = $authorFio;
            return $this;
        }
    }


    /**
     * @param int|null $userAssignment
     * @return $this|int
     */
    public function userAssignmentId(int $userAssignment = null)
    {
        if (is_null($userAssignment)) {
            return intval($this->user_assignment_id);
        } else {
            $this->user_assignment_id = $userAssignment;
            return $this;
        }
    }


    /**
     * @param string|null $userAssignmentFio
     * @return $this|string
     */
    public function userAssignmentFio(string $userAssignmentFio = null)
    {
        if (is_null($userAssignmentFio)) {
            return $this->user_assignment_fio;
        } else {
            $this->user_assignment_fio = $userAssignmentFio;
            return $this;
        }
    }


    /**
     * @param int|null $typeId
     * @return $this|int
     */
    public function typeId(int $typeId = null)
    {
        if (is_null($typeId)) {
            return intval($this->type_id);
        } else {
            $this->type_id = $typeId;
            return $this;
        }
    }


    /**
     * @param stdClass|array|null $data
     * @return $this|mixed|string
     */
    public function data($data = null)
    {
        if (is_null($data)) {
            if (is_string($this->data)) {
                error_reporting(0);
                if (unserialize($this->data) === false) {
                    error_reporting(E_ALL);
                    return null;
                } else {
                    error_reporting(E_ALL);
                    return unserialize($this->data);
                }
            } else {
                return $this->data;
            }
        }

        $this->data = $data;
        return $this;
    }


    //Параметры валидации при сохранении таблицы
    public function schema()
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'time' => [
                'required' => true,
                'type' => PARAM_INT
            ],
            'author_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'author_fio' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'user_assignment_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'user_assignment_fio' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'type_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'data' => [
                'required' => true,
                'type' => PARAM_STRING
            ]
        ];
    }
}