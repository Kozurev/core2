<?php

Core::requireClass('Vk');

class Vk_Group extends Vk_Group_Model
{
    /**
     * Получает данные группы вк исходя из указанной ссылки
     * stdClass
     *      ->type =>       enum    'page'||'group'||'user'
     *      ->object_id =>  int     идентификатор объекта
     *
     * @param string $link
     * @return stdClass|null
     */
    public static function getVkId(string $link)
    {
        if (!is_null($link)) {
            $linkName = explode('vk.com/', $link)[1] ?? null;
            if (is_string($linkName)) {
                $vk = new VK(self::getToken());
                $response = $vk->resolveScreenName($linkName);
                if (isset($response->error)) {
                    return null;
                } else {
                    if (empty($response->response)) {
                        return null;
                    } else {
                        return $response->response;
                    }
                }
            } else {
                return null;
            }
        } else {
            return null;
        }
    }


    /**
     * @return string
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
            //Такой небольшой костыль
            return '1677ccea4ae8498645725db24832c0c657fec5c5bc61c50e554a42e33de809d0697b32df400b79cf7f316';
            //throw new Exception('У вас нет ни одной группы с указанным секретным улючем');
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
            return null;
        }

        if (empty($this->vk_id)) {
            $vkData = self::getVkId($this->link);
            if (is_null($vkData)) {
                $this->_setValidateErrorStr(get_class($this) . '->save: неверно указана ссылка на сообщество');
                return null;
            } elseif ($vkData->type == 'page' || $vkData->type = 'group') {
                $this->vk_id = $vkData->object_id;
            }
        }

        if (empty(parent::save())) {
            return null;
        }

        Core::notify([&$this], 'after.VkGroup.save');
        return $this;
    }

}