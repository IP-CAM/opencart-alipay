<?php
class ControllerPaymentAlipay extends Controller {
	public function index() {
		$data['text_loading'] = $this->language->get('text_loading');
		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['continue'] = $this->url->link('checkout/success');
		$data['submit']  = $this->url->link('payment/alipay/submit', array('order_id' => $this->session->data['order_id']) ););
		//$data['submit'] .= '&order_id=' . $this->session->data['order_id'];

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/alipay.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/template/payment/alipay.tpl', $data);
		} else {
			return $this->load->view('default/template/payment/alipay.tpl', $data);
		}
	}

	public function submit(){
			$orderDetails = $this->getOrderDetails();
			/* *
			 * 功能：即时到账交易接口接入页
			 * 版本：3.3
			 * 修改日期：2012-07-23
			 * 说明：
			 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
			 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
	
			 *************************注意*************************
			 * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
			 * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
			 * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
			 * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
			 * 如果不想使用扩展功能请把扩展功能参数赋空值。
			*/

			//require_once(DIR_SYSTEM . 'helper/alipay/alipay.config.php'); 
			$alipay_config = $this->getconfig();
			require_once(DIR_SYSTEM . 'helper/alipay/lib/alipay_submit.class.php'); 
			 
	
			/**************************请求参数**************************/
			
			//支付服务
			$service = $this->config->get('config_template') == 'm' ?
			           'alipay.wap.create.direct.pay.by.user' :
			           'create_direct_pay_by_user';

			//支付类型
			$payment_type = "1";
			//必填，不能修改
			//服务器异步通知页面路径
			//$notify_url = "http://商户网关地址/create_direct_pay_by_user-PHP-UTF-8/notify_url.php";
			$notify_url = $this->url('alipay/notify');// . "/payment_gateway/alipay/notify_url.php";
			//需http://格式的完整路径，不能加?id=123这类自定义参数
	
			//页面跳转同步通知页面路径
			//$return_url = "http://商户网关地址/create_direct_pay_by_user-PHP-UTF-8/return_url.php";
			$return_url = $this->url('account/order');//HTTP_SERVER . "/index.php?route=account/order";
			//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
	
			//商户订单号
			//$out_trade_no = $_POST['WIDout_trade_no'];
			$out_trade_no = $orderDetails['order_id'] . 'T' . time();
			//商户网站订单系统中唯一订单号，必填
			//订单名称
			//$subject = $_POST['WIDsubject'];
			$subject = "";
			foreach($orderDetails['products'] as $product){
					$subject .= $product['name'] . "+";
			}
			$subject = substr($subject,0,count($subject)-2);
			//必填

			//付款金额
			$total_fee = round($orderDetails['total'], 2);
			//必填
	
			//商品展示地址
			$show_url = $this->url->link('account/order'); 
			//需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html
	
			//订单描述
			$body = "Description";

			//防钓鱼时间戳
			$anti_phishing_key = "";
			//若要使用请调用类文件submit中的query_timestamp函数
	
			//客户端的IP地址
			$exter_invoke_ip = '';
			//非局域网的外网IP地址，如：221.0.0.1
			
			/* 手机支付信息 */
			$it_b_pay = '';
			//选填

			//钱包token
			$extern_token = '';
			//选填
			
	
	
			/************************************************************/
	
			//构造要请求的参数数组，无需改动
			$parameter = array(
					"service"           => $service,
					"partner"           => trim($alipay_config['partner']),
					"seller_id"         => trim($alipay_config['partner']),
					"seller_email"      => trim($alipay_config['seller_email']),
					"payment_type"      => $payment_type,
					"notify_url"        => $notify_url,
					"return_url"        => $return_url,
					"out_trade_no"      => $out_trade_no,
					"subject"           => $subject,
					"total_fee"         => $total_fee,
					"body"              => $body,
					"show_url"          => $show_url,
					"anti_phishing_key"	=> $anti_phishing_key,
					"exter_invoke_ip"   => $exter_invoke_ip,
					"it_b_pay"          => $it_b_pay,     //选填
					"extern_token"      => $extern_token, //选填
					"_input_charset"    => trim(strtolower($alipay_config['input_charset']))
			);
 
			$order_id = $this->request->get['order_id'];
			if($order_id != 0 && $orderDetails['order_status_id'] == 0) {
				$this->load->model('customer/balance');
				$MCB = $this->model_customer_balance;
				$MCB->construct();
				
				

				$this->load->model('checkout/order');
				$this->model_checkout_order->updateOrderStatus($order_id, 1);
				 
			}
	
			//建立请求
			$alipaySubmit = new AlipaySubmit($alipay_config);
			$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
			 
			$data['html_text'] = $html_text;
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/payment/alipay_submit.tpl',$data));
	}

	public function notify(){

		$json = array();

		$PaidCode = '2';

		$alipay_config = $this->getconfig();
		require_once(DIR_SYSTEM . 'helper/alipay/lib/alipay_notify.class.php'); 
	    
	    $this->load->model('checkout/order');


	    if($this->request->post){

	    	$alipayNotify = new AlipayNotify($alipay_config);
            if($alipayNotify->verifyNotify()){

            }else{
            	//

            	$json['error'] = 'no callback data';
            }
	    }else{

	    	$json['error'] = 'no callback data';

	    }

	    $out_trade_no = $this->request->post['out_trade_no']; //商户订单号
		$TIndex = stripos($out_trade_no, 'T');
		$OrderID = substr($out_trade_no, 0, $TIndex); //截取订单号

		$OrderInfo = null; $Cmd = '';

		if(!$json){

			if($OrderID) {

				$order_info = $this->model_checkout_order->getOrder($order_id);
				   
				if($order_info) {

					if($OrderInfo['order_status_id'] == $PaidCode) {
						 
						$json['error']  = 'order has payed ';
					}
					 
				} else {
					 
					$json['error']  = 'order is null';
				}
			}
			else {
				$json['error']  = 'orderId is null';
			}

 
		}

		if(!$json){

			$trade_status = $this->request->post['trade_status'];
            
            if($trade_status == 'TRADE_FINISHED') {
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//如果有做过处理，不执行商户的业务程序
						
				//注意：
				//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}else if($trade_status == 'TRADE_SUCCESS'){
                $_data = array(
                	'type' => 'alipay', 
                	'order_id' => $OrderID,
                	'trade_no' => $this->request->post['trade_no'],
                	'discount' => $this->request->post['discount'],
                	'payment_type' => $this->request->post['payment_type'],
                    'subject' => $this->request->post['subject'],
                    'buyer_email' => $this->request->post['buyer_email'],
                    'gmt_create' => $this->request->post['gmt_create'],
                    'notify_type' => $this->request->post['notify_type'],
                    'quantity' => $this->request->post['quantity'],
                    'seller_id' => $this->request->post['seller_id'],
                    'notify_time' => $this->request->post['notify_time'],
                    'body' => $this->request->post['body'],
                    'trade_status' => $this->request->post['trade_status'],
                    'is_total_fee_adjust' => $this->request->post['is_total_fee_adjust'],
                    'total_fee' => $this->request->post['total_fee'],
                    'gmt_payment' => $this->request->post['gmt_payment'],
                    'price' => $this->request->post['price'],
                    'buyer_id' => $this->request->post['buyer_id'],
                    'notify_id' => $this->request->post['notify_id'],
                    'use_coupon' => $this->request->post['use_coupon'],
                    'sign_type' => $this->request->post['sign_type'],
                    'sign' => $this->request->post['sign']

                	);


                $alipay_order_id = $this->model_payment_alipay->addOrder($_data);
                
                $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('globalpay_order_status_success_unsettled_id'), $message, false);
				
			}
		}

		if($json){

			$data['result'] = 'fail';

		}else{


			$data['result'] = 'success';

		}

 
		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/alipay_response.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/payment/alipay_response.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('default/template/payment/alipay_response.tpl', $data));
		}

		
	}

	public function getOrderDetails() {
			$order_id = $this->request->get['order_id'];
			$this->load->model("account/order");
			$order_data['products'] = $this->model_account_order->getOrderProducts($order_id);
			$order = $this->model_account_order->getPreOrder($order_id);
			$order_data['total'] = $order['total'];
			$order_data['order_id'] = $order['order_id'];
			$order_data['order_status_id'] = $order['order_status_id'];
			return $order_data;
	}
 
	public function confirm(){
			if ($this->session->data['payment_method']['code'] == 'alipay') {
				$this->load->model('checkout/order');
				$paystatus = $this->model_payment_alipay->getOrderStatus($this->session->data['order_id']);
				if($paystatus==0){	
					$this->model_checkout_order->updateOrderStatus($this->session->data['order_id'], 1); 
				}
			}
	} 

	private function getconfig(){


		/* *
		 * 配置文件
		 * 版本：3.3
		 * 日期：2012-07-19
		 * 说明：
		 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
		 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
			
		 * 提示：如何获取安全校验码和合作身份者id
		 * 1.用您的签约支付宝账号登录支付宝网站(www.alipay.com)
		 * 2.点击“商家服务”(https://b.alipay.com/order/myorder.htm)
		 * 3.点击“查询合作者身份(pid)”、“查询安全校验码(key)”
			
		 * 安全校验码查看时，输入支付密码后，页面呈灰色的现象，怎么办？
		 * 解决方法：
		 * 1、检查浏览器配置，不让浏览器做弹框屏蔽设置
		 * 2、更换浏览器或电脑，重新登录查询。
		 */

		$alipay_config = array();
		 
		//↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
		//合作身份者id，以2088开头的16位纯数字
		$alipay_config['partner']		= '20881111111111111';

		//收款支付宝账号
		$alipay_config['seller_email']	=  'billing@test.cn';

		//安全检验码，以数字和字母组成的32位字符
		$alipay_config['key']			= '12345678901234567890123456789012';


		//↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑


		//签名方式 不需修改
		$alipay_config['sign_type']    = strtoupper('MD5');

		//字符编码格式 目前支持 gbk 或 utf-8
		$alipay_config['input_charset']= strtolower('utf-8');

		//ca证书路径地址，用于curl中ssl校验
		//请保证cacert.pem文件在当前文件夹目录中
		$alipay_config['cacert']    = getcwd().'\\cacert.pem';

		//访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
		$alipay_config['transport']    = 'http';

		return $alipay_config;
	}
}
