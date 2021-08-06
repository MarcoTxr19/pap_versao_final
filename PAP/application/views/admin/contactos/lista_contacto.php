<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container">
	<div class="row">
		<div class="col-md-2">
		<ul class="list-group">
				<a href="<?=base_url('admin')?>"><li class="list-group-item">Home</li></a>
				<a href="<?=base_url('admin/users')?>"><li class="list-group-item ">Users</li></a>
				<a href="<?=base_url('admin/forums_and_topics')?>"><li class="list-group-item ">Forums & topics</li></a>
				<a href="<?= base_url('admin/contactos') ?>"><li class="list-group-item active">Support</li></a>
			</ul>
		</div>
        <div class="panel-heading">
					<h3 class="panel-title">Support and Flags</h3>
					<p>Preview and reply all reports and support messages </p>
				</div>
		</div>
		<div class="col-md-10">
			<div class="panel panel-default">
				
				<div class="panel-body">
				<table class="table table-striped">
				<thead>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>
                    #
                </th>

                <th>
                    Type
                </th>
                <th>
                    Message
                </th>
                <th>
                    Sent By User #
                </th>
                <th>
                    Preview Report
                </th>

            </tr>
        </thead>
        {contactos}
    </table>

    <div>
    {preview}
    </div>
</div>