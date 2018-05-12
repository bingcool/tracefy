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
 
namespace Swoolefy\Core\Process;

use Swoolefy\Core\Swfy;
use Swoolefy\Core\Object;
use Swoolefy\Core\Hook;
use Swoolefy\Core\ZModel;
use Swoolefy\Core\Application;

class SwooleProcess extends Object {

	/**
	 * $config 当前应用层的配置 
	 * @var null
	 */
	public $config = null;

	/**
 	 * $ExceptionHanderClass 异常处理类
 	 * @var string
 	 */
 	private $ExceptionHanderClass = 'Swoolefy\\Core\\SwoolefyException';

 	/**
	 * __construct
	 * @param $config 应用层配置
	 */
	public function __construct(array $config=[]) {
		// 将应用层配置保存在上下文的服务
		$this->config = Swfy::$appConfig = $config;
		Application::$app = $this;
		// 注册错误处理事件
		$protocol_config = Swfy::getConf();
		if(isset($protocol_config['exception_hander_class']) && !empty($protocol_config['exception_hander_class'])) {
			$this->ExceptionHanderClass = $protocol_config['exception_hander_class'];
		}
		register_shutdown_function($this->ExceptionHanderClass.'::fatalError');
      	set_error_handler($this->ExceptionHanderClass.'::appError');
	}

 	/**
	 * afterRequest 请求结束后注册钩子执行操作
	 * @param	mixed   $callback 
	 * @param	boolean $prepend
	 * @return	void
	 */
	public function afterRequest(callable $callback, $prepend = false) {
		if(is_callable($callback)) {
			Hook::addHook(Hook::HOOK_AFTER_REQUEST, $callback, $prepend);
		}else {
			throw new \Exception(__NAMESPACE__.'::'.__function__.' the first param of type is callable');
		}
		
	}

	/**
	 * end
	 * @return  
	 */
	public function end() {
		// call hook callable
		Hook::callHook(Hook::HOOK_AFTER_REQUEST);
		ZModel::$_model_instances = [];
		// 销毁某些组件
		self::clearComponent(self::$_destroy_components);
	}

 	use \Swoolefy\Core\ComponentTrait;
}