<?php
$this->breadcrumbs=array(
	$this->logModelClass=>array('admin'),
	$model->id,
);

$this->menu=array(
	array('label'=>'Manage '.$this->logModelClass, 'url'=>array('admin')),
	array('label'=>'Delete '.$this->logModelClass, 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
);

$this->pageHeader = 'View '.$this->logModelClass.'#'.$model->id;

$this->layout = 'column2';

Yii::app()->clientScript->registerCssFile('https://google-code-prettify.googlecode.com/svn/trunk/src/prettify.css');
Yii::app()->clientScript->registerScriptFile('https://google-code-prettify.googlecode.com/svn/trunk/src/prettify.js');
?>

<?php
	parse_str(urldecode($model->post_data), $postData);
	$responseData = (PHP_VERSION >= '5.4') ? json_encode(json_decode($model->response_data), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE):$model->response_data;
?>

<?php $this->widget('bootstrap.widgets.TbDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'request_method',
		'request_url',
		'src_ip',
		'dst_ip',
		array(
			'name'=>'request_headers',
			'type'=>'raw',
			'value'=>'<pre>'.$model->request_headers."</pre>",
		),
		array(
			'name'=>'response_headers',
			'type'=>'raw',
			'value'=>'<pre>'.$model->response_headers."</pre>",
		),
		'request_time',
		'duration',
		array(
			'name'=>'post_data',
			'type'=>'raw',
			'value'=>'<pre>'.print_r($postData, true)."</pre>",
		),
		array(
			'name'=>'response_data',
			'type'=>'raw',
			'value'=>'<pre class="prettyprint">'.CHtml::encode($responseData).'</pre>',
		),
	),
)); ?>
<script language="JavaScript">
<!--
jQuery(function() { prettyPrint(); });
//-->
</script>