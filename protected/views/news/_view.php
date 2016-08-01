<div class="post" style="padding: 20px 0; box-shadow: 0 0 10px rgba(0,0,0,0.5);">
	<div class="title">
		<?php echo CHtml::link(CHtml::encode($data->title), $data->url); ?>
	</div>
	<div class="author">
		posted by <?php echo $data->author->username . ' on ' . date('F j, Y',$data->create_time); ?>
	</div>
	<div class="content">
		<?php
			$this->beginWidget('CMarkdown', array('purifyOutput'=>true));
                        if ('view' == Yii::app()->controller->action->id) {
							echo $data->content;
						} else {
							echo $data->preview;	
						}		
                        
			$this->endWidget();
		?>
	</div>
	<div class="nav">
		<b>Category:</b>
		<?php 
			$first = true;
			foreach ($data->category as $category) {
				if (!$first) {
					echo ', ';
				} else {
					$first = false;
				}
				echo CHtml::link($category->name, array('/category/' . $category->id));
			}
		?>
		<br/>
		<?php echo CHtml::link('Permalink', $data->url); ?> |
		<?php echo CHtml::link("Comments ({$data->commentCount})",$data->url.'#comments'); ?> |
		Last updated on <?php echo date('F j, Y',$data->update_time); ?>
	</div>
</div>
