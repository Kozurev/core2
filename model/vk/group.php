<?php

Core::requireClass('Vk');

class Vk_Group extends Vk_Group_Model
{
    /**
     * Получает id группы вк исходя из указанной ссылки
     *
     * @param string $link
     * @return string
     * @throws Exception
     */
    public static function getVkId(string $link) : string
    {
        if (!is_null($link)) {
            $linkName = substr($link, 15);
            if (is_string($linkName)) {
                $vk = new VK(self::getToken());
                $response = $vk->resolveScreenName($linkName);
                if (isset($response->error)) {
                    throw new Exception($response->error->error_msg);
                } else {
                    if ($response->response->type == 'page' || $response->response->type == 'group') {
                        return strval($response->response->object_id);
                    } else {
                        throw new Exception('Указанная ссылка не является сообществом');
                    }
                }
            } else {
                throw new Exception('Некорректно указана ссылка на страницу сообщества');
            }
        } else {
            throw new Exception('Для получения идентификатора сообщества необходимо указать ссылку на его страницу');
        }
    }


    /**
     * @return string
     * @throws Exception
     */
    public static function getToken() : string
    {
        $vkGroup = (new Vk_Group())
            ->queryBuilder()
            ->where('subordinated', '=', User_Auth::current()->getDirector()->getId())
            ->find();

        if (!is_null($vkGroup)) {
            return $vkGroup->secretKey();
        } else {
            throw new Exception('У вас нет ни одной группы с указанным секретным улючем');
        }
    }


    /**
     * @param string $key
     * @return string
     */
    public static function getHiddenKey(string $key) : string
    {
        if (empty($key)) {
            return '';
        } else {
            return '******' . substr($key, strlen($key) - 4);
        }
    }


    /**
     * @param null $obj
     * @return Vk_Group|null
     * @throws Exception
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'before.VkGroup.save');

        if (empty($this->link)) {
            $this->_setValidateError(
                get_class($this) . '-> link',
                self::VALID_REQUIRED,
                ['valid' => true, 'current' => false]
            );
        }

        if (empty($this->vk_id)) {
            $this->vk_id = self::getVkId($this->link);
        }

        if (empty(parent::save())) {
            return null;
        }

        Core::notify([&$this], 'after.VkGroup.save');
        return $this;
    }

}