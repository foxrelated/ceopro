<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<rss version="2.0">
    <channel>
        <atom:link rel="hub" href="http://tumblr.superfeedr.com/" xmlns:atom="http://www.w3.org/2005/Atom"/>
        <description></description>
        <title><?php echo htmlspecialchars($this->blogs[0] -> getOwner() -> getTitle())?></title>
        <generator>Tumblr (3.0; @<?php echo htmlspecialchars($this->blogs[0]-> getOwner() -> getTitle())?>)</generator>
        <link><?php echo "//".$_SERVER['HTTP_HOST'].$this->blogs[0] -> getOwner() -> getHref()?></link>
        <?php foreach ($this->blogs as $blog): ?>
        <item>
            <title><?php echo htmlspecialchars($blog->getTitle()) ?></title>
            <description>&lt;p&gt;<?php echo $blog->body ?>&lt;/p&gt;</description>
            <link><?php echo "//".$_SERVER['HTTP_HOST']. $blog -> getHref(); ?></link>
            <guid><?php echo "//".$_SERVER['HTTP_HOST']. $blog -> getHref(); ?></guid>
            <pubDate><?php echo $blog->creation_date ?></pubDate>
        </item>
        <?php endforeach;?>
    </channel>
</rss>