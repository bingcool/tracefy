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

namespace Swoolefy\Udp;

include_once SWOOLEFY_CORE_ROOT_PATH.'/EventInterface.php';

use Swoolefy\Core\Swfy;
use Swoolefy\Udp\UdpServer;
use Swoolefy\Core\UdpEventInterface;

abstract class UdpEventServer extends UdpServer implements UdpEventInterface {
	/**
	 * __construct 初始化
	 * @param array $config
	 */
	public function __construct(array $config=[]) {
		// 获取当前服务文件配置
		$config = array_merge(
				include(__DIR__.'/config.php'),
				$config
			);
		parent::__construct($config);
	}

	public abstract function onWorkerStart($server, $worker_id);

	/**
	 * onPack 
	 * @param    ovject $server
	 * @param    mixed $data
	 * @param    array $clientInfo
	 * @return    
	 */
	public function onPack($server, $data, $clientInfo) {
		swoole_unpack(self::$service)->run($data, $clientInfo);
	}

	/**
	 * onTask 
	 * @param    object  $server
	 * @param    int     $task_id
	 * @param    int     $from_worker_id
	 * @param    mixed   $data
	 * @return   
	 */
	public function onTask($server, $task_id, $from_worker_id, $data) {
		list($callable, $taskData, $clientInfo) = $data;		
		swoole_unpack(self::$service)->run([$callable, $taskData], $clientInfo);
		return ;
	}

	public abstract function onFinish($server, $task_id, $data);

	/**
	 * onPipeMessage 
	 * @param    object  $server
	 * @param    int     $src_worker_id
	 * @param    mixed   $message
	 * @return   void
	 */
	public abstract function onPipeMessage($server, $from_worker_id, $message);

}
