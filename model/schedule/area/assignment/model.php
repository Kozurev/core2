<?php
/**
 * Класс-модель связи объекта с филиалом
 *
 * @author BadWolf
 * @date 18.01.2019 16:28
 * @version 20190331
 */
class Schedule_Area_Assignment_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Название класса объекта
     *
     * @var string
     */
    protected $model_name;


    /**
     * уникальный идентификатор объекта
     *
     * @var int
     */
    protected $model_id;


    /**
     * id филиала, с которымсвязан объект
     *
     * @var int
     */
    protected $area_id;


    /**
     * @param string|null $modelName
     * @return $this|string
     */
    public function modelName(string $modelName = null)
    {
        if (is_null($modelName)) {
            return $this->model_name;
        } else {
            $this->model_name = trim($modelName);
            return $this;
        }
    }


    /**
     * @param int|null $modelId
     * @return $this|int
     */
    public function modelId(int $modelId = null)
    {
        if (is_null($modelId)) {
            return intval($this->model_id);
        } else {
            $this->model_id = $modelId;
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
     * @return array
     */
    public function schema() : array
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'model_name' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'model_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ],
            'area_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ]
        ];
    }

}