<?php if($_SERVER['HTTP_HOST']!='localhost'){ ?>
<base href="http://<?php echo $_SERVER['SERVER_NAME']; ?>/">
<?php } else {    ?>
<base href="http://<?php echo $_SERVER['HTTP_HOST']; ?><?php echo str_replace('\\',"/",(end(explode('htdocs',dirname(__FILE__)))));  ?>/">

<?php } ?>