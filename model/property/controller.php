<?php
/**
 * Класс-контроллер для работы с доп. свойствами
 *
 * @author Bad Wolf
 * @date 22.02.2019 9:54
 * @version 20190222
 * @version 20190712
 * Class Property_Controller
 */
class Property_Controller
{

    //Возможные типы доп. свойств для параметра $type метода factory
    const TYPE_INT =    'Int';
    const TYPE_STRING = 'String';
    const TYPE_BOOL =   'Bool';
    const TYPE_TEXT =   'Text';
    const TYPE_LIST =   'List';

    //Список возможных типов доп. свойств
    private static $types = [
        self::TYPE_INT,
        self::TYPE_STRING,
        self::TYPE_BOOL,
        self::TYPE_TEXT,
        self::TYPE_LIST
    ];


    /**
     * @param int|null $id
     * @param string|null $type
     * @return Property|Property_Int|Property_String|Property_Bool|Property_Text|Property_List|null
     */
    public static function factory(int $id = null, string $type = null)
    {
        if (!is_null($type) && !in_array($type, self::$types)) {
            return null;
        }

        is_null($type)
            ?   $className = 'Property'
            :   $className = 'Property_' . $type;

        is_null($id) || $id === 0
            ?   $obj = Core::factory($className)
            :   $obj = Core::factory($className, intval($id));

        return $obj;
    }


    /**
     * Фабрика для объекта "Доп. свойство" по значению tag_name
     *
     * @param string $tagName
     * @return Property|null
     */
    public static function factoryByTag(string $tagName)
    {
        return Core::factory('Property')->getByTagName($tagName);
    }


    /**
     * Фабрика для элемента списка свойства
     *
     * @param int|null $id
     * @param bool $isSubordinate
     * @return Property_List_Values|null
     */
    public static function factoryListValue(int $id = null, bool $isSubordinate = true)
    {
        if (is_null($id) || $id === 0) {
            return Core::factory('Property_List_Values');
        }

        $ResValue = Core::factory('Property_List_Values')
            ->queryBuilder()
            ->where('id', '=', $id);

        if ($isSubordinate === true) {
            $AuthUser = User::current();
            if (is_null($AuthUser)) {
                return null;
            }
            $Director = $AuthUser->getDirector();
            if (is_null($Director)) {
                return null;
            }
            $ResValue->where('subordinated', '=', $Director->getId());
        }

        return $ResValue->find();
    }
}