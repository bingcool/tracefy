<?php
namespace App\Controller;

use Swoolefy\Core\ZModel;
use Swoolefy\Core\Application;
use Swoolefy\Core\Controller\BController;

class IndexController extends BController {

	/**
	 * 默认查找相对于现在的前xxx分钟的记录
	 */
	const DEFAULT_RELATE_DATE = 5 * 60;

	/**
	 * 最大查找7天前的记录
	 */
	const LIMIT_RELATE_DATE = 7 * 24 * 60;

	/**
	 * 
	 */
	const DEFAULT_LIMIT = 30;

	/**
	 * index 页面首页
	 * @return 
	 */
	public function index() {
		$this->assign('name', 'hello word!');
		$this->display('index.html');
	}

	/**
	 * getTraceIds 获取跟踪的根跟踪和traceId
	 * @return array
	 */
	public function getTraceIds() {
		$params = $this->getRequestParam();
		$start_datetime = $params['start_time'];
		$end_datetime = $params['end_time'];
		$relate_datetime = $params['relate_time'];
		$page = isset($params['page']) ? (int)$params['page'] : 1;
		$limit = isset($params['limit']) ? (int)$params['limit'] : self::DEFAULT_LIMIT;

		if($start_datetime && $end_datetime) {
			$start_datetime = strtotime($start_datetime) * 1000 * 1000;
			$end_datetime = strtotime($end_datetime) * 1000 * 1000;
			$map['timestamp'] = ['$gte'=>$start_datetime, '$lte'=>$end_datetime];
		}else {
			// 前端传递过来的相对时间必须是单位分钟
			if($relate_datetime) {
				if($relate_datetime > self::LIMIT_RELATE_DATE) {
					$relate_datetime = self::LIMIT_RELATE_DATE;
				}
			}else {
				$relate_datetime = self::DEFAULT_RELATE_DATE;
			}
			// 相对现在的时间
			$map['timestamp'] = ['$gte'=>zipkin_timestamp() - $relate_datetime * 60 * 1000 * 1000]; 
		}

		$traceIdsCollection = $this->mongodb->collection('traceIds');
		
		$options['sort'] = ['timestamp' => 1];
		$options['limit'] = $limit;
		$options['skip'] = ($page - 1) * $limit;

		$traceIdInfo = $traceIdsCollection->find($map, $options);
		dump($traceIdInfo);
       	return $this->returnJson($traceIdInfo);
	}

	/**
	 * traceSpans 根据跟踪的traceId获取调用链信息
	 * @return array
	 */
	public function traceSpans() {
		$result = [];
		$map = [];
		$params  = $this->getRequestParam();
		$traceId = $params['traceId'];
		
		$tracespanCollection = $this->mongodb->collection('tracespan');

		$traceId && $map['traceId'] = $traceId;

		$options['sort'] = ['timestamp'=>1];
		 
		if($traceId) {
			// 查询
			$result = $tracespanCollection->find($map, $options);
		}

		$spans = [];
		foreach($result as &$document) {
			$document['spans'] = json_decode($document['spans'], true);
			array_unshift($document['spans'], array_pop($document['spans']));
			foreach($document['spans'] as $k=>$span) {
				array_push($spans, $span);
			}
		}

		$count = count($spans);
		for($i=0; $i<$count; $i++) {
			for($j=$i+1; $j<$count; $j++) {
				if($spans[$i]['id'] == $spans[$j]['id']) {
					$spans[$i]['name'] = $spans[$j]['name'];
					$new_annotations = [
						$spans[$i]['annotations'][0],
						$spans[$j]['annotations'][0],
						$spans[$j]['annotations'][1],
						$spans[$i]['annotations'][1],
					];
					$spans[$i]['annotations'] = $new_annotations;
					unset($spans[$j]);
					break;
				}
			}
		}

		$spans = array_values($spans);

		$spans[0]['parentId'] = 0;

		$modTree = ZModel::getInstance(\App\Common\CategoryTree::class);

		$tree_result = [];

		$tree_result = $modTree->getTree($spans, 0, $level = 0);

		dump($tree_result);

		return $this->returnJson($tree_result);
	}

}