<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="container">
	<div class="row">

		<div class="col-md-2">
			<ul class="list-group">
				<a href="<?= base_url('admin') ?>">
					<li class="list-group-item">Home</li>
				</a>
				<a href="<?= base_url('admin/users') ?>">
					<li class="list-group-item active">Users</li>
				</a>
				<a href="<?= base_url('admin/forums_and_topics') ?>">
					<li class="list-group-item">Forums & topics</li>
				</a>
				<a href="<?= base_url('admin/contactos') ?>">
					<li class="list-group-item">Support</li>
				</a>
			</ul>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Edit user <?= $user->username ?></h3>
				<p>Edit user rights</p>
			</div>
		</div>
		<br>
		<div class="col-md-10">



			<div class="panel-body">
				<?php if (validation_errors()) : ?>
					<div class="alert alert-danger" role="alert">
						<p><?= validation_errors() ?></p>
					</div>
				<?php endif; ?>
				<?php if (isset($error)) : ?>
					<div class="alert alert-danger" role="alert">
						<p><?= $error ?></p>
					</div>
				<?php endif; ?>
				<?php if (isset($success)) : ?>
					<div class="alert alert-success" role="alert">
						<p><?= $success ?></p>
					</div>
				<?php endif; ?>
				<?= form_open() ?>
				<div class="form-group">
					<label for="user_rights">Select user rights</label>
					<select class="form-control" name="user_rights" id="user_rights">
						<?= $options ?>
					</select>
				</div>
				<input type="submit" class="btn btn-default" value="Update rights">
				<?php if ($user->updated_at !== null) : ?>
					<small>(last update by <?= $user->updated_by ?>, <?= $user->updated_at ?>)</small>
				<?php endif; ?>
				</form>
			</div>
		</div>
	</div>
</div><!-- .row -->
</div><!-- .container -->

<?php //var_dump($users); 
?>