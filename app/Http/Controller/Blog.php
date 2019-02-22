<?php
namespace App\Http\Controller;

use Laf\util\Date;
use Laf\Controller;
use App\Libs\Parser;
use App\Model\blog\Article;
use App\Model\blog\Setting;

/**
 * 微信相关接口控制器
 */
class Blog extends  Controller
{
    /**
     * Api接口
     *
     * @var boolean
     */
    protected $isApi = true;

    /**
     * 允许跨域的域名
     *
     * @var [type]
     */
    protected $allowOrigin = ['http://blog.gdmon.com'];

    /**
     * 允许跨域的请求方式
     *
     * @var [type]
     */
    protected $allowMethods = ['get', 'post']; 

    /**
     * 查询文章列表
     *
     * @param  integer $page 分页数
     * @param  Date    $date 时间辅助类
     * @return [type]        [description]
     */
    public function query($page = 0, Date $date)
    {
        $data = Article::instance()->queryList(['page' => $page, 'status' => 1]);
        // 整理数据
        $data['list'] = $data['list']->toArray();
        foreach($data['list'] as &$v)
        {
            $v = [
                'id'    => $v['id'],
                'type'  => $v['name'],
                'title' => $v['title'],
                'desc'  => $v['desc'],
                'times' => $date->timeDiff($v['push_time'])
            ];
        }

        return $this->successJson('OK', $data);
    }

    /**
     * 阅读文章
     *
     * @param  [type] $idx    文章ID
     * @param  Date   $date   时间辅助器
     * @param  Parser $parser MarkDown解析器
     * @return [type]         [description]
     */
    public function article($idx, Date $date, Parser $parser)
    {
        $data = Article::instance()->join('mon_blog_tags', 'mon_blog_article.type=mon_blog_tags.id')->field('mon_blog_article.*, mon_blog_tags.name')->where('mon_blog_article.status', 1)->where('mon_blog_article.id', $idx)->find();
        if(!$data){
            return $this->errorJson('文章不存在或已下线');
        }

        // 整理数据
        $info['title'] = $data['title'];
        $info['type'] = $data['name'];
        $info['content'] = $parser->makeHtml($data['content']);
        $info['times'] = $date->timeDiff($data['push_time']);

        return $this->successJson('OK', $info);
    }

    /**
     * 归档列表
     *
     * @return [type] [description]
     */
    public function archive()
    {
        $data = Article::instance()->archive();

        return $this->successJson('OK', $data);
    }

    /**
     * 关于我
     *
     * @param  Parser $parser MarkDown解析器
     * @return [type]         [description]
     */
    public function about(Parser $parser)
    {
        $data = Setting::instance()->getAbout();

        // 整理数据
        $html = $parser->makeHtml($data['about']);

        return $this->successJson('OK', $html);
    }
}