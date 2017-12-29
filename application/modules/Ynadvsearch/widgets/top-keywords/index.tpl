<div id="ynadvsearch_top-keywords">
    <ol id="keyword_list">
        <?php foreach ($this->keywords as $key => $keyword) : ?>
            <li class="keyword_item">
            <?php echo $this->htmlLink(
                array(
                    'module' => 'ynadvsearch',
                    'route' => 'ynadvsearch_search',
                    'query' => $keyword->title
                ),
                $keyword->title,
                array()
            ) ?>
            <div class="keyword_count">
                (<?php echo $keyword->count;?>)
            </div>            
            </li>
        <?php endforeach;?>
    </ul>
</div>
