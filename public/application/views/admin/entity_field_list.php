<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<h3>
			<a class="add_new_field" entity_id="<?php echo $table_id?>" >
				<span class="glyphicon glyphicon-plus"></span>
			</a>
			<?php echo $table_name;?>
		</h3>
	</div>
</div>

<div class="row">
	<div class="col-md-1">
		<input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id;?>">
	</div>
	<div id="col-md-10">
	<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="entity_fields_table">
		<thead>
			<tr>
				<th>Tools</th>
				<th>Field Name</th>
				<th>Display Name</th>
				<th>Field Type</th>
				<th>Show In List</th>
				<th>Is Primary Key</th>
				<th>Field Order</th>
				<th>Last Updated</th>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
	</div>
</div>

<div class="row">
	<div class="col-md-1">&nbsp;</div>
	<div class="col-md-10">
		<a type="button" href="/admin" class="btn btn-default">Back</a>
	</div>
</div>