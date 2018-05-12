<?php
/**
+----------------------------------------------------------------------
| swoolefy framework bases on swoole extension development, we can use it easily!
+----------------------------------------------------------------------
| Licensed ( https://opensource.org/licenses/MIT )
+----------------------------------------------------------------------
| Author: bingcool <bingcoolhuang@gmail.com || 2437667702@qq.com>
+----------------------------------------------------------------------
*/

namespace Swoolefy\Core;

use Swoolefy\Core\Swfy;

class ServerManager {

	use \Swoolefy\Core\SingleTrait, \Swoolefy\Core\ServiceTrait;

	/**
	 * $server_port 监听port对象
	 * @var array
	 */
	private $server_ports = [];

	/**
	 * __construct 
	 */
	public function __construct() {

	}

	/**
	 * addListener 设置port实例
	 * @param string $host 
	 * @param int    $port 
	 * @param type   $type 
	 */
	public function addListener(string $host, $port, $type = SWOOLE_SOCK_TCP) {
		$port = (int)$port;
		$server_port = Swfy::getServer()->addListener($host, $port, $type);
		if(is_object($server_port)) {
			$this->server_ports[$port] = $server_port;
			return $server_port;
		}else {
			throw new \Exception("addListener $port failed", 1);
		}
	}

	/**
	 * getListener 获取port实例
	 * @param  int $port
	 * @return mixed
	 */
	public function getListener($port) {
		$port = (int)$port;
		if(isset($this->server_ports[$port])) {
			return $server_port;
		}
		return false;
	}

	/**
	 * stopWorker 
	 * @see https://wiki.swoole.com/wiki/page/547.html
	 * @param  int|integer  $worker_id
	 * @param  bool|boolean $waitEvent
	 * @return 
	 */
	public function stopWorker(int $worker_id = -1, bool $waitEvent = false) {
		if(version_compare(SWOOLE_VERSION, '1.9.19', '<')) {
			Swfy::getServer()->stop($worker_id);
			return true;
		}
		Swfy::getServer()->stop($worker_id, $waitEvent);
		return true;
	}

	/**
	 * getClientInfo 
	 * @see https://wiki.swoole.com/wiki/page/p-connection_info.html  
	 * @param  int          $fd       
	 * @param  int          $extraData
	 * @param  bool|boolean $ignoreError
	 * @return 
	 */
	public function getClientInfo(int $fd, int $extraData, bool $ignoreError = false) {
		return Swfy::getServer()->getClientInfo($fd, $extraData, $ignoreError);
	}
	
	/**
	 * __toString
	 * @return string
	 */
	public function __toString() {
		return get_called_class();
	}


}