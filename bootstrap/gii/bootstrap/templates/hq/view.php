<?php
/**
 * The following variables are available in this template:
 * - $this: the BootCrudCode object
 */
?>
<?php
echo "<?php\n";
$nameColumn=$this->guessNameColumn($this->tableSchema->columns);
$label=$this->pluralize($this->class2name($this->modelClass));
echo "\$this->breadcrumbs=array(
	'$label'=>array('admin'),
	\$model->{$nameColumn},
);\n";
?>

$this->menu=array(
	array('label'=>'Create <?php echo $this->modelClass; ?>','url'=>array('create')),
	array('label'=>'Update <?php echo $this->modelClass; ?>','url'=>array('update','id'=>$model-><?php echo $this->tableSchema->primaryKey; ?>)),
	array('label'=>'Delete <?php echo $this->modelClass; ?>','url'=>'#','linkOptions'=>array('submit'=>array('delete','id'=>$model-><?php echo $this->tableSchema->primaryKey; ?>),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage <?php echo $this->modelClass; ?>','url'=>array('admin')),
);

<?php $id = isset($this->tableSchema->columns['name']) ? '$model->name' : "\$model->{$this->tableSchema->primaryKey}"; ?>

$this->pageHeader = 'View <?php echo $this->modelClass; ?> <em>'.<?php echo $id; ?>.'</em>';
?>

<?php echo "<?php"; ?> $this->widget('bootstrap.widgets.TbDetailView',array(
	'data'=>$model,
	'attributes'=>array(
<?php
foreach($this->tableSchema->columns as $column)
	echo "\t\t'".$column->name."',\n";
?>
	),
)); ?>
