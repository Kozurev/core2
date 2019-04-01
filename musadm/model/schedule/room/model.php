<?php
/**
 * Класс-модель для "Класса проведения занятия"
 *
 * @author BadWolf
 * @date 19.03.2019 14:36
 * @version 20190401
 * Class Schedule_Room_Model
 */
class Schedule_Room_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Название класса в филиале
     *
     * @var string
     */
    protected $title = '';


    /**
     * id филиала, которому принадлежит класс
     *
     * @var int
     */
    protected $area_id = 0;


    /**
     * Порядковый номер класса
     *
     * @var int
     */
    protected $class_id = 0;


    /**
     * @param string|null $title
     * @return $this|string
     */
    public function title(string $title = null)
    {
        if (is_null($title)) {
            return $this->title;
        } else {
            $this->title = $title;
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
     * @param int|null $classId
     * @return $this|int
     */
    public function classId(int $classId = null)
    {
        if (is_null($classId)) {
            return intval($this->class_id);
        } else {
            $this->class_id = $classId;
            return $this;
        }
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
            'title' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'area_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'class_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ]
        ];
    }

}