<?php
/**
 * Класс-модель связи комментария с объектом системы
 *
 * @author: BadWolf
 * @date 02.08.2019 13:37
 * Class Comment_Assignment_Model
 */
class Comment_Assignment_Model extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $object_id;

    /**
     * @var int
     */
    protected $comment_id;



    /**
     * @param int|null $objectId
     * @return $this|int
     */
    public function objectId(int $objectId = null)
    {
        if (is_null($objectId)) {
            return intval($this->object_id);
        } else {
            $this->object_id = $objectId;
            return $this;
        }
    }


    /**
     * @param int|null $commentId
     * @return $this|int
     */
    public function commentId(int $commentId = null)
    {
        if (is_null($commentId)) {
            return intval($this->comment_id);
        } else {
            $this->comment_id = $commentId;
            return $this;
        }
    }

}