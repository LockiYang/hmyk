<?php

namespace app\index\controller;

use app\common\controller\IndexCommon;
use think\Db;
use think\Log;

/**
 * 通过扫码方式提交支付订单
 */
class Scan extends IndexCommon
{

    protected $noNeedRight = ['*'];
    protected $noNeedLogin = ['*'];

    protected $layout = '';


    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {

        $params = json_decode(base64_decode($this->request->param('data')), true);

        Log::info('start pay: ' . json_encode($params));

        if ($params['hm_type'] == 'goods') {
            $order = db::name('goods_order')->where(['out_trade_no' => $params['out_trade_no']])->find();
        }
        if ($params['hm_type'] == 'recharge') {
            $order = db::name('recharge_order')->where(['out_trade_no' => $params['out_trade_no']])->find();
        }

        $plugin_path = ROOT_PATH . "content/" . $this->scan_template;
        $info = include_once "{$plugin_path}/setting.php";

        //        var_dump($order);die;

        //        echo '<pre>'; print_r($params);die;

        $this->assign([
            'order' => $order,
            'pay_code' => urlencode($params['qr_code']),
            'plugin_info' => $info,
            'params' => $params,
            'user' => $this->user
        ]);
        return view($this->scan_template . '/index');
    }
}
