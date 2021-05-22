<?php

/**
 * Class User_Balance
 */
class User_Balance extends User_Balance_Model
{
    const LESSONS_INDIVIDUAL = 1;
    const LESSONS_GROUP = 2;

    /**
     * @param float $number
     * @param int $type
     * @param bool $withSave
     */
    public function setCountLessons(float $number, int $type, bool $withSave = true): void
    {
        if ($type === self::LESSONS_INDIVIDUAL) {
            $this->setIndividualLessonsCount($number);
        } elseif ($type === self::LESSONS_GROUP) {
            $this->setGroupLessonsCount($number);
        }
        if ($withSave) {
            $this->save();
        }
    }

    /**
     * @param float $count
     * @param int $type
     * @param bool $withSave
     */
    public function addLessons(float $count, int $type, bool $withSave = true)
    {
        if ($type === self::LESSONS_INDIVIDUAL) {
            $this->addIndividualLessons($count, $withSave);
        } elseif ($type === self::LESSONS_GROUP) {
            $this->addGroupLessons($count, $withSave);
        }
    }

    /**
     * @param float $count
     * @param bool $withSave
     */
    public function addIndividualLessons(float $count, bool $withSave): void
    {
        $this->individual_lessons_count += $count;
        if ($withSave) {
            $this->save();
        }
    }

    /**
     * @param float $count
     * @param bool $withSave
     */
    public function addGroupLessons(float $count, bool $withSave): void
    {
        $this->group_lessons_count += $count;
        if ($withSave) {
            $this->save();
        }
    }

    /**
     * @param float $number
     * @param int $type
     * @param bool $withSave
     */
    public function deductLessons(float $number, int $type, bool $withSave = true): void
    {
        if ($type === self::LESSONS_INDIVIDUAL) {
            $this->deductIndividualLessons($number, $withSave);
        } elseif ($type === self::LESSONS_GROUP) {
            $this->deductGroupLessons($number, $withSave);
        }
    }

    /**
     * @param float $number
     * @param bool $withSave
     */
    public function deductIndividualLessons(float $number, bool $withSave = true): void
    {
        $this->individual_lessons_count -= $number;
        if ($withSave) {
            $this->save();
        }
    }

    /**
     * @param float $number
     * @param bool $withSave
     */
    public function deductGroupLessons(float $number, bool $withSave = true): void
    {
        $this->group_lessons_count -= $number;
        if ($withSave) {
            $this->save();
        }
    }

    /**
     * @param int $type
     * @return float
     */
    public function getCountLessons(int $type): float
    {
        if ($type === self::LESSONS_INDIVIDUAL) {
            return $this->individual_lessons_count;
        } elseif ($type === self::LESSONS_GROUP) {
            return $this->group_lessons_count;
        } else {
            return 0.0;
        }
    }

    /**
     * @param float $price
     * @param int $type
     * @param bool $withSave
     */
    public function setAvgPrice(float $price, int $type, bool $withSave = true): void
    {
        if ($type === self::LESSONS_INDIVIDUAL) {
            $this->individual_lessons_average_price = $price;
        } elseif ($type === self::LESSONS_GROUP) {
            $this->group_lessons_average_price = $price;
        }
        if ($withSave) {
            $this->save();
        }
    }

    /**
     * @param int $type
     * @return float
     */
    public function getAvgPrice(int $type): float
    {
        if ($type === self::LESSONS_INDIVIDUAL) {
            return $this->individual_lessons_average_price;
        } elseif ($type === self::LESSONS_GROUP) {
            return $this->group_lessons_average_price;
        } else {
            return 0.0;
        }
    }

    /**
     * @param float $price
     * @param int $type
     * @param bool $withSave
     */
    public function addAvgPrice(float $price, int $type, bool $withSave = true): void
    {
        if ($type === self::LESSONS_INDIVIDUAL) {
            $this->addAvgIndividualPrice($price, $withSave);
        } elseif ($type === self::LESSONS_GROUP) {
            $this->addAvgGroupPrice($price, $withSave);
        }
    }

    /**
     * @param float $price
     * @param int $type
     * @param bool $withSave
     */
    public function deductAvgPrice(float $price, int $type, bool $withSave = true): void
    {
        if ($type === self::LESSONS_INDIVIDUAL) {
            $this->deductAvgIndividualPrice($price, $withSave);
        } elseif ($type === self::LESSONS_GROUP) {
            $this->deductAvgGroupPrice($price, $withSave);
        }
    }

    /**
     * @param float $price
     * @param bool $withSave
     */
    public function addAvgIndividualPrice(float $price, bool $withSave = true): void
    {
        $this->individual_lessons_average_price += $price;
        if ($withSave) {
            $this->save();
        }
    }

    /**
     * @param float $price
     * @param bool $withSave
     */
    public function deductAvgIndividualPrice(float $price, bool $withSave = true): void
    {
        $this->individual_lessons_average_price -= $price;
        if ($withSave) {
            $this->save();
        }
    }

    /**
     * @param float $price
     * @param bool $withSave
     */
    public function addAvgGroupPrice(float $price, bool $withSave = true): void
    {
        $this->group_lessons_average_price += $price;
        if ($withSave) {
            $this->save();
        }
    }

    /**
     * @param float $price
     * @param bool $withSave
     */
    public function deductAvgGroupPrice(float $price, bool $withSave = true): void
    {
        $this->group_lessons_average_price -= $price;
        if ($withSave) {
            $this->save();
        }
    }
}