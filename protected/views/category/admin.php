<?php
/* @var $this CategoryController */
/* @var $model Category */

$this->breadcrumbs=array(
	'Список',
);

$this->menuCategory=array(
	array('label'=>'Главная', 'url'=>array('/')),
	array('label'=>'Создать категорию', 'url'=>array('create')),
);
?>

<h1>Список категорий</h1>


<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'category-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'parent_id',
		array(
                    'name' => 'update_time',
                    'type' => 'datetime',
                    'filter' => false,
                ),
		array(
			'class'=>'CButtonColumn',
			'template'=>'{delete}{update}',
		),
	),
)); ?>
