<?php


namespace Model\Checkout;

/**
 * Class Model
 * @package Model\Checkout
 */
class Model extends \Core_Entity
{
    /**
     * @var string|null
     */
    protected ?string $title;

    /**
     * @var int|null
     */
    protected ?int $type;

    /**
     * @var string|null
     */
    protected ?string $data;

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return 'Checkouts';
    }

    /**
     * @return mixed
     */
    public function getData() : \stdClass
    {
        $decodedData = json_decode($this->data);
        return $decodedData instanceof \stdClass ? $decodedData : new \stdClass();
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = json_encode($data);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function __get(string $name)
    {
        return isset($this->$name) ? $this->$name : $this->getData()->$name ?? null;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        } else {
            $data = $this->getData();
            $data->$name = $value;
            $this->setData($data);
        }
    }
}