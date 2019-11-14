<?php
/**
 * RestFull Api接口专用
 * Date: 2018\2\23 0020 14:15
 */

class IndexController extends Controller
{
    // 处理一下退款功能

    public function backAction()
    {
//        $wallet_model = new WalletModel();
//         $list = \think\Db::name('withdraw')->select();
//        $res = 0;
//         foreach ($list as $key => $item)
//         {
//             // 退款
//             $refundMoney = $item['total'];
//             $uid = $item['uid'];
//             \think\Db::startTrans();
//             try {
//
//                 \think\Db::name('withdraw')->where('id', $item['id'])->update(['state' => 9]);
//                 $wallet_model->addMoney($uid, $item['currency_id'], $refundMoney, $item['order_no'], '提现退款返还');
//                 \think\Db::commit();
//                 $res++;
//             } catch (\Exception $e) {
//                 \think\Db::rollback();
//
//             }
//         }
//
//         echo $res;die();
    }
    public function treeUserAction()
    {
        $tree_user_model = new TreeUserModel();
        $tree_user = $tree_user_model->getTreeUserByUidEchat(1, 1);
        $data['name'] = '平台';
        $data['children'] = $tree_user;
        echo json_encode($data);die();
    }
    public function addressAction()
    {
        $address = ["0xc5c1adf03ec9316e3ae65d24fd9f71fbe6348103","0xd3aaee3b3198a8d49b1109b28b64f792413c5bf3","0x12cd0cc314eebd67293d8fb3ee29c683f7b79330","0xc9af5c138e6c35a4560dabc1550d313469362be3","0xf6ff7991cfde4c7a4b0df9e50d2f6d7aa6a1374f","0x9bf218257d5518faf50b864ed7c8779a6317919b","0xd6dc06348b035844515544bb879feeec32ccb8fa","0x2f8ac49ca9a4e81d4898ea9e0e2cc651181ed222","0x1646cadef11ced0ed09f27acfe2d0786f5599321","0xd28e95489c1a0cbd237ff10f6d8fe3d118321fe6","0xe075f10c50db5f2a2710f645ad39f6bb1108d048","0xa2f02f0d9f3fb311e89da9b9a0a35bbc1f29d703","0xf29a3b0fa45390376311f3d7dbd51cfd5c05165c","0xba73de7aa2aec3301a009fea2978ca6a43791019","0xef5144f4ddfaf4ac2ca3866d0d9f257273820091","0x1f3e481ffb6d329ea09c9b125eaddb68593b02e3","0x76a250d7a4d4f7ab70110f52484b5f5cb249b270","0x2b130ab0a92577bb7c7c61fdfe06bb6bc633c51d","0xda0fa8c65ed8fa63d20e285b9c8cb1fa9a50dd5d","0xbe9d1aa75a283f75478c94a9b256dfdd8b680292","0x2ea5e4cc4d6f9733aa7119c40fad1f52523cdb7b","0x2e5f7593e81fb3bdd49248811a51a37d53520c46","0x3272fc5671cb63f91b0bbc3c5a929151f28db8f5","0x5283823f1c656c6df780899ef8c2213cbc6dbfc5","0x59306296027385b915e2572bfd491b88355a8239","0x6e90780fd70d879f701aae38367ff3ea5d91b46a","0x066b06d7dfbb7dd17888bc8086b01298a390590b","0xb17b4453f3e91234ecf5827e1cf769829813aea4","0x08bc9c3fc35f341e764d0083f49b7be133ab1079","0xe5a6fedb964ee0e8e2dc31d6169f9ffc1197b162","0x72201825e30669b92062f547a4ac9128aa30ec16","0xf0daef8c6d9d8c6b06d19b08d73e2e2f8a050436","0x8b8ac3f8a051f966ea052f779d43dfdb2644298f","0x0f5f6bb24d09b80564ebee210d42a07f42d23042","0xcd27b7c157ea60aae0bbe6fcc08f5222d24f9663","0x1b2b1d09c89cf7c9b505787d5876ed709d7828d5","0xa6bbdb0d5a0ca36e5e1fa6e7a4643db86e31088d","0x25e69d51f99b7bf79534cc0c9e4d18d50dcd7c2c","0xb4e7eb1bd66192b865058857ab10065acbabaf72","0xf678fb3296ac571fa6c91363564b631adecec19b","0x077205b6dc5ba52988b49c1620a853fb99de69fd","0x9078a3fdf064d1050e5dd4e4c8f991c6a568dec8","0xcc02cf62e6b25f9bc9cc363192736b98fea083f1","0xb2141cb200d134cc8beb859aa11365cf1abcf8bb","0x7b34a98aca53763fbe3f0804c2da61a68734bfb3","0xb51858b17dbed5362cbb0c6daf2a749f298a849e","0x9db625b301f2490576614877dc5b6c1349437523","0x372f5f3f692c78f98adae36e7134ee565e02ebc2","0x697c820e5a6765e9bcd4e84a659c227f76ba0d91","0x613ed40f4f533d44771c5cbc6252c7d38cea155d","0x4a3883f8636dedf96eb5baa4ab4d3ede512ac6a3","0xb02d0e63ed7c1bbce50c3b0751f082e33abe918a","0x20c55c1758f94fb85f63ad7bb897dfe1b7e067af","0xbaac9e86b3d35e478d419c2dc5421e01f39f165e","0x167b66de5da75435047ed68f9db4eda444bada39","0x76b8d55545c7c53a54043b36a9c9de52a648b76e","0x2535297bc6518ad43a8243a995b3a8620893c6f0","0xcd67a8e08a2ed836ef1f0ee244870a9faf93e8c6","0x86de36047c223f65afaf6e854251443c42b293aa","0xd28c015414a45f5297200fb4cf800ff893e599c0","0xe7eeb314254ae521398d05984fbfefb9ebfd2e34","0xe6444b9b93260e0de34827d0e0b80e76fc12f5c0","0xc25df9659807c0df7b277d9d041b847657cfc907","0x4412dc360c778dc851f83119c8bc1dfd5af8579a","0x249fdbdd3c5b282011af0a0aedd1d9d6bfdb7fbd","0x2b9a003836a03816e291696749e019556561757f","0x1f5fb63e409f6a3bf433dd67658ddc5bb0d2fd75","0x70e8beaae9d022bf8572b52e9fd87b73a3641dd5","0x6ac27d659b923cc48896c6b37c0b172e6ee31769","0xbcd1c0f82e7d9d01daad7dd15f1718b038c975af","0x9affa9ccd3dff59d89f9d9811a0b98a03269e2cc","0xe4743f451584ca3bf20f688d95b92d1d78998445","0xbe89432506cc953c14c79dc4bc20a55496aa907f","0xc430290db83b5500b9005f2fc67ebd2e54586c3f","0x45714c0354e3187360578a673598cab5d87dfd3e","0x4480c94ca124673511c75cdfb73f764587a19e81","0xcc8f544bffbbb842e9f0e2775c358a2dd3f96347","0x172bd61d83a3617c88a216241167eb26f3e465fd","0x1dcf32cdad6b664b0e04bc5a737dd323578a1181","0x7e859a6d4c21456590b782dc65f562c9ded9f5d3","0x1c56e9225a337131255a351571070bc0da61d205","0x42e6768d9d9a06adaa412e5a7f72c6cb7c9c79ac","0xd2c4bc0f684c2e751e5344ec99f4aeaf3a7821b4","0x0188d80f64bf7f5da9f70cfa345adf98227adba1","0x3d13e2cc74da2d0cb6e11ee695c4017bb2bb7960","0xfbcd46a534827a4fd06c6c6263ea5507fa4a5b44","0x05bfeebb2febf4ad8aa53c5b57996b80d0e85daa","0x76ee4254fe28705a0a59609d1fb9a3a0f3e5c1f4","0xbaeee2f3e5a12430ccc0b68bd16cc4b11d486f6a","0x8720c60f910466206d08ca7fca1a951adf82925c","0x418a7b6d8fc17064220ac5e7151a64d66f3cd85a","0xd7258d966550349d681c3cf8c19ff1d3dec0a530","0x4e43b08b7d2b98478dd95e059a63234a3455f3d8","0xbbdb480a157594a5dff48da619a2219dee2eca06","0xc37c7c79255ba15b5b4c3c79499a9f77ff71332a","0xc7a5136854f12f0c6c41b0bc4eb137d15cbb11a6","0x06a2b519c21244b9b10aee2b57ea44facdc1c6ac","0xb70bbc7d4e66da591960c52c6ec468f9916f129d","0x496ad48ce481282e0285a37c36b0ca85d0198635","0x50b7e8ba38dee97b3e46b88ad16f27340db40d30","0xe8aada5be357643d500d753df4649b9788e59298","0x07e4f53d2fb72260fc1894641142e38a44ff78c0","0x5ef342dfa91d6c67035e8a6bf5ec2221855f6e3b","0xadd5d9397d2ec0dc0088b1e3dc13c6dce4a12dc1","0x1bdc898c5f7f01df03bca8ee786511afcf7e3ac0","0x565e1a5b757e1e484e984a05fa98e9761a98abd5","0x99dbf4e48763afdb069f9d6cb6ce7def4468a24c"];

        echo count($address);die();
        $users = [];
        foreach ($address as $key => $item)
        {
            $res = \think\Db::name('address')->where('address', $item)->find();
            if ($res['uid'] != 0)
            {
                $users[] = $res['uid'];
            } else
            {
               \think\Db::name('address')->where('id', $res['id'])->delete();
            }
        }
        print_r($users);die();

    }
    public function testAction()
    {
        $freePay = new Freepay();
        $witdrawData['to'] = '0x61010e9524f84879ea210EafB97Fe0a0AcfC6B0F';
        $witdrawData['from'] = '0x9ef3f6506e38c89b691d2c3576361bf1bf204c6f';
        $witdrawData['order_on'] = time();
        $witdrawData['price'] = 100;
        $witdrawData['currency'] = 'dyx';
        $cash_res = $freePay->withdraw($witdrawData);
        print_r($cash_res);die();
        include "address.php";
//        echo count($address);die();
        foreach ($address as $item)
        {
           $add_item['address'] = $item;
           $add_item['create_time'] = time();
           \think\Db::name('address')->insert($add_item);
        }
        echo '666';die();
        $arr = ['a' => 1, 'b' => 5, 'c' => 3];
        $treeUser_mode = new TreeUserModel();
//        $user =  $treeUser_mode->getTreeUserOrderLevel(1, 1);
//        echo pow((int)3, 0);die();
//        print_r($user);die();
        $tree = \think\Db::name('tree')->where('id', 1)->find();
        $tree_location = $treeUser_mode->getTreeLocation($tree, 41, 34);
        print_r($tree_location);die();
    }

    public function treechatAction()
    {
        /** 获取群组信息 */
        $this->getView('treechat');

    }


    public function indexAction()
    {

        //如果是移动端 走mobile文件
        if ($this->request->isMobile())
        {
            /** 获取群组信息 */
            //$this->getView('mobile');
           header('location:'.'/index/mobile');die();

        } else
        {
            /** 获取群组信息 */
            $this->getView('index');
        }


    }

    /** 移动端 */
    public function mobileAction()
    {
        $this->getView('mobile');
    }

    /** 下载 */
    public function downloadAction()
    {

        $file_name="water_Android_apk.apk";
        $file_path=APP_PATH.'/public/';

        $fp=fopen($file_path,"r");
        $file_size=filesize($file_path);
        //下载文件需要用到的头
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:".$file_size);
        Header("Content-Disposition: attachment; filename=".$file_name);
        $buffer=1024;
        $file_count=0;
//向浏览器返回数据
        while(!feof($fp) && $file_count<$file_size){
            $file_con=fread($fp,$buffer);
            $file_count+=$buffer;
            echo $file_con;
        }
        fclose($fp);
    }

}
