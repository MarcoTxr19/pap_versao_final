<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?= $breadcrumb ?>
		</div>
		<div class="col-md-12">
			<div class="page-header">
				<h1>Create a new forum</h1>
			</div>
		</div>
		<?php if (!$criarForum) : ?>
			<div class="col-md-12">
				<div class="alert alert-danger" role="alert">
					<p>Your Fame needs to be  higher than 25! Gain fame by helping users</p>
					<p>Please <a href="<?= base_url('login') ?>">login</a> ||| <a href="#">Confirm</a> </p>
				</div>
			</div>
		<?php else : ?>
			<?php if (validation_errors()) : ?>
				<div class="col-md-12">
					<div class="alert alert-danger" role="alert">
						<?= validation_errors() ?>
					</div>
				</div>
			<?php endif; ?>
			<?php if (isset($error)) : ?>
				<div class="col-md-12">
					<div class="alert alert-danger" role="alert">
						<?= $error ?>
					</div>
				</div>
			<?php endif; ?>
			<div class="col-md-12">
				<form	method="post">
					<div class="form-group">
						<label for="title">Title</label>
						<input type="text" class="form-control" id="title" name="title" placeholder="Title " value="<?= $title ?>">
					</div>
					<div class="form-group">
						<label for="description">Description</label>
						<textarea rows="6" class="form-control" id="description" name="description" placeholder="Description    (max 80 chars)"><?= $description ?></textarea>
					</div>
					<div class="form-group">
						<label for="tags">Tags</label>
						<input class="form-control" type="text" id="tags" name="tags" placeholder="Insert your forum tags. You should use a ' - ' so separate different tags" value=<?= $tags?>>
					</div>
					<div class="form-group">
						<input class="btn btn-primary" type="submit" class="btn btn-default" value="Create forum">
					</div>
				</form>
			</div>
		<?php endif; ?>
	</div><!-- .row -->
</div><!-- .container -->
