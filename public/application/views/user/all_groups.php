<div class="row pad-top">
	<div class="col-md-1">&nbsp;</div>
	<div class="col-md-3">
		<a type="button" class="btn btn-default btn-sm" href="/user/create_group">
			<span class="glyphicon glyphicon-plus"></span>&nbsp;New Group
		</a>
	</div>
</div>

<div class="row pad-top">
	<div class="col-md-1">&nbsp;</div>
	<div class="col-md-3">
		<ul class="list-group">
			<?php if(count($groups)):?>
			<?php foreach($groups as $id=>$name):?>
				<li class="list-group-item">
					<a class="btn btn-danger pointer btn-xs"><span groupid="<?php echo $id;?>" class="remove_group glyphicon glyphicon-remove"></span></a>
					&nbsp;<a class="btn btn-info pointer btn-xs"><span groupid="<?php echo $id;?>" class="edit_group glyphicon glyphicon-pencil"></span></a>
					&nbsp;<?php echo $name; ?>
				</li>
			<?php endforeach;?>
			<?php endif;?>
		</ul>
	</div>
	<div class="col-md-1">&nbsp;</div>
</div>
