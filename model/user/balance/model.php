<?php

class User_Balance_Model extends Core_Entity
{
    /**
     * @var int|null
     */
    public ?int $user_id = null;

    /**
     * @var float|null
     */
    public ?float $amount = null;

    /**
     * @var float|null
     */
    public ?float $individual_lessons_count = null;

    /**
     * @var float|null
     */
    public ?float $group_lessons_count = null;

    /**
     * @var float|null
     */
    public ?float $individual_lessons_average_price = null;

    /**
     * @var float|null
     */
    public ?float $group_lessons_average_price = null;

    /**
     * @return array|string|string[]
     */
    public function getPrimaryKeyName()
    {
        return 'user_id';
    }

    /**
     * @return array|int
     */
    public function getPrimaryKeyValue()
    {
        return $this->user_id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return intval($this->user_id);
    }

    /**
     * @param int $userId
     * @return static|null
     */
    public static function find(int $userId): ?self
    {
        return self::query()->where('user_id', '=', $userId)->find();
    }

    /**
     * @return array
     */
    public function getObjectProperties() : array
    {
        return collect(parent::getObjectProperties())->except(['id'])->toArray();
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return floatval($this->amount);
    }

    /**
     * @return float
     */
    public function getIndividualLessonsCount(): float
    {
        return floatval($this->individual_lessons_count);
    }

    /**
     * @return float
     */
    public function getGroupLessonsCount(): float
    {
        return floatval($this->group_lessons_count);
    }

    /**
     * @return float
     */
    public function getIndividualLessonsAvg(): float
    {
        return floatval($this->individual_lessons_average_price);
    }

    /**
     * @return float
     */
    public function getGroupLessonsAvg(): float
    {
        return floatval($this->group_lessons_average_price);
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->user_id = $userId;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param float $count
     */
    public function setIndividualLessonsCount(float $count): void
    {
        $this->individual_lessons_count = $count;
    }

    /**
     * @param float $count
     */
    public function setGroupLessonsCount(float $count): void
    {
        $this->group_lessons_count = $count;
    }

    /**
     * @param float $avg
     */
    public function setIndividualLessonsAvg(float $avg): void
    {
        $this->individual_lessons_average_price = $avg;
    }

    /**
     * @param float $avg
     */
    public function setGroupLessonsAvg(float $avg): void
    {
        $this->group_lessons_average_price = $avg;
    }

    /**
     * @return array[]
     */
    public function schema(): array
    {
        return [
            'user_id' => [
                'required' => true,
                'type' => PARAM_INT
            ],
            'amount' => [
                'required' => true,
                'type' => PARAM_FLOAT
            ],
            'individual_lessons_count' => [
                'required' => true,
                'type' => PARAM_FLOAT
            ],
            'group_lessons_count' => [
                'required' => true,
                'type' => PARAM_FLOAT
            ],
            'individual_lessons_average_price' => [
                'required' => true,
                'type' => PARAM_FLOAT
            ],
            'group_lessons_average_price' => [
                'required' => true,
                'type' => PARAM_FLOAT
            ],
        ];
    }
}