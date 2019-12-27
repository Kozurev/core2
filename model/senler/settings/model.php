<?php

class Senler_Settings_Model extends Core_Entity
{
    /**
     * id статуса. для которого произвдится настройка
     *
     * @var int
     */
    protected $lid_status_id = 0;

    /**
     * id другого статуса
     *
     * @var int
     */
    protected $other_status = 0;

    /**
     * Идентиикатор группы из таблицы Vk_Group
     *
     * @var int
     */
    protected $vk_group_id;

    /**
     * @var int
     */
    protected $senler_subscription_id;

    /**
     * Идентификатор направления подготовки
     *
     * @var int
     */
    protected $training_direction_id = 0;

    /**
     * id филлиала, для которого применяется настройка
     *
     * @var int
     */
    protected $area_id;


    /**
     * @param int|null $lidStatusId
     * @return $this|int
     */
    public function lidStatusId(int $lidStatusId = null)
    {
        if (is_null($lidStatusId)) {
            return intval($this->lid_status_id);
        } else {
            $this->lid_status_id = $lidStatusId;
            return $this;
        }
    }

    /**
     * @param int|null $otherStatus
     * @return $this|int
     */
    public function otherStatus(int $otherStatus = null)
    {
        if (is_null($otherStatus)) {
            return intval($this->other_status);
        } else {
            $this->other_status = $otherStatus;
            return $this;
        }
    }

    /**
     * @param int|null $vkGroupId
     * @return $this|string
     */
    public function vkGroupId(int $vkGroupId = null) {
        if (is_null($vkGroupId)) {
            return intval($this->vk_group_id);
        } else {
            $this->vk_group_id = $vkGroupId;
            return $this;
        }
    }

    /**
     * @param int|null $senlerSubscriptionId
     * @return $this|int
     */
    public function senlerSubscriptionId(int $senlerSubscriptionId = null)
    {
        if (is_null($senlerSubscriptionId)) {
            return intval($this->senler_subscription_id);
        } else {
            $this->senler_subscription_id = $senlerSubscriptionId;
            return $this;
        }
    }

    /**
     * @param int|null $trainingDetectionId
     * @return $this|int
     */
    public function trainingDetectionId(int $trainingDetectionId = null)
    {
        if (is_null($trainingDetectionId)) {
            return intval($this->training_direction_id);
        } else {
            $this->training_direction_id = $trainingDetectionId;
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

}