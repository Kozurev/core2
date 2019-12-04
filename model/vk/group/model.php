<?php

//namespace model\vk\group;


/**
 * Класс-модель сообщества вконтакте, для которого используется senler
 *
 * Class Senler_Group_Model
 */
class Vk_Group_Model extends Core_Entity
{

    /**
     * Напзвание сообщества
     *
     * @var string
     */
    protected $title;

    /**
     * Ссылка на группу
     *
     * @var string
     */
    protected $link;

    /**
     * id сообщества вк
     *
     * @var int
     */
    protected $vk_id;

    /**
     * Секретный токен для группы
     *
     * @var string
     */
    protected $secret_key = '';

    /**
     * @var int
     */
    protected $subordinated;


    /**
     * @param string|null $title
     * @return $this|string
     */
    public function title(string $title = null)
    {
        if (is_null($title)) {
            return $this->title;
        } else {
            $this->title = $title;
            return $this;
        }
    }

    /**
     * @param string|null $link
     * @return $this|string
     */
    public function link(string $link = null)
    {
        if (is_null($link)) {
            return $this->link;
        } else {
            $this->link = $link;
            return $this;
        }
    }

    /**
     * @param string|null $secretKey
     * @return $this|string
     */
    public function secretKey(string $secretKey = null)
    {
        if (is_null($secretKey)) {
            return $this->secret_key;
        } else {
            $this->secret_key = $secretKey;
            return $this;
        }
    }

    /**
     * @param int|null $vkId
     * @return $this|int
     */
    public function vkId(int $vkId = null)
    {
        if (is_null($vkId)) {
            return intval($this->vk_id);
        } else {
            $this->vk_id = intval($vkId);
            return $this;
        }
    }

    /**
     * @param int|null $subordinated
     * @return $this|int
     */
    public function subordinated(int $subordinated = null)
    {
        if (is_null($subordinated)) {
            return intval($this->subordinated);
        } else {
            $this->subordinated = $subordinated;
            return $this;
        }
    }

    /**
     * @return array
     */
    public function schema() : array
    {
        return [
            'id' => [
                'required' => false,
                'type' => PARAM_INT
            ],
            'title' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'link' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 50
            ],
            'vk_id' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 15
            ],
            'secret_key' => [
                'required' => true,
                'type' => PARAM_STRING,
                'maxlength' => 255
            ],
            'subordinated' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ]
        ];
    }

}