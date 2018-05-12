<?php
namespace App\Module\api;

use Swoolefy\Core\ZModel;
use App\Taskspan\SpanService;
use Swoolefy\Core\Task\TaskManager;
use Swoolefy\Core\Controller\BController;

class V1Controller extends BController {

	public function span() {
		if(!empty($_POST)) {
			TaskManager::asyncTask([SpanService::class, 'spanHander'], $_POST);
		} 
		
	} 
}