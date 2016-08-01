<?php
/* @var $this NewsController */
/* @var $model News */

$this->breadcrumbs=array(
	'Create',
);

$this->menuNews=array(
	array('label'=>'Главная', 'url'=>array('index')),
	array('label'=>'Список новостей', 'url'=>array('admin')),
);
?>

<h1>Create News</h1>

<?php $this->renderPartial('_form', array('model'=>$model,'categories'=>$categories)); ?>