<? include("../dbconnection_3evchey.php"); //connecting Database
session_start();
if($_POST['client_id']=='' && $_POST['project_id']==''){
header("location: ../index.java");
}
$mail_message = "";

$check_client_active = mysql_query("SELECT * FROM Client_Information WHERE id = ".$_POST['client_id']." AND active_option = 1");
$cca_num = mysql_num_rows($check_client_active);
$cca_row = mysql_fetch_array($check_client_active);
if($cca_num==0){
header("location: ../index.java");
}
$projectname = mysql_query("SELECT * FROM video_project WHERE id = ".$_POST['project_id']);
$projectname_row = mysql_fetch_array($projectname);

$last_video_under_project = mysql_query("SELECT * FROM video_under_project WHERE video_project_id = ".$_POST['project_id']." AND enabling = 1 ORDER BY id DESC LIMIT 0, 1");
$last_video_under_project_row = mysql_fetch_array($last_video_under_project);
$forloopcount = 0;


/*=======================================*/
/*          Time feedback of video       */
/*=======================================*/							

for($i=0; $i<1000; $i++){//runing 1000 time to add feedback to array
	if($_POST['feedback_'.$i]!=""){
		$feedback[$forloopcount] = $_POST['feedback_'.$i];
		$feedback_strat[$forloopcount] = $_POST['time_start_'.$i];
		$feedback_end[$forloopcount] = $_POST['time_end_'.$i];
		$feedback_type[$forloopcount] = $_POST['comment_option_'.$i];
		$forloopcount = $forloopcount + 1;
	}
}
/*for($i=0; $i<$forloopcount; $i++){
	$update_video_client_request = mysql_query("INSERT INTO video_client_request VALUES(NULL, ".$last_video_under_project_row["id"].", '".$feedback_strat[$i]."', '".$feedback_end[$i]."', '".$feedback[$i]."', '".$feedback_type[$i]."')");
	$list_comment .= '<tr><td>'.$feedback_strat[$i].'</td><td>'.$feedback_end[$i].'</td><td>'.$feedback_type[$i].'</td><td>'.$feedback[$i].'</td></tr>';
	//echo "INSERT INTO video_client_request VALUES(NULL, ".$last_video_under_project_row["id"].", '".$_POST['time_start'.$i]."', '".$_POST['time_end'.$i]."', '".$_POST['feedback'.$i]."')";
}

if($_POST['voice_comment']!=""){
	$update_video_client_addition_request = mysql_query("INSERT INTO video_client_addition_request VALUES(NULL, '".$last_video_under_project_row["id"]."', '".$_POST['script1']."', '".$_POST['script2']."', '".$_POST['logoandimage_email']."', '".$_POST['logoandimage_dropbox']."', '".$_POST['voice_id']."', '".$_POST['voice_comment']."', '".$_POST['audio_comment']."', '".$_POST['contact_info1']."', '".$_POST['contact_info2']."', '".$_POST['contact_info3']."', '".$_POST['contact_info4']."')");
	$general_comment = $_POST['voice_comment'];
}
*/


$last_video_a_request = mysql_query("SELECT * FROM video_client_addition_request WHERE video_id = ".$last_video_under_project_row['id']." ORDER BY id DESC LIMIT 0, 1");
$last_video_a_request_row = mysql_fetch_array($last_video_a_request);
include('../inc/youtube_function.php');
$contents_location = "http://gdata.youtube.com/feeds/api/videos?q={".cleanYoutubeLink($last_video_under_project_row['video_link'])."}&alt=json";
$JSON = file_get_contents($contents_location);
$JSON_Data = json_decode($JSON);
$video_lenght = $JSON_Data->{'feed'}->{'entry'}[0]->{'media$group'}->{'yt$duration'}->{'seconds'};
$video_lenght_result = $video_lenght.':000';
$end_time = gmdate("i:s", (int)$video_lenght_result);
?>
<!DOCTYPE html>
<html>
	<? include('../inc/head.php');?>
<?php
/*=======================================*/
/*         Get Feedback Deadline         */
/*=======================================*/							
	function check_deadline($function_v_project_id, $version, $mode){
		$check_deadline = mysql_query("SELECT upload_time FROM video_under_project WHERE video_project_id = ".$function_v_project_id." AND version LIKE '".$version."' AND enabling = 1 ORDER BY id LIMIT 0, 1");
		$check_deadline_row = mysql_fetch_array($check_deadline);
		 $now = time(); // or your date as well
		 $data_format = substr($check_deadline_row['upload_time'], 0, 10);
		 $upload_date = strtotime($data_format);
		 $datediff = $now - $upload_date;
		 if($mode == "deadline"){
		 	return 21 - floor($datediff/(60*60*24));
		 }else{
		 	return date('d-F-Y', strtotime($check_deadline_row['upload_time']));
		 }
		 //return  $data_format ;
	}
?>
	<body class="">
		<? include('client_header.php'); ?>
        <input value="<?php echo $end_time;?>" id="video_end_time" type="hidden">
		<main>
<?
$forloopcount = 0;
for($i=0; $i<1000; $i++){//runing 1000 time to add feedback to array
	if($_POST['feedback_'.$i]!=""){
		$feedback[$forloopcount] = $_POST['feedback_'.$i];
		$feedback_strat[$forloopcount] = $_POST['time_start_'.$i];
		$feedback_end[$forloopcount] = $_POST['time_end_'.$i];
		$feedback_type[$forloopcount] = $_POST['comment_option_'.$i];
		$forloopcount = $forloopcount + 1;
	}
}
for($j=0; $j<$forloopcount; $j++){
	if($j==0){
		$addcommenttimes .= '<ul id="time-comment_first"><li><input value="'.$forloopcount.'" name="old_loop_time" type="hidden"/></li>';	
	}
	$addcommenttimes .= '
		<li>
		<span class="timeline_picker four columns">
		<label for="start">Start</label>
		<input value="'.$feedback_strat[$j].'" name="addtimestart'.$j.'">
		</span>
		<span class="timeline_picker end four columns">
		<label for="start">End</label>
		<input value="'.$feedback_end[$j].'" name="addtimeend'.$j.'">
		</span>
		<select name="addcommentoption'.$j.'"  class="five columns">
			<option';
	if($feedback_type[$j]=='Visual Comment'){ echo 'selected';}
	$addcommenttimes .= '>Visual Comment</option>
			<option ';
	if($feedback_type[$j]=='Audio Comment'){ echo 'selected';}
	$addcommenttimes .= '>Audio Comment</option>
			<option';
	if($feedback_type[$j]=='Other Comment'){ echo 'selected';}
	$addcommenttimes .= '>Other Comment</option>
		</select>
		<textarea rows="5" cols="30" name="addfeedback'.$j.'" class="fourteen columns">'.$feedback[$j].'</textarea>
		</li>	
	';
}
$addcommenttimes .= '</ul>';
?>							

		<section class="container">
			<h1 class="">Please Double confirm Your Comments before submit</h1>
				<ul id="videos" >
<?php if($last_video_under_project_row['version']!="Final"){?>            
			<form id="client_view_update" action="client_view.java" method="post">
					<input value="<?=$_POST['client_id'];?>" name="client_id" type="hidden">
					<input value="<?=$_POST['project_id'];?>" name="project_id" type="hidden">
					<input value="1" name="add_comments" type="hidden">
					<li class="video_obj featured">
						<h1 class="title">
                        
						<?php echo $cca_row['company_name'];?> - <?php echo $projectname_row['project_name']?> - <span><?php echo $last_video_under_project_row['version']; ?>  (<? echo check_deadline($_POST['project_id'], $last_video_under_project_row['version']); ?>)</span>
						</h1>
                        <h2>
                        <?php 
						$list_day_counter = check_deadline($_POST['project_id'], $last_video_under_project_row['version'], 'deadline');
						if($list_day_counter>0){ ?>
                            You have <? echo check_deadline($_POST['project_id'], $last_video_under_project_row['version'], 'deadline'); ?> days left to submit your feedback
                        <? }else{ ?>
                        	Sorry, We have not got any change request in last 3 weeks, If you need any change of your video, we will charge for time involved.
                        <? } ?>
                        </h2>
						<div class="video eight columns">
							<!-- VIMEO EMBED -->
							<iframe width="500" height="400" src="//www.youtube.com/embed/<?=cleanYoutubeLink($last_video_under_project_row['video_link']);?>?rel=0" frameborder="0" allowfullscreen></iframe>
							<!-- VIMEO EMBED -->
						</div>
						<div id='action_box' class="actions eight columns">
							<label class="title" for="">Director's Notes</label>
							<textarea disabled="true" name="" id="" cols="30" rows="10"><?=$last_video_under_project_row['notes']?></textarea>
						</div>
						<div class="comment_check">
						<label class="title" for="">Your Notes</label>
						<ul id="comments-general" class="container">
							
							<li>
							<textarea name="voice_comment" id="general-comment" class="ten columns" cols="30" rows="10" placeholder="General Comments on the Video"><?php echo $_POST['voice_comment']; ?></textarea>
							</li>
							</ul>
                            <?php echo $addcommenttimes; ?>
							<ul id="time-comments">
								
							</ul>
							<div class="submit-actions eight columns">
							<a href="javascript:void(0)" onClick="NewTimelineComment()" class="btn blue columns five"><span>Add More Timeline Comments</span> <i class="fa fa-clock-o"></i></a>
							<a class="btn green columns five" onClick="document.getElementById('client_view_update').submit();"><span>Submit All Comments</span> <i class="fa fa-send"></i></a>
							</div>
						</div>
					</li>
				</form>
<?php }else{ ?>            
			<form id="charge_update" action="client_view.java" method="post">
					<input value="<?=$_POST['client_id'];?>" name="client_id" type="hidden">
					<input value="<?=$_POST['project_id'];?>" name="project_id" type="hidden">
					<input value="1" name="charge_change" type="hidden">
					<li class="video_obj featured">
						<h1 class="title">
                        
						<?php echo $cca_row['company_name'];?> - <?php echo $projectname_row['project_name']?> - <span><?php echo $last_video_under_project_row['version']; ?>  (<? echo check_deadline($_POST['project_id'], $last_video_under_project_row['version']); ?>)</span>
						</h1>
						<div class="video eight columns">
							<!-- VIMEO EMBED -->
							<iframe width="500" height="400" src="//www.youtube.com/embed/<?=cleanYoutubeLink($last_video_under_project_row['video_link']);?>?rel=0" frameborder="0" allowfullscreen></iframe>
							<!-- VIMEO EMBED -->
						</div>
						<div id='action_box' class="actions eight columns">
                         <?php if($projectname_row['download_file']!=""){ ?>
							<label class="title" for="">Congratulations your video is now ready for downlaod now.</label>
						<?php }else{ ?>
							<label class="title" for="">We are editing your video now.</label>
                        <?php } ?>
							<textarea disabled="true" name="" id="" cols="30" rows="5">Versions included:
1 x MP4  - 1280 x 720 - h264 - suitable for youtube
1 x MP4  - 640 x480 h264 idea for uploading to your website.
                            
Other Formats
Please contact our video production team if you request a different formats DVD's etc 
(video@surgemedia.com.au)

Extended storate
Your Data will be stored for 6 months. Please contact if your request any copy.
                            </textarea>
						</div>
						<div class="comment_check">
						<label class="title" for="">Your Notes</label>
						<ul id="comments-general" class="container">
							
							<li>
							<textarea name="voice_comment" id="general-comment" class="ten columns" cols="30" rows="10" placeholder="General Comments on the Video"><?php echo $_POST['voice_comment']; ?></textarea>
							</li>
							</ul>
                            <?php echo $addcommenttimes; ?>
							<ul id="time-comments">
								
							</ul>
							<div class="submit-actions eight columns">
							<a href="javascript:void(0)" onClick="NewTimelineComment()" class="btn blue columns five"><span>Add More Timeline Comments</span> <i class="fa fa-clock-o"></i></a>
							<a class="btn green columns five" onClick="document.getElementById('charge_update').submit();"><span>Submit All Comments</span> <i class="fa fa-send"></i></a>
							</div>
						</div>
					</li>
                </form>

<?php } ?>            
			</section>
			</main>
			<? include('../footer.php');?>
			<div id="overlay_wrapper" onclick="closeAllCards()"></div>
		</body>
	</html>