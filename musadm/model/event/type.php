<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 26.11.2018
 * Time: 16:07
 */

class Event_Type extends Core_Entity
{

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
    protected $name = "";


    /**
     * Название события
     *
     * @var string
     */
    protected $title = "";


    public function getId()
    {
        return intval( $this->id );
    }


    public function parentId( $val = null )
    {
        if( is_null( $val ) )   return intval( $this->parent_id );

        $this->parent_id = intval( $val );
        return $this;
    }


    public function name( $val = null )
    {
        if( is_null( $val ) )   return strval( $this->name );

        $this->name = strval( $val );
        return $this;
    }


    public function title( $val = null )
    {
        if( is_null( $val ) )   return strval( $this->title );

        $this->title = strval( $val );
        return $this;
    }


    public function save( $obj = null )
    {
        Core::notify( array( &$this ), "beforeEventTypeSave" );

        if( $this->name != "" )
        {
            $Type = Core::factory( "Event_Type" )
                ->where( "name", "=", $this->name );

            if( $this->id != null )
                $Type->where( "id", "<>", $this->id );

            $Type = $Type->find();

            if( $Type !== false )
                die( "Тип события с названием <b>'". $this->name ."'</b> уже существует" );
        }

        parent::save();

        Core::notify( array( &$this ), "afterEventTypeSave" );
    }


    /**
     * Добавление дочернего типа события
     * Возвращает созданный дочерний тип
     *
     * @param $child_name
     * @return Event_Type
     */
    public function appendChild( $child_title, $child_name = "" )
    {
        if( $this->id == null )
            die( "Невозможно добавить дочерний элемент '". strval( $child_name ) ."' к несохраненному (не имеющему id) типу события" );

        $ChildType = Core::factory( "Event_Type" )
            ->parentId( $this->id )
            ->name( $child_name )
            ->title( strval( $child_title ) );
        //Orm::debug(true);
        $ChildType->save();
        //Orm::debug(false);
        return $ChildType;
    }


    /**
     * Получение типа события по уникальному названию
     *
     * @param $name
     * @return mixed
     */
    public function getByName( $name )
    {
        $Type = Core::factory( "Event_Type" )
            ->where( "name", "=", strval( $name ) )
            ->find();

        if( $Type === false )
            die( "Типа события с именем <b>'". strval( $name ) ."'</b> не найдено" );
        else
            return $Type;
    }


}