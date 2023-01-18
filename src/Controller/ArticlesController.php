<?php
// src/Controller/ArticlesController.php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Controller\Component\PaginatorComponent;

class ArticlesController extends AppController
{

    public $paginate = [
        'limit' => 5,
    ];

    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Paginator');
        $this->loadComponent('Flash'); // FlashComponentをインクルード

        $this->Auth->allow(['tags']);
    }

    public function index()
    {
        $this->loadComponent('Paginator'); // ページネーションについて、あってもなくてもページ分けされる？
        // $articles = $this->Paginator->paginate($this->Articles->find());
        // $this->set(compact('articles'));
        $this->set('articles', $this->paginate()); // ページネーション用
    }

    public function view($slug = null) // 初期値null、指定しなければnullになる
    {
        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        $this->set(compact('article'));
    }

    public function add()
    {
        $article = $this->Articles->newEntity();
        if ($this->request->is('post')) {
            // postデータをArticleエンティティに変換、patchEntityはマニュアル参照
            $article = $this->Articles->patchEntity($article, $this->request->getData());

            // セッションからuser_idをセット
            $article->user_id = $this->Auth->user('id');

            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to add your article.'));
        }
        // タグのリストを取得
        $tags = $this->Articles->Tags->find('list'); // find('list')でレコードのリストを取得
        // ビューコンテキストにtagsをセット
        $this->set('tags', $tags);

        $this->set('article', $article);
    }

    public function edit($slug) // 記事の編集
    {
        $article = $this->Articles
            ->findBySlug($slug)
            ->contain('Tags') // 関連付けられたTagsを読み込む
            ->firstOrFail();

        if ($this->request->is(['post', 'put'])) {
            $this->Articles->patchEntity($article, $this->request->getData(), [
                'accessibleFields' => ['user_id' => false]
            ]);
            if ($this->Articles->save($article)) {
                $this->Flash->success(__('Your article has been updated.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Unable to update your article.'));
        }

        // タグのリストを取得
        $tags = $this->Articles->Tags->find('list');

        // ビューコンテキストにtagsをセット
        $this->set('tags', $tags);

        $this->set('article', $article);
    }

    public function delete($slug)
    {
        $this->request->allowMethod(['post', 'delete']);

        $article = $this->Articles->findBySlug($slug)->firstOrFail();
        if ($this->Articles->delete($article)) {
            $this->Flash->success(__('The {0} article has been deleted.', $article->title));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function tags()
    {
        // passキー　リクエストに渡された全てのURLパスセグメントを含む
        $tags = $this->request->getParam('pass');

        // ArticlesTableを使用して、タグ付きの記事を検索
        $articles = $this->Articles->find('tagged', [
            'tags' => $tags
        ]);

        // 変数をビューテンプレートのコンテキストに渡す
        $this->set([
            'articles' => $articles,
            'tags' => $tags
        ]);
    }
    /**
     * 渡された引数はメソッドのパラメータとして渡されるので、
     * PHPの可変引数を使用してアクションを記述することもできる
     * public function tags(...$tags)
     * {
     *      $articles = $this->Articles->find('tagged', [
     *          'tags' => $tags
     *      ]); 
     * 
     *      $this->set([
     *          'articles' => $articles,
     *          'tags' => $tags
     *      ]);
     * }
     */

     public function isAuthorized($user)
     {
        $action = $this->request->getParam('action');
        // addおよびtagsアクションは、常にログインしているユーザーに許可
        if (in_array($action, ['add', 'tags'])) {
            return true;
        }

        // 他のすべてのアクションにはスラッグが必要　　　・・・？
        $slug = $this->request->getParam('pass.0');
        if (!$slug) {
            return false;
        }

        // 記事が現在のユーザーに属していることを確認
        $article = $this->Articles->findBySlug($slug)->first();

        return $article->user_id === $user['id'];
     }
}