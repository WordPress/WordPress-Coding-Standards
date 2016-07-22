<div>
<?php echo $var; ?>
Some content here.
<% echo $var; %>
<p>Some text <% echo $var; %> and some more text</p>
<%= $var . ' and some more text to make sure the snippet works'; %>
<p>Some text <%= $var %> and some more text</p>
<script language="php">
echo $var;
</script>
<script language='php'>echo $var;</script>
<script type="text/php" language="php">
echo $var;
</script>
<script language='PHP' type='text/php'>
echo $var;
</script>
</div>
