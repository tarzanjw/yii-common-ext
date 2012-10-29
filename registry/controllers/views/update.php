<?php
$this->breadcrumbs=array(
	'Registries'=>array('admin'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'Create Registry', 'url'=>array('create')),
	array('label'=>'View Registry', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Registry', 'url'=>array('admin')),
);

$this->pageHeader = 'Update Registry <em>'.$model->name.'</em>';
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>