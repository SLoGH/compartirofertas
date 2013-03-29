<?php
require_once('../../../../../../wp-load.php');
//error_reporting (E_ALL ^ E_NOTICE);
	
	$emailto = '';
	$messages = '';
	
	if(!empty($_POST))
	{	
		if(isset($_POST['subject']))
		{
			$subject = stripslashes($_POST['subject']);
		}
		else
		{
			$subject = stripslashes("[".get_bloginfo('name')."]");
		}
		
		if(isset($_POST['to']))
		{
			$emailto = trim($_POST['to']);	
		}
		else
		{
			$emailto = get_option('admin_email');
		}
		
		foreach ($_POST as $field => $text)
		{
			if(!in_array($field, array('to', 'subject')))
			{
				$messages .= "<br><strong>{$field}</strong> : {$text}";
			}
		}
		
		if($emailto)
		{
			
			$mail = wp_mail($emailto, $subject, $messages, "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"");	

			if($mail) {
				echo 'success';
			}
		}
	}
?>