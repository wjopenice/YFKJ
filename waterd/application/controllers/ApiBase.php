<?php
/**
 * Date: 2018\4\2 0002 11:26
 */

class ApiBaseController extends Rest
{
    // 网站的userId
    public $uid;
    /*网站的token*/
    protected $token;
    /**签名*/
    protected $sign;


    protected function init()
    {
        parent::init();
        $this->getUid();
        /*Action路由*/
        $action = $this->_request->getActionName();
        // 验证签名
        $action = str_replace('POST_', '', $action);
        $action = str_replace('GET_', '', $action);
        $whiteList = ['avatar', 'wechat'];
        if (!in_array($action, $whiteList))
        {
            $this->sign = $this->request->header('sign');
            $this->checkSign(input('param.'));
        }
    }

    protected function checkLogin()
    {

        $authKey = $this->request->header('token');
        empty($authKey) && $this->response(10001, '请先登录');
        $userInfo = \think\Db::name('user')->where('token', $authKey)->field('uid,state')->find();
        if (!($userInfo) || ($userInfo['state'] > 0)) {
            return $this->response(10002, '登录超时,请重新登录');
        }
        $this->token = $authKey;
        $this->uid = $userInfo['uid'];
        cache('auth_' . $authKey, $userInfo, \beans\CacheTime::LOGIN_TIME);
    }

    protected function getUid()
    {

        $authKey = $this->request->header('token');
        if (empty($authKey))
        {
            return '';
        }
        $userInfo = \think\Db::name('user')->where('token', $authKey)->field('uid,state')->find();
        if (!($userInfo) || ($userInfo['state'] > 0)) {
            return '';
        }
        $this->uid = $userInfo['uid'];

        $this->addActiveUser();
    }

    /** 验证签名 */
    public function checkSign($param = [])
    {
        foreach ($param as $key => $item)
        {
            if (strpos($key,'/api/') !==false)
            {
                unset($param[$key]);
            }
        }
        $sign = bulidApiSign($param);
        if ($sign != $this->sign)
        {
            $this->error('验签失败');
        }
    }

    /** 增加日活跃用户 */
    public function addActiveUser()
    {
        $active = \think\Db::name('active')->whereTime('create_time', 'today')->find();
        if (empty($active))
        {
            $active_add['users'] = $this->uid;
            $active_add['create_time'] = time();
            $active_add['update_time'] = time();
            \think\Db::name('active')->insert($active_add);

        } else
        {
            /** 判断是否包含 */
            $user_arr = explode(',', $active['users']);
            if (!in_array($this->uid, $user_arr))
            {
                $active_up['users'] = $active['users'].','.$this->uid;
                $active_up['update_time'] = time();
                \think\Db::name('active')->where('id', $active['id'])->update($active_up);
            }

        }
    }
}
