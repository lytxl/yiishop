<table class="table">
       <tr>
           <th>图片</th>
       </tr>
    <?php foreach($gallery as $g):?>
    <tr>
           <td>
               <img src="<?=$g->path?>" alt="">
           </td>
       </tr>
    <?php endforeach;?>
    <tr>
        <td><?=$content->content?></td>
    </tr>
</table>