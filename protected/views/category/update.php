<?php
/* @var $this CategoryController */
/* @var $model Category */

$this->breadcrumbs=array(
	'Update',
);

$this->menuCategory=array(
	array('label'=>'Главная', 'url'=>array('index')),
	array('label'=>'Список категорий', 'url'=>array('admin')),
);
?>

<h1>Update Category <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model,'categories'=>$categories)); ?>