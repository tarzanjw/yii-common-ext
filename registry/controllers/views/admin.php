<?php
$this->breadcrumbs=array(
	'Registries'=>array('admin'),
	'Manage',
);

$this->menu=array(
	array('label'=>'Create Registry', 'url'=>array('create')),
);

$this->pageHeader = 'Manage Registries';

?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'type'=>'striped',
	'id'=>'registry-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'value',
		array(
			'name'=>'last_modified_time',
        	'value'=>'Yii::app()->dateFormatter->formatDatetime($data->last_modified_time, "medium", "medium")',
		),
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
