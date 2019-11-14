<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */

class IndexController extends ApiBaseController
{

   /** 首页 获取官网财富树  */
   public function Get_homeTreeAction()
   {
       $treeModel = new TreeModel();
       $tree = $treeModel->getTreeInfo(1);
       $this->success($tree);
   }

   /** 意见反馈 */
   public function Post_optionAction()
   {
       $option['content'] = input('content');
       $option['email'] = input('email');

       $rule = [
           'content'  => 'require|min:6|max:100',
           'email' => 'require|email',
       ];

       $msg = [
           'content.require' => '意见内容必须填写',
           'content.min'     => '意见内容最少6位字符',
           'content.max'     => '意见内容最多100位字符',
           'email.require'   => '邮箱必须填写',
           'email'  => '邮箱格式不正确',
       ];

       $validate = new Validate($rule, $msg);
       $result = $validate->check($option);
       if (!$result)
       {
           $this->success(['error' => 1, 'msg' => $validate->getError()]);
       }

       $option['uid'] = $this->uid;
       $option['create_time'] = time();
       $res = \think\Db::name('opinion')->insert($option);
       $this->success(['error' => 0]);
   }

   /** 版本控制与到账通知 */
   public function Get_mustAction()
   {

       $data['version'] = '1.4';
       $data['notice'] = false;
       if (!empty($this->uid))
       {

          $money = \think\Db::name('tree_log')->where(['uid' => $this->uid, 'notice' => 0])->where('money', '>', 0)->sum('money');

          if ($money > 0)
          {
              $data['notice'] = [];
              $money_rmb = bcmul($money, 7, 2);
              $data['notice']['money_rmb'] = $money_rmb;
              $data['notice']['money'] = $money;

              \think\Db::name('tree_log')->where(['uid' => $this->uid, 'notice' => 0])->update(['notice' => 1]);
          }

       }

       $this->success($data);

   }

}
