<?php

namespace Zhiyi\Component\ZhiyiPlus\PlusComponentPc\Controllers;

use DB;
use Illuminate\Http\Request;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentPc\getTime;
use function Zhiyi\Component\ZhiyiPlus\PlusComponentPc\createRequest;

class NewsController extends BaseController
{
    /**
     * 资讯首页
     * @author Foreach
     * @param  Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->PlusData['current'] = 'news';

        // 资讯分类
        $cates = createRequest('GET', '/api/v2/news/cates');
        $data['cates'] = array_merge($cates['my_cates'], $cates['more_cates']);

        $data['cate_id'] = $request->query('cate_id') ?: 0;

        return view('pcview::news.index', $data, $this->PlusData);
    }

    /**
     * 资讯列表
     * @author Foreach
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function list(Request $request)
    {
        $params = [
            'recommend' => $request->query('recommend'),
            'cate_id' => $request->query('cate_id'),
            'after' => $request->query('after', 0)
        ];

        // 获取资讯列表
        $news['news'] = createRequest('GET', '/api/v2/news', $params);
        $new = clone $news['news'];
        $after = $new->pop()->id ?? 0;
        $news['cate_id'] = $params['cate_id'];

        $news['space'] =  $this->PlusData['config']['ads_space']['pc:news:list'] ?? [];
        $news['page'] = $request->loadcount;

        // 加入置顶资讯
        if ($params['cate_id']) {
            $topNews = createRequest('GET', '/api/v2/news/categories/pinneds', $params);
            if (!empty($topNews) && $request->loadcount == 1) {
                $topNews->reverse()->each(function ($item, $key) use ($news) {
                    $item->top = 1;
                    $news['news']->prepend($item);
                });
            }
        }

        $newsData = view('pcview::templates.news', $news, $this->PlusData)->render();

        return response()->json([
                'status'  => true,
                'data' => $newsData,
                'after' => $after
        ]);
    }


    /**
     * 资讯详情
     * @author Foreach
     * @param  int    $news_id [资讯id]
     * @return mixed
     */
    public function read(int $news_id)
    {
        $this->PlusData['current'] = 'news';

        // 获取资讯详情
        $news = createRequest('GET', '/api/v2/news/' . $news_id);
        $news->reward = createRequest('GET', '/api/v2/news/' . $news_id . '/rewards/sum');
        $news->rewards = $news->rewards->filter(function ($value, $key) {
            return $key < 10;
        });
        $news->collect_count = $news->collections->count();

        // 相关资讯
        $news_rel = createRequest('GET', '/api/v2/news/' . $news_id . '/correlations');

        $data['news'] = $news;
        $data['news_rel'] = $news_rel;
        return view('pcview::news.read', $data, $this->PlusData);
    }

    /**
     * 资讯投稿
     * @author ZsyD
     * @param  Request     $request
     * @param  int $news_id [资讯id]
     * @return mixed
     */
    public function release(Request $request, int $news_id = 0)
    {
        if ($this->PlusData['config']['bootstrappers']['news:contribute']['verified'] && !$this->PlusData['TS']['verified']) {
            abort(403, '未认证用户不能投稿');
        }

        $this->PlusData['current'] = 'news';

        // 资讯分类
        $cates = createRequest('GET', '/api/v2/news/cates');
        $data['cates'] = array_merge($cates['my_cates'], $cates['more_cates']);
        // 标签
        $data['tags'] = createRequest('GET', '/api/v2/tags');

        if ($news_id > 0) {
            $data['data'] = createRequest('GET', '/api/v2/news/'.$news_id);
        }

        return view('pcview::news.release', $data, $this->PlusData);
    }

    /**
     * 文章评论列表
     * @author ZsyD
     * @param  Request $request
     * @param  int     $news_id [资讯id]
     * @return \Illuminate\Http\JsonResponse
     */
    public function comments(Request $request, int $news_id)
    {
        $params = [
            'after' => $request->query('after') ?: 0
        ];

        $comments = createRequest('GET', '/api/v2/news/'.$news_id.'/comments', $params);
        $comment = clone $comments['comments'];
        $after = $comment->pop()->id ?? 0;
        if ($comments['pinneds'] != null) {

            $comments['pinneds']->each(function ($item, $key) use ($comments) {
                $item->top = 1;
                $comments['comments']->prepend($item);
            });
        }

        $commentData = view('pcview::templates.comment', $comments, $this->PlusData)->render();

        return response()->json([
            'status'  => true,
            'data' => $commentData,
            'after' => $after
        ]);
    }
}
