<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>

<h1 class="page-header">Job
	<a href="/index.php?r=job/create" class="btn btn-primary pull-right">
	Create Job</a>
</h1>


<?php if(null !== Yii::$app->session->getflash('sucess')) : ?>
	<div class="alert alert-success"><?php echo Yii::$app->session->getflash('sucess'); ?></div>
<?php endif ?>


<?php if (!empty($jobs)) : ?>
	<ul>
		<?php foreach ($jobs as $job) : ?> 
			<?php  
				$phpDate = strtotime($job->create_date);
				$formattedDate = date("F j, Y, g:i a", $phpDate);
			?>
			<li class="list-group-item">
				<a href="/index.php?r=job/details&id=<?php echo $job->id ?>">
					<?php echo $job->title ?>
				</a>
				&nbsp;&nbsp; Location - 
				<strong><?php echo $job->city ?></strong>
				- 
				<strong><?php echo $job->state ?></strong>
				&nbsp;&nbsp; Listed on - 
				<span><?php echo $formattedDate; ?></span>
			</li>
		<?php endforeach; ?>	
	</ul>

<?php else : ?>
	<p>No Jobs available</p>

<?php endif; ?>


<?php 
	LinkPager::widget(['pagination'=>$pagination]);
 ?>