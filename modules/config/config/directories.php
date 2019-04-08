<?php

use app\common\helpers\Url;

return [
    [
        'key' => 'position',
        'label' => 'Должности',
        'ormClass' => \app\modules\organization\models\orm\Position::className(),
        'config' => [
            'createForm' => [
                'ormClass' => \app\modules\organization\models\orm\Position::className(),
                'action' => Url::to(['/organization/ui/position/create']),
                'validationUrl' => Url::to(['/organization/ui/position/validate-create']),
                'attributes' => [
                    'title',
                    'short_title',
                    'description',
                    [
                        'attribute' => 'department_id',
                        'type' => 'dropdown',
                        'value' => function () {
                            return \app\modules\organization\models\orm\Department::listAll();
                        },
                    ],
                ]
            ],
            'updateForm' => [
                'ormClass' => \app\modules\organization\models\orm\Position::className(),
                'action' => Url::to(['/organization/ui/position/update']),
                'validationUrl' => Url::to(['/organization/ui/position/validate-update']),
                'attributes' => [
                    'title',
                    'short_title',
                    'description',
                    [
                        'attribute' => 'department_id',
                        'type' => 'dropdown',
                        'value' => function () {
                            return \app\modules\organization\models\orm\Department::listAll();
                        },
                    ],
                ]
            ],
            'grid' => [
                'finderClass' => \app\modules\organization\models\finders\PositionFinder::className(),
                'columns' => [
                    'title',
                    'short_title',
                    'description',
                    [
                        'header' => \app\modules\organization\OrganizationModule::t('common', 'Title department'),
                        'attribute' => 'department.title'
                    ],
                ]
            ]
        ]
    ],
    [
        'key' => 'organization',
        'label' => 'Организации',
        'ormClass' => \app\modules\organization\models\orm\Organization::className(),
        'config' => [
            'createForm' => [
                'ormClass' => \app\modules\organization\models\orm\Organization::className(),
                'action' => Url::to(['/organization/ui/organization/create']),
                'validationUrl' => Url::to(['/organization/ui/organization/validate-create']),
                'attributes' => [
                    'title',
                    'short_title',
                    'description',
                ]
            ],
            'updateForm' => [
                'ormClass' => \app\modules\organization\models\orm\Organization::className(),
                'action' => Url::to(['/organization/ui/organization/update']),
                'validationUrl' => Url::to(['/organization/ui/organization/validate-update']),
                'attributes' => [
                    'title',
                    'short_title',
                    'description',
                ]
            ],
            'grid' => [
                'finderClass' => \app\modules\organization\models\finders\OrganizationFinder::className(),
                'columns' => [
                    'title',
                    'short_title',
                    'description',
                ]
            ]
        ]
    ],
    [
        'key' => 'department',
        'label' => 'Подразделения',
        'ormClass' => \app\modules\organization\models\orm\Department::className(),
        'config' => [
            'createForm' => [
                'ormClass' => \app\modules\organization\models\orm\Department::className(),
                'action' => Url::to(['/organization/ui/department/create']),
                'validationUrl' => Url::to(['/organization/ui/department/validate-create']),
                'attributes' => [
                    'title',
                    'short_title',
                    'description',
                    [
                        'attribute' => 'organization_id',
                        'type' => 'dropdown',
                        'value' => function () {
                            return \app\modules\organization\models\orm\Organization::listAll();
                        },
                    ],
                ]
            ],
            'updateForm' => [
                'ormClass' => \app\modules\organization\models\orm\Department::className(),
                'action' => Url::to(['/organization/ui/department/update']),
                'validationUrl' => Url::to(['/organization/ui/department/validate-update']),
                'attributes' => [
                    'title',
                    'short_title',
                    'description',
                    [
                        'attribute' => 'organization_id',
                        'type' => 'dropdown',
                        'value' => function () {
                            return \app\modules\organization\models\orm\Organization::listAll();
                        },
                    ],
                ]
            ],
            'grid' => [
                'finderClass' => \app\modules\organization\models\finders\DepartmentFinder::className(),
                'columns' => [
                    'title',
                    'short_title',
                    'description',
                    [
                        'header' => \app\modules\organization\OrganizationModule::t('common', 'Title organization'),
                        'attribute' => 'organization.title'
                    ],
                ]
            ]
        ]
    ],
    [
        'key' => 'cabinet',
        'label' => 'Кабинеты',
        'ormClass' => \app\modules\organization\models\orm\Cabinet::className(),
        'config' => [
            'createForm' => [
                'ormClass' => \app\modules\organization\models\orm\Cabinet::className(),
                'action' => Url::to(['/organization/ui/cabinet/create']),
                'validationUrl' => Url::to(['/organization/ui/cabinet/validate-create']),
                'attributes' => [
                    'number',
                    'description',
                    [
                        'attribute' => 'organization_id',
                        'type' => 'dropdown',
                        'value' => function () {
                            return \app\modules\organization\models\orm\Organization::listAll();
                        },
                    ],
                    [
                        'attribute' => 'department_id',
                        'type' => 'dropdown',
                        'value' => function () {
                            return \app\modules\organization\models\orm\Department::listAll();
                        },
                    ],
                ]
            ],
            'updateForm' => [
                'ormClass' => \app\modules\organization\models\orm\Cabinet::className(),
                'action' => Url::to(['/organization/ui/cabinet/update']),
                'validationUrl' => Url::to(['/organization/ui/cabinet/validate-update']),
                'attributes' => [
                    'number',
                    'description',
                    [
                        'attribute' => 'organization_id',
                        'type' => 'dropdown',
                        'value' => function () {
                            return \app\modules\organization\models\orm\Organization::listAll();
                        },
                    ],
                    [
                        'attribute' => 'department_id',
                        'type' => 'dropdown',
                        'value' => function () {
                            return \app\modules\organization\models\orm\Department::listAll();
                        },
                    ],
                ]
            ],
            'grid' => [
                'finderClass' => \app\modules\organization\models\finders\CabinetFinder::className(),
                'columns' => [
                    'number',
                    'description',
                    [
                        'header' => 'Организация',
                        'attribute' => 'organization.title',
                    ],
                    [
                        'header' => 'Подразделение',
                        'attribute' => 'department.title',
                    ],
                ]
            ]
        ]
    ],
    [
        'key' => 'medical-service',
        'label' => 'Медицинские услуги',
        'ormClass' => \app\modules\medical\models\orm\Service::class,
        'config' => [
            'createForm' => [
                'ormClass' => \app\modules\medical\models\orm\Service::class,
                'action' => Url::to(['/medical/rest/service/create']),
                'validationUrl' => Url::to(['/medical/rest/service/validate-create']),
                'attributes' => [
                    'code',
                    'title',
                    'short_title',
                    'description',
                    [
                        'attribute' => 'speciality_id',
                        'type' => 'dropdown',
                        'value' => function () {
                            return \app\modules\medical\models\orm\Speciality::listAll();
                        },
                    ],
                ]
            ],
            'updateForm' => [
                'ormClass' => \app\modules\medical\models\orm\Service::class,
                'action' => Url::to(['/medical/rest/service/update']),
                'validationUrl' => Url::to(['/medical/rest/service/validate-update']),
                'attributes' => [
                    'code',
                    'title',
                    'short_title',
                    'description',
                    [
                        'attribute' => 'speciality_id',
                        'type' => 'dropdown',
                        'value' => function () {
                            return \app\modules\medical\models\orm\Speciality::listAll();
                        },
                    ],
                ]
            ],
            'grid' => [
                'finderClass' => \app\modules\medical\models\finders\ServiceFinder::class,
                'columns' => [
                    'code',
                    'title',
                    'short_title',
                    'description',
                    [
                        'header' => 'Медицинские услуги',
                        'attribute' => 'speciality.title'
                    ],
                ]
            ]
        ]
    ],
    [
        'key' => 'medical-speciality',
        'label' => 'Медицинские специальности',
        'ormClass' => \app\modules\medical\models\orm\Speciality::class,
        'config' => [
            'createForm' => [
                'ormClass' => \app\modules\medical\models\orm\Speciality::class,
                'action' => Url::to(['/medical/rest/speciality/create']),
                'validationUrl' => Url::to(['/medical/rest/speciality/validate-create']),
                'attributes' => [
                    'title',
                    'short_title',
                    'description',
                ]
            ],
            'updateForm' => [
                'ormClass' => \app\modules\medical\models\orm\Speciality::class,
                'action' => Url::to(['/medical/rest/speciality/update']),
                'validationUrl' => Url::to(['/medical/rest/speciality/validate-update']),
                'attributes' => [
                    'title',
                    'short_title',
                    'description',
                ]
            ],
            'grid' => [
                'finderClass' => \app\modules\medical\models\finders\SpecialityFinder::class,
                'columns' => [
                    'title',
                    'short_title',
                    'description',
                ]
            ]
        ]
    ],
    [
        'key' => 'medical-insurance',
        'label' => 'Страховые компании',
        'ormClass' => \app\modules\medical\models\orm\Insurance::class,
        'config' => [
            'createForm' => [
                'ormClass' => \app\modules\medical\models\orm\Insurance::class,
                'action' => Url::to(['/medical/rest/insurance/create']),
                'validationUrl' => Url::to(['/medical/rest/insurance/validate-create']),
                'attributes' => [
                    'title',
                    'short_title',
                    'code',
                ]
            ],
            'updateForm' => [
                'ormClass' => \app\modules\medical\models\orm\Insurance::class,
                'action' => Url::to(['/medical/rest/insurance/update']),
                'validationUrl' => Url::to(['/medical/rest/insurance/validate-update']),
                'attributes' => [
                    'title',
                    'short_title',
                    'code',
                ]
            ],
            'grid' => [
                'finderClass' => \app\modules\medical\models\finders\InsuranceFinder::class,
                'columns' => [
                    'title',
                    'short_title',
                    'code',
                ]
            ]
        ]
    ],
];