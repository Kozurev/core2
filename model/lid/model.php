<?php
/**
 * Класс-модель лида
 *
 * @author Kozurev Egor
 * @date 24.04.2018 22:11
 * @version 20190328
 * Class Lid_Model
 */
class Lid_Model extends Core_Entity
{
    /**
     * @var int|null
     */
    public $id;

    /**
     * Имя
     *
     * @var string
     */
    public string $name = '';

    /**
     * Фамилия
     *
     * @var string
     */
    public string $surname = '';

    /**
     * Номер телефона
     *
     * @var string
     */
    public string $number = '';

    /**
     * Ссылка Вконтакте
     *
     * @var string
     */
    public string $vk = '';

    /**
     * id или текстовое значение источника лида
     *
     * @var string
     */
    public string $source = '';

    /**
     * Дата контроля лида
     *
     * @var string
     */
    public string $control_date = '';

    /**
     * id организации которой принадлежит лид
     *
     * @var int
     */
    public int $subordinated = 0;

    /**
     * id филиала которому принадлежит лид
     *
     * @var int
     */
    public int $area_id = 0;

    /**
     * id статуса лида
     *
     * @var int
     */
    public int $status_id = 0;

    /**
     * Переключатель для смс оповещений
     *
     * @var int
     */
    public int $sms_notification = 1;

    /**
     * Дата создания лида
     *
     * @var string|null
     */
    public ?string $date_create = null;

    /**
     * Указатель на приоритет лида
     * Список приоритетов, пока что, задан статически:
     *  1 - низкий
     *  2 - средний
     *  3 - высокий
     *
     * @var int
     */
    public int $priority_id = 1;

    /**
     * @param string|null $name
     * @return $this|string
     */
    public function name(string $name = null)
    {
        if (is_null($name)) {
            return $this->name;
        } else {
            $this->name = trim($name);
            return $this;
        }
    }

    /**
     * @param string|null $surname
     * @return $this|string
     */
    public function surname(string $surname = null)
    {
        if (is_null($surname)) {
            return $this->surname;
        } else {
            $this->surname = trim($surname);
            return $this;
        }
    }

    /**
     * @param string|null $number
     * @return $this|string
     */
    public function number(string $number = null)
    {
        if (is_null($number)) {
            return $this->number;
        } else {
            $this->number = trim($number);
            return $this;
        }
    }

    /**
     * @param string|null $vk
     * @return $this|string
     */
    public function vk(string $vk = null)
    {
        if (is_null($vk)) {
            return $this->vk;
        } else {
            $this->vk = trim($vk);
            return $this;
        }
    }

    /**
     * @param string|null $source
     * @return $this|int|string
     */
    public function source(string $source = null)
    {
        if (is_null($source)) {
            if (is_numeric($this->source)) {
                return intval($this->source);
            } else {
                return $this->source;
            }
        } else {
            $this->source = trim($source);
            return $this;
        }
    }

    /**
     * @param string|null $controlDate
     * @return $this|string
     */
    public function controlDate(string $controlDate = null )
    {
        if (is_null($controlDate)) {
            return $this->control_date;
        } else {
            $this->control_date = $controlDate;
            return $this;
        }
    }

    /**
     * @param int|null $subordinated
     * @return $this|int
     */
    public function subordinated(int $subordinated = null)
    {
        if (is_null($subordinated)) {
            return intval($this->subordinated);
        } else {
            $this->subordinated = $subordinated;
            return $this;
        }
    }

    /**
     * @param int|null $areaId
     * @return $this|int
     */
    public function areaId(int $areaId = null)
    {
        if (is_null($areaId)) {
            return intval($this->area_id);
        } else {
            $this->area_id = $areaId;
            return $this;
        }
    }

    /**
     * @param int|null $statusId
     * @return $this|int
     */
    public function statusId(int $statusId = null )
    {
        if (is_null($statusId)) {
            return intval($this->status_id);
        } else {
            $this->status_id = $statusId;
            return $this;
        }
    }

    /**
     * @param int|null $priorityId
     * @return $this|int
     */
    public function priorityId(int $priorityId = null)
    {
        if (is_null($priorityId)) {
            return intval($this->priority_id);
        } else {
            $this->priority_id = $priorityId;
            return $this;
        }
    }

    /**
     * @param int|null $notification
     * @return $this|int
     */
    public function smsNotification(int $notification = null)
    {
        if (is_null($notification)) {
            return intval($this->sms_notification);
        } else {
            $this->sms_notification = $notification;
            return $this;
        }
    }

    /**
     * @param string|null $dateCreate
     * @return $this|string|null
     */
    public function dateCreate(string $dateCreate = null)
    {
        if (is_null($dateCreate)) {
            return $this->date_create;
        } else {
            $this->date_create = $dateCreate;
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
            'name' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'surname' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'number' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'vk' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'source' => [
                'required' => false,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'subordinated' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'area_id' => [
                'required' => false,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'status_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'priority_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'sms_notification' => [
                'required' => false,
                'type' => PARAM_INT,
                'minval' => 0
            ]
        ];
    }

}