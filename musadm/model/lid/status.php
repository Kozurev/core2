<?php
/**
 * Класс-модель статуса лида
 *
 * @author Bad Wolf
 * @date 30.01.2019 17:37
 * @version 20190328
 * Class Lid_Status
 */
class Lid_Status extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Название статуса
     *
     * @var string
     */
    protected $title;


    /**
     * Название класса для карточек лидов
     *
     * @var string
     */
    protected $item_class = '';


    /**
     * Порядок сортировки
     *
     * @var int
     */
    protected $sorting = 0;


    /**
     * id организации (директора) которой рпинадлежит статус
     *
     * @var int
     */
    protected $subordinated = 0;


    /**
     * @param string|null $title
     * @return $this|string
     */
    public function title(string $title = null)
    {
        if (is_null( $title)) {
            return $this->title;
        } else {
            $this->title = $title;
            return $this;
        }
    }


    /**
     * @param string|null $itemClass
     * @return $this|string
     */
    public function itemClass(string $itemClass = null)
    {
        if (is_null($itemClass)) {
            return $this->item_class;
        } else {
            $this->item_class = $itemClass;
            return $this;
        }
    }


    /**
     * @param int|null $sorting
     * @return $this|int
     */
    public function sorting(int $sorting = null)
    {
        if (is_null($sorting)) {
            return intval($this->sorting);
        } else {
            $this->sorting = $sorting;
            return $this;
        }
    }


    /**
     * @param int|null $subordinated
     * @return $this|int
     */
    public function subordinated(int $subordinated = null)
    {
        if (is_null($subordinated)) {
            return intval($this->subordinated);
        } else {
            $this->subordinated = $subordinated;
            return $this;
        }
    }


    //Параметры валидации при сохранении таблицы
    public function schema()
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'title' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'sorting' => [
                'required' => true,
                'type' => PARAM_INT
            ],
            'item_class' => [
                'required' => false,
                'type' => PARAM_INT,
                'maxlength' => 50
            ],
            'subordinated' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ]
        ];
    }


    /**
     * Поиск возможных статусов лида в пределах одной организации
     *
     * @param bool $isSubordinate
     * @return array
     */
    public function getList(bool $isSubordinate = true)
    {
        $Statuses = Core::factory('Lid_Status');

        if ($isSubordinate === true) {
            $User = User::current();
            if (is_null($User)) {
                return [];
            }

            $subordinated = $User->getDirector()->getId();
            $Statuses->queryBuilder()
                ->where('subordinated', '=', $subordinated);
        }

        return $Statuses->queryBuilder()
            ->orderBy('sorting')
            ->orderBy('id')
            ->findAll();
    }


    /**
     * Список возможных цветов для карточек
     *
     * @return array
     */
    public static function getColors()
    {
        $colors = [];

        $default = new stdClass();
        $default->name = 'Стандартный';
        $default->class = 'item-default';

        $orange = new stdClass();
        $orange->name = 'Оранжевый';
        $orange->class = 'item-orange';

        $blue = new stdClass();
        $blue->name = 'Синий';
        $blue->class = 'item-blue';

        $green = new stdClass();
        $green->name = 'Зеленый';
        $green->class = 'item-green';

        $red = new stdClass();
        $red->name = 'Красный';
        $red->class = 'item-red';

        $purple = new stdClass();
        $purple->name = 'Фиолетовый';
        $purple->class = 'item-purple';

        $pink = new stdClass();
        $pink->name = 'Розовый';
        $pink->class = 'item-pink';

        $colors[] = $default;
        $colors[] = $orange;
        $colors[] = $blue;
        $colors[] = $green;
        $colors[] = $red;
        $colors[] = $purple;
        $colors[] = $pink;

        return $colors;
    }


    /**
     * Поиск названия цвета по названию класса
     *
     * @param string $className - название класса элемента
     * @return string
     */
    public static function getColor(string $className)
    {
        $colors = Lid_Status::getColors();
        $colorName = '';

        foreach ($colors as $color) {
            if ($color->class == $className) {
                $colorName = $color->name;
                break;
            }
        }

        return $colorName;
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'beforeLidStatusSave');
        parent::save();
        Core::notify([&$this], 'afterLidStatusSave');
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'beforeLidStatusDelete');
        parent::delete();
        Core::notify([&$this], 'afterLidStatusDelete');
    }

}