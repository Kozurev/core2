<?php


/**
*	Модель структуры
*/
class Structure_Model extends Core_Entity
{
	protected $id;
	protected $title; 
	protected $parent_id; 
	protected $path; 
	protected $action; 
	protected $template_id; 
	protected $description;
	protected $children_name;
	//protected $properties_list;
	protected $active;
	protected $menu_id;
	protected $meta_title; 
	protected $meta_description; 
	protected $meta_keywords; 
	protected $sorting;


	public function __construct()
	{
		//$this->properties_list = unserialize($this->properties_list);
	}


	public function getId()
    {
		return intval( $this->id );
    }


    public function children_name( $val = null )
    {
        if ( is_null( $val ) )  return $this->children_name;

        if ( strlen( $val ) > 255 )
        {
            exit ( Core::getMessage( "TOO_LARGE_VALUE", ["children_name", "Structure", 255] ) );
        }

        $this->children_name = $val;

        return $this;
    }


	public function title( $val = null )
	{
		if ( is_null( $val ) )  return $this->title;

		if ( strlen( $val ) > 150 )
        {
            exit ( Core::getMessage( "TOO_LARGE_VALUE", ["title", "Structure", 150] ) );
        }
				
		$this->title = $val;

		return $this;
	}


	public function active( $val = null )
	{
		if ( is_null( $val ) )  return intval( $this->active );
		if ( $val == true )     $this->active = 1;
		elseif ( $val == false )$this->active = 0;

		return $this;
	}


	public function parentId( $val = null )
	{
		if ( is_null( $val ) ) 	return $this->parent_id;

		if ( $val < 0 )
        {
            exit ( Core::getMessage( "UNSIGNED_VALUE", ["parent_id", "Structure"] ) );
        }
		
		$this->parent_id = intval( $val );

		return $this;
	}


    public function menuId( $val = null )
    {
        if ( is_null( $val ) ) 	return intval( $this->menu_id );

        if ( $val < 0 )
        {
            exit ( Core::getMessage( "UNSIGNED_VALUE", ["menu_id", "Structure"] ) );
        }

        $this->menu_id = intval( $val );

        return $this;
    }


	public function template_id( $val = null )
	{
		if ( is_null( $val ) ) 	return intval( $this->template_id );

		if ( $val < 0 )
        {
            exit ( Core::getMessage( "UNSIGNED_VALUE", ["template_id", "Structure"] ) );
        }

		$this->template_id = intval( $val );

		return $this;
	}


	public function description( $val = null )
	{
		if ( is_null( $val ) ) 	return $this->description;

		$this->description = $val;

		return $this;
	}


	public function path( $val = null )
	{
		if ( is_null( $val ) )  return $this->path;

		if ( strlen( $val ) > 100 )
        {
            exit ( Core::getMessage( "TOO_LARGE_VALUE", ["title", "Structure", 100] ) );
        }
		
		$this->path = $val;

		return $this;
	}


	public function action( $val = null )
	{
		if ( is_null( $val ) )  return $this->action;

		if ( strlen( $val ) > 100 )
        {
            exit ( Core::getMessage( "TOO_LARGE_VALUE", ["title", "Structure", 100] ) );
        }
		
		$this->action = $val;

		return $this;
	}


	public function meta_title( $val = null )
	{
		if ( is_null( $val ) )  return $this->meta_title;

		if ( strlen( $val ) > 100 )
        {
            exit ( Core::getMessage( "TOO_LARGE_VALUE", ["title", "Structure", 100] ) );
        }
				
		$this->meta_title = $val;

		return $this;
	}


	public function meta_keywords( $val = null )
	{
		if ( is_null( $val ) )  return $this->meta_keywords;

		if ( strlen( $val ) > 100 )
        {
            exit ( Core::getMessage( "TOO_LARGE_VALUE", ["title", "Structure", 100] ) );
        }
				
		$this->meta_keywords = $val;

		return $this;
	}


	public function meta_description( $val = null )
	{
		if ( is_null( $val ) ) 	return $this->meta_description;

		$this->meta_description = $val;

		return $this;
	}


	public function sorting($val = null)
	{
		if ( is_null( $val ) )  return intval( $this->sorting );

		$this->sorting = intval( $val );

		return $this;
	}

}