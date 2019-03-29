<?php
/**
 * Класс реализующий методы для работы с лидами
 *
 * @author BadWolf
 * @date 24.04.2018 22:10
 * @version 20190328
 * Class Lid
 */
class Lid extends Lid_Model
{
    /**
     * @param string $date
     */
    public function changeDate(string $date)
    {
        $ObserverArgs = [
            'Lid' => &$this,
            'new_date' => $date,
            'old_date' => $this->controlDate()
        ];

        Core::notify($ObserverArgs, 'beforeLidChangeDate');
        $this->controlDate($date)->save();
        Core::notify($ObserverArgs, 'afterLidChangeDate');
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function save($obj = null)
    {
        if (empty($this->control_date)) {
            $this->control_date = date('Y-m-d');
        }

        Core::notify([&$this], 'beforeLidSave');
        parent::save();
        Core::notify([&$this], 'afterLidSave');
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'beforeLidDelete');

        if (!is_null($this->id)) {
            $Comments = Core::factory('Lid_Comment')
                ->queryBuilder()
                ->where('lid_id', '=', $this->id)
                ->findAll();

            foreach ($Comments as $Comment) {
                $Comment->delete();
            }

            Core::factory('Property')->clearForObject($this);
        }

        parent::delete();
        
        Core::notify([&$this], 'afterLidDelete');
    }


    /**
     * @return array
     */
    public function getComments()
    {
        if (empty($this->id)) {
            return [];
        }

        return Core::factory('Lid_Comment')
            ->queryBuilder()
            ->where('lid_id', '=', $this->id)
            ->orderBy('datetime', 'DESC')
            ->findAll();
    }


    /**
     * Добавление комментария к лиду
     *
     * @param string $text
     * @param bool $triggerObserver
     * @return $this
     */
    public function addComment(string $text, $triggerObserver = true)
    {
        if (empty($this->id)) {
            exit('Не указан id лида при сохранении комментария');
        }

        $User = User::current();
        is_null($User)
            ?   $authorId = 0
            :   $authorId = $User->getId();

        $Comment = Core::factory('Lid_Comment')
            ->datetime(date('Y-m-d H:i:s'))
            ->authorId($authorId)
            ->lidId($this->id)
            ->text($text);

        if ($triggerObserver === true) {
            Core::notify([&$Comment], 'beforeLidAddComment');
        }

        $Comment->save();

        if ($triggerObserver === true) {
            Core::notify( [&$Comment], 'afterLidAddComment' );
        }

        return $this;
    }


    /**
     * Поиск списка доступных статусов лида
     *
     * @return array Property_List_Values
     */
    public function getStatusList()
    {
        $User = User::current();
        !is_null($User)
            ?   $subordinated = $User->getDirector()->getId()
            :   $subordinated = 0;

        return Core::factory('Lid_Status')
            ->queryBuilder()
            ->where('subordinated', '=', $subordinated)
            ->orderBy('sorting', 'DESC')
            ->findAll();
    }


    /**
     * @param int $statusId
     * @return $this
     */
    public function changeStatus(int $statusId)
    {
        if ($this->subordinated() == 0) {
            return $this;
        }

        $Status = Core::factory('Lid_Status')
            ->queryBuilder()
            ->where('id', '=', $statusId)
            ->where('subordinated', '=', $this->subordinated())
            ->find();

        if (is_null($Status)) {
            Core_Page_Show::instance()->error(404);
        }

        $observerArgs = [
            'Lid' => &$this,
            'old_status' => $this->getStatus(),
            'new_status' => $Status
        ];

        Core::notify($observerArgs, 'beforeChangeLidStatus');
        $this->statusId($statusId)->save();
        $observerArgs['Lid'] = &$this;
        Core::notify($observerArgs, 'afterChangeLidStatus');

        return $this;
    }


    /**
     * Метод поиска объекта текущего статуса
     *
     * @return Property_List_Values
     */
    public function getStatus()
    {
        return Core::factory('Lid_Status')
            ->queryBuilder()
            ->where('id', '=', $this->statusId())
            ->where('subordinated', '=', $this->subordinated())
            ->find();
    }

}