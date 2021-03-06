<?php
namespace app\api\controller;

use app\api\model\Cate;
use app\api\model\Enterprise;
use app\api\model\Gain;
use app\api\model\GainVideo;
use app\api\model\News;
use think\Controller;
use app\api\model\Expert;
use think\Log;

class Common extends Controller
{
    /**
     * 专家信息含分页 [get]
     * @return \think\response\Json
     */
    public function expertList(){
        try {
            if (request()->isGet()) {
                $params = request()->get();
                if (!isset($params['page'])) {
                    $params['page'] = 1;
                }
                if (!isset($params['pageSize'])) {
                    $params['pageSize'] = 10;
                }
                $expertCount = (new Expert())->count();
                $lastPage = ceil($expertCount / $params['pageSize']);
                if ($params['page'] > $lastPage) {
                    $params['page'] = $lastPage;
                }
                $pageOffset = ($params['page'] - 1) * $params['pageSize'];
                $expert = (new Expert())->field(['*','from_unixtime(create_t) create_t'])->limit($pageOffset, $params['pageSize'])->select()->toArray();
                $json = [
                    'codeMsg' => 'SUCCESS',
                    'code' => 200,
                    'totalCount' => $expertCount,
                    'page' => $params['page'],
                    'totalPage' => $lastPage,
                    'pageSize' => $params['pageSize'],
                    'data' => $expert
                ];
                return json($json);
            } else {
                return json(['codeMsg' => '请求错误', 'code' => 400]);
            }
        }catch (\Exception $e){
            Log::error($e);
            return json(['codeMsg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

    /**
     *专家信息详情  [get]
     * @return \think\response\Json
     */
    public function expertDetail(){
        try {
            if (request()->isGet()){
                $id = input('id');
                $expertDetail = (new Expert())
                    ->field(['*','from_unixtime(create_t) create_t'])
                    ->where(['id'=>$id])->find()->toArray();
                $gainVideo = (new GainVideo())->where(['expert_id'=>$expertDetail['id']])->find();
                if ($gainVideo){
                    $expertDetail['video_name'] = $gainVideo['name'];
                    $expertDetail['video'] = $gainVideo['video'];
                    $gain = (new Gain())->where(['id'=>$gainVideo['gain_id']])->find()->toArray();
                    if ($gain) {
                        $expertDetail['gain_name'] = $gain['name'];
                        $expertDetail['gain_face'] = $gain['face'];
                    }
                }
                $json = [
                    'codeMsg' => 'SUCCESS',
                    'code' => 200,
                    'data' => $expertDetail
                ];
                return json($json);
            } else {
                return json(['codeMsg' => '请求错误', 'code' => 400]);
            }
        }catch (\Exception $e){
            Log::error($e);
            return json(['codeMsg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

    /**
     * 企业信息含分页  {get]
     * @param  $uid      string  [必填]   注册用户编号
     * @param  $page     string  [非必填] 当前页
     * @param  $pageSize string  [非必填] 显示条数
     * @return \think\response\Json
     */
    public function enterpriseList(){
        try{
            if (request()->isGet()){
                $params = request()->get();
                if (!isset($params['uid']) || empty($params['uid'])){
                    return json(['codeMsg' => 'uid不能为空', 'code' => 400]);
                }
                if (!isset($params['page'])) {
                    $params['page'] = 1;
                }
                if (!isset($params['pageSize'])) {
                    $params['pageSize'] = 10;
                }
                $expertCount = (new Enterprise())->where(['uid'=>$params['uid']])->count();
                if ($expertCount <= 0){
                    $json = [
                        'codeMsg' => 'SUCCESS',
                        'code' => 200,
                        'totalCount' => 0,
                        'page' => $params['page'],
                        'totalPage' => 1,
                        'pageSize' => $params['pageSize'],
                        'data' => []
                    ];
                    return json($json);
                }
                $lastPage = ceil($expertCount / $params['pageSize']);
                if ($params['page'] > $lastPage) {
                    $params['page'] = $lastPage;
                }
                $pageOffset = ($params['page'] - 1) * $params['pageSize'];
                $expert = (new Enterprise())
                    ->field(['*','from_unixtime(create_t) create_t','from_unixtime(update_t) update_t'])
                    ->where(['uid'=>$params['uid']])
                    ->limit($pageOffset, $params['pageSize'])->select()->toArray();
                if ($expert){
                    foreach ($expert as $k=>$e){
                        switch ($e['status']){
                            case 0:
                                $expert[$k]['status_name'] = '已申请';
                                break;
                            case 1:
                                $expert[$k]['status_name'] = '审核中';
                                break;
                            case 2:
                                $expert[$k]['status_name'] = '已通过';
                                break;
                            case 3:
                                $expert[$k]['status_name'] = '未通过';
                                break;
                        }
                        switch ($e['type']){
                            case 1:
                                $expert[$k]['type_name'] = '企业工商营业执照';
                                break;
                            case 2:
                                $expert[$k]['type_name'] = '其他资质证件';
                                break;
                        }
                    }
                }
                $json = [
                    'codeMsg' => 'SUCCESS',
                    'code' => 200,
                    'totalCount' => $expertCount,
                    'page' => $params['page'],
                    'totalPage' => $lastPage,
                    'pageSize' => $params['pageSize'],
                    'data' => $expert
                ];
                return json($json);
            } else {
                return json(['codeMsg' => '请求错误', 'code' => 400]);
            }
        }catch (\Exception $e){
            Log::error($e);
            return json(['codeMsg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

    /**
     * 企业信息详情  [get]
     * @return \think\response\Json
     */
    public function enterpriseDetail(){
        try {
            if (request()->isGet()){
                $id = input('id');
                $uid = input('uid');
                $EnterpriseDetail = (new Enterprise())->field(['*','from_unixtime(create_t) create_t','from_unixtime(update_t) update_t'])->where(['id'=>$id,'uid'=>$uid])->find()->toArray();
                if ($EnterpriseDetail){
                    switch ($EnterpriseDetail['status']){
                        case 0:
                            $EnterpriseDetail['status_name'] = '已申请';
                            break;
                        case 1:
                            $EnterpriseDetail['status_name'] = '审核中';
                            break;
                        case 2:
                            $EnterpriseDetail['status_name'] = '已通过';
                            break;
                        case 3:
                            $EnterpriseDetail['status_name'] = '未通过';
                            break;
                    }
                    switch ($EnterpriseDetail['type']){
                        case 1:
                            $EnterpriseDetail['type_name'] = '企业工商营业执照';
                            break;
                        case 2:
                            $EnterpriseDetail['type_name'] = '其他资质证件';
                            break;
                    }
                }
                $json = [
                    'codeMsg' => 'SUCCESS',
                    'code' => 200,
                    'data' => $EnterpriseDetail
                ];
                return json($json);
            } else {
                return json(['codeMsg' => '请求错误', 'code' => 400]);
            }
        }catch (\Exception $e){
            Log::error($e);
            return json(['codeMsg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

    /**
     * 注册企业
     * @param $credit_id string  统一代码
     * @param $type      int     资质类型
     * @param $card_id   string  身份证
     * @param $corp      string  法人
     * @param $tel       string  电话
     * @param $name      string  企业名称
     * @param $uid       string  用户id
     * @param $validity_start  string 开始有效期
     * @param $validity_end    string 结束有效期
     * @param $certificate     string 资质文件
     * @return \think\response\Json
     */
    public function enterpriseAdd(){
        try {
            if (request()->isPost()){
                $params = request()->post();
                $field = (new Enterprise())->where(['name'=>$params['name'],'credit_id'=>$params['credit_id'],'uid'=>$params['uid']])->find();
                if ($field){
                    return json(['codeMsg' => '该企业以申请注册，请查看相关信息', 'code' => 400]);
                }
                $params['create_t'] = time();
                $params['status'] = 1;
                if ((new Enterprise())->save($params)){
                    return json(['codeMsg' => '注册成功', 'code' => 200]);
                }else{
                    return json(['codeMsg' => '注册失败', 'code' => 400]);
                }
            } else {
                return json(['codeMsg' => '请求错误', 'code' => 400]);
            }
        }catch (\Exception $e){
            Log::error($e);
            return json(['codeMsg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

    /**
     * 成果视频  [get]
     * @return \think\response\Json
     */
    public function gainVideoList(){
        try {
            if (request()->isGet()) {
                $params = request()->get();
                if (!isset($params['page'])) {
                    $params['page'] = 1;
                }
                if (!isset($params['pageSize'])) {
                    $params['pageSize'] = 10;
                }
                $expertCount = (new Expert())->count();
                $lastPage = ceil($expertCount / $params['pageSize']);
                if ($params['page'] > $lastPage) {
                    $params['page'] = $lastPage;
                }
                $pageOffset = ($params['page'] - 1) * $params['pageSize'];
                $GainVideo = (new GainVideo())
                    ->alias('v')
                    ->field(['v.*','g.name gain_name','e.name expert_name'])
                    ->join('crm_gain g','v.gain_id = g.id')
                    ->join('crm_expert e','v.expert_id = e.id')
                    ->limit($pageOffset, $params['pageSize'])->select()->toArray();
                $json = [
                    'codeMsg' => 'SUCCESS',
                    'code' => 200,
                    'totalCount' => $expertCount,
                    'page' => $params['page'],
                    'totalPage' => $lastPage,
                    'pageSize' => $params['pageSize'],
                    'data' => $GainVideo
                ];
                return json($json);
            } else {
                return json(['codeMsg' => '请求错误', 'code' => 400]);
            }
        }catch (\Exception $e){
            Log::error($e);
            return json(['codeMsg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

    /**
     * 新闻列表  [get]
     * @return \think\response\Json
     */
    public function newsList(){
        try {
            if (request()->isGet()) {
                $params = request()->get();
                if (!isset($params['page'])) {
                    $params['page'] = 1;
                }
                if (!isset($params['pageSize'])) {
                    $params['pageSize'] = 10;
                }
                $where = $whereCount = [];
                if (isset($params['cate_id']) && !empty($params['cate_id'])){
                    $where = ['n.cate_id'=>$params['cate_id']];
                    $whereCount = ['cate_id'=>$params['cate_id']];
                }
                $expertCount = (new News())->where($whereCount)->count();
                $lastPage = ceil($expertCount / $params['pageSize']);
                if ($params['page'] > $lastPage) {
                    $params['page'] = $lastPage;
                }
                $pageOffset = ($params['page'] - 1) * $params['pageSize'];
                $NewsList = (new News())
                    ->alias('n')
                    ->field(['n.*','u.user_name','c.name cate_name','from_unixtime(n.create_t) create_t','from_unixtime(n.update_t) update_t'])
                    ->join('crm_users u','n.uid = u.id')
                    ->join('crm_cate c','n.cate_id = c.id')
                    ->where($where)
                    ->limit($pageOffset, $params['pageSize'])->order(['n.update_t'=>'desc'])->select()->toArray();
                $json = [
                    'codeMsg' => 'SUCCESS',
                    'code' => 200,
                    'totalCount' => $expertCount,
                    'page' => $params['page'],
                    'totalPage' => $lastPage,
                    'pageSize' => $params['pageSize'],
                    'data' => $NewsList
                ];
                return json($json);
            } else {
                return json(['codeMsg' => '请求错误', 'code' => 400]);
            }
        }catch (\Exception $e){
            Log::error($e);
            return json(['codeMsg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

    /**
     * 新闻详情  [get]
     * @return \think\response\Json
     */
    public function NewsDetail(){
        try {
            if (request()->isGet()){
                $id = input('id');
                $NewsDetail = (new News())->alias('n')
                    ->field(['n.*','u.user_name','c.name cate_name','from_unixtime(n.create_t) create_t','from_unixtime(n.update_t) update_t'])
                    ->join('crm_users u','n.uid = u.id')
                    ->join('crm_cate c','n.cate_id = c.id')
                    ->where(['n.id'=>$id])->find()->toArray();
                (new News())->update(['id'=>$NewsDetail['id'],'view'=>$NewsDetail['view']+1]);
                $json = [
                    'codeMsg' => 'SUCCESS',
                    'code' => 200,
                    'data' => $NewsDetail
                ];
                return json($json);
            } else {
                return json(['codeMsg' => '请求错误', 'code' => 400]);
            }
        }catch (\Exception $e){
            Log::error($e);
            return json(['codeMsg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

    /**
     * 栏目列表  [get]
     * @return \think\response\Json
     */
    public function cateList(){
        try {
            if (request()->isGet()){
                $CateCount = (new Cate())->count();
                $CateList = (new Cate())->select()->toArray();
                $json = [
                    'codeMsg' => 'SUCCESS',
                    'code' => 200,
                    'totalCount' => $CateCount,
                    'page' => 1,
                    'totalPage' => 1,
                    'pageSize' => $CateCount,
                    'data' => $CateList
                ];
                return json($json);
            }
        }catch (\Exception $e){
            Log::error($e);
            return json(['codeMsg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

    /**
     * 栏目列表和对应的新闻6条数据
     * @return \think\response\Json
     */
    public function cateListNews(){
        try{
            $CateList = (new Cate())->select()->toArray();
            foreach ($CateList as $k=>$c){
                $NewsList = (new News())
                    ->alias('n')
                    ->field(['n.*','u.user_name','c.name cate_name','from_unixtime(n.create_t) create_t','from_unixtime(n.update_t) update_t'])
                    ->join('crm_users u','n.uid = u.id')
                    ->join('crm_cate c','n.cate_id = c.id')
                    ->where(['n.cate_id'=>$c['id']])
                    ->limit(6)->order(['n.update_t'=>'desc'])->select()->toArray();
                $CateList[$k]['newsData'] = $NewsList;
            }
            $json = [
                'codeMsg' => 'SUCCESS',
                'code' => 200,
                'data' => $CateList
            ];
            return json($json);
        }catch (\Exception $e){
            Log::error($e);
            return json(['codeMsg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

    /**
     * 上传图片的提交实例

     * @return string
     */
    public function upload(){
        try {
            $file = request()->file('file');//获取文件信息
            $path = 'uploads';//文件目录
            //创建文件夹
            if(!is_dir($path)){
                mkdir($path, 0755, true);
            }
            $info = $file->move($path);//保存在目录文件下
            if ($info && $info->getPathname()) {
                $json = [
                    'codeMsg' => 'SUCCESS',
                    'code' => 200,
                    'data' => '/'.$info->getPathname()
                ];
                return json($json);
            } else {
                return json(['codeMsg'=>$file->getError(),'code'=>400]);
            }
        }catch (\Exception $e){
            Log::error($e);
            return json(['codeMsg'=>$e->getMessage(),'code'=>$e->getCode()]);
        }
    }

}