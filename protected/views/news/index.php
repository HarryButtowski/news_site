<?php
/* @var $this NewsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Новости',
);

$this->menuNews=array(
	array('label'=>'Создать', 'url'=>array('create')),
	array('label'=>'Список новостей', 'url'=>array('admin')),
);

$this->menuCategory=array(
	array('label'=>'Создать', 'url'=>array('/category/create')),
	array('label'=>'Список категорий', 'url'=>array('/category/admin')),
);
?>
<h1>Новости</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
        'emptyText'=>'В данной категории нет новостей.',
        'sortableAttributes'=>array('create_time'),
        'template'=>"{sorter}\n{items}\n{pager}",
        
)); ?>
