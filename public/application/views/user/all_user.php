
<div class="row pad-top">
	<div class="col-md-3 col-md-offset-1">
		<a type="button" class="btn btn-default btn-sm" href="/user/create_user">
			<span class="glyphicon glyphicon-plus"></span>&nbsp;New User
		</a>
	</div>
</div>

<div class="row pad-top">
	<div class="col-md-3 col-md-offset-1">
		<ul class="list-group">
			<?php if(count($users)):?>
			<?php foreach($users as $id=>$email):?>
				<li class="list-group-item">
					<a class="btn btn-danger pointer btn-xs"><span userid="<?php echo $id;?>" class="remove_user glyphicon glyphicon-remove"></span></a>
					&nbsp;<a class="btn btn-info pointer btn-xs"><span userid="<?php echo $id;?>" class="edit_user_group glyphicon glyphicon-pencil"></span></a>
					&nbsp;<?php echo $email; ?>
				</li>
			<?php endforeach;?>
			<?php endif;?>
		</ul>
	</div>
	<div class="col-md-1">&nbsp;</div>
</div>
