<?php
/**
 * Класс-модель типа события
 *
 * @author BadWolf
 * @date 26.11.2018 16:07
 * @version 20190328
 * Class Event_Type
 */
class Event_Type extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * id родительского типа
     * К примеру тип события может называться "Клиент", а дочерние типы - это всё, что с ним связано:
     *  - добавление в архив
     *  - восстановление из архива
     *  - добавление в рсаписание
     *  - и т.д.
     *
     * @var int
     */
    protected $parent_id = 0;


    /**
     * Название типа события для метода getByName для лучшей читаемости кода
     *
     * @var string
     */
    protected $name;


    /**
     * Название события
     *
     * @var string
     */
    protected $title;


    /**
     * @param int|null $parentId
     * @return $this|int
     */
    public function parentId(int $parentId = null)
    {
        if (is_null($parentId)) {
            return intval($this->parent_id);
        } else {
            $this->parent_id = $parentId;
            return $this;
        }
    }


    /**
     * @param string|null $name
     * @return $this|string
     */
    public function name(string $name = null)
    {
        if (is_null($name)) {
            return $this->name;
        } else {
            $this->name = $name;
            return $this;
        }
    }


    /**
     * @param string|null $title
     * @return $this|string
     */
    public function title(string $title = null)
    {
        if (is_null($title)) {
            return $this->title;
        } else {
            $this->title = $title;
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
            'name' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'parent_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ]
        ];
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'beforeEventTypeSave');

        if ($this->name != '') {
            $Type = Core::factory('Event_Type')
                ->queryBuilder()
                ->where('name', '=', $this->name);

            if (!empty($this->id)) {
                $Type->where('id', '<>', $this->id);
            }

            $Type = $Type->find();

            if(!is_null($Type)) {
                die("Тип события с названием <b>'". $this->name ."'</b> уже существует" );
            }
        }

        parent::save();

        Core::notify([&$this], 'afterEventTypeSave');
    }


    /**
     * Добавление дочернего типа события
     * Возвращает созданный дочерний тип
     *
     * @param string $child_title
     * @param string $child_name
     * @return Event_Type
     */
    public function appendChild(string $child_title, string $child_name = '')
    {
        if(empty($this->id)) {
            die("Невозможно добавить дочерний элемент '". strval($child_name) . "' к несохраненному (не имеющему id) типу события");
        }

        $ChildType = Core::factory('Event_Type')
            ->parentId($this->id)
            ->name($child_name)
            ->title(strval($child_title));
        $ChildType->save();
        return $ChildType;
    }


    /**
     * Получение типа события по уникальному названию
     *
     * @param string $name
     * @return Event_Type|null
     */
    public function getByName(string $name)
    {
        return Core::factory('Event_Type')
            ->queryBuilder()
            ->where('name', '=', $name)
            ->find();
    }


}