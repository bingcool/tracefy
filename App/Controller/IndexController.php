<?php
namespace App\Controller;

use Swoolefy\Core\Application;
use Swoolefy\Core\Controller\BController;

class IndexController extends BController {

	public function index() {
		$this->assign('name', 'hello word!');
		$this->display('index.html');
	}

	public function getSpans() {
		$collection = $this->mongodb->collection('tracespan');
		$result = $collection->find(
			[
				'traceId'=>'52dad35f2aff6389736aeaba41312464',
				''
			]
		);
		foreach($result as $k=>&$row) {
			$row['spans'] = json_decode($row['spans'], true);
		}

		dump($result);

	}

	public function test1() {
		$collection = $this->mongodb->collection('tracespan');
		$result = $collection->distinct('traceId');
		dump($result);
	}
}