<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?= $breadcrumb ?>
		</div>
		<div class="col-md-12">
			<div class="page-header">
				<h1>Create a new topic</h1>
			</div>
		</div>
		<?php if ($login_needed) : ?>
			<div class="col-md-12">
				<div class="alert alert-danger" role="alert">
					<p>You need to be logged in to create a new topic!</p>
					<p>Please <a href="<?= base_url('login') ?>">login</a> or <a href="<?= base_url('register') ?>">register a new account</a>.</p>
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
				<form method="post" enctype="multipart/form-data">
					<div class="form-group">
						<label for="title">Topic title</label>
						<input type="text" class="form-control" id="title" name="title" placeholder="Enter a topic title" value="<?= $title ?>">
					</div>
					<div class="form-group">
						<label for="content">Content</label>
						<textarea rows="6" class="form-control" id="content" name="content" placeholder="Enter your topic content here"><?= $content ?></textarea>
					</div>
					<div class="form-group">
						<label for="contentimg">Image (not mandatory)</label><br>
						<input type="file"  id="contentimg" name="contentimg" value="Upload Image" />
					</div>
					<div class="form-group">
						<input class="btn btn-xs btn-primary" type="submit" class="btn btn-default" value="Create topic">
					</div>
				</form>
			</div>
		<?php endif; ?>
	</div><!-- .row -->
</div><!-- .container -->
