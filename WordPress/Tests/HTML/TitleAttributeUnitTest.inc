<iframe title="normal"></iframe> <?php // OK ?>
Not a title="" attribute <?php // OK ?>
<!-- title="comment" -->  <?php // OK ?>
<span class=""></span> <?php // OK ?>

<span title="normal"></span> <?php // Bad ?>
<span title=""></span> <?php // Bad ?>

<hr title="normal" /> <?php // Bad ?>
<hr title=""><span data-title="">This span is fine</span> <?php // Bad ?>
