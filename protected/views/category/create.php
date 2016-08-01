<?php
/* @var $this CategoryController */
/* @var $model Category */

$this->breadcrumbs=array(
	'Create',
);

$this->menuCategory=array(
	array('label'=>'Главная', 'url'=>array('/')),
	array('label'=>'Список категорий', 'url'=>array('admin')),
);
?>

<h1>Create Category</h1>

<?php $this->renderPartial('_form', array('model'=>$model,'categories'=>$categories)); ?>