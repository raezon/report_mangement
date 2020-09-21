<a href="/index.php?r=job">Back to job</a>
<h2 class="page-header"><?php echo $job->title; ?>
 <small> in <?php echo $job->city ?>, <?php echo $job->state ?></small>
 <?php if(Yii::$app->user->identity->id == $job->user_id) : ?>
	 <span class="pull-right">
	 	<a class="btn btn-default" href="/index.php?r=job/edit&id=<?php echo $job->id; ?>">Edit</a>
	 	<a class="btn btn-danger" href="/index.php?r=job/delete&id=<?php echo $job->id; ?>">Delete</a>
	 </span>
<?php endif; ?>
</h2>

<?php if (!empty($job->description)): ?>
<div class="well">
	<h4>Job Description</h4>
	<?php echo $job->description; ?>
</div>
<?php endif; ?>

<ul class="list-group">
	<?php if (!empty($job->create_date)): ?>
	<?php  
		$phpDate = strtotime($job->create_date);
		$formattedDate = date("F j, Y, g:i a", $phpDate);
	?>
	<li class="list-group-item"><strong>Listing Date: </strong><?php echo $formattedDate; ?></li>
	<?php endif; ?>

	<?php if (!empty($job->category->name)): ?>
	<li class="list-group-item">
		<strong>Category: </strong><?php echo $job->category->name; ?>
	</li>
	<?php endif; ?>

	<?php if (!empty($job->type)): ?><li class="list-group-item"><strong>
		Type: </strong><?php echo $job->type; ?></li>
	<?php endif; ?>

	<?php if (!empty($job->salary_range)): ?><li class="list-group-item"><strong>
		Salary Range: </strong><?php echo $job->salary_range; ?></li>
	<?php endif; ?>

	<?php if (!empty($job->contact_email)): ?><li class="list-group-item"><strong>
		Contact Email: </strong><?php echo $job->contact_email; ?></li>
	<?php endif; ?>

	<?php if (!empty($job->contact_phone)): ?><li class="list-group-item"><strong>
		Contact Phone: </strong><?php echo $job->contact_phone; ?></li>
	<?php endif; ?>
</ul>

<a href="mailto:<?php echo $job->contact_email; ?>?Subject=Job%20Application" class="btn btn-primary">	Contact Employer </a>