<!-- File: src/Template/Articles/index.ctp 4.xでは.php -->

<h1>記事一覧</h1>
<?= $this->Html->link('記事の追加', ['action' => 'add']) ?>
<table>
    <tr>
        <th><?= $this->Paginator->sort('title', 'タイトル') ?></th>
        <th><?= $this->Paginator->sort('created', '作成日時') ?></th>
        <th>操作</th>
    </tr>

    <!-- ここで$articlesクエリオブジェクトを繰り返して、記事の情報を出力 -->
    <!-- $articlesは src/Controller/ArticlesController.phpで定義 -->

    <?php foreach ($articles as $article): ?>
    <tr>
        <td>
            <?= $this->Html->link($article->title, ['action' => 'view', $article->slug]) ?>
        </td>
        <td>
            <?= $article->created->format(DATE_RFC850) ?>
        </td>
        <td>
            <?= $this->Html->link('編集', ['action' => 'edit', $article->slug]) ?>
            <?= $this->Form->postLink('削除', 
                                      ['action' => 'delete', $article->slug],
                                      ['confirm' => 'よろしいですか？'])
            ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<div class="paginator">
    <ul class="pagination">
        <?= $this->Paginator->first('Top<<') ?>
        <?= $this->Paginator->prev('<') ?>
        <?= $this->Paginator->numbers() ?>
        <?= $this->Paginator->next('>') ?>
        <?= $this->Paginator->last('>>Last') ?>
    </ul>
</div>