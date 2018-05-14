<?php
namespace App\Controller;

use Swoolefy\Core\Application;
use Swoolefy\Core\Controller\BController;

class IndexController extends BController {

	const DEFAULT_RELATE_DATE = 5;
	const LIMIT_RELATE_DATE = 7;

	/**
	 * index description
	 * @return [type] [description]
	 */
	public function index() {
		$this->assign('name', 'hello word!');
		$this->display('index.html');
	}

	/**
	 * getTraceIds 获取跟踪的根跟踪和traceId
	 * @return [type] [description]
	 */
	public function getTraceIds() {
		$traceIdsCollection = $this->mongodb->collection('traceIds');
		$map = [
			'timestamp'=>['$gte'=> zipkin_timestamp() - 3 * 3600 * 1000 * 1000]
		];
		$options = [
			'sort'=>['timestamp' => 1]
		];
		$traceIdInfo = $traceIdsCollection->find($map, $options);
		dump($traceIdInfo);
       	return $traceIdInfo;
	}

	/**
	 * traceSpans 根据跟踪的traceId获取调用链信息
	 * @return [type] [description]
	 */
	public function traceSpans() {
		$result = [];
		$traceId = $_GET['traceId'];
		$start_datetime = $_GET['start_time'];
		$end_datetime = $_GET['end_time'];
		$relate_datetime = $_GET['relate_time'];

		$map = [];
		$tracespanCollection = $this->mongodb->collection('tracespan');

		$traceId && $map['traceId'] = $traceId;

		if($start_datetime && $end_datetime) {
			$start_datetime = strtotime($start_datetime) * 1000 * 1000;
			$end_datetime = strtotime($end_datetime) * 1000 * 1000;
			$map['timestamp'] = ['$gte'=>$start_datetime, '$lte'=>$end_datetime];
		}else {
			// 小时数
			if($relate_datetime) {
				if($relate_datetime > self::LIMIT_RELATE_DATE) {
					$relate_datetime = self::LIMIT_RELATE_DATE;
				}
			}else {
				$relate_datetime = self::DEFAULT_RELATE_DATE;
			}
			// 相对现在的时间
			$map['timestamp'] = ['$gte'=>zipkin_timestamp()- $relate_datetime * 3600 * 1000 * 1000]; 
		}

		$options['sort'] = ['timestamp'=>1];
		 
		if($traceId) {
			// 查询
			$result = $tracespanCollection->find($map, $options);
		}

		foreach($result as &$document) {
			$document['spans'] = json_decode($document['spans'], true);
		}

		$root_span = $result[0];

		$children_span = array_splice($result, 1);

		$span_ids = array_keys($root_span['spans']);

		$res = [];

		foreach($span_ids as $k=>$span_id) {
			foreach($children_span as $p=>$document) {
				// 找到对应的子级
				if($document['spanId'] == $span_id) {
					
				}
			}
		}

		dump($children_span);
	}

	public function test1() {
		$collection = $this->mongodb->collection('tracespan');
		$pipe = [
			[
				'$match'=>[
					'timestamp'=>['$gte'=> zipkin_timestamp() - 3 * 3600 * 1000 * 1000]
				]
			],
			[
				'$group'=>[
					'_id'=>'$traceId',
				]
			]
		];
		$result = $collection->aggregate($pipe);
		foreach ($result as $value) {
           dump($value);
        }
	}
}