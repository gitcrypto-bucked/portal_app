<?php

namespace App\Http\Controllers;

use App\Models\NewsModel;
use Illuminate\Http\Request;

class NewsController extends Controller
{
        /** função que traz as noticias paginadas  */
        public static function getAllNews()
        {
            $model = new NewsModel();
            return $model->getAllNews(18);
        }

        /** função que cadastra a noticia  */
        public function addnews(Request $request)
        {
            $request->validate([
                'thumb' => 'required|mimes:jpg,png,webp|max:2048',
                'url' => 'required|url',
            ]);

            if(parse_url($request->url, PHP_URL_SCHEME)==false |
                parse_url($request->url, PHP_URL_HOST)==false)
            {
                return redirect('/add-news')->with('error', 'A url informada não é valida.');
            }

            if ($request->file('thumb')->isValid())
            {
                $file = $request->file('thumb');
            }
            else
            {
                return redirect('/add-news')->with('error', 'O arquivo de imagem tem que ser jpg, png, jpeg ou webp.');
            }

            //'storage/thumb/'
            $path=$file->storeAs('public/thumb', $file->hashName());
            $hora = time();
            $news = [
                'created_at'=>date('Y-m-d H:i:s', $hora),
                'thumb'=>$file->hashName(),
                'intro' => $request->intro,
                'active' => '1',
                'title' => $request->titulo,
                'url' => $request->url,
            ];
            $newsModel = new NewsModel();
            if($newsModel->insert($news))
            {
                return redirect('/add-news')->with('success', 'Noticia cadastrada com sucesso!');
            }
            return redirect('/add-news')->with('error', 'Houve um erro ao cadastrar a noticia!.');
        }

        /** função que desativa a noticia  */
        public function removenews(Request $request)
        {
            $model = new NewsModel();
            if($model->remove($request->id))
            {
                return redirect('/list-news')->with('success', 'Noticia desativada com sucesso!');
            }
            return redirect('/list-news')->with('error', 'Houve um erro ao desativar a noticia!.');
        }
}
