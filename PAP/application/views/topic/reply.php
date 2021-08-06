<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="container">

	<div class="row">
		<div class="col-md-12">
			{breadcrumb}
		</div>
	</div>
	<div class="col-md-12">
		<div class="page-header">
			<h1></h1>
		</div>
	</div>

	<div class="col-md-12">


		{posts}



	</div>




	{login_needed}
	{val_error}
	{error}

	<div class="col-md-12">
		<form method="post">
			<div class="form-group">
				<label for="reply">Reply</label>
				<textarea rows="6" class="form-control" id="reply" name="reply" placeholder="">{content}</textarea>
			</div>
			<div class="form-group">
				<input  class="btn  btn-primary" type="submit" class="btn btn-default" value="Reply">
			</div>
		</form>
	</div>

</div><!-- .container -->

<?php //var_dump($forum, $topic, $posts); 
?>