<?
function sendm( $from, $to, $subj, $text, $filename, $fln) { 
$f = fopen($filename,"rb"); 
$un = strtoupper(uniqid(time())); 
$head = "From: $from\n"; 
$head .= "To: $to\n"; 
$head .= "Subject: $subj\n"; 
$head .= "X-Mailer: PHPMail Tool\n"; 
$head .= "Reply-To: $from\n"; 
$head .= "Mime-Version: 1.0\n"; 
$head .= "Content-Type:multipart/mixed;"; 
$head .= "boundary=\"----------".$un."\"\n\n"; 
$zag = "------------".$un."\nContent-Type:text/plain;\n"; 
$zag .= "Content-Transfer-Encoding: 8bit\n\n$text\n\n"; 
$zag .= "------------".$un."\n"; 
$zag .= "Content-Type: application/octet-stream;"; 
$zag .= "name=\"".basename($fln)."\"\n"; 
$zag .= "Content-Transfer-Encoding:base64\n"; 
$zag .= "Content-Disposition:attachment;"; 
$zag .= "filename=\"".basename($fln)."\"\n\n"; 
$zag .= chunk_split(base64_encode(fread($f,filesize($filename))))."\n"; 

return @mail("$to", "$subj", $zag, $head); 
}

function send_mail($emailaddress, $fromaddress, $emailsubject, $body, $attachments=false)
{
  $eol="\r\n";
  $mime_boundary=md5(time());
  
  # Common Headers
  $headers .= 'From: MyName<'.$fromaddress.'>'.$eol;
  $headers .= 'Reply-To: MyName<'.$fromaddress.'>'.$eol;
  $headers .= 'Return-Path: MyName<'.$fromaddress.'>'.$eol;    // these two to set reply address
  $headers .= "Message-ID: <".$now." TheSystem@".$_SERVER['SERVER_NAME'].">".$eol;
  $headers .= "X-Mailer: PHP v".phpversion().$eol;          // These two to help avoid spam-filters

  # Boundry for marking the split & Multitype Headers
  $headers .= 'MIME-Version: 1.0'.$eol;
  $headers .= "Content-Type: multipart/related; boundary=\"".$mime_boundary."\"".$eol;

  $msg = "";      
  
  if ($attachments !== false)
  {

   for($i=0; $i < count($attachments); $i++)
   {
     if (is_file($attachments[$i]["file"]))
     {  
       # File for Attachment
       $file_name = substr($attachments[$i]["file"], (strrpos($attachments[$i]["file"], "/")+1));
       
       $handle=fopen($attachments[$i]["file"], 'rb');
       $f_contents=fread($handle, filesize($attachments[$i]["file"]));
       $f_contents=chunk_split(base64_encode($f_contents));    //Encode The Data For Transition using base64_encode();
       fclose($handle);
       
       # Attachment
       $msg .= "--".$mime_boundary.$eol;
       $msg .= "Content-Type: ".$attachments[$i]["content_type"]."; name=\"".$file_name."\"".$eol;
       $msg .= "Content-Transfer-Encoding: base64".$eol;
       $msg .= "Content-Disposition: attachment; filename=\"".$file_name."\"".$eol.$eol; // !! This line needs TWO end of lines !! IMPORTANT !!
       $msg .= $f_contents.$eol.$eol;
       
     }
   }
  }
  
  # Setup for text OR html
  $msg .= "Content-Type: multipart/alternative".$eol;
  
  # Text Version
  $msg .= "--".$mime_boundary.$eol;
  $msg .= "Content-Type: text/plain; charset=iso-8859-1".$eol;
  $msg .= "Content-Transfer-Encoding: 8bit".$eol;
  $msg .= strip_tags(str_replace("<br>", "\n", $body)).$eol.$eol;
  
  # HTML Version
  $msg .= "--".$mime_boundary.$eol;
  $msg .= "Content-Type: text/html; charset=iso-8859-1".$eol;
  $msg .= "Content-Transfer-Encoding: 8bit".$eol;
  $msg .= $body.$eol.$eol;
  
  # Finished
  $msg .= "--".$mime_boundary."--".$eol.$eol;  // finish with two eol's for better security. see Injection.
   
  # SEND THE EMAIL
  ini_set(sendmail_from,$fromaddress);  // the INI lines are to force the From Address to be used !
  mail($emailaddress, $emailsubject, $msg, $headers);
  ini_restore(sendmail_from);
  echo "mail send";
}

  
# To Email Address
$emailaddress="to@address.com";

# From Email Address
$fromaddress = "from@address.com";

# Message Subject
$emailsubject="This is a test mail with some attachments";

# Use relative paths to the attachments
$attachments = Array(
  Array("file"=>"../../test.doc", "content_type"=>"application/msword"), 
  Array("file"=>"../../123.pdf", "content_type"=>"application/pdf")
);

# Message Body
$body="This is a message with <b>".count($attachments)."</b> attachments and maybe some <i>HTML</i>!";

send_mail($emailaddress, $fromaddress, $emailsubject, $body, $attachments);
?> 
