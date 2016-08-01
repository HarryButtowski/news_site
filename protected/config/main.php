<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Сайт новостей',

	// preloading 'log' component
	'preload'=>array(
            'log',
            //'debug'
            ),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
                'ext.aii.behaviors.*'
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'admin00',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		
	),
    
        'defaultController'=>'news/index',

	// application components
	'components'=>array(

		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
                'cache' => array(
                        'class' => 'CDbCache',
                ),
                'debug' => array(
                    'class' => 'ext.yii2-debug.Yii2Debug', // manual installation
                ),

		// uncomment the following to enable URLs in path-format
                
                'urlManager' => array(
                    'urlFormat' => 'path',
                    'showScriptName'=>false,
                    'urlSuffix' => '',
                    'rules' => array(
                        'gii'=>'gii',
                        'gii/<controller:\w+>'=>'gii/<controller>',
                        'gii/<controller:\w+>/<action:\w+>'=>'gii/<controller>/<action>',
                        ''=>'news/index',
                        'news/create'=>'news/create',
                        'news/update/<id:\d+>'=>'news/update',
                        'news/delete/<id:\d+>'=>'news/delete',
                        'news/admin'=>'news/admin',
                        'category/create'=>'category/create',
                        'category/update/<id:\d+>'=>'category/update',
                        'category/delete/<id:\d+>'=>'category/delete',
                        'category/admin'=>'category/admin',
                        'category/<category:\w+>'=>'news/index',
                        'admin'=>'site/login',
                        '/admin/logout'=>'/site/logout',
                        '<slug:.*?>'=>'news/view',
                    ),
                ),

		// database settings are configured in database.php
		'db'=>require(dirname(__FILE__).'/database.php'),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>YII_DEBUG ? null : 'news/error',
		),

		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
                                    
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
                    ),

	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
                'postsPerPage'=>3,
                // maximum number of comments that can be displayed in recent comments portlet
                'recentCommentCount'=>10,
                // whether post comments need to be approved before published
                'commentNeedApproval'=>false,
	),
);
