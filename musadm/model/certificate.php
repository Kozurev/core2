<?php
/**
 * Класс-модель сертификата
 *
 * @author BadWolf
 * @date 21.05.2018 10:07
 * @version 20190402
 * Class Certificate
 */
class Certificate extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * Дата продажи сертификата
     *
     * @var string
     */
    protected $sell_date;


    /**
     * Номер сертификата
     *
     * @var string
     */
    protected $number;


    /**
     * Дата до которой действителен сертификат
     *
     * @var string
     */
    protected $active_to;


    /**
     * id организации (директора), которому принадлежит сертификат
     *
     * @var int
     */
    protected $subordinated = 0;


    /**
     * @param string|null $sellDate
     * @return $this|string
     */
    public function sellDate(string $sellDate = null)
    {
        if (is_null($sellDate)) {
            return $this->sell_date;
        } else {
            $this->sell_date = $sellDate;
            return $this;
        }
    }


    /**
     * @param string|null $number
     * @return $this|string
     */
    public function number(string $number =  null)
    {
        if (is_null($number)) {
            return $this->number;
        } else {
            $this->number = $number;
            return $this;
        }
    }


    /**
     * @param string|null $activeTo
     * @return $this|string
     */
    public function activeTo(string $activeTo = null)
    {
        if (is_null($activeTo)) {
            return $this->active_to;
        } else {
            $this->active_to = $activeTo;
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
            return $this->subordinated;
        } else {
            $this->subordinated = $subordinated;
            return $this;
        }
    }


    /**
     * Поиск всех комментариев сертификата
     *
     * @return array
     */
    public function getNotes()
    {
        $Notes = Core::factory('Certificate_Note')
            ->queryBuilder()
            ->select(['Certificate_Note.id', 'date', 'certificate_id', 'author_id', 'text'])
            ->addSelect('usr.surname')
            ->addSelect('usr.name')
            ->join('User as usr', 'author_id = usr.id')
            ->orderBy('date', 'DESC')
            ->orderBy('Certificate_Note.id', 'DESC');
        if (!empty($this->id)) {
            $Notes->where('certificate_id', '=', $this->id);
        }
        return $Notes->findAll();
    }


    /**
     * Метод добавления комментария
     *
     * @param string $text
     * @param bool $triggerObserver
     */
    public function addNote(string $text, bool $triggerObserver = true)
    {
        $Note = Core::factory('Certificate_Note')
            ->text($text)
            ->certificateId($this->id);

        if ($triggerObserver == true) {
            Core::notify([&$Note], 'beforeCertificateAddComment');
        }

        $Note->save();

        if ($triggerObserver == true) {
            Core::notify([&$Note], 'afterCertificateAddComment');
        }
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function save($obj = null)
    {
        Core::notify([&$this], 'beforeCertificateSave');

        if ($this->sell_date == '') {
            $this->sell_date = date('Y-m-d');
        }

        parent::save();
        Core::notify([&$this], "afterCertificateSave");
    }


    /**
     * @param null $obj
     * @return $this|void
     */
    public function delete($obj = null)
    {
        Core::notify([&$this], 'beforeCertificateDelete');
        parent::delete();
        Core::notify([&$this], 'afterCertificateDelete');
    }

}