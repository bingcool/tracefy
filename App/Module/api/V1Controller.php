<?php
namespace App\Module\api;

use Swoolefy\Core\ZModel;
use Swoolefy\Core\Task\TaskManager;
use Swoolefy\Core\Controller\BController;

class V1Controller extends BController {

	public function span() {
		$post = $this->getRequestParam();
		if(!empty($post)) {
			TaskManager::asyncTask([\App\Taskspan\SpanService::class, 'spanHander'], $post);
		} 
		
	} 
}