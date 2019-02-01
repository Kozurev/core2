<?php

/**
 *	Модель элемента структуры
 */
class Structure_Item_Model extends Core_Entity
{
	protected $id;
	protected $title; 
	protected $path; 
	protected $parent_id; 
	protected $description;
	protected $active; 
	protected $meta_title; 
	protected $meta_description; 
	protected $meta_keywords; 
	protected $sorting;



	public function getId()
    {
		return intval( $this->id );
    }


	public function title( $val = null )
	{
		if ( is_null( $val ) )  return $this->title;

		if ( strlen( $val ) > 150 )
        {
            exit ( Core::getMessage( 'TOO_LARGE_VALUE', ['title', 'Structure_Item', 150] ) );
        }
	
		$this->title = strval( $val );

		return $this;
	}


	public function path( $val = null )
	{
		if ( is_null( $val ) )  return $this->path;

		if ( strlen( $val ) > 100 )
        {
            exit ( Core::getMessage( 'TOO_LARGE_VALUE', ['path', 'Structure_Item', 100] ) );
        }
				
		$this->path = strval( $val );

		return $this;	
	}


	public function parentId( $val = null )
	{
		if ( is_null( $val ) )  return $this->parent_id;

		if ( $val < 0 )
        {
            exit ( Core::getMessage( 'UNSIGNED_VALUE', ['parent_id', 'Structure_Item'] ) );
        }
		
		$this->parent_id = intval( $val );

		return $this;
	}


	public function description($val = null)
	{
		if(is_null($val)) 	return $this->description;
	
		$this->description = $val;
		return $this;
	}


	public function active( $val = null )
	{
		if ( is_null( $val ) ) 		return $this->active;

		if ( $val == true ) 		$this->active = 1;
		elseif( $val == false )	    $this->active = 0;

		return $this;
	}


	public function meta_title( $val = null )
	{
		if ( is_null( $val ) ) 		return $this->meta_title;

		if ( strlen( $val ) > 100 )
        {
            exit ( Core::getMessage( 'TOO_LARGE_VALUE', ['meta_title', 'Structure_Item', 100] ) );
        }
				
		$this->meta_title = strval( $val );

		return $this;
	}


	public function meta_keywords( $val = null )
	{
		if ( is_null( $val ) ) 		return $this->meta_keywords;

		if ( strlen( $val ) > 100 )
        {
            exit ( Core::getMessage( "TOO_LARGE_VALUE", ['meta_keywords', 'Structure_Item', 100] ) );
        }
				
		$this->meta_keywords = strval( $val );

		return $this;
	}


	public function meta_description( $val = null )
	{
		if ( is_null( $val ) )      return $this->meta_description;

		$this->meta_description = strval( $val );

		return $this;
	}


	public function sorting( $val = null )
	{
		if ( is_null( $val ) )	    return $this->sorting;

		$this->sorting = intval( $val );

		return $this;
	}



}