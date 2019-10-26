<?php

namespace Agenda\Service;

use DateTime;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;
use Agenda\Entity\AgendaItem;
use Agenda\Repository\AgendaItemRepository;

class AgendaService implements AgendaServiceInterface
{

    /*
 * @var AgendaItemRepository
 */
    public $agendaItemRepository;

    public function __construct(
        AgendaItemRepository $agendaItemRepository
    )
    {
        $this->agendaItemRepository = $agendaItemRepository;
    }

    /**
     * Check if it is a valid date
     * @param string $date
     * @return bool|DateTime|null
     * @throws \Exception
     */
    public function checkDayDate($date = null) {
        $length =  strlen($date);
        if($length != 8) {
            return false;
        }
        $date = new DateTime($date);
        return $date;
    }

    /**
     * calculate number of weeks in a particular month
     * @param $month
     * @param $year
     * @return int
     * @throws \Exception
     */
    public function weeksInMonth($month=null,$year=null){
        // find number of days in this month
        $daysInMonths = (int) $this->daysInMonth($month,$year);
        $numOfWeeks = ($daysInMonths%7==0?0:1) + $daysInMonths/7;
        $monthEndingDay= date('N',strtotime($year.'-'.$month.'-'.$daysInMonths));
        $monthStartDay = date('N',strtotime($year.'-'.$month.'-01'));
        if($monthEndingDay<$monthStartDay){
            $numOfWeeks++;
        }
        return (int) $numOfWeeks;
    }

    /**
     * calculate number of days in a particular month
     * @param $month
     * @param $year
     * @return int
     * @throws \Exception
     */
    public function daysInMonth($month=null,$year=null){
        $date = new DateTime($year.'-'.$month.'-01');
        return (int) $date->format('t');
    }

    /**
     * Get Day labels based on language and notation type (short, middle or long)
     * @param string $language
     * @param string $notation
     * @return mixed
     */
    public function getDayLabels($language = 'en', $notation = 'short')
    {
        $labelsArray = [
            "en" => [
                "short" => [1 => "M", "T", "W", "T", "F", "S", "S"],
                "middle" => [1 => "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
                "long" => [1 => "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"]
            ],
            "nl" => [
                "short" => [1 => "M", "D", "W", "D", "V", "Z", "Z"],
                "middle" => [1 => "Maan", "Dins", "Woens", "Don", "Vrij", "Zat", "Zon"],
                "long" => [1 => "Maandag", "Dinsdag", "Woensdag", "Donderdag", "Vrijdag", "Zaterdag", "Zondag"]
            ]
        ];

        return $labelsArray[$language][$notation];
    }

    /**
     * Get month labels based on language
     * @param string $language
     * @param int $month
     * @return mixed
     */
    public function getMonthLabels($language = 'en', $month = 1)
    {
        $labelsArray = [
            "en" => [
                1 => "January",
                "February",
                "March",
                "April",
                "May",
                "June",
                "July",
                "August",
                "September",
                "October",
                "November",
                "December"
            ],
            "nl" => [
                1 => "Januari",
                "Februari",
                "Maart",
                "April",
                "Mei",
                "Juni",
                "Juli",
                "Augustus",
                "September",
                "Oktober",
                "November",
                "December"
            ]
        ];

        return $labelsArray[$language][$month];
    }

    /**
     *
     * @param $week
     * @param $year
     * @return mixed
     * @throws \Exception
     */
    function getStartAndEndDate($week, $year) {
        $dto = new DateTime();
        $ret['week_start'] = $dto->setISODate($year, $week)->format('Y-m-d');
        $ret['week_end'] = $dto->modify('+6 days')->format('Y-m-d');
        return $ret;
    }

    /**
     * Create navigation params for navigation
     * @param string $currentMonth
     * @param string $currentYear
     * @return string
     * @throws \Exception
     */
    public function getNavigationParams($currentMonth, $currentYear)
    {

        $currentMonth = (int)$currentMonth;
        $currentYear = (int)$currentYear;
        $nextMonth = $currentMonth == 12 ? 1 : $currentMonth + 1;
        $nextYear = $currentMonth == 12 ? $currentYear + 1 : $currentYear;
        $preMonth = $currentMonth == 1 ? 12 : $currentMonth - 1;
        $preYear = $currentMonth == 1 ? $currentYear - 1 : $currentYear;

        return [
            'nextMonth' => $nextMonth,
            'nextYear' => $nextYear,
            'preMonth' => $preMonth,
            'preYear' => $preYear
        ];
    }

    /**
     * Create form of an object
     * @param       object $agendaItem
     * @return      form
     */
    public function createAgendaItemForm($agendaItem) {
        $builder = new AnnotationBuilder($this->agendaItemRepository->returnEntityManager());
        $form = $builder->createForm($agendaItem);
        $form->setHydrator(new DoctrineHydrator($this->agendaItemRepository->returnEntityManager(), AgendaItem::class));
        $form->bind($agendaItem);

        return $form;
    }

}
