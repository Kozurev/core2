<?php
/**
 * Период отсутствия пользователя
 *
 * @author BadWolf
 * @date 07.05.2018 11:28
 * @version 20191060
 */
class Schedule_Absent extends Schedule_Absent_Model
{

    /**
     * @return null|User|Schedule_Group
     */
    public function getObject()
    {
        Core::requireClass('Schedule_Lesson');
        Core::requireClass('User_Controller');

        if (empty($this->objectId())) {
            return User_Controller::factory();
        }

        if ($this->typeId() == Schedule_Lesson::TYPE_GROUP) {
            return Core::factory('Schedule_Group', $this->objectId());
        } else {
            return User_Controller::factory($this->objectId());
        }
    }


    /**
     * @return null|User|Schedule_Group
     */
    public function getClient()
    {
        return $this->getObject();
    }


    /**
     * @param null $obj
     * @return $this|null
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'before.ScheduleAbsent.save');

        //Ограничение по времени
        if (User_Auth::current()->groupId() == ROLE_CLIENT) {
            $tomorrow = date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day'));
            $endDayTime = Property_Controller::factoryByTag('schedule_edit_time_end')->getValues(User_Auth::current()->getDirector())[0]->value();
            if ($this->dateFrom() < $tomorrow || ($this->dateFrom() == $tomorrow && date('H:i:s') >= $endDayTime)) {
                $errorMsg = 'Дата начала периода отсутствия в данном случае не может быть ранее чем ' . date('d.m.Y', strtotime($tomorrow . ' +1 day'));
                Log::instance()->error(
                    'absent_period',
                    $errorMsg . '; Клиент: ' . User_Auth::current()->surname() . ' ' . User_Auth::current()->name()
                    . '; дата начала: ' . $this->dateFrom() . ' дата завершения: ' . $this->dateTo());

                $this->_setValidateErrorStr($errorMsg);
            }
        }

        if (empty(parent::save())) {
            return null;
        }
        Core::notify([&$this], 'after.ScheduleAbsent.save');
        return $this;
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'before.ScheduleAbsent.delete');
        parent::delete();
        Core::notify([&$this], 'after.ScheduleAbsent.delete');
    }
}