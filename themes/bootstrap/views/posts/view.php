<?php
/* @var $this PostsController */
/* @var $row Posts */
?>

<div class="box-cell">
	<div class="panel">
		<p>
			<?php if (Yii::app()->user->id) {
				$saveCount = Save::model()->countByAttributes(array('bp_id'=>$row->bp_id, 'bu_id'=>Yii::app()->user->id, 'type'=>'1'));
				//判断是否是自己发布的
				if ($row->bu_id==Yii::app()->user->id) {
			?>
				<span style="width:13px; color:red;">*</span>
			<?php
				//判断是否是已经收藏的
				}elseif ($saveCount==1) {
			?>
				<span><img src="<?php echo tbu();?>images/s.gif" width="13"></span>
			<?php
				}else{
			?>
				<span onclick="getScore('<?php echo $row->bp_id; ?>',this)"><img src="<?php echo tbu();?>images/grayarrow.png" width="13" title="赞"></span>
			<?php
				}
			}else{
			?>
				<span onclick="getScore('<?php echo $row->bp_id; ?>',this)"><img src="<?php echo tbu();?>images/grayarrow.png" width="13" title="赞"></span>
			<?php } ?>
			
			<?php echo CHtml::link($row->bp_title, $row->bp_url, array('target'=>'_blank', 'class'=>'post-title'));?>
			<span>(<?php echo GetDomain($row->bp_url); ?>)</span>
		</p>
		<p><span><?php echo $row->bp_like; ?></span>个赞
		来自 <?php echo CHtml::link($row->user->bu_name, array('/user/view', 'id'=>$row->bu_id)); ?>
		<?php echo tranTime($row->bp_create_time); ?>
		|
		<?php echo CHtml::link(Comments::model()->Count('bp_id='.$row->bp_id).'条评论', array('/posts/view', 'id'=>$row->bp_id)); ?></p>
		<?php if($row->bp_video_url): ?>
			<object type="application/x-shockwave-flash" data="<?php echo $row->bp_video_url; ?>" width="100%" height="520px">
			    <param name="movie" value="<?php echo $row->bp_video_url; ?>">
			</object>
		<?php endif; ?>
		<?php echo ($row->bp_content)?'<pre>'.$row->bp_content.'</pre>':''; ?>
	</div>

	<!-- 评论 -->
	<div class="form">
		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'comments-form',
			'enableClientValidation'=>true,
			'clientOptions'=>array(
				'validateOnSubmit'=>true,
			),
			'htmlOptions'=>array(
				'class'=>'form-horizontal',
			)
		)); ?>

			<div class="form-group">
				<div class="col-lg-9">
					<?php echo $form->textArea($model,'bc_text',array('rows'=>6,'class'=>'form-control')); ?>
					<?php echo $form->error($model,'bc_text'); ?>
				</div>
			</div>

			<div class="form-group">
				<div class="col-lg-9">
					<?php echo CHtml::submitButton(t('addComments','main'), array('class'=>'btn btn-default')); ?>
				</div>
			</div>

		<?php $this->endWidget(); ?>
	</div>

</div>

</br>
<!-- 加载评论 -->
<?php if (Comments::model()->count('bp_id='.$row->bp_id)){ ?>
		<?php  $this->renderPartial('_comments', array('comments'=>$comments));?>
<?php } ?>

<script type="text/javascript">
//赞
<?php  if (Yii::app()->user->id){ ?>
function getScore(id,that,type){
	type=type||'1';
	$.ajax({
        type:"POST",
        url: "<?php echo Yii::app()->createUrl('/posts/ajaxGetScore/') ?>",
        data:"id="+id+"&type="+type,
        success: function(msg){
        	$(that).parent().next().children('span').html(msg);
        	$(that).removeAttr("onclick");
        	$(that).children('img').attr('src','<?php echo tbu();?>images/s.gif');
		}
    });
};
<?php } else{?>
function getScore(id,that){
	location.href = "<?php echo Yii::app()->createUrl('/site/login') ?>";
};
<?php } ?>
</script>