<?php
/**
 * Класс-модель моддификации времени занятия
 *
 * @author BadWolf
 * @date 10.05.2018 14:44
 * @version 20190401
 * Class Schedule_Lesson_TimeModified
 */
class Schedule_Lesson_TimeModified extends Core_Entity
{
    /**
     * @var int
     */
    protected $id;


    /**
     * @var int
     */
    protected $lesson_id;


    /**
     * @var string
     */
    protected $date;


    /**
     * @var string
     */
    protected $time_from;


    /**
     * @var string
     */
    protected $time_to;


    /**
     * @param int|null $lessonId
     * @return $this|int
     */
    public function lessonId(int $lessonId = null)
    {
        if (is_null($lessonId)) {
            return intval($this->lesson_id);
        } else {
            $this->lesson_id = $lessonId;
            return $this;
        }
    }


    /**
     * @param string|null $date
     * @return $this|string
     */
    public function date(string $date = null)
    {
        if (is_null($date)) {
            return $this->date;
        } else {
            $this->date = $date;
            return $this;
        }
    }


    /**
     * @param string|null $timeFrom
     * @return $this|string
     */
    public function timeFrom(string $timeFrom = null)
    {
        if (is_null($timeFrom)) {
            return $this->time_from;
        } else {
            $this->time_from = $timeFrom;
            return $this;
        }
    }


    /**
     * @param string|null $timeTo
     * @return $this|string
     */
    public function timeTo(string $timeTo = null)
    {
        if (is_null($timeTo)) {
            return $this->time_to;
        } else {
            $this->time_to = $timeTo;
            return $this;
        }
    }

    /**
     * @return Schedule_Lesson
     */
    public function getLesson(): ?Schedule_Lesson
    {
        return Schedule_Lesson::find($this->lessonId());
    }

    /**
     * @param null $obj
     * @return $this|null
     */
    public function save($obj = null)
    {
        if (mb_strlen($this->time_from) == 5) {
            $this->time_from .= ':00';
        }

        if (mb_strlen($this->time_to) == 5) {
            $this->time_to .= ':00';
        }

        if (empty(parent::save())) {
            return null;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function _validateModel(): bool
    {
        if ($this->timeFrom() >= $this->timeTo()) {
            $this->_setValidateErrorStr('Время начала занятия должно быть меньше времени завершения');
        }
        if ($this->timeFrom() < SCHEDULE_TIME_START) {
            $this->_setValidateErrorStr('Время начала занятия не может быть ранее чем ' . refactorTimeFormat(SCHEDULE_TIME_START));
        }
        if ($this->timeTo() > SCHEDULE_TIME_END) {
            $this->_setValidateErrorStr('Время завершения занятия не может быть позднее чем ' . refactorTimeFormat(SCHEDULE_TIME_END));
        }

        if (!empty($this->_getValidateErrors())) {
            return false;
        }

        $lesson = $this->getLesson();
        $lesson->lessonType(Schedule_Lesson::SCHEDULE_CURRENT);
        $lesson->insertDate($this->date());
        $lesson->timeFrom($this->timeFrom());
        $lesson->timeTo($this->timeTo());

        if (!$lesson->_validateModel()) {
            $this->_setValidateErrorStr($lesson->_getValidateErrorsStr());
            return false;
        }

        return parent::_validateModel();
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
            'lesson_id' => [
                'required' => true,
                'type' => PARAM_INT,
                'minval' => 1
            ],
            'date' => [
                'required' => true,
                'type' => PARAM_STRING,
                'minlength' => 10,
                'maxlength' => 10
            ],
            'time_from' => [
                'required' => true,
                'type' => PARAM_STRING,
                'minlength' => 8,
                'maxlength' => 8
            ],
            'time_to' => [
                'required' => true,
                'type' => PARAM_STRING,
                'minlength' => 8,
                'maxlength' => 8
            ]
        ];
    }

}