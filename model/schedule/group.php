<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 24.04.2018
 * Time: 19:59
 */

class Schedule_Group extends Schedule_Group_Model
{

    /**
     * Получение списка клиентов группы
     *
     * @return array
     */
    public function getClientList()
    {
        if($this->id == null)   return array();

        $assignments = Core::factory("Schedule_Group_Assignment")
            ->where("group_id", "=", $this->id)
            ->findAll();

        $output = array();

        foreach ($assignments as $assignment)
        {
            $aoGroupUsers = Core::factory("User")
                ->where("id", "=", $assignment->userId())
                ->findAll();

            $output = array_merge($output, $aoGroupUsers);
        }

        return $output;
    }


    /**
     * Очистка списка клиентов группы
     */
    public function clearClientList()
    {
        if($this->id == null)   return;

        $assignments = Core::factory("Schedule_Group_Assignment")
            ->where("group_id", "=", $this->id)
            ->findAll();

        foreach ($assignments as $assignment)   $assignment->delete();
    }


    /**
     * Получение объекта учителя
     *
     * @return object
     */
    public function getTeacher()
    {
        return Core::factory("User", $this->teacher_id);
    }


    /**
     * Добавление пользователя в список клиентов
     *
     * @param $userid
     */
    public function appendClient($userid)
    {
        if($this->id == null)   return;

        Core::factory("Schedule_Group_Assignment")
            ->groupId($this->id)
            ->userId($userid)
            ->save();
    }


    public function delete($obj = null)
    {
        Core::notify(array(&$this), "beforeScheduleGroupDelete");
        parent::delete();
        Core::notify(array(&$this), "ScheduleGroupDelete");
    }


    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforeScheduleGroupSave");
        parent::save();
        Core::notify(array(&$this), "afterScheduleGroupSave");
    }

}