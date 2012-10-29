<?php
$this->breadcrumbs=array(
    'OpenID Users'=>array('admin'),
    'Manage',
);

$this->menu=array(
    array('label'=>'Create OpenIDUser','url'=>array('create')),
);

$this->pageHeader = 'Manage OpenID Users';
?>

<form method="POST">
<?php $this->widget('bootstrap.widgets.TbGridView',array(
    'id'=>'open-iduser-grid',
//    'type'=>'striped',
    'dataProvider'=>$model->search(),
    'selectableRows'=>false,
    'ajaxUpdate'=>false,
    'filter'=>$model,
    'rowCssClassExpression'=>function($i, $e) {
    	$classes = !($i%2)?array('odd'):array('even');
    	if (in_array('ADMIN', $e->roles)) $classes[] = 'success';
    	if (!$e->enable) $classes[] = 'error';

    	return implode(' ', $classes);
    },
    'columns'=>array(
        array(
        	'name'=>'email',
        	'type'=>'raw',
        	'value'=>function($e) {
        		$i = $e->enable ? '<i class="icon-thumbs-up"></i>':'<i class="icon-thumbs-down"></i>';
            	$i .= '&nbsp'.$e->email;

            	return CHtml::link($i, array('', 'update'=>$e->id), array(
//                	'data-toggle'=>"modal",
//                	'data-target'=>"#modal",
            	));
			}
        ),
        array(
        	'name'=>'name',
        	'type'=>'raw',
        	'value'=>function($e) {
            	if (empty($e->avatar)) return CHtml::tag('span', array(
                	'style'=>'margin-left:20px',
            	), '&nbsp;'.CHtml::encode($e->name), true);

            	return CHtml::image($e->avatar, $e->name, array('style'=>'width:20px')).' '.CHtml::encode($e->name);
        	},
        ),
        array(
        	'name'=>'roles',
        	'type'=>'raw',
        	'value'=>'implode(", ",$data->roles)',
        ),
        array(
        	'name'=>'created_time',
        	'htmlOptions'=>array(
            	'style'=>'text-align:right;'
        	),
        ),
        /*array(
            'class'=>'bootstrap.widgets.TbButtonColumn',
            'template'=>'{update} {delete}',
        ), */
    ),
)); ?>
</form>
