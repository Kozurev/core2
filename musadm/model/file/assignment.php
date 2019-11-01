<?php
/**
 * Связь файла и объекта
 *
 * @author BadWolf
 * @date 06.10.2019 18:08
 */
class File_Assignment extends Core_Entity
{
    /**
     * id файла
     *
     * @var int
     */
    protected $file_id = 0;

    /**
     * id модели: значение одной из констант с префиксом MODEL_
     *
     * @var int
     */
    protected $model_id = 0;

    /**
     * id объекта в БД
     *
     * @var int
     */
    protected $object_id = 0;


    /**
     * @param int|null $fileId
     * @return $this|int
     */
    public function fileId(int $fileId = null)
    {
        if (is_null($fileId)) {
            return intval($this->file_id);
        } else {
            $this->file_id = $fileId;
            return $this;
        }
    }


    /**
     * @param int|null $modelId
     * @return $this|int
     */
    public function modelId(int $modelId = null)
    {
        if (is_null($modelId)) {
            return intval($this->model_id);
        } else {
            $this->model_id = $modelId;
            return $this;
        }
    }


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
}