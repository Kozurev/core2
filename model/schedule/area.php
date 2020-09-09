<?php
/**
 * Класс реализующий методы для рапботы с филиалами
 *
 * @author Bad Wolf
 * @date 01.05.2018 11:22
 * @version 20190219
 */
class Schedule_Area extends Schedule_Area_Model
{
    /**
     * @var array|null
     */
    public ?array $rooms = null;

    /**
     * @return array
     */
    public function getRooms() : array
    {
        if (is_null($this->rooms)) {
            $rooms = Schedule_Room::query()
                ->where('area_id', '=', $this->getId())
                ->findAll();

            $this->rooms = [];
            /** @var Schedule_Room $room */
            foreach ($rooms as $room) {
                $this->rooms[$room->classId()] = $room;
            }
        }
        return $this->rooms;
    }

    /**
     * Генератор свойства path (url путь к филиалу) на основе названия и идентификатора
     *
     * @return $this
     * @throws Exception
     */
    public function renderPath()
    {
        if ($this->subordinated() > 0) {
            $subordinated = $this->subordinated();
        } else {
            $authUser = User_Auth::current();
            if (is_null($authUser)) {
                throw new Exception('Невозможно сформировать путь филиала так как не удается получить значение subordinated');
            }
            $subordinated = $authUser->getDirector()->getId();
        }

        $this->path = translite($this->title()) . '-' . $subordinated;
        return $this;
    }

    /**
     * Поиск списка активных филиалов той же организации что и авторизованный пользователь
     *
     * @param bool $isSubordinate
     *      true:   поиск только тех филиалов, которые принадлежат той же организации что и авторизованный пользователь
     *      false:  поиск филиалов всех организаций
     * @param bool $isActive
     *      true:   поиск только активных филиалов
     *      false:  поиск филиалов вне зависимости от их активности
     * @return array
     */
    public function getList(bool $isSubordinate = true, bool $isActive = true) : array
    {
        $areas = Schedule_Area::query();

        if ($isSubordinate === true) {
            $user = User_Auth::current();
            if (is_null($user)) {
                return [];
            }
            $areas->where('subordinated', '=', $user->getDirector()->getId());
        }

        if ($isActive === true) {
            $areas->where('active', '=', 1);
        }

        return $areas
            ->orderBy('sorting')
            ->orderBy('title')
            ->findAll();
    }

    /**
     * Это реально ебаное волшебство. Я не помню зачем я добавлял кастомное свойство oldTitle
     * но без него филиал не сохраняется почему-то. Я хз что делать, просто оставлю так. Не трогать!
     *
     * @param string|null $title
     * @return $this|string
     */
    public function title(string $title = null)
    {
        if (is_null($title)) {
            return $this->title;
        } else {
            $this->oldTitle = $this->title;
            $this->title = $title;
            return $this;
        }
    }

    /**
     * @param int $classId
     * @return Schedule_Room|null
     */
    private function getClass(int $classId)
    {
        if (!$this->id) {
            return null;
        }

        return Schedule_Room::query()
            ->where('class_id', '=', $classId)
            ->where('area_id', '=', $this->id)
            ->find();
    }

    /**
     * @param int $classId
     * @param string $default
     * @return string
     */
    public function getClassName(int $classId, string $default) : string
    {
        return $this->getRooms()[$classId]->title() ?? $default;
    }

    /**
     * @param int $classId
     * @param string $name
     * @return Schedule_Room
     */
    public function setClassName(int $classId, string $name)
    {
        $ExistingRoom = $this->getClass($classId);

        !is_null($ExistingRoom)
            ?   $Room = $ExistingRoom
            :   $Room = Core::factory('Schedule_Room')
                    ->areaId($this->id)
                    ->classId($classId);

        $Room->title($name)->save();
        return $Room;
    }

    /**
     * @param null $obj
     * @return $this|null
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'before.ScheduleArea.save');
        if (isset($this->oldTitle)) {
            unset($this->oldTitle);
        }
        if (empty(parent::save())) {
            return null;
        }
        Core::notify([&$this], 'after.ScheduleArea.save');
        return $this;
    }

    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'beforeScheduleAreaDelete');
        parent::delete();
        Core::notify([&$this], 'afterScheduleAreaDelete');
    }

}