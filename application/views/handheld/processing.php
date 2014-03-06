<html>
<head>
<title>Processing File</title>
</head>
<body>

<h3>Your file was successfully uploaded and is ready to be processed!</h3>

<ul>
<?php foreach ($upload_data as $item => $value):?>
<li><?php echo $item;?>: <?php echo $value;?></li>
<?php endforeach; ?>
</ul>

<p><?php echo anchor('handheld/process', 'Process File'); ?></p>

</body>
</html>