<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 19.11.2019
 * Time: 10:42
 */
class Schedule_Teacher extends Schedule_Teacher_Model
{
    /**
     * @return User|null
     */
    public function getTeacher()
    {
        if (empty($this->teacherId())) {
            return null;
        } else {
            return Core::factory('User', $this->teacherId());
        }
    }
}