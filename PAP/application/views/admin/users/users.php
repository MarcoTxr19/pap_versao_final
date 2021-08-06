<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container">
	<div class="row">
		<div class="col-md-2">
		<ul class="list-group">
				<a href="<?=base_url('admin')?>"><li class="list-group-item">Home</li></a>
				<a href="#"><li class="list-group-item active">Users</li></a>
				<a href="<?= base_url('admin/forums_and_topics') ?>"><li class="list-group-item">Forums & topics</li></a>
				<a href="<?= base_url('admin/contactos') ?>"><li class="list-group-item">Support</li></a>
			</ul>
		</div>
		<div class="panel-heading">
					<h3 class="panel-title">Users</h3>
					<p>Manage all users</p>
				</div>
	</div>
		<div class="col-md-10">
			<div class="panel panel-default">
				<div class="panel-heading">
					
				</div>
				<div class="panel-body">
					<table class="table table-striped">
						<caption></caption>
						<thead>
							<tr>
								<th>#</th>
								<th>Username</th>
								<th>Rights</th>
								<th class="hidden-xs">Registration date</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($users as $user) : ?>
								<tr>
									<td><?= $user->id ?></td>
									<td><a href="<?= base_url('user/' . $user->username) ?>" target="_blank"><?= $user->username ?></a></td>
									<?php if ($user->is_admin) : ?>
									<td>admin</td>
									<?php elseif ($user->is_moderator) : ?>
									<td>mod</td>
									<?php else : ?>
									<td>user</td>
									<?php endif; ?>
									<td class="hidden-xs"><?= $user->created_at ?></td>
									<td><a class="btn btn-xs btn-primary" href="<?= base_url('admin/edit_user/' . $user->username) ?>">Edit</a> <a class="btn btn-xs btn-danger" href="<?= base_url('admin/delete_user/' . $user->username) ?>" onclick="return confirm('Are you sure you want to delete your account? If you click OK, your account will be immediatly and permanently deleted.')">Delete</a></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					
				</div>
			</div>
		</div>
	</div><!-- .row -->
</div><!-- .container -->

<?php //var_dump($users); ?>