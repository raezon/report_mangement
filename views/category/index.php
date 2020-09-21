<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
<h1 class="page-header">Category
	<a href="/index.php?r=category/create" class="btn btn-primary pull-right">
	Create Category</a>
</h1>

<?php if(null !== Yii::$app->session->getflash('sucess')) : ?>
	<div class="alert alert-success"><?php echo Yii::$app->session->getflash('sucess'); ?></div>
<?php endif ?>

<ul>
	<?php foreach ($categories as $category) : ?> 
		<li class="list-group-item"><a href="/index.php?r=job&category=<?php $category->id ?>"></a><?php echo $category->name ?></li>
	<?php endforeach; ?>	
</ul>

<?php 
	LinkPager::widget(['pagination'=>$pagination]);
 ?>


