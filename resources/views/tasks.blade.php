<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Tasks list</title>

		<style type="text/css">
			.user_tasks{
				width: 25%;
				min-height: 300px;
				float: left;
				border: 1px solid gray;
				padding: 15px;
				margin-right: 15px;
				margin-bottom: 15px;
			}
			ul{
				margin-top: 10px;
				margin-bottom: 10px;
			}
		</style>
	</head>
	<body>
		@foreach($tasks as $key => $t)
			<div class="user_tasks">
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
	</body>
</html>