<?php
/**
 * The following variables are available in this template:
 * - $this: the BootCrudCode object
 */
?>
<?php echo "<?php\n"; ?>
/* @var $this <?php echo $this->getControllerClass(); ?> */
/* @var $model <?php echo $this->getModelClass(); ?> */
<?php echo '?>',"\n"; ?>
<?php
echo "<?php\n";
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	'$label'=>array('admin'),
	'Manage',
);\n";
?>

$this->menu=array(
	array('label'=>'Create <?php echo $this->modelClass; ?>','url'=>array('create')),
);

$this->pageHeader = 'Manage <?php echo $this->pluralize($this->class2name($this->modelClass)); ?>';
?>

<?php echo "<?php"; ?> $this->widget('bootstrap.widgets.TbGridView',array(
	'id'=>'<?php echo $this->class2id($this->modelClass); ?>-grid',
	'type'=>'striped',
	'ajaxUpdate'=>false,
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
<?php
$count=0;
foreach($this->tableSchema->columns as $column)
{
	if(++$count==7)
		echo "\t\t/*\n";
	echo "\t\t'".$column->name."',\n";
}
if($count>=7)
	echo "\t\t*/\n";
?>
		array(
			'class'=>'bootstrap.widgets.TbButtonColumn',
		),
	),
)); ?>
