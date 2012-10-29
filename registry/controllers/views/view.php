<?php
$this->breadcrumbs=array(
	'Registries'=>array('admin'),
	$model->name,
);

$this->menu=array(
	array('label'=>'Create Registry', 'url'=>array('create')),
	array('label'=>'Update Registry', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Registry', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Registry', 'url'=>array('admin')),
);

$this->pageHeader = 'View Registry <em>'.$model->name.'</em>';
?>

<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'value',
		array(
			'name'=>'last_modified_time',
			'value'=>Yii::app()->dateFormatter->formatDatetime($model->last_modified_time, 'full', 'medium'),
		),
	),
)); ?>
