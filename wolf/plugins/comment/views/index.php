<?php
/* Security measure */
if (!defined('IN_CMS')) {
    exit();
}
?>

<h1><?php echo __('Comments'); ?></h1>
<div id="comments-def">
    <div class="comment"><?php echo __('Comments'); ?></div>
    <div class="modify"><?php echo __('Modify'); ?></div>
</div>
<?php
global $__CMS_CONN__;
$sql = "SELECT COUNT(*) FROM " . TABLE_PREFIX . "comment WHERE is_approved = 1";
$stmt = $__CMS_CONN__->query($sql);
if ($stmt) {
    $comments_count = $stmt->fetchColumn();
    $stmt->closeCursor();
}
if (isset($page)) {
    $CurPage = $page;
} else {
    $CurPage = 0;
}
$rowspage = Plugin::getSetting('rowspage', 'comment');

$start = $CurPage * $rowspage;

$totalrecords = $comments_count;
$sql = "SELECT comment.is_approved, comment.id, comment.page_id, comment.author_name, comment.author_email, comment.author_link, comment.body, comment.created_on, page.title FROM " .
        TABLE_PREFIX . "comment AS comment, " . TABLE_PREFIX .
        "page AS page WHERE comment.is_approved = 1 AND comment.page_id = page.id ORDER BY comment.created_on DESC LIMIT " . $rowspage . " OFFSET " . $start;

$stmt = $__CMS_CONN__->prepare($sql);
$stmt->execute();
$lastpage = ceil($totalrecords / $rowspage);
if ($comments_count <= $rowspage) {
    $lastpage = 0;
} else {
    $lastpage = abs($lastpage - 1);
}
?>
<?php if ($comments_count > 0) { ?>
    <ol id="comments">
        <?php while ($comment = $stmt->fetchObject()): ?>
            <li class="<?php echo odd_even(); ?> moderate">
                <strong><?php
        if ($comment->author_link != '') {
            echo '<a href="' . $comment->author_link . '" title="' . $comment->author_name . '">' . $comment->author_name . '</a>';
        } else {
            echo $comment->author_name;
        }
            ?></strong> <?php
                    if ($comment->author_email != '') {
                        echo '(' . $comment->author_email . ')';
                    } else {
                        
                    }
                    ?>
                <?php echo __('about'); ?> <strong><?php echo $comment->title; ?></strong>
                <p><?php echo $comment->body; ?></p>
                <div class="infos">
                    <?php echo date('D, j M Y', strtotime($comment->created_on)); ?> &#8212;
                    <a href="<?php echo getUrl('plugin/comment/edit/' . $comment->id); ?>"><?php echo __('Edit'); ?></a> |
                    <a href="<?php echo getUrl('plugin/comment/delete/' . $comment->id); ?>" onclick="return confirm('<?php echo __('Are you sure you wish to delete it?'); ?>');"><?php
            echo
            __('Delete');
                    ?></a> | <?php if ($comment->is_approved): ?>
                        <a href="<?php echo getUrl('plugin/comment/unapprove/' . $comment->id); ?>"><?php echo __('Reject'); ?></a>
                    <?php else: ?>
                        <a href="<?php echo getUrl('plugin/comment/approve/' . $comment->id); ?>"><?php echo __('Approve'); ?></a>
                    <?php endif; ?>
                </div>
            </li>
        <?php endwhile; ?>
    </ol>
    <?php
} else {
    echo '<h3>' . __('No comments found.') . '</h3>';
}
?>
<br />
<div class="pagination">
    <?php
    if ($CurPage == $lastpage) {
        $next = '<span class="disabled">Next Page</span>';
    } else {
        $nextpage = $CurPage + 1;
        $next = '<a href="' . getUrl('plugin/comment/index/') . '' . $nextpage .
                '">Next Page</a>';
    }
    if ($CurPage == 0) {
        $prev = '<span class="disabled">Previous Page</span>';
    } else {
        $prevpage = $CurPage - 1;
        $prev = '<a href="' . getUrl('plugin/comment/index/') . '' . $prevpage .
                '">Previous Page</a>';
    }
    if ($CurPage != 0) {
        echo "<a href=" . getUrl('plugin/comment/index/') . "0>First Page</a>\n ";
    } else {
        echo "<span class=\"disabled\">First Page</span>";
    }
    echo $prev;
    for ($i = 0; $i <= $lastpage; $i++) {
        $j = $i + 1;
        if ($i == $CurPage)
            echo '<span class="current">' . $j . '</span>';
        else
            echo " <a href=" . getUrl('plugin/comment/index/') . "$i>$j</a>\n";
    }
    echo $next;
    if ($CurPage != $lastpage) {
        echo "\n<a href=" . getUrl('plugin/comment/index/') . "$lastpage>Last Page</a>&nbsp&nbsp;";
    } else {
        echo "<span class=\"disabled\">Last Page</span>";
    }
    ?>
</div>
