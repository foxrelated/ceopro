<?php
/**
 * SocialEngine
 *
 * @category   Application_Extension
 * @package    Ynforum
 * @author     DangTH
 */
?>

<ul class="advforum_statistic_posts">
  <?php foreach( $this->paginator as $post ):
    $user = $post->getOwner();
    $topic = $post->getParent();
    $forum = $topic->getParent();
    ?>
    <li>
      <div class='info'>
        <div class='author'>
          <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
        </div>
        <div class="parent">
          <?php echo $this->translate('In') ?>
          <?php echo $this->htmlLink($topic->getHref(), $topic->getTitle()) ?>
          -
          <?php echo $this->htmlLink($forum->getHref(), $forum->getTitle()) ?>
        </div>
        <div class='date'>
          <?php echo $this->timestamp($post->creation_date) ?>
        </div>
      </div>
      <div class='description'>
        <?php echo $this->viewMore(strip_tags($post->getDescription()), 64) ?>
      </div>
    </li>
  <?php endforeach; ?>
</ul>