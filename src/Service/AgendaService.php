<?php

namespace Agenda\Service;

use DateTime;
use Doctrine\Laminas\Hydrator\DoctrineObject as DoctrineHydrator;
use Agenda\Entity\AgendaItem;
use Agenda\Repository\AgendaItemRepository;
use Laminas\Form\Annotation\AnnotationBuilder;

class AgendaService implements AgendaServiceInterface
{

    /**
     * @var AgendaItemRepository
     */
    public $agendaItemRepository;
    /**
     * @var config
     */
    private $config;

    public function __construct(
        $agendaItemRepository,
        $config
    )
    {
        $this->agendaItemRepository = $agendaItemRepository;
        $this->config = $config;
    }

    /**
     * Get 12 or 24 hours settings
     * @return array
     */
    public function getTwentyFourHours()
    {
            $clock = $this->config['agenda_settings']['clock'];
            //Default 24 hours
            $hours = [
                '0:00',
                '1:00',
                '2:00',
                '3:00',
                '4:00',
                '5:00',
                '6:00',
                '7:00',
                '8:00',
                '9:00',
                '10:00',
                '11:00',
                '12:00',
                '13:00',
                '14:00',
                '15:00',
                '16:00',
                '17:00',
                '18:00',
                '19:00',
                '20:00',
                '21:00',
                '22:00',
                '23:00',
            ];
            if ($clock == '12' ) {
                $hours = [
                    '0:00',
                    '1:00',
                    '2:00',
                    '3:00',
                    '4:00',
                    '5:00',
                    '6:00',
                    '7:00',
                    '8:00',
                    '9:00',
                    '10:00',
                    '11:00',
                    '12:00',
                ];
            }
            return $hours;
    }

    /**
     * Check if it is a valid date
     * @param string $date
     * @return bool|DateTime|null
     * @throws \Exception
     */
    public function checkDayDate($date = null)
    {
        $length = strlen($date);
        if ($length != 8) {
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
    public function weeksInMonth($month = null, $year = null)
    {
        // find number of days in this month
        $daysInMonths = (int)$this->daysInMonth($month, $year);
        $numOfWeeks = ($daysInMonths % 7 == 0 ? 0 : 1) + $daysInMonths / 7;
        $monthEndingDay = date('N', strtotime($year . '-' . $month . '-' . $daysInMonths));
        $monthStartDay = date('N', strtotime($year . '-' . $month . '-01'));
        if ($monthEndingDay < $monthStartDay) {
            $numOfWeeks++;
        }
        return (int)$numOfWeeks;
    }

    /**
     * calculate number of days in a particular month
     * @param $month
     * @param $year
     * @return int
     * @throws \Exception
     */
    public function daysInMonth($month = null, $year = null)
    {
        $date = new DateTime($year . '-' . $month . '-01');
        return (int)$date->format('t');
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
    function getStartAndEndDate($week, $year)
    {
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
     * @param object $agendaItem
     * @return      form
     */
    public function createAgendaItemForm($agendaItem)
    {
        $builder = new AnnotationBuilder($this->agendaItemRepository->returnEntityManager());
        $form = $builder->createForm($agendaItem);
        $form->setHydrator(new DoctrineHydrator($this->agendaItemRepository->returnEntityManager(), AgendaItem::class));
        $form->bind($agendaItem);

        return $form;
    }

    public function convertAgendaItemsToJson($agendaItems)
    {
        $result = [];
        if (count($agendaItems) > 0)
        {
            foreach ($agendaItems as $index => $agendaItem)
            {
                $item = [];
                $item['title'] = $agendaItem->getTitle();
                $item['startTime'] = $agendaItem->getStartTime();
                $item['endTime'] = $agendaItem->getEndTime();
                $result[] = $item;
            }
        }

        return $result;
    }

}
