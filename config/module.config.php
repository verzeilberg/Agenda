<?php

namespace Agenda;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'controllers' => [
        'factories' => [
            Controller\AgendaController::class => Controller\Factory\AgendaControllerFactory::class,
            Controller\AgendaAjaxController::class => Controller\Factory\AgendaAjaxControllerFactory::class
        ],
        'aliases' => [
            'agendabeheer' => Controller\AgendaController::class,
            'agendaAjax' => Controller\AgendaAjaxController::class,
        ],
    ],
    'service_manager' => [
        'invokables' => [
            Agenda\Service\AgendaServiceInterface::class => Agenda\Service\AgendaService::class
        ],
    ],
    'view_helpers' => [
        'factories' => [
            View\Helper\AgendaHelper::class => View\Helper\Factory\AgendaViewHelperFactory::class,
        ],
        'aliases' => [
            'agendaViewHelper' => View\Helper\AgendaHelper::class,
        ],
    ],
    'router' => [
        'routes' => [
            'agenda' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/agenda[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'agendabeheer',
                        'action' => 'index',
                    ],
                ],
            ],
            'agendaAjax' => [
                'type' => 'segment',
                'options' => [
                    'route' => '/agenda-ajax[/:action][/:id]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'agendaAjax',
                    ],
                ],
            ]
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'agenda' => __DIR__ . '/../view',
        ],
    ],
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'controllers' => [
            'agendabeheer' => [
                // to anyone.
                ['actions' => '*', 'allow' => '+agenda.manage']
            ],
            'agendaAjax' => [
                // to anyone.
                ['actions' => '*', 'allow' => '+agenda.manage']
            ],
        ]
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ],
    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                __DIR__ . '/../public',
            ],
        ],
    ],
    'agenda_settings' => [
        'clock' => '24' // 12/24 clock
    ]
];
