<?php


namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;
use App\Models\Account;

class User extends Controller
{
    /**
     * 查询用户信息
     * 
     * @return $data
     */
    function index()
    {
        //获取参数
        $params = $this->request()->getRequestParam();
        if(isset($params['id']) || empty($params['id'])){
            $this->writeJson(1,[],'failed');
            return;
        }
        $data = Account::create()->get($params['id']);

        $this->writeJson(0,$data,'success');
    }
}