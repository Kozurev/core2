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

}