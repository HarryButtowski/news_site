<?php
/* @var $this NewsController */
/* @var $model News */

$this->breadcrumbs=array(
	'Список новостей',
);

$this->menuNews=array(
	array('label'=>'Главная', 'url'=>array('index')),
	array('label'=>'Создать новость', 'url'=>array('create')),
);
?>

<h1>Список новостей</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'news-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
                array(
                    'name' => 'title',
                    'type' => 'raw',
                    'value' => 'CHtml::link(CHtml::encode($data->title), $data->url)',
                ),
                array(
                    'name' => 'status',
                    'value' => 'Lookup::item("PostStatus",$data->status)',
                    'filter' => Lookup::items("PostStatus"),
                ),
                array(
                    'name' => 'create_time',
                    'type' => 'datetime',
                    'filter' => false,
                ),
		array(
			'class'=>'CButtonColumn',
			'template'=>'{delete}{update}',
		),
	),
)); ?>
