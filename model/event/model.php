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
    const SCHEDULE_APPEND_PRIVATE =         31;

    const CLIENT_ARCHIVE =                  7;
    const CLIENT_UNARCHIVE =                8;
    const CLIENT_APPEND_COMMENT =           9;
    const CLIENT_ACTIVITY=                  30;

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
     * @deprecated
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


    public function setData($data)
    {
        $this->data = json_encode($data, JSON_UNESCAPED_UNICODE);
        return $this;
    }


    public function getData()
    {
        return json_decode($this->data);
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