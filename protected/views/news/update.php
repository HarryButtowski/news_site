<?php
/* @var $this NewsController */
/* @var $model News */
$this->breadcrumbs=array(
	$model->title=>array('/'.$model->slug),
	'Update',
);

$this->menuNews=array(
	array('label'=>'Главная', 'url'=>array('index')),
	array('label'=>'Создать', 'url'=>array('create')),
	array('label'=>'Список новостей', 'url'=>array('admin')),
);
?>

<h1>Update <?php echo $model->title; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model,'categories'=>$categories)); ?>