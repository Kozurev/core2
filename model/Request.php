<?php


namespace Model;


use Illuminate\Support\Collection;

/**
 * Class Request
 * @package Model
 */
class Request
{
    /**
     * @var $this |null
     */
    protected static ?self $_instance = null;

    /**
     * @var Collection
     */
    protected Collection $data;

    /**
     * Request constructor.
     */
    protected function __construct()
    {
        $data = (array)json_decode(file_get_contents('php://input'), true);
        if (empty($data)) {
            $data = $_REQUEST;
        }
        $this->data = collect($data);
    }

    /**
     * @return static
     */
    public static function instance(): self
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->data->has($key);
    }

    /**
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->data->get($key, $default);
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->data;
    }
}