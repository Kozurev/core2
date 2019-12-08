<?php

/**
 * Класс реализующий методы для работы с отвалом пользователей
 *
 * @author BadWolf
 * @version 20190328
 * Class User_Model
 */
class User_Activity extends User_Activity_Model
{
    public function save()
    {
        if (empty($this->dump_date_start)) {
            $this->dump_date_start = date('Y-m-d');
        }

        if (empty(parent::save())) {
            return null;
        }
        Core::notify([&$this], 'after.UserActivity.save');
        return $this;
    }
}