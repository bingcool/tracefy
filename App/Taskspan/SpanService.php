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
	 * @param    {String}
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
		foreach($data as $k=>$span) {
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
		
		if($insertId) {
			$traceIdCollection = $this->mongodb->collection('traceIds');
			$data = [
						'traceId'=>$this->traceId,
						'timestamp'=>(int)$local_service_span['timestamp'],
						'requestUrl'=>$local_service_span['name'],
						'serverName'=>$local_service_span['annotations'][0]['endpoint']['serviceName']
					];

			$map =  [
						'traceId'=>$this->traceId,
						'timestamp'=>['$gt'=>(int)$local_service_span['timestamp']]
					];

			$result = $traceIdCollection->findOne($map, ['projection'=>['traceId'=>1]]);
			if($result) {
				$traceIdCollection->updateOne($map, ['$set'=>$data]);
			}else {
				$findResult = $traceIdCollection->findOne(['traceId'=>$this->traceId], ['projection'=>['traceId'=>1]]);
				// 如果该文档不存在，则创建
				if(!$findResult) {
					$insertId = $traceIdCollection->insertOne($data);
				}
			}
		}
	}

}