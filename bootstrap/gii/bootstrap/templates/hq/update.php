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
$nameColumn=$this->guessNameColumn($this->tableSchema->columns);
$label=$this->pluralize($this->class2name($this->modelClass));
$id = isset($this->tableSchema->columns['name']) ? '$model->name' : "\$model->{$this->tableSchema->primaryKey}";

echo "\$this->breadcrumbs=array(
	'$label'=>array('admin'),
	{$id}=>array('view','id'=>\$model->{$this->tableSchema->primaryKey}),
	'Update',
);\n";
?>

$this->menu=array(
	array('label'=>'Create <?php echo $this->modelClass; ?>','url'=>array('create')),
	array('label'=>'View <?php echo $this->modelClass; ?>','url'=>array('view','id'=>$model-><?php echo $this->tableSchema->primaryKey; ?>)),
	array('label'=>'Manage <?php echo $this->modelClass; ?>','url'=>array('admin')),
);

$this->pageHeader = 'Update <?php echo $this->modelClass; ?> <em>'.<?php echo $id;  ?>.'</em>';
?>

<?php echo "<?php echo \$this->renderPartial('_form',array('model'=>\$model)); ?>"; ?>