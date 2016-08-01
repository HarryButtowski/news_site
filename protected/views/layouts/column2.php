<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<div class="span-19">
	<div id="content">
		<?php echo $content; ?>
	</div><!-- content -->
</div>
<div class="span-5 last">
	<div id="sidebar">
	<?php
                if (!Yii::app()->user->isGuest) {
                    $this->beginWidget('zii.widgets.CPortlet', array(
                            'title'=>'Новости',
                    ));
                    $this->widget('zii.widgets.CMenu', array(
                            'items'=>$this->menuNews,
                            'htmlOptions'=>array('class'=>'operations'),
                    ));
                    $this->endWidget();
                }
	?>
	</div><!-- sidebar -->
        <div id="sidebar">
	<?php
                if (!Yii::app()->user->isGuest) {
                    $this->beginWidget('zii.widgets.CPortlet', array(
                            'title'=>'Рубрики',
                    ));
                    $this->widget('zii.widgets.CMenu', array(
                            'items'=>$this->menuCategory,
                            'htmlOptions'=>array('class'=>'operations'),
                    ));
                    $this->endWidget();
                }
	?>
	</div><!-- sidebar -->
</div>
<?php $this->endContent(); ?>