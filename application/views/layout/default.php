<!doctype html>
<html>
<head>
<?php include_once('base/header.php'); ?>
<?php if (file_exists($_header)) { include_once($_header); } ?>
</head>
<body>
<div class="container">
<?php include_once($_container); ?>
</div>
<?php if (file_exists($_footer)) { include_once($_footer); } ?>
<?php include_once('base/footer.php'); ?>
</body>
</html>
