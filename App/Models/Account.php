<?php

namespace App\Models;

use EasySwoole\ORM\AbstractModel;

/**
 * 用户模型
 * Class User
 */
class Account extends AbstractModel
{
    /**
     * 用户模型
     * 
     * @var string
     */
    protected  $tableName = 't_account';

    /**
     * 数据库连接名
     * 
     * @var string
     */
    protected $connectionName = 'default';
    /**
     * 获取用户信息
     */
    public function getUserInfo(){
        $res = static::create()->get(['id' => 11]);
        return $res;
    }

    
}
