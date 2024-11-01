<?php 
/*
*Plugin Name: WP-Auto-Publish
*Plugin URI: http://moligu.com/wp-auto-publish/
*Description: 灵活设定自动批量定时发布文章。
*Version: 1.0
*Author: flashcol
*Author URI: http://moligu.com/
*License: GPL v2 or later
*Text Domain: wp-auto-publish
*Domain Path: /lang
*/
if ( ! defined( 'ABSPATH' ) ) exit; 
class WP_Auto_Publish{
	function __construct(){		
		add_action( 'admin_menu',array($this,'menu_page') );		//加入管理菜单
		add_filter( 'cron_schedules',array($this,'add_custom_cron_intervals') );		//自定义计划任务重复时间间隔
		add_action( 'WIND_cron_draft_update_hook',array($this,'WIND_cron_draft_update') );		//加入执行计划任务调用
		add_action( 'plugins_loaded', array($this,'wind_load_plugin_textdomain' ));
	}
function wind_load_plugin_textdomain() {
    load_plugin_textdomain( 'wp-auto-publish', FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
}

	//add menu page
	function menu_page(){
		//add_menu_page( 'WP-auto-publish','自动发布','manage_options','WP-auto-publish',array($this,'cron_form'),plugins_url('icon.png',__FILE__) );
		add_menu_page( 'WP-auto-publish',__( 'Auto Publish' , 'wp-auto-publish' ),'manage_options','WP-auto-publish',array($this,'cron_form'));
	}
	function cron_form(){
		include_once('form.php');
	}

	//自定义计划任务重复时间间隔
	function add_custom_cron_intervals($schedules){
		$recurrence=intval(get_option('WIND_recurrence_times'));
		
		switch (get_option('WIND_recurrence'))
		{
		case 'daily':
 		 	$recurrence= $recurrence*DAY_IN_SECONDS;
 		 	break;  
		case 'hourly':
  			$recurrence= $recurrence*HOUR_IN_SECONDS;
  			break;
		case 'weekly':
 		 	$recurrence= $recurrence*WEEK_IN_SECONDS;
 		 	break;  
		default:
  			$recurrence= $recurrence*DAY_IN_SECONDS;
		}
		$schedules['WIND_custom_recurrence'] = array(
			'interval' =>$recurrence,
			'display' => '定时'
		);
		return $schedules;	
	}	

	//设定草稿发布计划
	function WIND_cron_draft_update(){
		query_posts(array('posts_per_page'=>get_option('WIND_draft_num'),'orderby'=>get_option('WIND_draf_orderby'),'post_status'=>'draft','post_type'=>'post','order'=>'ASC'));
		$num=0;
		while(have_posts()){
			$ndate=date('Y-m-d H:i:s',time()+$num*(intval(get_option('WIND_interval_time'))));
			$ndate_gmt=get_gmt_from_date($ndate);
			
			the_post();
			kses_remove_filters();
			wp_update_post(array('ID'=>get_the_ID(),'post_status'=>'future','post_date'=>$ndate,'post_date_gmt'=>$ndate_gmt));
			kses_init_filters();
			$num++;
		}
		wp_reset_query();
		//wp_clear_scheduled_hook( 'WIND_cron_draft_update_hook' );
		//wp_schedule_event( strtotime("+1 day",strtotime(date("Y-m-d")." ".$starTime)),'WIND_custom_recurrence','WIND_cron_draft_update_hook' );
	}

}
//设置默认时区：
date_default_timezone_set( get_option('timezone_string') );
new WP_Auto_Publish();
