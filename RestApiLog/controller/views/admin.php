<?php
/** @var RestApiLogController $this */
$this->breadcrumbs=array(
	$this->logModelClass=>array('admin'),
	'Manage',
);

$this->pageHeader = 'Manage <em>'.$this->logModelClass.'</em> logs';
$this->layout = 'column1';

?>

<?php $this->widget('bootstrap.widgets.TbGridView', array(
	'id'=>'http-conn-log-grid',
//	'type'=>'striped',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'rowCssClassExpression'=>'$data->response_code == 200 ? "":"error"',
	'columns'=>array(
		array(
			'name'=>'id',
			'type'=>'raw',
			'value'=>'CHtml::link($data->id, array("view", "id"=>$data->id))',
			'htmlOptions'=>array(
				'style'=>'width:50px;text-align:right;',
			),
		),
		array(
			'name'=>'request_method',
			'header'=>'Method',
			'htmlOptions'=>array(
				'style'=>'width:50px;',
			),
		),
		array(
			'name'=>'request_url',
			'value'=>'urldecode($data->request_url)',
		),
		array(
			'name'=>'response_code',
			'htmlOptions'=>array(
				'style'=>'width:50px;text-align:right;',
			),
		),
		array(
			'name'=>'src_ip',
			'htmlOptions'=>array(
				'style'=>'width:90px;',
			),
		),
		array(
			'name'=>'dst_ip',
			'htmlOptions'=>array(
				'style'=>'width:90px;',
			),
		),
		array(
			'name'=>'request_time',
			'htmlOptions'=>array(
				'style'=>'text-align:right;',
			),
		),
		array(
			'name'=>'duration',
			'value'=>'sprintf("%0.3f", $data->duration)',
			'htmlOptions'=>array(
				'style'=>'width:50px;text-align:right;',
			),
		),
	),
)); ?>
