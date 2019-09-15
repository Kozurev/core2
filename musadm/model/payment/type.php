<?php
/**
 * Класс-модель для работы с типами платежей
 *
 * @author BadWolf
 * @date 21.05.2018 16:17
 * @version 20190328
 * Class Payment_Type
 */
class Payment_Type extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Название типа платежа
     *
     * @var string
     */
    protected $title;


    /**
     * id организации (директора) которому принадлежит тип
     *
     * @var int
     */
    protected $subordinated = 0;


    /**
     * Указатель на возможность удаления типа платежа
     * Так как в системе присутствуют как кастомные так и обязательные типы платежей
     * то удаление возможно лишь для тех у кого значение данного свойства равно 1
     * Первые 3 типа платежа - внесение средств на баланс, списание средств и выплата преподавателю
     *
     * @var int
     */
    protected $is_deletable = 1;


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
     * @param int|null $isDeletable
     * @return $this|int
     */
    public function isDeletable(int $isDeletable = null)
    {
        if (is_null($isDeletable)) {
            return intval($this->is_deletable);
        } elseif ($isDeletable == true) {
            $this->is_deletable = 1;
        } elseif ($isDeletable == false) {
            $this->is_deletable = 0;
        }

        return $this;
    }


    /**
     * Параметры валидации при сохранении таблицы
     */
    public function schema()
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
            'is_deletable' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0,
                'maxval' => 1
            ],
            'subordinated' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 0
            ]
        ];
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function save($obj = null)
    {
        Core::notify([&$this],'beforePaymentTypeSave');

//        if ($this->isDeletable() !== 1) {
//            return;
//        }

        parent::save();

        Core::notify([&$this],'afterPaymentTypeSave');
    }


    /**
     * @return $this|void
     */
    public function delete()
    {
        Core::notify([&$this], 'beforePaymentTypeDelete');

        if ($this->isDeletable() !== 1) {
            return;
        }

        parent::delete();

        Core::notify([&$this], 'afterPaymentTypeDelete');
    }



}