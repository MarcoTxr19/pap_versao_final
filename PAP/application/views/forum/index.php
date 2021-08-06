<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<?= $breadcrumb ?>
		</div>
		<div class="col-md-12">
			<div class="page-header">
				<h1>Forums</h1>
			</div>
		</div>
		
		<div class="col-md-12">
			<table class="table table-striped table-condensed table-hover">
				<caption></caption>
				<thead>
					<tr>
						<th>Forums</th>
						<th>Topics</th>
						<th>Posts</th>
						<th class="hidden-xs">Latest topic</th>
					</tr>
				</thead>
				<tbody>
					<?php if ($forums) : ?>
						<?php foreach ($forums as $forum) : ?>
							<tr>
								<td>
									<p>
										<a href="<?= base_url('forum/'.$forum->slug) ?>"><?= $forum->title ?></a><br>
										<small><?= $forum->description ?></small>
									</p>
								</td>
								<td>
									<p>
										<small><?= $forum->count_topics ?></small>
									</p>
								</td>
								<td>
									<p>
										<small><?= $forum->count_posts ?></small>
									</p>
								</td>
								<td class="hidden-xs">
									<?php if ($forum->latest_topic->title !== null) : ?>
										<p>
											<small><a href="<?= base_url('forum/'.$forum->latest_topic->permalink) ?>"><?= $forum->latest_topic->title ?></a><br>by <a href="<?= base_url('user/' . $forum->latest_topic->author) ?>"><?= $forum->latest_topic->author ?></a>, <?= $forum->latest_topic->created_at ?></small>
											<a href='<?=base_url('report/forum/'.$forum->id)?>'><img style='margin-left: 1em;' src='<?= base_url('uploads/icons/report.png') ?>' alt='report' width='15px' height='15px'></a>
											<?php if($forum->id_user == $this->session->userdata('user_id') || $this->session->userdata('is_admin')==true):?>
												<a href='<?=base_url('forum/delete/forum/'.$forum->id)?>'><img style='margin-left: 1em;' src='<?= base_url('uploads/icons/remove.png') ?>' alt='delete' width='15px' height='15px'></a>

											<?php endif; ?>
										</p>
									<?php else : ?>
										<p>
											<small>no topic yet</small>
											<a href='<?=base_url('report/forum/'.$forum->id)?>'><img style='margin-left: 1em;' src='<?= base_url('uploads/icons/report.png') ?>' alt='delete' width='15px' height='15px'></a>
											<?php if($forum->id_user == $this->session->userdata('user_id') || $this->session->userdata('is_admin')==true):?>
												<a href='<?=base_url('forum/delete/forum/'.$forum->id)?>'><img style='margin-left: 1em;' src='<?= base_url('uploads/icons/remove.png') ?>' alt='delete' width='15px' height='15px'></a>

											<?php endif; ?>
										</p>
									<?php endif; ?>
									
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
			
		</div>
		
		<?php if ( $this->session->userdata('fame') > 25 ) : ?>
			<div class="col-md-12">
				<a class="btn btn-xs btn-primary" href="<?= base_url('create_forum') ?>" >Create a new forum</a>
			</div>
		<?php endif; ?>
		
	</div><!-- .row -->
</div><!-- .container -->

<?php //var_dump($forums); ?>
