<?php
/**
 * @author BadWolf
 * @version 20190321
 * Class Core_Entity_Model
 */
class Core_Entity_Model
{
    protected $id;

    protected $aEntityVars = [
        'name' => 'root',
        'value' => null,
        'xslPath' => '',
        'custom_tag' => '',
        'orm' => null
    ];

    //Массив дочерних сущьностей
    protected $childrenObjects = [];


    /**
     * @param string|null $datetime
     * @return $this|int|null
     */
    public function timecreated($datetime = null)
    {
        if (is_null($datetime) && isset($this->timecreated)) {
            return $this->timecreated;
        } elseif (is_null($datetime) && !isset($this->timecreated)) {
            return null;
        } else {
            $this->timecreated = $datetime;
            return $this;
        }
    }

    /**
     * @param string|null $datetime
     * @return $this|int
     */
    public function timemodified(string $datetime = null)
    {
        if (is_null($datetime) && isset($this->timemodified)) {
            return intval($this->timemodified);
        } elseif (is_null($datetime) && !isset($this->timemodified)) {
            return null;
        } else {
            $this->timemodified = $datetime;
            return $this;
        }
    }

    /**
     * @param int|null $isDeleted
     * @return $this|int
     */
    public function deleted(int $isDeleted = null)
    {
        if (is_null($isDeleted) && isset($this->deleted)) {
            return intval($this->deleted);
        } else {
            $this->deleted = $isDeleted;
            return $this;
        }
    }

    /**
     * @param Orm|null $orm
     * @return $this|mixed
     */
    public function orm(Orm $orm = null)
    {
        if (is_null($orm)) {
            return $this->aEntityVars['orm'];
        } else {
            $this->aEntityVars['orm'] = $orm;
            return $this;
        }
    }

    /**
     * @param string null $name
     * @return $this|mixed
     */
    public function _entityName(string $name = null)
    {
        if (is_null($name)) {
            return $this->aEntityVars['name'];
        } else {
            $this->aEntityVars['name'] = $name;
            return $this;
        }
    }

    /**
     * @param string null $val
     * @return $this|mixed
     */
    public function _entityValue(string $val = null)
    {
        if (is_null($val)) {
            return $this->aEntityVars['value'];
        } else {
            $this->aEntityVars['value'] = $val;
            return $this;
        }
    }

    /**
     * @param string null $xsl
     * @return $this|mixed
     */
    public function xsl(string $xsl = null)
    {
        if (is_null($xsl)) {
            return $this->aEntityVars['xslPath'];
        } else {
            $this->aEntityVars['xslPath'] = ROOT . '/xsl/' . $xsl;
            return $this;
        }
    }

    /**
     * @param string null $tag
     * @return $this|mixed
     */
    public function _customTag(string $tag = null)
    {
        if (is_null($tag)) {
            return $this->aEntityVars['custom_tag'];
        } else {
            $this->aEntityVars['custom_tag'] = $tag;
            return $this;
        }
    }


    public function _childrenObjects()
    {
        return $this->childrenObjects;
    }

}