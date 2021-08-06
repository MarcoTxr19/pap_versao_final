<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container">
	<div class="row">
		<div class="col-md-2">
			<ul class="list-group">
					<a href="<?=base_url('admin')?>"><li class="list-group-item">Home</li></a>
					<a href="<?=base_url('admin/users')?>"><li class="list-group-item ">Users</li></a>
					<a href="#"><li class="list-group-item active">Forums & topics</li></a>
					<a href="<?= base_url('admin/contactos') ?>"><li class="list-group-item">Support</li></a>
				</ul>
			</div>
		<div class="col-md-10">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Forums & topics</h3>
					<p>Manage all forum topics and posts</p>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="panel-body">
				<table class="table table-striped">
				<thead>
					<tr>
						<th>
							#
						</th>

						<th>
							Title
						</th>
						<th>
							Slug
						</th>
						<th>
							Description
						</th>
						<th>
							Created At
						</th>
						
						<th>
							Created By 
						</th>
						<th>
							Tags 
						</th>
						<th>
							Actions 
						</th>
						

					</tr>
				</thead>
				{listForum}
			</table>
			
		</div>
	</div>
	<div class="row">
		<div class="col">
			{topic}
		</div>
	</div>
	<div class="row">
		<div class="col">
			{post}
		</div>
	</div>
	
</div><!-- .container -->