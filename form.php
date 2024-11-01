<?php 
if ( ! defined( 'ABSPATH' ) ) exit; 

if($_POST['update_draft']){
	if ( ! isset( $_POST['name_of_nonce_field'] ) 
    	|| ! wp_verify_nonce( $_POST['name_of_nonce_field'], 'name_of_my_action' ) 
	) {
   		print 'Sorry, your nonce did not verify.';
	} else {
		update_option('WIND_start_date',sanitize_text_field($_POST['start_date']));
		update_option('WIND_start_time',sanitize_text_field($_POST['start_time']));
		update_option('WIND_interval_time',sanitize_text_field($_POST['interval_to_publish']));
		update_option('WIND_draft_num',sanitize_text_field($_POST['publish_num']));
		update_option('WIND_draf_orderby',sanitize_text_field($_POST['orderby']));
		update_option('WIND_recurrence',sanitize_text_field($_POST['recurrence']));
		update_option('WIND_recurrence_times',sanitize_text_field($_POST['recurrence_to_publish']));
	
		$starTime=get_option('WIND_start_time');
		wp_schedule_event( strtotime(get_option('WIND_start_date')." ".$starTime),'WIND_custom_recurrence','WIND_cron_draft_update_hook' );
	}
}
if($_POST['delete_draft']){
	if ( ! isset( $_POST['name_of_nonce_field'] ) 
    	|| ! wp_verify_nonce( $_POST['name_of_nonce_field'], 'name_of_my_action' ) 
	) {

   		print 'Sorry, your nonce did not verify.';
	} else {
		wp_clear_scheduled_hook( 'WIND_cron_draft_update_hook' );
	}
}
?>

<style type="text/css">
#main{ width:700px; border:1px solid #ccc; background-color:#f9f9f9; padding:10px; margin-top:20px; }
.button-primary{margin-right:20px;}
</style>

<div class="wrap">

	<div id="icon-options-general" class="icon32"><br></div><h2><?php  _e('Schedule Task', 'wp-auto-publish'); ?></h2>
	
	<div class="card">
	
		<h3><?php  _e('Auto Publish Setting', 'wp-auto-publish'); ?></h3>
		<hr>
		<div id="draft">
		<form action="" method="post">
			<p><label for="start_publish"><?php  _e('Begin From:', 'wp-auto-publish'); ?></label>
			<input type="date" id="start_date" name="start_date" value="<?php echo get_option('WIND_start_date'); ?>" />
			<?php  _e('Time:', 'wp-auto-publish'); ?><input style="width:100px;" type="time" id="start_time" name="start_time" value="<?php echo get_option('WIND_start_time'); ?>" /> 
			<?php  _e('Time Format', 'wp-auto-publish'); ?> 11:35</p>
			<p><label for="interval_to_publish"><?php  _e('Interval', 'wp-auto-publish'); ?></label>
			<input style="width:60px;" type="number" min="1" id="interval_to_publish" name="interval_to_publish" value="<?php echo get_option('WIND_interval_time'); ?>" />
			<label for="publish_num"><?php  _e('second,', 'wp-auto-publish'); _e('Total of publishing', 'wp-auto-publish'); ?></label>
			<input style="width:60px;" type="number" min="1" id="publish_num" name="publish_num" value="<?php echo get_option('WIND_draft_num'); ?>" />
			<label for="orderby"><?php  _e('Post,', 'wp-auto-publish');_e('Orderby', 'wp-auto-publish'); ?></label>
			<select name="orderby">
				<option value="rand" <?php selected( get_option('WIND_draf_orderby'), 'rand' ); ?>><?php  _e('Random', 'wp-auto-publish'); ?></option>
				<option value="ID" <?php selected( get_option('WIND_draf_orderby'), 'ID' ); ?>>ID</option>
			</select>
			</p>
			<p>
			<label for="recurrence"><?php  _e('Repeat by', 'wp-auto-publish'); ?></label><input style="width:60px;" type="number" min="0" id="recurrence_to_publish" name="recurrence_to_publish" value="<?php echo get_option('WIND_recurrence_times'); ?>" />
			<select name="recurrence">
				<option value="daily" <?php selected( get_option('WIND_recurrence'), 'daily' ); ?>><?php  _e('day', 'wp-auto-publish'); ?></option>
				<option value="hourly" <?php selected( get_option('WIND_recurrence'), 'hourly' ); ?>><?php  _e('hour', 'wp-auto-publish'); ?></option>
				<option value="weekly" <?php selected( get_option('WIND_recurrence'), 'weekly' ); ?>><?php  _e('week', 'wp-auto-publish'); ?></option>
			</select> <?php  _e('input 0,no-repeat', 'wp-auto-publish'); ?>
			</P>
			<p>
		<?php 
		submit_button( __('Update and Start', 'wp-auto-publish'),'primary','update_draft',''); 
		echo"  " ;
		submit_button(__('Cancel the Scheduled Task', 'wp-auto-publish'),'secondary','delete_draft','');?>
			</p>
			<?php wp_nonce_field( 'name_of_my_action', 'name_of_nonce_field' ); ?>
		</form>
		<?php if(wp_next_scheduled('WIND_cron_draft_update_hook')):?><p style="color:red"><?php  _e('Next scheduled task time:', 'wp-auto-publish'); ?>
			<?php echo date('Y-m-d H:i:s',wp_next_scheduled('WIND_cron_draft_update_hook'));?></p>
		<?php else:?>
		<p style="color:gray;"><?php  _e('The scheduled task has stopped!', 'wp-auto-publish'); ?></p>
		<?php endif;?>
		
		</div>
		<hr>
		<div>
			<h3><?php  _e('Instructions for use', 'wp-auto-publish'); ?></h3>
			<?php  _e('Save all the articles you plan to publish as a draft and make the settings above, then <strong> Update and Start </strong> .', 'wp-auto-publish'); ?>
		</div>
	</div>
<div  class="card">
	<p><?php  _e('Plugin introduction:', 'wp-auto-publish'); ?><a href=" http://moligu.com/2018/09/03/wp-auto-publish-plugin/" target="_blank">MoLiGu</a></p>
	<h3>感谢您的捐赠支持！</h3>
	<p>感谢有您捐赠支持本插件的开发，能让我们为您提供更多的助力！ </p>
		<a href="<?php echo plugins_url('ali-pay.jpg',__FILE__) ?>"><img width="200" src="<?php echo plugins_url('ali-pay.jpg',__FILE__) ?>"></a>
		<a href="<?php echo plugins_url('wx-pay.jpg',__FILE__) ?>">	<img width="200" src="<?php echo plugins_url('wx-pay.jpg',__FILE__) ?>"></a>
</div>
</div>
