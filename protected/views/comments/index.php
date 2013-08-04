<?php
/* @var $this CommentsController */
/* @var $dataProvider CActiveDataProvider */
?>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'emptyText'=>'暂时没有数据',  
   	'template'=>'{items}{pager}',
   	'htmlOptions'=>array('class'=>'box'),
    'itemsTagName'=>'ol',
    'itemsCssClass'=>'box-cell',
)); ?>

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