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

namespace Swoolefy\Core\Controller;

use Swoolefy\Core\Controller\BController;

class NotFound extends BController {
	
	/**
	 * page404 默认404
	 * @return   string
	 */
	public function page404() {
		return $this->response->end('<body style="text-align:center"><div style="margin:300px auto; width:800px; height:100px;"><h1>SORRY!  404 NOT FOUND!</h1></div></body>');
	}
}