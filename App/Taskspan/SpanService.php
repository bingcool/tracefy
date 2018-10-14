<?php
namespace App\Taskspan;

use Swoolefy\Core\Object;
use Swoolefy\Core\Swfy;
use Swoolefy\Core\Application;
use Swoolefy\Core\Task\TaskController;

class SpanService extends TaskController {

	/**
	 * $traceId 请求追踪id
	 * @var null
	 */
	public $traceId = null;

	/**
	 * $local_service_span 每个服务的sr的本地span
	 * @var null
	 */
	public $local_service_span = null;

	/**
	 * __construct 
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * spanHander 处理每个server发送过来的span
	 * @param    $data
	 * @return   void
	 */
	public function spanHander($data) {
		$spans = [];
		list($post, $get) = json_decode($data, true);
		foreach($post as $k=>$span) {
			// 获取每个服务的本地的span
			if($span['annotations'][0]['value'] == 'sr') {
				$local_service_span = $span;
			}
			$spans[$span['id']] = $span;
		}
		$this->traceId = $local_service_span['traceId'];

		$tracespanCollection = $this->mongodb->collection('tracespan');

		$insertData = [
			'traceId' => $this->traceId,
			'spans' => json_encode($spans),
			'spanId' => $local_service_span['id'],
			'requestUrl' => $local_service_span['name'],
			'timestamp' => (int)$local_service_span['timestamp']
		];

		$insertId = $tracespanCollection->insertOne($insertData);
		
		// 最前端的服务,即根服务
		if($insertId && $get['isFront']) {
			$traceIdCollection = $this->mongodb->collection('traceIds');
			$traceIdInfo = [
						'traceId'=>$this->traceId,
						'timestamp'=>(int)$local_service_span['timestamp'],
						'requestUrl'=>$local_service_span['name'],
						'serverName'=>$local_service_span['annotations'][0]['endpoint']['serviceName'],
						'datetime' => strtotime('now')
					];
			$insertId = $traceIdCollection->insertOne($traceIdInfo);
		}
	}

}