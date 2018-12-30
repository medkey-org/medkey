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
];