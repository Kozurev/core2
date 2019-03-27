<?php
/**
 * Класс-модель для шаблона страницы
 *
 * Class Core_Page_Template_Model
 */
class Core_Page_Template_Model extends Core_Entity
{
    /**
     * @var int
     */
	protected $id;


    /**
     * @var string
     */
	protected $title;

    /**
     * id родительского макета
     *
     * @var int
     */
	protected $parent_id = 0;


    /**
     * id директории, которой принадлежит макет
     *
     * @var int
     */
	protected $dir = 0;



    /**
     * @param string|null $title
     * @return string|$this
     */
	public function title(string $title = null)
	{
        if (is_null($title)) {
            return $this->title;
        } else {
            if (strlen($title) > 255) {
                die(Core::getMessage('TOO_LARGE_VALUE', ['title', 'Page_Template', 255]));
            }
            $this->title = $title;
            return $this;
        }
	}


    /**
     * @param int|null $parentId
     * @return $this|int
     */
    public function parentId(int $parentId = null)
    {
        if (is_null($parentId)) {
            return $this->parent_id;
        } else {
            $this->parent_id = intval($parentId);
            return $this;
        }
    }


    /**
     * @param int|null $dir
     * @return $this|int
     */
    public function dir(int $dir = null)
    {
        if (is_null($dir)) {
            return intval($this->dir);
        } else {
            $this->dir = $dir;
            return $this;
        }
    }

}