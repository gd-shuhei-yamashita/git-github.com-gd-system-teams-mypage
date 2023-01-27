<?php

namespace App\Http\Middleware;

use Closure;
// use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\FileViewFinder;
use Illuminate\Support\Facades\Log;

/**
 * ブランド毎にビュー表示の優先順位を切り替える
 * ex. ミドルウェアを使ってビューの探索パスを変更する  
 * https://qiita.com/sogawa@github/items/644710df8d58d54f2665  
 * 
 */
class ViewSwitchMiddleware
{
    /**
     * The view factory implementation.
     *
     * @var \Illuminate\Contracts\View\Factory
     */
    protected $view;

    /**
     * Create a new instance.
     *
     * @param  \Illuminate\Contracts\View\Factory  $view
     * @return void
     */
    public function __construct(ViewFactory $view)
    {
        $this->view = $view;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // 読み込みパスにconst.ViewThame を追加する
        $app = app();
        $paths = $app['config']['view.paths'];

        // configから
        $view_thame = $app['config']['const.ViewThame'];
        Log::debug("view_thame : " . print_r($view_thame,1) );
        foreach($view_thame as $newpath) {
            Log::debug("ViewSwitch : " . $newpath);
            // array_unshift($paths, resource_path('views_doubutsu') ); // パスの追加
            array_unshift($paths, resource_path( $newpath ) ); // パスの追加
        }

        $this->view->setFinder(new FileViewFinder($app['files'], $paths));

        return $next($request);
    }
}
