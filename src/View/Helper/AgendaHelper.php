<?php

namespace Agenda\View\Helper;

use Agenda\Service\AgendaService;
use Zend\View\Helper\AbstractHelper;
use DateTime;

class AgendaHelper extends AbstractHelper
{

    /*
     * @var AgendaService
     */
    protected $agendaService;

    public function __construct(
        AgendaService $agendaService
    ) {
        $this->agendaService = $agendaService;
    }

    /**
     * @param $currentMonth
     * @param $currentYear
     * @param $dayCount
     * @param $currentDay
     * @return DateTime|null
     * @throws \Exception
     */
    public function showDay($currentMonth, $currentYear, $dayCount)
    {
            if ($dayCount <= $this->agendaService->daysInMonth($currentMonth, $currentYear)) {
                $cellContent = $dayCount;
                return new DateTime($currentYear . '-' . $currentMonth . '-' . $cellContent);
            } else {
                return null;
            }
    }


    public function getDayLabels($language = 'en', $notation = 'short')
    {
        $label = $this->agendaService->getDayLabels($language, $notation);
        return $label;
    }

    /**
     * Get month labels based on language and month number (1-12)
     * @param string $language
     * @param int $month
     * @return mixed
     */
    public function getMonthLabels($language = 'en', $month = 1)
    {
        $label = $this->agendaService->getMonthLabels($language, $month);
        return $label;
    }

    /**
     * Returns a weeknumber based on day, month and year
     * @param int $dayNumber
     * @param $year
     * @param $month
     * @return DateTime|string
     * @throws \Exception
     */
    public function getWeekNumber($dayNumber = 0, $year, $month)
    {

        if ($dayNumber == 0) {
            $startDay = 1;
        } else {
            $startDay = $dayNumber * 7;
            //Check if startdate is not higher than last date of the current month
            $lastDayOfMonth = new DateTime($year . '-' . $month . '-1');
            $lastDayOfMonth->modify('last day of this month');
            $lastDayOfMonth = $lastDayOfMonth->format('d');
            if ($startDay > $lastDayOfMonth) {
                $startDay = $lastDayOfMonth;
            }
        }

        //Set startdate
        $startDate = $year . '-' . $month . '-' . $startDay;
        //Get weeknumber by date
        $weekNumber = new DateTime($startDate);
        $weekNumber = $weekNumber->format('W');

        return $weekNumber;
    }

    /**
     * Shorten title to fit in agenda box
     * @param $title
     * @return string
     */
    public function shortenTitle($title)
    {
        if(strlen($title) > 30)
        {
            $title = trim(substr($title, 0, 30))."&hellip;";
        }

        return $title;
    }
}