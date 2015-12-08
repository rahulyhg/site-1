<?php

namespace LaravelItalia\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Auth;
use LaravelItalia\Http\Requests;
use LaravelItalia\Http\Controllers\Controller;
use LaravelItalia\Http\Requests\ArticleAddRequest;
use LaravelItalia\Entities\Factories\ArticleFactory;
use LaravelItalia\Entities\Repositories\SeriesRepository;
use LaravelItalia\Entities\Repositories\ArticleRepository;
use LaravelItalia\Entities\Repositories\CategoryRepository;

class ArticleController extends Controller
{
    public function getIndex(Request $request, ArticleRepository $articleRepository)
    {
        return view('admin.articles_index', [
            'articles' => $articleRepository->getAll($request->get('page', 1))
        ]);
    }

    public function getAdd(CategoryRepository $categoryRepository, SeriesRepository $seriesRepository)
    {
        return view('admin.articles_add', [
            'categories' => $categoryRepository->getAll(),
            'series' => $seriesRepository->getAll()
        ]);
    }

    public function postAdd(ArticleAddRequest $request, ArticleRepository $articleRepository, SeriesRepository $seriesRepository)
    {
        $article = ArticleFactory::createArticle(
            $request->get('title'),
            $request->get('digest'),
            $request->get('body'),
            $request->get('metadescription')
        );

        if($request->get('series_id') != 0)
            $article->setSeries($request->get('series_id'));

        $article->setUser(Auth::user());

        $articleRepository->save($article);

        $article->categories()->sync($request->get('categories'));

        return redirect('admin/articles')->with('success_message', 'Articolo aggiunto correttamente.');
    }
}
