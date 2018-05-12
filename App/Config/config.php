<?php
return [
	'route_model' => 1, //1代表pathinfo,2代表普通url模式
	'default_route' => 'Index/index',
	'app_namespace' => 'App',
	'not_found_function' => ['App\Controller\NotFound','page404'],
	'components' => [
		'view' => [
			'class' => 'Swoolefy\Core\View',
		],

		'log' => [
			'class' => 'Swoolefy\Tool\Log',
		],

		'mailer' =>[
			'class'=> 'Swoolefy\Tool\Swiftmail',
			'smtpTransport' => [
				"server_host"=>"smtp.163.com",
				"port"      =>25,
				"security"  =>null,
				"user_name" =>"13560491950@163.com",
				"pass_word" =>"2xxxxxxxxxxxx",
			],
		],

		'mongodb'=>[
			'is_delay' => true,//延迟创建实例，请求时候再创建
			'class'=>'Swoolefy\Core\Mongodb\MongodbModel',
			'database'=>'mytest',
			'uri'=>'mongodb://123.207.19.149:27017',
			'driverOptions'=> [
					'typeMap' => [ 'array' => 'MongoDB\Model\BSONArray', 'document' => 'MongoDB\Model\BSONArray', 'root' => 'MongoDB\Model\BSONArray']
			],
			'_id' => 'unid'
		],

	],
];