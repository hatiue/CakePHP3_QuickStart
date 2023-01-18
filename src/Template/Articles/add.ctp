<!-- File: src/Template/Articles/add.ctp -->

<h1>記事の追加</h1>
<?php
    echo $this->Form->create($article); // <form method="post" action="/articles/add">を生成
    echo $this->Form->control('title');
    echo $this->Form->control('body', ['rows' => '3']);
    echo $this->Form->control('tag_string', ['type' => 'text']);
    echo $this->Form->button(__('Save Article'));
    echo $this->Form->end();
?>