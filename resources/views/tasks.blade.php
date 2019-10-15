<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
    	<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Tasks Management</title>

		<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
		
		<style type="text/css">
			#errors{
				margin-bottom: 15px;
			}
			.form-control{
				border-radius: 0px;
			}
			#create_task{
				margin-top: 25px;
				border-radius: 0px;
			}
			.user_tasks{
				width: 25%;
				min-height: 300px;
				float: left;
				border: 1px solid gray;
				padding: 15px;
				margin-right: 15px;
				margin-bottom: 15px;
			}
			.alert-danger{
				width: 50%;
			}
			ul{
				margin-top: 10px;
				margin-bottom: 10px;
			}
		</style>
	</head>
	<body>
		<div class="container-fluid">
			<form action="#" class="form-horizontal" style="padding-top: 40px;">
				<h4>Create task</h4>
				<div id="errors"></div>
				<div class="form-group">
					<div class="col-md-3">
						<label for="">User</label>
						<select name="user_id" class="form-control">
							@if(isset($user['data']) && count($user['data']) > 0)
								@foreach($user['data'] as $u)
									<option value="{{ $u['id'] }}">{{ $u['first_name'] }} {{ $u['last_name'] }}</option>
								@endforeach
							@endif
						</select>
					</div>
					<div class="col-md-3">
						<label for="">Email</label>
						<input type="email" name="email" class="form-control" placeholder="Email">
					</div>
					<div class="col-md-3">
						<label for="">Parent Task</label>
						<select name="parent_id" class="form-control">
							<option value="">Select</option>
							@foreach($task_list as $tl)
								<option value="{{ $tl->id }}">{{ $tl->title }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-3">
						<label for="">Task title</label>
						<input type="text" name="title" class="form-control" placeholder="Task title">
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-3">
						<label for="">Points</label>
						<input type="number" name="points" class="form-control" placeholder="Points">
					</div>
					<div class="col-md-3">
						<label for="">Is done</label>
						<select name="is_done" class="form-control">
							<option value="1">Yes</option>
							<option value="0">No</option>
						</select>
					</div>
					<div class="col-md-3">
						<button type="button" id="create_task" class="btn btn-success btn-block">Create task</button>
					</div>
				</div>
			</form>
			<hr>
			<h4>Task list</h4>
			@foreach($task_data as $key => $t)
				<div class="user_tasks" style="padding-top: 40px;">
					<p>{{ $t['name'] }} &#40;{{ $t['task_done'] }}/{{ $t['total_task'] }}&#41;</p>
					@foreach($t['tasks'] as $tl)
						<ul>
							<li>{{ $tl->title }} &#40;{{ isset($tl->total_points) ? $tl->total_points : 0 }}&#41;</li>
							@if(count($tl->subtasks) > 0)
								<ul>
									@foreach($tl->subtasks as $st)
										<li>{{ $st->title }} &#40;{{ isset($st->total_points) ? $st->total_points : 0 }}&#41;</li>
										@if(count($st->subsubtasks) > 0)
											<ul>
												@foreach($st->subsubtasks as $sst)
													<li>{{ $sst->title }} &#40;{{ $sst->points }}&#41;</li>
												@endforeach
											</ul>
										@endif
									@endforeach
								</ul>
							@endif
						</ul>
					@endforeach
				</div>
			@endforeach
		</div>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
		
		<script type="text/javascript">
			// Define event when click to the save button
			$('#create_task').click(function(event) {
				event.preventDefault();

				$('#errors').html('');
				var form = $(this).closest('form');
				var formData = getFormData(form);

				// Ajax POST request
				$.ajax({
					url: "{{ url('task') }}",
					type: 'POST',
					contentType: 'application/json',
					dataType: 'json',
					data: JSON.stringify(formData),
				})
				.done(function(res) {
					location.reload();
				})
				.fail(function(err) {
					if (typeof(err.responseJSON) != 'undefined') {
						var errorTxt = '';
						$.each(err.responseJSON, function(index, val) {
							errorTxt += '<div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button><strong>'+index+':</strong> '+val+'</div>'
						});
						$('#errors').html(errorTxt);
					}
				});
			});

			/**
			 * Form data to structured object
			 * @param form
			 * @return onject
			 */
			function getFormData(form){
			    var unindexed_array = form.serializeArray();
			    var indexed_array = {};

			    $.map(unindexed_array, function(n, i){
			    	if (n['name'] == 'is_done' || n['name'] == 'points')
			    		indexed_array[n['name']] = parseInt(n['value']);
			    	else if(n['value'] == '')
			    		indexed_array[n['name']] = null;
			    	else
			    		indexed_array[n['name']] = n['value'];
			    });

			    return indexed_array;
			}
		</script>
	</body>
</html>