<?php
$this->breadcrumbs=array(
	'Registries'=>array('admin'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Registry', 'url'=>array('index')),
	array('label'=>'Manage Registry', 'url'=>array('admin')),
);

$this->pageHeader = 'Create Registry';
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>