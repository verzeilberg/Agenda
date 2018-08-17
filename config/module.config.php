<?php

namespace Agenda;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'controllers' => [
        'factories' => [
            Controller\AgendaController::class => Factory\AgendaControllerFactory::class,
        ],
        'aliases' => [
            'agendabeheer' => Controller\AgendaController::class,
        ],
    ],
    'service_manager' => [
        'invokables' => [
            'Agenda\Service\agendaServiceInterface' => 'Agenda\Service\agendaService'
        ],
    ],
    // The following section is new and should be added to your file
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
];
