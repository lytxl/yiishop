<table class="table">
        <?php echo \yii\bootstrap\Carousel::widget([
            'items' => [
                // 包含图片和字幕的格式
                [
                    'content' => '<img src="foreach($gallery as $g){$g->path }"/>',
                    'caption' => '<h4>详情</h4><p>"{$content->content}"</p>',
                ],
            ]
        ]);
        ?>
</table>