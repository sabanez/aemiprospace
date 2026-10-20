<?php

use WordPlate\Acf\ConditionalLogic;
use WordPlate\Acf\Fields\DatePicker;
use WordPlate\Acf\Fields\DateTimePicker;
use WordPlate\Acf\Fields\File;
use WordPlate\Acf\Fields\Group;
use WordPlate\Acf\Fields\Image;
use WordPlate\Acf\Fields\Repeater;
use WordPlate\Acf\Fields\Select;
use WordPlate\Acf\Fields\Tab;
use WordPlate\Acf\Fields\Text;
use WordPlate\Acf\Fields\Textarea;
use WordPlate\Acf\Fields\TimePicker;
use WordPlate\Acf\Location;

class RegisterWebinarsCustomFields
{
    public $date_format = 'Y-m-d\TH:i:s\Z';

    function __construct(){
        add_action( 'acf/init', [$this, 'createFields'] );
    }

    public function createFields()
    {
        register_extended_field_group([
            'title' => __('Información del seminario web', AEMI_TEXT_DOMAIN),
            'fields' => [
                Tab::make(__('Sobre el Webinar', AEMI_TEXT_DOMAIN)),

                Select::make(__('Tipo de seminario web', AEMI_TEXT_DOMAIN), 'type_webinar')
                    ->choices([
                        'single_session'  => __('Sesión Única', AEMI_TEXT_DOMAIN),
                        'series'          => __('Series', AEMI_TEXT_DOMAIN),
                        'sequence'        => __('Secuencia', AEMI_TEXT_DOMAIN)
                    ])
                    ->wrapper([
                        'id' => 'type_webinar'
                    ]),

                /** Single Session */
                Group::make(__('', AEMI_TEXT_DOMAIN), 'single_session_times')
                    ->fields([
                        DateTimePicker::make(__('Fecha de inicio', AEMI_TEXT_DOMAIN), 'startTime')
                            ->displayFormat('d/m/Y H:i:s')
                            ->returnFormat($this->date_format),
                        DateTimePicker::make(__('Fecha de finalización', AEMI_TEXT_DOMAIN), 'endTime')
                            ->displayFormat('d/m/Y H:i:s')
                            ->returnFormat($this->date_format),
                    ])
                    ->conditionalLogic([
                        ConditionalLogic::if('type_webinar', '==', 'single_session')
                    ])
                    ->wrapper([
                        'id' => 'single_session_times',
                        'class' => 'times-options'
                    ]),

                /** Series */
                Repeater::make(__('', AEMI_TEXT_DOMAIN), 'series_times')
                    ->fields([
                        DateTimePicker::make(__('Fecha de inicio', AEMI_TEXT_DOMAIN), 'startTime')
                            ->displayFormat('d/m/Y H:i:s')
                            ->returnFormat($this->date_format),
                        DateTimePicker::make(__('Fecha de finalización', AEMI_TEXT_DOMAIN), 'endTime')
                            ->displayFormat('d/m/Y H:i:s')
                            ->returnFormat($this->date_format),
                    ])
                    ->buttonLabel('Nuevo tiempo')
                    ->conditionalLogic([
                        ConditionalLogic::if('type_webinar', '==', 'series')
                    ])
                    ->min(1)
                    ->wrapper([
                        'id' => 'series_times',
                        'class' => 'times-options'
                    ]),

                /** Sequence */
                Repeater::make(__('', AEMI_TEXT_DOMAIN), 'sequence_times')
                    ->fields([
                        DateTimePicker::make(__('Fecha de inicio', AEMI_TEXT_DOMAIN), 'startTime')
                            ->displayFormat('d/m/Y H:i:s')
                            ->returnFormat($this->date_format),
                        DateTimePicker::make(__('Fecha de finalización', AEMI_TEXT_DOMAIN), 'endTime')
                            ->displayFormat('d/m/Y H:i:s')
                            ->returnFormat($this->date_format),
                    ])
                    ->conditionalLogic([
                        ConditionalLogic::if('type_webinar', '==', 'sequence')
                    ])
                    ->min(1)
                    ->wrapper([
                        'class' => 'times-options sequence_times',
                    ]),

                DatePicker::make('Fin de recurrencia', 'recurrenceEnd')
                    ->displayFormat('d/m/Y')
                    ->returnFormat('Y-m-d')
                    ->conditionalLogic([
                        ConditionalLogic::if('type_webinar', '==', 'sequence')
                    ])
                    ->wrapper([
                        'class' => 'times-options sequence_times',
                    ]),

                Select::make(__('Tipo de secuencia', AEMI_TEXT_DOMAIN), 'recurrencePattern')
                    ->choices([
                        'daily' => __('Diariamente', AEMI_TEXT_DOMAIN)
                    ])
                    ->conditionalLogic([
                        ConditionalLogic::if('type_webinar', '==', 'sequence')
                    ])
                    ->wrapper([
                        'class' => 'times-options sequence_times',
                    ]),

//                Group::make(__('', AEMI_TEXT_DOMAIN), 'sequence_params')
//                    ->fields([
//                        DatePicker::make('Fin de recurrencia', 'recurrenceEnd')
//                            ->displayFormat('d/m/Y')
//                            ->returnFormat('Y-m-d'),
//                        Select::make(__('Tipo de secuencia', AEMI_TEXT_DOMAIN), 'recurrencePattern')
//                            ->choices([
//                                'daily' => __('Diariamente', AEMI_TEXT_DOMAIN)
//                            ]),
//                    ])
//                    ->conditionalLogic([
//                        ConditionalLogic::if('type_webinar', '==', 'sequence')
//                    ])
//                    ->wrapper([
//                        'class' => 'times-options sequence_times',
//                    ]),

                Select::make(__('Tipo de experiencia', AEMI_TEXT_DOMAIN), 'experience_type')
                    ->choices([
                        'CLASSIC'   => 'CLASSIC',
                        'BROADCAST' => 'BROADCAST',
                        'SIMULIVE'  => 'SIMULIVE',
                    ]),

                Textarea::make(__('Descripción', AEMI_TEXT_DOMAIN), 'descripcion')
                    ->required(),
                Text::make(__('Oradores', AEMI_TEXT_DOMAIN), 'oradores'),
                Text::make(__('Duración', AEMI_TEXT_DOMAIN), 'duracion'),

                Tab::make(__('Recursos', AEMI_TEXT_DOMAIN)),
                $this->createRepeaterFieldRecursos(),

                Tab::make(__('Instructor', AEMI_TEXT_DOMAIN)),
                $this->createRepeaterFieldsInstructors(),

                Tab::make(__('Habilidades', AEMI_TEXT_DOMAIN)),
                $this->createRepeaterFieldsHabilidades(),
            ],
            'location' => [
                Location::if('post_type', 'webinars')
            ],
            'style' => 'metabox'
        ]);
    }


    private function createRepeaterFieldRecursos()
    {
        return Repeater::make('Recursos', 'repeater_recursos')
            ->fields([
                Select::make('Tipo de Recurso', 'type_recurso')
                    ->choices([
                        'link' => 'Link',
                        'text' => 'Texto',
                        'files' => 'Arquivos'
                    ]),
                Text::make('', 'link_recurso')
                    ->conditionalLogic([
                        ConditionalLogic::if('type_recurso', '==', 'link')
                    ]),
                Text::make('', 'texto_recurso')
                    ->conditionalLogic([
                        ConditionalLogic::if('type_recurso', '==', 'text')
                    ]),
                File::make('', 'files_recurso')
                    ->conditionalLogic([ConditionalLogic::if('type_recurso', '==', 'files')])
                    ->mimeTypes(['pdf', 'png', 'jpg', 'jpeg', 'doc'])
                    ->library('all')
                    ->returnFormat('array'),
            ])
            ->collapsed('type_recurso')
            ->buttonLabel('Adicionar Novo Recurso')
            ->min(1)
            ->layout('block');
    }

    private function createRepeaterFieldsInstructors()
    {
        return Repeater::make('Instructors', 'repeater_instructors')
            ->fields([
                Text::make(__('Name Instructor', AEMI_TEXT_DOMAIN), 'name_instructor')
                    ->required(),
                Text::make(__('Occupation Instructor'), 'occupation_instructor')
                    ->required(),
                Textarea::make(__('About the Instructor', AEMI_TEXT_DOMAIN), 'about_instructor')
                    ->required(),
                Image::make('Photo Instructor', 'photo_instructor')
                    ->mimeTypes(['png', 'jpg', 'jpeg'])
                    ->library('all')
                    ->returnFormat('array')
                    ->previewSize('thumbnail')
                    ->required(),
                Text::make('Know More', 'know_more_instructor')
                    ->instructions('Link Know More'),
            ])
            ->collapsed('name_instructor')
            ->buttonLabel(__('New Instructor', AEMI_TEXT_DOMAIN))
            ->min(1)
            ->layout('block');
    }

    private function createRepeaterFieldsHabilidades()
    {
        return Repeater::make(__('Habilidades a desarrollar', AEMI_TEXT_DOMAIN), 'repetidor_habilidades')
            ->fields([
                Text::make(__('Tipo de Habilidad', AEMI_TEXT_DOMAIN), 'tipo_habilidad')
            ])
            ->collapsed('tipo_habilidad')
            ->buttonLabel(__('Agregar nueva habilidad', AEMI_TEXT_DOMAIN))
            ->min(1)
            ->layout('block');
    }
}

new RegisterWebinarsCustomFields();

