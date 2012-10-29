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
