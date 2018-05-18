<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 16.05.2018
 * Time: 16:50
 */

class Task_Note extends Core_Entity
{
    protected $id;
    protected $date;
    protected $task_id;
    protected $author_id;
    protected $text;

    public function __construct(){}


    public function getId()
    {
        return $this->id;
    }


    public function date($val = null)
    {
        if(is_null($val))   return $this->date;
        $this->date = strval($val);
        return $this;
    }


    public function authorId($val = null)
    {
        if(is_null($val))   return $this->author_id;
        $this->author_id = intval($val);
        return $this;
    }


    public function taskId($val = null)
    {
        if(is_null($val))   return $this->task_id;
        $this->task_id = intval($val);
        return $this;
    }


    public function text($val = null)
    {
        if(is_null($val))   return $this->text;
        $this->text = strval($val);
        return $this;
    }


    public function getAuthor()
    {
        return Core::factory("User", $this->author_id);
    }


    public function save($obj = null)
    {
        Core::notify(array(&$this), "beforeTaskNoteSave");
        if($this->date == "")   $this->date = date("Y-m-d H:i:s");
        if($this->author_id == "")  $this->author_id = Core::factory("User")->getCurrent()->getId();
        parent::save();
        Core::notify(array(&$this), "afterTaskNoteSave");
    }


    public function delete($obj = null)
    {
        Core::notify(array(&$this), "beforeTaskNoteDelete");
        parent::delete();
        Core::notify(array(&$this), "afterTaskNoteDelete");
    }

}