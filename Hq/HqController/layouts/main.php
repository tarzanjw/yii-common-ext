<!doctype html>
<html lang="<?php echo Yii::app()->language ?>">
<head>
	<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<meta content="<?php echo Yii::app()->language; ?>" name="language">
</head>
<body>
	<div id='globalbar'>
		<?php if (is_file($this->globalBarViewfile)): ?>
        	<?php include $this->globalBarViewfile; ?>
        <?php else: ?>
            <h3><?php echo $this->globalBarViewfile; ?></h3>
		<?php endif; ?>
	</div>

	<div class="container-fluid">
		<?php if(isset($this->breadcrumbs)):?>
			<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
				'homeLink'=>CHtml::link('Home', array((is_null($this->module)? $this->id:$this->module->id).'/')),
				'links'=>$this->breadcrumbs,
			)); ?><!-- breadcrumbs -->
		<?php endif?>

		<?php if (!empty($this->pageHeader)): ?>
		<legend><?= $this->pageHeader; ?></legend>
		<?php endif; ?>

		<?php echo $content; ?>
	</div>

	<hr>
	<footer>
		<div class="container-fluid copy">
			&copy; 2012 by VNPrice Secure Department.
		</div>
	</footer>
</body>
</html>