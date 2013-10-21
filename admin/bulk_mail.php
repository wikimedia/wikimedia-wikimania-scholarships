<?php
require_once('init.php');

function mail_msg($to, $subject, $body) {
	require_once "Mail.php";
	$from = $wgLang->message('email-from') . "<$email_from>";
	$headers = array ('From' => $from,
	  	'To' => $to,
		'Subject' => $subject,
		'MIME-Version' => '1.0',
		'Content-type' => 'text/html; charset=utf-8',
		'Reply-To' => $email_from,
		'X-Mailer' => 'PHP/' . phpversion()
	);
	$smtp = Mail::factory('mail');
	$mail = $smtp->send($to, $headers, $body);

	if (PEAR::isError($mail)) {
		echo("<p>" . $mail->getMessage() . "</p>");
		return false;
	} else {
		return true;
	}
}

$early_reject_mail_template = <<<EOM
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<body>

<h2>%s</h2>

<p>Dear %s,</p>

<p>The Wikimania 2013 Scholarship Committee has carefully reviewed your application. With regret, we cannot sponsor your travel to attend Wikimania 2013 in Hong Kong.</p>

<p>We received more than ??? applications from around the world for a limited number of scholarships. Preference has been given to applications from individuals who are very active contributors or volunteers on the Wikimedia projects as well as participants in other free knowledge initiatives.</p>

<p>If you can make other arrangements to attend Wikimania 2013, we encourage you to do so! Conference registration can be found <a href="http://wikimania2013.wikimedia.org/wiki/Registration">here</a>.</p>

<p>To qualify for scholarship for next year's Wikimania conference (2014 location to be decided), we encourage you to actively participate in and contribute to the Wikimedia projects and free knowledge initiatives. If you have not yet created a user account, you may do so by clicking "login/create account" on your favorite Wikimedia project. A good place to learn how you can help on the English Wikipedia is <a href="http://en.wikipedia.org/wiki/Wikipedia:Community_portal">here</a>; many other projects and languages, which you can access through our <a href="http://www.wikimedia.org">main portal</a>, have similar pages.</p>

<p>We strongly encourage your participation on Wikimedia's projects. If you need help finding out what you can do to contribute, please do not hesitate to <a href="mailto:wikimania-scholarships@wikimedia.org">write us</a>. Thank you for your interest in Wikimania and the Wikimedia projects.</p>

<p>Sincerely,</p>

<p>Simon Shek<br>
Community Coordinator (Scholarship & Program)<br>
Wikimania 2013 Organizing Team</p> 
</body>
</html>
EOM;
/*
$full_acceptance_mail_template = <<<EOM
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<body>

<center><p><img src="http://upload.wikimedia.org/wikipedia/commons/0/0c/Haifa_wikimania_3.png" width=600 /></p></center>

<h2>Wikimania 2011 Scholarship Decision</h2>

<p>Dear $1,</p>


<p>Congratulations! On behalf of the 2011 Wikimania Scholarships Review Committee and the Wikimedia Foundation, we are very pleased to inform you that your scholarship application has been approved to pay for your air travel, registration, and dorms accommodations for Wikimania 2011 in Haifa, Israel from August 4 - 7, 2011.</p>

<p>You are among a handful of individuals, out of thousands of applicants from all over the world, who have been selected for this opportunity. You have been selected based on your dedication and participation in the Wikimedia movement or other free knowledge and educational initiatives and your potential to add great value to Wikimania and the Wikimedia projects going forward. We hope that you will be engaged by the unique opportunity to attend Wikimania 2011 and convene face-to-face with the global Wikimedia community.</p>

<p>Please reply promptly to this email to accept or decline this invitation (see REPLY & ACCEPTANCE AGREEMENT below). The deadline to accept or decline this offer is April 15, 2011, at which time outstanding scholarships will be given to a waitlist of individuals.</p>

<p>It is important to thoroughly read and understand the information below, and send back your reply in the REPLY & ACCEPTANCE AGREEMENT section.</p>

<p>We look forward to your reply and hope to see you in Haifa!</p>


<p>Sincerely,</p>

<p>Harel Cain<br>
Communications coordinator<br>
Wikimania 2011 Scholarship Review Committee</p> 

<p>Jessie Wild<br>
Special Projects Manager<br>
Global Development<br>
Wikimedia Foundation</p> 


<h3>EXPENSES COVERED / NOT COVERED</h3>

<p>Wikimania 2011 Scholarships cover the cost of round-trip travel, dorms accommodations, and registration for Wikimania 2011 in Haifa, Israel.</p>

<p>Costs that are not covered are incidentals, local transportation during the conference, and meals outside of the conference venue. These costs will be your responsibility (do note that during the conference, most meals are provided, and shuttles will provide transportation from the dorms to the venue).</p>

<p>The Wikimedia Foundation will not cover costs for Visa fees for travel unless the individual deems that the cost prohibits them from traveling to Wikimania.</p>

<h3>TRAVEL ARRANGEMENTS</h3>

<p><b>Travel Visas:</b>
You are responsible for obtaining any visa or travel documentation required for travel from your country of residence to Israel. We recommend that you visit
 <a href=http://wikimania2011.wikimedia.org/wiki/Visas>the visa information page</a> immediately to determine if you need to obtain a visa as requirements vary for each country of origin, and it may take some time and effort to obtain a visa. 
 If you require a visa to enter Israel, please fill out the relevant section while completing the <a href=http://wmreg.wikimedia.org.il>conference registration form</a>, so that the conference organizers will pass this information to the Israeli Ministry of Foreign Affairs before you approach your nearest Israeli embassy for a visa applicaiton.</p>

<p><b>Travel Booking by Travel Agent:</b>
After indicating your acceptance of the scholarship, Wikimedia recommends that you contact our travel agency to book your travel, which will be directly paid for by the Wikimedia Foundation. The travel agency is familiar with the conference dates and reasonable rates associated with travel to Haifa from your location and has been authorized to book your travel based on these rates. Any costs for personal travel or ticket change costs beyond the reasonable agency rate will be your responsibility. To book your ticket, you may contact:</p>

Kelly Sanders, Travel Consultant (Altour travel agency)<br/>
Kelly.Sanders@altour.com<br/>
T 805-938-5246<br/>
F 310-220-6652<br/>

<p>The agency will request the following information from you. Please have this on hand.
<ul>
<li>Departing Airport
<li>Nationality (on passport)
<li>Passport Expiration date
<li>Birth date
<li>Gender
<li>Seat Preference (aisle, window)
<li>Mileage Numbers (frequent flyer)
<li>Phone Number
<li>Email Address
</ul>

<b>Direct Reimbursement for Air Travel:</b>
While we encourage you to use the travel agency, if you need to book your air travel on your own and be reimbursed by the Wikimedia Foundation, please elect this option in the REPLY & ACCEPTANCE section below, stating the reason for the choice. If you elect to be reimbursed, we will provide you with an estimate for travel cost. AFTER that estimate has been provided, you may purchase the ticket up to the cost of the estimate, and provide us with a receipt of purchase. You will only be reimbursed for your travel expenses if you provide receipt of purchase. We will process reimbursements for travel via PayPal in June 2011. It is also possible to reimburse by wire transfer, but there may be associated fees.

<h3>CONFERENCE REGISTRATION AND ACCOMMODATIONS</h3>

<p>
If you decide to accept this scholarship, please visit the<a href=http://wikimania2011.wikimedia.org/wiki/Registration> conference registration page</a> and be sure to complete the <a href=http://wmreg.wikimedia.org.il>conference registration form</a>, entering the discount code <emph><b>WMFFULL:$2</b></emph> in the discount code field.
<br>
As a scholarship recipient, you don't have to pay the registration fee or accommodation fee. Should the system ask you to pay them, please ignore.  
Dormitory accommodations will be covered for scholarship recipients, so please select that option in the accommodation section. Note that the dorms are at a distance from the venue. 
Shuttles will provide transportation to the venue.</p>

<h3>SPECIAL NEEDS</h3>

<p>When filling out the registration form for the conference, please indicate any special needs you have related to mobility and wheelchair access to ensure appropriate accommodations.</p>

<h3>REPLY & ACCEPTANCE AGREEMENT</h3>

<p>Please reply to this email with answers to the questions below, in the space indicated.</p>

<ul>
<li> By accepting this scholarship, you are making a firm commitment to travel to and attend Wikimania 2011 in Haifa. If you are not sure that you want to attend Wikimania, please do not commit to do so. There are many other individuals who would benefit from this support. The deadline to accept or decline is April 15, 2011. If you do not respond by April 15, the scholarship will be awarded to someone else. Please indicate ACCEPT or DECLINE in the space below, in reply to this email.</li>
<li> <b>YOUR RESPONSE</b>: ACCEPT or DECLINE </li>
<li> Please indicate your travel booking preference by writing TRAVEL AGENCY or REIMBURSEMENT FOR AIR TRAVEL, in the space below (see TRAVEL ARRANGEMENTS section above for explanation). </li>
<li> <b>YOUR RESPONSE</b>: TRAVEL AGENCY or REIMBURSEMENT FOR AIR TRAVEL </li>
<li> By accepting, you agree to respond to a short survey following the conference, which will ask about your conference experience. Please confirm that you are willing to fill out this survey by writing YES in the space below.</li>
<li> <b>YOUR RESPONSE</b>: YES or NO </li>
</ul>

<h3>USEFUL LINKS</h3>

<ul>
 
<li><a href=http://wikimania2011.wikimedia.org/wiki/Scholarships>Scholarships Information Page - key updates will be posted here </a></li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Main_Page>Wikimania 2011 Page </a></li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Registration>Registration </a> </li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Visas>Visas </a></li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Getting_to_Haifa>Additional information on travel to Haifa</a>  </li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Local_information>Local information</a></li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Haifa >General Information on Haifa </a></li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Israel >General Information on Israel </a></li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Attendees>See who's attending Wikimania 2011!</a> </li>
</ul>

Questions? <a href=mailto:wikimania-scholarships@wikimedia.org>Contact us</a>
</body>
</html>
EOM;

$full_reject_mail_template = <<<EOM
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<body>

<center><p><img src="http://upload.wikimedia.org/wikipedia/commons/0/0c/Haifa_wikimania_3.png" width=600 /></p></center>

<h2>Wikimania 2011 Scholarship Decision</h2>

<p>Dear $1,</p>

<p>The Wikimania 2011 Scholarships Program Committee has made its final decision on scholarship recipients. We regret to tell you that you will not receive a scholarship to attend Wikimania 2011 in Haifa, Israel.</p>

<p>The Committee judged over 1100 applications from individuals around the world on the basis of their contributions to the Wikimedia projects, participation as a volunteer in both on and off-line activities, and/or involvement in initiatives related to open source, education, free culture, etc. We wish that we could send every applicant to Wikimania, but we are only able to support to a small percentage of those who applied.</p>

<p>If you can make other arrangements for Wikimania 2011, we strongly encourage you to do so! Conference registration can be found <a href=http://wikimania2011.wikimedia.org/wiki/Registration>here</a>. You still have time to receive early registration discounts, so register early.</p>

<p>We value your participation on Wikimedia's projects, and encourage your future participation. If you need help finding out what you can do to contribute, please do not hesitate to <a href=mailto:wikimania-scholarships@wikimedia.org>contact us</a>. Thank you for your interest in Wikimania and for your involvement in the Wikimedia movement.</p>

<p>Sincerely,</p>

<p>Harel Cain<br>
Communications coordinator<br>
Wikimania 2011 Scholarship Review Committee</p> 

<p>Jessie Wild<br>
Special Projects Manager<br>
Global Development<br>
Wikimedia Foundation</p> 
EOM;

$full_standby_mail_template = <<<EOM
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<body>

<center><p><img src="http://upload.wikimedia.org/wikipedia/commons/0/0c/Haifa_wikimania_3.png" width=600 /></p></center>

<h2>Wikimania 2011 Scholarship Decision</h2>

<p>Dear $1,</p>

<p>The Wikimania 2011 Scholarships Program Committee has made its final decision on scholarship awardees for Wikimania 2011 in Haifa, Israel, August 4 - 7 (Hacking days will take place on August 2 - 3). We would like to inform you that, while your application for the full scholarship has not been approved, your application received a high score and we have put your name on a short waiting list of candidates. Please note that we had over 1100 candidates this year, and only a handful were accepted or selected for the waiting-list.</p>

<p>When we send acceptance letters, scholarship recipients are given conditions to respond and accept. If they decline to accept, or if they do not reply in the designated acceptance time period, we award scholarships to waiting-list candidates. The period for the initial list of candidates to accept ends on April 15, 2011. At that time, we will notify you if you have or have not received an award. It is important to note that this is not a guarantee of award, and that many factors can affect the number of scholarships that can be granted.</p>

<p>That said, there are a subset of partial scholarships that are available.  While initially you indicated that you would not be able to attend Wikimania without a full scholarship, the partial scholarship has been increased to $425.  If you would be interested in attending given this subsidy, please email back by April 1.</p>

<p>We highly value your participation on the Wikimedia projects, and encourage your continued dedication and participation. Thank you for your patience, and your interest in Wikimania.</p>

<p>Sincerely,</p>

<p>Harel Cain<br>
Communications coordinator<br>
Wikimania 2011 Scholarship Review Committee</p> 

<p>Jessie Wild<br>
Special Projects Manager<br>
Global Development<br>
Wikimedia Foundation</p> 
</body>
</html>
EOM;



$partial_acceptance_mail_template = <<<EOM
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<body>

<center><p><img src="http://upload.wikimedia.org/wikipedia/commons/0/0c/Haifa_wikimania_3.png" width=600 /></p></center>

<h2>Wikimania 2011 Scholarship Decision</h2>

<p>Dear $1,</p>


<p>Congratulations! On behalf of the 2011 Wikimania Scholarships Program Committee and the Wikimedia Foundation, we are very pleased to inform you that your scholarship application has been approved to pay for a partial scholarship for Wikimania 2011 in Haifa, Israel from August 4 - 7, 2011. This is award is for up to $425 in travel expenses (i.e., transit to/from Haifa).</p>

<p>You are among a handful of individuals, out of thousands of applicants from all over the world, who have been selected for this opportunity. You have been selected based on your dedication and participation in the Wikimedia movement or other free knowledge and educational initiatives and your potential to add great value to Wikimania and the Wikimedia projects going forward. We hope that you will be engaged by the unique opportunity to attend Wikimania 2011 and convene face-to-face with the global Wikimedia community.</p>

<p>Please reply promptly to this email to accept or decline this invitation (see REPLY & ACCEPTANCE AGREEMENT below). The deadline to accept or decline this offer is April 15, 2011, at which time outstanding scholarships will be given to a waitlist of individuals.</p>

<p>It is important to thoroughly read and understand the information below, and send back your reply in the REPLY & ACCEPTANCE AGREEMENT section.</p>

<p>We look forward to your reply and hope to see you in Haifa!</p>


<p>Sincerely,</p>

<p>Harel Cain<br>
Communications coordinator<br>
Wikimania 2011 Scholarship Review Committee</p> 

<p>Jessie Wild<br>
Special Projects Manager<br>
Global Development<br>
Wikimedia Foundation</p> 


<h3>EXPENSES COVERED / NOT COVERED</h3>

<p>Wikimania 2011 Partial Scholarships cover up to $425 of costs for round-trip travel for Wikimania 2011 in Haifa, Israel.</p>

<p>Costs that are not covered are accommodations, registration for Wikimania, incidentals, local transportation during the conference, and meals outside of the conference venue. These costs will be your responsibility (do note that during the conference, most meals are provided, and shuttles will provide transportation from the dorms to the venue).</p>

<p>The Wikimedia Foundation will not cover costs for Visa fees for travel.</p>

<h3>TRAVEL ARRANGEMENTS</h3>

<p><b>Travel Visas:</b>
You are responsible for obtaining any visa or travel documentation required for travel from your country of residence to Israel. We recommend that you visit
 <a href=http://wikimania2011.wikimedia.org/wiki/Visas>the visa information page</a> immediately to determine if you need to obtain a visa as requirements vary for each country of origin, and it may take some time and effort to obtain a visa. 
 If you require a visa to enter Israel, please fill out the relevant section while completing the <a href=http://wmreg.wikimedia.org.il>conference registration form</a>, so that the conference organizers will pass this information to the Israeli Ministry of Foreign Affairs before you approach your nearest Israeli embassy for a visa applicaiton.</p>

<p><b>Direct Reimbursement for Air Travel</b>
Once you indicate acceptance of the scholarship (by submitting the REPLY & ACCEPTANCE section below), you may book the travel of your choosing. Please then provide Wikimedia Foundation with a receipt of purchase. You will only be reimbursed for your travel expenses if you provide receipt of purchase. We will process reimbursements for travel for up to $425 via PayPal in June 2011. It is also possible to reimburse by wire transfer, but there may be associated fees.</p>

<h3>CONFERENCE REGISTRATION AND ACCOMMODATIONS</h3>

<p>
If you decide to accept this scholarship, please visit the<a href=http://wikimania2011.wikimedia.org/wiki/Registration> conference registration page</a> and be sure to complete the <a href=http://wmreg.wikimedia.org.il>conference registration form</a>.
<br>
Dormitory accommodations are available for booking, though note that the associated fees are not covered under the partial scholarship.</p>

<h3>SPECIAL NEEDS</h3>

<p>When filling out the registration form for the conference, please indicate any special needs you have related to mobility and wheelchair access to ensure appropriate accommodations.</p>

<h3>REPLY & ACCEPTANCE AGREEMENT</h3>

<p>Please reply to this email with answers to the questions below, in the space indicated.</p>

<ul>
<li> By accepting this scholarship, you are making a firm commitment to travel to and attend Wikimania 2011 in Haifa. If you are not sure that you want to attend Wikimania, please do not commit to do so. There are many other individuals who would benefit from this support. The deadline to accept or decline is April 15, 2011. If you do not respond by April 15, the scholarship will be awarded to someone else. Please indicate ACCEPT or DECLINE in the space below, in reply to this email.</li>
<li> <b>YOUR RESPONSE</b>: ACCEPT or DECLINE </li>
<li> By accepting, you agree to respond to a short survey following the conference, which will ask about your conference experience. Please confirm that you are willing to fill out this survey by writing YES in the space below.</li>
<li> <b>YOUR RESPONSE</b>: YES or NO </li>
</ul>

<h3>USEFUL LINKS</h3>

<ul>
 
<li><a href=http://wikimania2011.wikimedia.org/wiki/Scholarships>Scholarships Information Page - key updates will be posted here </a></li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Main_Page>Wikimania 2011 Page </a></li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Registration>Registration </a> </li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Visas>Visas </a></li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Getting_to_Haifa>Additional information on travel to Haifa</a>  </li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Local_information>Local information</a></li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Haifa >General Information on Haifa </a></li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Israel >General Information on Israel </a></li>
<li><a href=http://wikimania2011.wikimedia.org/wiki/Attendees>See who's attending Wikimania 2011!</a> </li>
</ul>

Questions? <a href=mailto:wikimania-scholarships@wikimedia.org>Contact us</a>
</body>
</html>
EOM;
*/
session_start();

if (!isset($_SESSION['user_id'])) {
	header('location: ' . $BASEURL . 'user/login');
	exit();
}

$start = (int)($_GET['start'] ? $_GET['start'] : 0);
$howmany = (int)($_GET['howmany'] ? $_GET['howmany'] : 1);
//
$dal = new DataAccessLayer();
$schols = $dal->GetPhase1EarlyRejects($start, $howmany);

include "$BASEDIR/templates/header_review.php";

//$text = file('wmf_partial_accept.txt') or die ("ERROR: Unable to read file");
$cnt = 0;
//foreach($text as $line):
foreach($schols as $line):
	echo $line;
	
  	$columns = explode("\t", $line);
	$mail_instant = preg_replace('/\$1/',trim($columns[0]), $early_reject_mail_template);	
	//$mail_instant = preg_replace('/\$1/',trim($columns[0]), $partial_acceptance_mail_template);	
	//$mail_instant = preg_replace('/\$2/',sprintf('%02d', $cnt) . substr(md5('HAIFA2011' . sprintf('%02d', $cnt)), 0, 4), $mail_instant);
	$to = $columns[0] . '  <' . $columns[1] . '>';

	echo $mail_instant;
	/*if (!mail_msg($to, 'Wikimania 2011 Scholarship Decision', $mail_instant))
	{
		echo 'Notice: Mail delivery error. Contact Wikimania team.';
	}
	else {
		echo 'Mailing ' . $to . "<br>";
	}*/

	$cnt++;
endforeach;

include "$BASEDIR/templates/footer.php";
