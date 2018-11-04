<?php
namespace App\Module\api;

use Swoolefy\Core\ZModel;
use Swoolefy\Core\Task\TaskManager;
use Swoolefy\Core\Controller\BController;

class V1Controller extends BController {

	public function span() {
		$post = $this->getPostParams();
		if(!$post) {
			$post = json_decode($this->request->rawContent(), true);
		}
		$get = $this->getQueryParams();
		if(!isset($get['isFront'])) {
			// 1代表前端 0代表后端
			$get['isFront'] = 0;
		}
		$data = json_encode([$post, $get]);
		if(!empty($data)) {
			TaskManager::asyncTask([\App\Taskspan\SpanService::class, 'spanHander'], $data);
		} 
		
	} 
}