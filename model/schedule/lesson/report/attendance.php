<?php
/**
 * Created by PhpStorm.
 * User: Egor
 * Date: 18.04.2019
 * Time: 14:26
 */

class Schedule_Lesson_Report_Attendance extends Core_Entity
{
    protected $id;
    protected $report_id;
    protected $client_id;
    protected $attendance;
    protected $lessons_written_off;

    public function reportId(int $reportId = null)
    {
        if (is_null($reportId)) {
            return intval($this->report_id);
        } else {
            $this->report_id = $reportId;
            return $this;
        }
    }

    public function clientId(int $clientId = null)
    {
        if (is_null($clientId)) {
            return intval($this->client_id);
        } else {
            $this->client_id = $clientId;
            return $this;
        }
    }

    public function attendance(int $attendance = null)
    {
        if (is_null($attendance)) {
            return intval($this->attendance);
        } else {
            $this->attendance = $attendance;
            return $this;
        }
    }

    public function lessonsWrittenOff(float $lessonsWrittenOff = null)
    {
        if (is_null($lessonsWrittenOff)) {
            return floatval($this->lessons_written_off);
        } else {
            $this->lessons_written_off = $lessonsWrittenOff;
            return $this;
        }
    }
}