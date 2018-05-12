<?php
namespace App\Taskspan;

use Swoolefy\Core\Object;
use Swoolefy\Core\Swfy;
use Swoolefy\Core\Application;
use Swoolefy\Core\Task\TaskController;

class SpanService extends TaskController {

	public $traceId = null;

	public $local_service_span = null;

	public function __construct() {
		parent::__construct();
		
	}
	
	/**
	 * spanHander
	 * @Author   huangzengbing
	 * @DateTime 2018-05-03
	 * @param    {String}
	 * @param    $data
	 * @return   
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

		$collection = $this->mongodb->collection('tracespan');

		$insertData = [
			'traceId' => $this->traceId,
			'spans' => json_encode($spans),
			'requestUrl' => $local_service_span['name'],
			'timestamp' => $local_service_span['timestamp']
		];

		$inserId = $collection->insertOne($insertData);
	}

}