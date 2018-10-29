<?php
namespace App\Common;

class CategoryTree {
	
	/**
	 * $tree_result 树结构结果
	 * @var array
	 */
	public $tree_result = [];

	/**
	 * getTree 
	 * @param    array    $data
	 * @param    array    $parent_id
	 * @param    integer  $level
	 * @return   mixed
	 */
	public function getTree($data, $parent_id = 0, $level = 0) {
		if(empty($data)) {
			foreach($data as $k=>$document) {
				$document['level'] = $level;
				if(strcmp($document['parentId'], $parent_id) == 0) {
					$this->tree_result[] = $document;
					unset($data[$k]);
					$this->getTree($data, $document['id'], $level+1);
				}
			}
		}
		return $this->tree_result;
	}

	/**
	 * __destruct 销毁
	 */
	public function __destruct() {
		$this->tree_result = [];
	}
}