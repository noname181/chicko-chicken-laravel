@if (count($errors) > 0)
<!-- Start Box Body -->
<div class="box-body">
	<div class="alert alert-danger">

		<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			<span aria-hidden="true">×</span>
		</button>

		<ul class="m-0">
			@foreach ($errors->all() as $error)
			<li><i class="glyphicon glyphicon-remove myicon-right"></i> <small> {{{ $error }}} </small> </li>
			@endforeach
		</ul>
	</div>
</div><!-- /.box-body -->
@endif