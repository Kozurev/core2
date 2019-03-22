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
     * Генератор свойства path (url путь к филиалу) на основе названия и идентификатора
     *
     * @return $this
     */
    public function renderPath()
    {
        if ($this->subordinated() > 0) {
            $subordinated = $this->subordinated();
        } else {
            $AuthUser = User::current();
            if (is_null($AuthUser)) {
                exit('Невозможно сформировать путь филиала так как не удается получить значение subordinated');
            }
            $subordinated = $AuthUser->getDirector()->getId();
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
        Core::factory( 'Schedule_Area_Controller' );
        $Areas = Schedule_Area_Controller::factory();

        if ($isSubordinate === true) {
            $User = User::current();

            if (is_null($User)) {
                return [];
            }

            $Areas->queryBuilder()
                ->where('subordinated', '=', $User->getDirector()->getId());
        }

        if ($isActive === true) {
            $Areas->queryBuilder()
                ->where('active', '=', 1);
        }

        return $Areas->queryBuilder()
            ->orderBy('sorting')
            ->orderBy('title')
            ->findAll();
    }


    /**
     * Переопределенный сеттер для названия филиала
     * Это реально ебаное волшебство. Я не помню зачем я добавлял кастомное свойство oldTitle
     * но без него филиал не сохраняется почему-то. Я хз что делать, просто оставлю так. Не трогать!
     *
     * @param null $val
     * @return Schedule_Area
     */
    public function title($val = null)
    {
        if (is_null($val)) {
            return $this->title;
        }

        if (strlen($val) > 255) {
            exit(Core::getMessage('TOO_LARGE_VALUE', ['title', 'Schedule_Area', 255]));
        }

        $this->oldTitle = $this->title;
        $this->title = strval($val);
        return $this;
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

        return Core::factory('Schedule_Room')
            ->queryBuilder()
            ->clearQuery()
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
        $Room = $this->getClass($classId);

        if (is_null($Room)) {
            return $default;
        } else {
            return $Room->title();
        }
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


    public function save($obj = null)
    {
        Core::notify([&$this], 'beforeScheduleAreaSave');
        if (isset($this->oldTitle)) {
            unset($this->oldTitle);
        }
        parent::save();
        Core::notify([&$this], 'afterScheduleAreaSave');
    }


    public function delete($obj = null)
    {
        Core::notify([&$this], 'beforeScheduleAreaDelete');
        parent::delete();
        Core::notify([&$this], 'afterScheduleAreaDelete');
    }

}