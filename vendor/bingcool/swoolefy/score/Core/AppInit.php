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

use Swoolefy\Core\Init;
use Swoolefy\Core\Application;

class AppInit extends Init {
	/**
	 * _init 初始化一下超全局变量,兼容php-fpm的web模式
	 */
	public static function _init() {
		parent::_init();
		// init 
		$_POST = $_GET = $_REQUEST = $_COOKIE = $_SESSION = $_FILES = [];
		//请求对象
		$request = Application::$app->request;
		self::resetServer($request);
		self::resetPost($request);
		self::resetGet($request);
		self::resetCookie($request);
		self::resetFile($request);
		// 设置在最后执行
		self::resetRequest($request);
	}

	/**
	 * resetServer 重置SERVER超全局数组
	 * @param  object  $request 请求对象
	 * @return void
	 */
	public static function resetServer($request) {
		foreach($request->server as $p=>$val) {
			$upper = strtoupper($p);
			// 判断是否已经是大写，不然异步任务进程在这里会将原来大写的删除
			if($upper != $p) {
				$request->server[$upper] = $val;
				unset($request->server[$p]);
			}	
		}
		foreach ($request->header as $key => $value) {
            $_key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
            $request->server[$_key] = $value;
        }

        $_SERVER = $request->server = array_merge($_SERVER, $request->server);
	}

	/**
	 * resetPost 重置POST超全局数组
	 * @param  $request 请求对象
	 * @return void
	 */
	public static function resetPost($request) {
		if(isset($request->post)) {
			$_POST = array_merge($_POST, $request->post);
		}
	}

	/**
	 * resetGet 重置GET超全局数组
	 * @param  $request 请求对象
	 * @return void
	 */
	public static function resetGet($request) {
		if(isset($request->get)) {
			$_GET = array_merge($_GET, $request->get);
		}
	}

	/**
	 * resetCookie 重置COOKIE超全局数组
	 * @param  $request 请求对象
	 * @return void
	 */
	public static function resetCookie($request) {
		if(isset($request->cookie)) {
			$_COOKIE = array_merge($_COOKIE, $request->cookie);
		}
			
	}

	/**
	 * resetFile 重置FILE超全局数组
	 * @param  $request 请求对象
	 * @return void
	 */
	public static function resetFile($request) {
		if(isset($request->fiels)) {
			$_FILES = array_merge($_FILES, $request->fiels);
		}
	}

	/**
	 * resetRequest 重置REQUEST超全局数组
	 * @param  $request 请求对象
	 * @return void
	 */
	public static function resetRequest($request) {
		$_REQUEST = array_merge($_POST, $_GET, $request->cookie);
	}
}
