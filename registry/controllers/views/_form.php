<?php /** @var BootActiveForm $form */
	$form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
	'id'=>'registry-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'well'),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model, 'name', array('class'=>'span8')); ?>
	<?php echo $form->textAreaRow($model, 'value', array('class'=>'span8', 'cols'=>'80','rows'=>8)); ?>

	<div class="form-actions">
    	<?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'type'=>'primary', 'icon'=>'ok white', 'label'=>$model->isNewRecord?'Create':'Save')); ?>
    </div>

<?php $this->endWidget(); ?>
