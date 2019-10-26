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
     * Create the day in the calendar
     * @param string $currentMonth
     * @param string $currentYear
     * @param string $cellNumber
     * @return string
     */
    public function showDay($currentMonth, $currentYear, $cellNumber, $currentDay)
    {
        if ($currentDay == 0) {
            $firstDayOfTheWeek = date('N', strtotime($currentYear . '-' . $currentMonth . '-01'));
            if (intval($cellNumber) == $firstDayOfTheWeek) {
                $currentDay = 1;
            }
        }
        if (($currentDay != 0) &&
            ($currentDay <= $this->agendaService->daysInMonth($currentMonth, $currentYear))) {
            $cellContent = $currentDay;
            $currentDay++;
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
}