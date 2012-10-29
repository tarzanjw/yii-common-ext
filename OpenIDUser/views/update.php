<?php
//$this->layout = false;

$this->breadcrumbs=array(
    'OpenID Users'=>array('admin'),
    'Update '.$model->email,
);

$this->pageHeader = 'Update User <em>'.$model->name.'('.$model->email.')</em>';
?>

<?php /** @var TbActiveForm */$form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'open-id-user-form',
	'type'=>'horizontal',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'well'),
)); ?>

	<?php $this->widget('bootstrap.widgets.TbAlert', array(
        'block'=>true, // display a larger alert block?
        'fade'=>true, // use transitions?
        'closeText'=>'&times;', // close link text - if set to false, no close link is displayed
        'alerts'=>array( // configurations per alert type
            'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'&times;'), // success, info, warning, error or danger
        ),
    )); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model, 'email'); ?>
	<?php echo $form->textFieldRow($model, 'name'); ?>
	<?php echo $form->checkBoxRow($model, 'enable'); ?>

	<?php echo $form->checkBoxListRow($model, 'roles',  array_combine($this->roles,$this->roles), array('multiple'=>"multiple")); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'icon'=>'ok', 'label'=>$model->isNewRecord ? 'Create' : 'Save')); ?>
	</div>

<?php $this->endWidget(); ?>
