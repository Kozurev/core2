<?php
/**
 * Класс реалзующий работу с файлами в системе
 *
 * @author BadWolf
 * @date 27.09.2019 12:42
 * Class File_Mode;
 */
class File_Model extends Core_Entity
{
    const TYPE_DEFAULT = 0;
    const TYPE_PROFILE = 1;

    /**
     * Исходное название файла при загрузке на сервер
     *
     * @var string
     */
    protected $name = '';

    /**
     * Название файла до загрузки на сервер
     *
     * @var string
     */
    protected $real_name = '';

    /**
     * Тип загружаемого документа (необходим при скачивании)
     *
     * @var string
     */
    protected $type = '';

    /**
     * Идентификатор типа документа
     * также это значение является директорией в папке /upload
     *
     * @var int
     */
    protected $type_id = 0;

    /**
     * Дата/время создания файла
     *
     * @var string
     */
    protected $timecreated = '';

    /**
     * Дата/время изменения файла
     *
     * @var string
     */
    protected $timemodified = '';



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
     * @param string|null $realName
     * @return $this|string
     */
    public function realName(string $realName = null)
    {
        if (is_null($realName)) {
            return $this->real_name;
        } else {
            $this->real_name = $realName;
            return $this;
        }
    }


    /**
     * @param string|null $type
     * @return $this|string
     */
    public function type(string $type = null)
    {
        if (is_null($type)) {
            return $this->type;
        } else {
            $this->type = $type;
            return $this;
        }
    }


    /**
     * @param int|null $typeId
     * @return $this|int
     */
    public function typeId(int $typeId = null)
    {
        if (is_null($typeId)) {
            return intval($this->type_id);
        } else {
            $this->type_id = $typeId;
            return $this;
        }
    }


    /**
     * @param null $datetime
     * @return $this|int|null
     */
    public function timecreated($datetime = null)
    {
        return parent::timecreated($datetime);
    }


    /**
     * @param string|null $datetime
     * @return $this|int
     */
    public function timemodified(string $datetime = null)
    {
        return parent::timemodified($datetime);
    }


    /**
     * В файлах должно сохраняться время создания и время редактирования
     *
     * @return bool
     */
    public function timestamps()
    {
        return true;
    }

}