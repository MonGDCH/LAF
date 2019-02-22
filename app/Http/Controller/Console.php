<?php
namespace App\Http\Controller;

use Laf\Controller;
use App\Model\Account;

class Console extends Controller
{
    /**
     * 用户模型
     *
     * @var [type]
     */
    protected $accountModel;

    /**
     * 构造方法
     */
    public function __construct()
    {
        parent::__construct();

        $this->accountModel = Account::instance();
    }

    /**
     * 查看信息
     *
     * @return [type] [description]
     */
    public function info()
    {
        $list = $this->accountModel->where('uid', __USERID__)->field('id, ip, port, encrypt, total_flow, use_flow, status, account, update_time')->select();

        return $this->successJson('Success', $list);
    }

    /**
     * 查看密码
     *
     * @return [type] [description]
     */
    public function getPwd()
    {
        $idx = $this->request->post('idx');
        if(empty($idx) || !is_numeric($idx) || !is_int($idx + 0) || $idx < 1){
            return $this->errorJson('Params invalid');
        }

        $info = $this->accountModel->where('uid', __USERID__)->where('id', $idx)->field('account, password')->find();
        if(!$info){
            return $this->errorJson('account not exists');
        }

        return $this->successJson('Success', $info);
    }

}