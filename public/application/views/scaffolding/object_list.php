<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<h3>
			<a class="add_new_object">
				<span class="glyphicon glyphicon-plus"></span>
			</a>
			<?php echo $table_name;?>
			<input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id;?>">
		</h3>
	</div>
</div>

<div class="row">
	<div class="col-md-10 col-md-offset-1">
	<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="object_list_table">
		<thead>
			<tr>
				<?php

				if(count($fields)){

					foreach($fields as $field){
						if(!$field->showInList()){
							continue;
						}

						echo '<th>' . $field->display_name . '</th>';
					}
				}else{
					echo '<th>No columns are currently set for view.</th>';
				}

				?>
			</tr>
		</thead>
		<tbody></tbody>
	</table>
	</div>
</div>