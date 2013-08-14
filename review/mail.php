<html>
<head><title>Mailing</title></head>
<body>
	<p>Send mail:</p>
	<p>Sent email to following list</p>
<?php
	require_once('init.php');

	session_start();

	if (!isset($_SESSION['user_id']))
	{
		header('location: ' . $BASEURL . 'user/login');
		exit();
	}
	
	include("PHPMailer_5.2.4/class.phpmailer.php");
	class mailing{
		private $mail;

		function __construct(){
			$this->mail= new PHPMailer();
			$this->mail->Host = "mail.wikimedia.ch";
			$this->mail->From = "wikimania-scholarships@wikimedia.org";
			$this->mail->FromName = "Wikimania 2013 Scholarship Committee";
		}

		/*function p1reject($name, $email){
			$subject = "Wikimania 2013 scholarship result";
   			$body = "<p>Dear ".$name.",</p>".
"<p>The Wikimania 2013 Scholarships Review Committee has carefully reviewed your application. With regret, we cannot sponsor your travel to attend Wikimania 2013 in Hong Kong.</p>

<p>We received more than 1200 applications from around the world for a limited number of scholarships. Preference has been given to applications from individuals who are very active contributors or volunteers on the Wikimedia projects as well as participants in other free knowledge initiatives.</p>

<p>If you can make other arrangements to attend Wikimania 2013, we encourage you to do so! Conference registration can be found at <a href='http://wikimania2013.wikimedia.org/wiki/Registration'>http://wikimania2013.wikimedia.org/wiki/Registration</a>.</p>

<p>To qualify for scholarship for next year's Wikimania conference (2014 location to be decided), we encourage you to actively participate in and contribute to the Wikimedia projects and free knowledge initiatives. If you have not yet created a user account, you may do so by clicking 'login/create account' on your favorite Wikimedia project. A good place to learn how you can help on the English Wikipedia is located at <a href='http://en.wikipedia.org/wiki/Wikipedia:Community_portal'>http://en.wikipedia.org/wiki/Wikipedia:Community_portal</a>; many other projects and languages, which you can access through our main portal at <a href='http://www.wikimedia.org'>http://www.wikimedia.org</a>, have similar pages.</p>

<p>We strongly encourage your participation on Wikimedia's projects. If you need help finding out what you can do to contribute, please do not hesitate to contact us at wikimania-scholarships@wikimedia.org. Thank you for your interest in Wikimania and the Wikimedia projects.</p>

<p>Sincerely,</p>

<p>Simon Shek, on behalf of Wikimania 2013 Scholarship Committee</p>";
   			$this->sendMail($name, $email, $subject, $body);
		}*/
		
		/*function p1success($name, $email){
			$subject = "Wikimania 2013 scholarship update";
   			$body = "<p>Dear ".$name.",</p>".
"<p>Thank you for applying for a scholarship to attend Wikimania 2013 in Hong Kong! The Wikimania 2013 Scholarships Review Committee is entering the second round of carefully reviewing all applications. </p>

<p>This is a notification that your application has made it to Phase 2 of the scholarship rating process. Note that about 700 applications have made it to Phase 2 after an initial screening of more than 1200 applicants from around the world. Preference has been given to applications from individuals who are very active contributors or volunteers on the Wikimedia projects as well as participants in other free knowledge initiatives.</p>

<p>You can expect notification regarding your final status in early April. We anticipated around 75 full scholarships sponsored by the Wikimedia Foundation, 50 partial scholarships sponsored by the Wikimedia Foundation, and around 75 scholarships sponsored by various Wikimedia Chapters. </p>

<p>Thank you for your interest in Wikimania and the Wikimedia projects.</p>

<p>Sincerely,</p>

<p>Simon Shek, on behalf of Wikimania 2013 Scholarship Committee</p>";
   			$this->sendMail($name, $email, $subject, $body);
		}*/
		
		/*function p2WMFFull($id, $name, $email){
		$subject = "WMF Acceptance letter";
   		$body = "<p>Dear ".$name.",</p>
<p>Congratulations! On behalf of the 2013 Wikimania Scholarships Program Committee and the Wikimedia Foundation, we are very pleased to inform you that your scholarship application has been approved to pay for your air travel, registration, and dormitory accommodations for Wikimania 2013 in Hong Kong from August 7 -11, 2013.</p>
<p>You are among a handful of individuals, out of thousands of applicants from all over the world, who have been selected for this opportunity. You have been selected based on your dedication and participation in the Wikimedia movement or other free knowledge and educational initiatives and your potential to add great value to Wikimania and the Wikimedia projects going forward. We hope that you will be engaged by the unique opportunity to attend Wikimania 2013 and convene face-to-face with the global Wikimedia community. We also encourage you to also submit a proposal for a workshop, seminar, tutorial, panel, or presentation! The deadline is April 30th. If interested, see <a href='http://wikimania2013.wikimedia.org/wiki/Submissions#How_to_submit_a_proposal'>http://wikimania2013.wikimedia.org/wiki/Submissions#How_to_submit_a_proposal</a></p>
<p><b>Please reply promptly to this email to accept or decline this invitation (see REPLY & ACCEPTANCE AGREEMENT below). The deadline to accept or decline this offer is April 19, 2013</b>.</p>
<p>It is important to thoroughly read and understand the information below, and send back your reply in the REPLY & ACCEPTANCE AGREEMENT section.</p>
<p>We look forward to your reply and hope to see you in Hong Kong!<br />
Sincerely,<br />
Simon Shek, Wikimania 2013 Scholarships Program Committee<br />
Jessie Wild, Wikimedia Foundation<br />
wikimania-scholarships@wikimedia.org<br /></p>
<hr />
<p>REPLY & ACCEPTANCE AGREEMENT<br />
<b>Please reply to this email with answers to the questions below, in the space indicated.</b></p>
<p>1. By accepting this offer, you are making a firm commitment to travel to and attend Wikimania 2013 in Hong Kong. If you are not sure that you want to attend Wikimania, please do not commit to do so. There are many other individuals who would benefit from this support. The deadline to accept or decline is April 19, 2013. If you do not respond then, the scholarship will be awarded to someone else.</p>
<p>2. Your contact information may be shared with a travel agency contracted with the Wikimedia Foundation. They will be arranging your transportation to Wikimania in Hong Kong (see TRAVEL ARRANGEMENTS section below).</p>
<p>3. You are welcome to tell people that you received this scholarship, but we will use your name and contact information only for the purposes described in this letter. Wikimedia is a worldwide movement, so by accepting this scholarship, you consent to the transfer of your information to the United States and other places as may be necessary to provide your scholarship and arrange attendance at Wikimania.</p>
<p>4. The Wikimedia Foundation and the Scholarships Program Committee may contact you for a potential interview at Wikimania, a post-conference survey, and other purposes related to your scholarship or attendance. Your participation is highly encouraged and greatly appreciated. If you do not wish to be contacted after receiving the scholarship, please send an opt-out notification by contacting the Scholarships Program Committee (wikimania-scholarships@wikimedia.org).</p>
<p>Please indicate ACCEPT or DECLINE in the space below, in reply to this email.<br />
<br />
<br /></p>
<hr />
<p>EXPENSES COVERED / NOT COVERED<br />
Wikimania 2013 Scholarships cover the cost of round-trip travel, dorm accommodations, and registration for Wikimania 2013 in Hong Kong.</p>
<p>Expanses that are not covered are: incidentals, local transportation during the conference, meals outside of the conference venue, any expenses associated with a holiday or vacation taken outside of the conference dates of Aug 7-11. These expenses will be your responsibility. (Please note that during the conference, some meals are provided, and assistance with transportation to and from the dorms to the venue may be offered.) The Wikimedia Foundation will not cover costs for Visa fees. However, if you deem that these fees prohibit you from traveling to Wikimania, please let us know in the REPLY section below.</p>
<hr />
<p>SCHOLARSHIP ID You will use your Scholarship ID to indicate you are a scholarship recipient on the <a href='http://wikimania2013.wikimedia.org/wiki/Registration'>registration form</a>. By providing your Scholarship ID, you will ensure that registration or dorms accommodations are paid for by the Wikimedia Foundation.</p>
<p>Your Scholarship ID is ".$id."</p>
<hr />
<p>TRAVEL ARRANGEMENTS<br />
Travel Booking by Travel Agent: After indicating your acceptance of the scholarship, you must contact our travel agency to book your travel, which will be directly paid for by the Wikimedia Foundation. Travel arrangements must be confirmed no later than June 15. The travel agency is familiar with the conference dates and reasonable rates associated with travel to Hong Kong from your location and has been authorized to book your travel based on these rates. Any costs for personal travel or ticket change costs beyond the reasonable agency rate will be your responsibility. To book your ticket, you should contact:</p>
<p>Email: wikigroup@tandt.com Toll Free Tel: +1-877-504-1896</p>
<p>The agency will request the following information from you. Please have this on hand: Your name as it appears on your passport -Departing Airport -Nationality (on passport) -Passport Expiration date -Birth date -Gender -Seat Preference (aisle, window) -Mileage Numbers (frequent flyer) -Your Phone Number -Your Email Address</p>
<p>Travel Visas: You are responsible for obtaining any visa or travel documentation required for travel to Hong Kong based on the nationality on your passport. We recommend that you visit Hong Kong's Immigration Department Website immediately to determine if you need to obtain a visa. Requirements vary for each country, and it may take some time and effort to obtain a visa. If you need an invitation letter from conference organizers that confirms your intention to travel, please indicate in the REPLY section.</p>
<hr />
<p>CONFERENCE REGISTRATION AND ACCOMMODATIONS<br />
Please register at the <a href='http://wikimania2013.wikimedia.org/wiki/Registration'>Registration Website</a> using the above unique scholarship ID. Dormitory accommodations for 4 nights will be covered for scholarship recipients. We will be contacting you about how to register for the dorms soon after your acceptance.</p>
<hr />
<p>SPECIAL NEEDS<br />
Please inform us of any special needs you have related to mobility and wheelchair access to ensure appropriate accommodations.</p>
<hr />
<p>USEFUL LINKS<br />
Scholarships Information Page - key updates will be posted here<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Scholarships'>http://wikimania2013.wikimedia.org/wiki/Scholarships</a></p>
<p>Wikimania 2013 Page<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Main_Page'>http://wikimania2013.wikimedia.org/wiki/Main_Page</a></p>
<p>Registration<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Registration'>http://wikimania2013.wikimedia.org/wiki/Registration</a></p>
<p>Hong Kong's Immigration Department Website (Visa information)<br />
<a href='http://www.immd.gov.hk/en/services/hk-visas/visit-transit/visit-visa-entry-permit.html'>http://www.immd.gov.hk/en/services/hk-visas/visit-transit/visit-visa-entry-permit.html</a> Additional information on travel to Hong Kong <a href='http://wikimania2013.wikimedia.org/wiki/Getting_to_Hong_Kong'>http://wikimania2013.wikimedia.org/wiki/Getting_to_Hong_Kong</a></p>
<p>General Information on Hong Kong and Wikimania<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Hong_Kong'>http://wikimania2013.wikimedia.org/wiki/Hong_Kong</a>  <a href='http://wikimania2013.wikimedia.org/wiki/Local_Information'>http://wikimania2013.wikimedia.org/wiki/Local_Information</a></p>
<p>See who's attending Wikimania 2013!<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Attendees'>http://wikimania2013.wikimedia.org/wiki/Attendees</a><br />
Questions? Contact wikimania-scholarships@wikimedia.org</p>
";
   			$this->sendMail($name, $email, $subject, $body);
		}*/
		
		/*function p2WMDEFull($id, $name, $email){
		$subject = "WMDE / WMF Acceptance letter";
   		$body = "<p>Dear ".$name.",</p>
<p>Congratulations! On behalf of the 2013 Wikimania Scholarships Program Committee, Wikimedia Deutschland, and the Wikimedia Foundation, we are very pleased to inform you that your scholarship application has been approved to pay for your air travel, registration, and dormitory accommodations for Wikimania 2013 in Hong Kong from August 7 - 11, 2013! Wikimedia Deutschland is funding your full scholarship, and the Wikimedia Foundation will be making your travel arrangements.</p>
<p>You are among a handful of individuals, out of thousands of applicants from all over the world, who have been selected for this opportunity. You have been selected based on your dedication and participation in the Wikimedia movement or other free knowledge and educational initiatives and your potential to add great value to Wikimania and the Wikimedia projects going forward. We hope that you will be engaged by the unique opportunity to attend Wikimania 2013 and meet face-to-face with the global Wikimedia community. If you wish to submit a proposal for a workshop, seminar, tutorial, panel, or presentation, please do so by the April 30th deadline. See <a href='http://wikimania2013.wikimedia.org/wiki/Submissions#How_to_submit_a_proposal'>http://wikimania2013.wikimedia.org/wiki/Submissions#How_to_submit_a_proposal</a></p>
<p>Please reply promptly to this email to accept or decline this invitation (see REPLY &amp; ACCEPTANCE AGREEMENT below). <b>The deadline to accept or decline this offer is April 19, 2013</b>.</p>
<p>It is important to thoroughly read and understand the information below, and send back your reply in the REPLY &amp; ACCEPTANCE AGREEMENT section.</p>
<p>We look forward to your reply and hope to see you in Hong Kong!</p>
<p>Sincerely, Pavel Richter, Wikimedia Deutschland<br />
Simon Shek, Wikimania 2013 Scholarships Program Committee, wikimania-scholarships@wikimedia.org<br />
Jessie Wild, Wikimedia Foundation<br /></p>
<hr />
<p>REPLY &amp; ACCEPTANCE AGREEMENT<br />
<b>Please reply to this email with answers to the questions below, in the space indicated.</b></p>
<p>1. By accepting this offer, you are making a firm commitment to travel to and attend Wikimania 2013 in Hong Kong. If you are not sure that you want to attend Wikimania, please do not commit to do so. There are many other individuals who would benefit from this support. The deadline to accept or decline is April 19, 2013. If you do not respond by April 19, the scholarship will be awarded to someone else.</p>
<p>2. Your contact information may be shared with a travel agency contracted with the Wikimedia Foundation. They will be arranging your transportation to Wikimania in Hong Kong (see the TRAVEL ARRANGEMENTS section below).</p>
<p>3. You are welcome to tell people that you received this scholarship, but we will use your name and contact information only for the purposes described in this letter. Wikimedia is a worldwide movement, so by accepting this scholarship, you consent to the transfer of your information to the United States and other places as may be necessary to provide your scholarship and arrange travel and attendance at Wikimania.</p>
<p>4. The Wikimedia Foundation and the Scholarships Program Committee may contact you for a potential interview at Wikimania, a post-conference survey, and other purposes related to your scholarship or attendance. Your participation is highly encouraged and greatly appreciated. If you do not wish to be contacted after receiving the scholarship, please send an opt-out notification by contacting the Scholarships Program Committee (wikimania-scholarships@wikimedia.org).</p>
<p>Please indicate ACCEPT or DECLINE in the space below, in reply to this email.</p>
<hr />
<p>EXPENSES COVERED / NOT COVERED<br />
Wikimania 2013 Scholarships cover the cost of round-trip travel, dorm accommodations, and registration for Wikimania 2013 in Hong Kong.</p>
<p>Expenses that are not covered are: incidentals, local transportation during the conference, meals outside of the conference venue, any expenses associated with a holiday or vacation taken outside of the conference dates of Aug 8-12. These expenses will be your responsibility. (Please note that during the conference, some meals are provided, and assistance with transportation to and from the dorms to the venue may be offered.) The Wikimedia Foundation will not cover the costs for Visa fees for travel. However, if you deem that the cost prohibits you from traveling to Wikimania, please let us know in the REPLY section.</p>
<hr />
<p>SCHOLARSHIP ID You will use your Scholarship ID to indicate you are a scholarship recipient on the <a href='http://wikimania2013.wikimedia.org/wiki/Registration'>registration form</a>. By providing your Scholarship ID, you will ensure that registration or dorms accommodations are paid for by the Wikimedia Foundation.</p>
<p>Your Scholarship ID is ".$id."</p>
<hr />
<p>TRAVEL ARRANGEMENTS<br />
Travel Booking by Travel Agent: After indicating your acceptance of the scholarship, you will be asked to contact our travel agency to book your travel, which will be directly paid for by the Wikimedia Foundation. Travel arrangements must be confirmed no later than June 15. The travel agency is familiar with the conference dates and reasonable rates associated with travel to Hong Kong from your location and has been authorized to book your travel based on these rates. Any costs for personal travel or ticket change costs beyond the reasonable agency rate will be your responsibility. To book your ticket, you should contact:</p>
<p>Email: wikigroup@tandt.com Toll Free Tel: +1-877-504-1896</p>
<p>The agency will request the following information from you. Please have this on hand: Your name as it appears on your passport -Departing Airport -Nationality (on passport) -Passport Expiration date -Birth date -Gender -Seat Preference (aisle, window) -Mileage Numbers (frequent flyer) -Your Phone Number -Your Email Address</p>
<p>Travel Visas: You are responsible for obtaining any visa or travel documentation required for travel to Hong Kong based on the nationality on your passport. We recommend that you visit Hong Kong's Immigration Department Website immediately to determine if you need to obtain a visa. Requirements vary for each country, and it may take some time and effort to obtain a visa. If you need an invitation letter from conference organizers that confirms your intention to travel, please indicate in the REPLY section.</p>
<hr />
<p>CONFERENCE REGISTRATION AND ACCOMMODATIONS<br />
Please register for the conference at <a href='http://wikimania2013.wikimedia.org/wiki/Registration'>Registration web site</a> using the above unique scholarship ID. Dormitory accommodations for 4 nights will be covered for scholarship recipients. We will be contacting you about how to register for the dorms soon after your acceptance.</p>
<hr />
<p>SPECIAL NEEDS<br />
Please inform us of any special needs you have related to mobility and wheelchair access to ensure appropriate accommodations.</p>
<hr />
<p>USEFUL LINKS<br />
Scholarships Information Page - key updates will be posted here<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Scholarships'>http://wikimania2013.wikimedia.org/wiki/Scholarships</a></p>
<p>Wikimania 2013 Page<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Main_Page'>http://wikimania2013.wikimedia.org/wiki/Main_Page</a></p>
<p>Registration<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Registration'>http://wikimania2013.wikimedia.org/wiki/Registration</a></p>
<p>Hong Kong's Immigration Department Website (Visa information) <a href='http://www.immd.gov.hk/en/services/hk-visas/visit-transit/visit-visa-entry-permit.html'>http://www.immd.gov.hk/en/services/hk-visas/visit-transit/visit-visa-entry-permit.html</a> Additional information on travel to Hong Kong <a href='http://wikimania2013.wikimedia.org/wiki/Getting_to_Hong_Kong'>http://wikimania2013.wikimedia.org/wiki/Getting_to_Hong_Kong</a></p>
<p>General Information on Hong Kong and Wikimania<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Hong_Kong'>http://wikimania2013.wikimedia.org/wiki/Hong_Kong</a></p>
<p>See who's attending Wikimania 2013!<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Attendees'>http://wikimania2013.wikimedia.org/wiki/Attendees</a><br />
Questions? Contact wikimania-scholarships@wikimedia.org</p>
";
   			$this->sendMail($name, $email, $subject, $body);
		}*/
		
		/*function p2WMFPartial($id, $name, $email){
		$subject = "Partial Scholarship Acceptance letter";
   		$body = "<p>Dear ".$name.",</p>
		<p>Congratulations! On behalf of the 2013 Wikimania Scholarships Program Committee and the Wikimedia Foundation, we are very pleased to inform you that your scholarship application has been approved to pay for a partial scholarship for Wikimania 2013 in Hong Kong from August 7 - 11, 2013. This award will be for the following amount which is based on our best estimate for 50% of the travel expenses to/from Hong Kong based on your region of origin:</p>
<table>
<tr>
<td align='center' style='background:#f0f0f0;'><b>Region</b></td>
<td align='center' style='background:#f0f0f0;'><b>Est. Flight</b></td>
</tr>
<tr>
<td>Africa</td>
<td>$660</td>
</tr>
<tr>
<td>Asia-Pacific</td>
<td></td>
</tr>
<tr>
<td>
<dl>
<dd>Australia</dd>
</dl>
</td>
<td>$685</td>
</tr>
<tr>
<td>
<dl>
<dd>India</dd>
</dl>
</td>
<td>$475</td>
</tr>
<tr>
<td>
<dl>
<dd>Other Asia-Pacific</dd>
</dl>
</td>
<td>$300</td>
</tr>
<tr>
<td>Central & South America</td>
<td>$1,130</td>
</tr>
<tr>
<td>East Europe & Central Asia</td>
<td>$600</td>
</tr>
<tr>
<td>Europe</td>
<td>$600</td>
</tr>
<tr>
<td>North Africa & West Asia</td>
<td>$675</td>
</tr>
<tr>
<td>North America</td>
<td>$820</td>
</tr>
</table>
<p>You are among a handful of individuals, out of thousands of applicants from all over the world, who have been selected for this opportunity. You have been selected based on your dedication and participation in the Wikimedia movement or other free knowledge and educational initiatives and your potential to add great value to Wikimania and the Wikimedia projects going forward. We hope that you will be engaged by the unique opportunity to attend Wikimania 2013 and meet face-to-face with the global Wikimedia community. We also encourage you to also submit a proposal for a workshop, seminar, tutorial, panel, or presentation! The deadline is April 30th. If interested, see <a href='http://wikimania2013.wikimedia.org/wiki/Submissions#How_to_submit_a_proposal'>http://wikimania2013.wikimedia.org/wiki/Submissions#How_to_submit_a_proposal</a></p>
<p>Please reply promptly to this email to accept or decline this invitation (see REPLY & ACCEPTANCE AGREEMENT below). <b>The deadline to accept or decline this offer is April 19, 2013.</b></p>
<p>It is important to thoroughly read and understand the information below, and send back your reply in the REPLY & ACCEPTANCE AGREEMENT section.</p>
<p>We look forward to your reply and hope to see you in Hong Kong!</p>
<p>Sincerely,<br /></p>
<p>Simon Shek, Wikimania 2013 Scholarships Committee<br />
Jessie Wild, Wikimedia Foundation<br />
wikimania-scholarships@wikimedia.org<br /></p>
<hr />
<p>REPLY & ACCEPTANCE AGREEMENT<br />
Please reply to this email with answers to the questions below, in the space indicated.</p>
<p>1) By accepting this scholarship, you are making a firm commitment to travel to and attend Wikimania 2013 in Hong Kong. If you are not sure that you want to attend Wikimania, please do not commit to do so. There are many other individuals who would benefit from this support. The deadline to accept or decline is April 19, 2013. If you do not respond by April 19, the scholarship will be awarded to someone else.<br />
2) The Wikimedia Foundation and the Scholarships Program Committee may contact you for a potential interview at Wikimania, a post-conference survey, and other purposes related to your scholarship or attendance. Your participation is highly encouraged and greatly appreciated. If you do not wish to be contacted after receiving the scholarship, please send an opt-out notification by contacting the Scholarships Program Committee (wikimania-scholarships@wikimedia.org).<br />
<br />
Please indicate ACCEPT or DECLINE in the space below, in reply to this email.<br />
<br />
<br /></p>
<hr />
<p>EXPENSES COVERED / NOT COVERED<br />
Wikimania 2013 Partial Scholarships cover up to 50% of the cost for round-trip travel for Wikimania 2013 in Hong Kong based on estimated air travel listed above.</p>
<p>Expenses that are not covered are: incidentals, local transportation during the conference, meals outside of the conference venue, any costs associated with a holiday or vacation taken outside of the conference dates of Aug 7-11. These expenses will be your responsibility. (Please note that during the conference, some meals are provided, and assistance with transportation to and from the dorms to the venue may be offered.)</p>
<p>The Wikimedia Foundation will not cover costs for Visa fees for travel.<br /></p>
<hr />
<p>TRAVEL ARRANGEMENTS<br />
Direct Reimbursement for Air Travel: Once you indicate acceptance of the scholarship (by submitting the REPLY &amp; ACCEPTANCE section below), you may book the travel of your choosing. You will receive the amount listed above to help cover your travel expenses only if you provide the Wikimedia Foundation with a receipt of purchase. We will process your request via PayPal in June 2013. It is also possible to reimburse by wire transfer, but there may be associated fees. Upon acceptance of the partial scholarship, you will be sent a form for reimbursement to submit along with your receipt of purchase.<br />
Travel Visas: You are responsible for obtaining any visa or travel documentation required for travel to Hong Kong based on the nationality on your passport. We recommend that you visit Hong Kong's Immigration Department Website immediately to determine if you need to obtain a visa as requirements vary for each country, and it may take some time and effort to obtain a visa. If you need an invitation letter from the Wikimania organizers that confirms your intention to travel, please indicate in the REPLY section below.</p>
<hr />
<p>CONFERENCE REGISTRATION AND ACCOMMODATIONS<br />
Registration: Please register at the <a href='http://wikimania2013.wikimedia.org/wiki/Registration'>Registration Website</a>.<br />
Accommodations: You may pay and reserve dorm accommodations on the web site. There will also be a list of suggested hotels on the web site that you can book directly with.</p>
<hr />
<p>SPECIAL NEEDS<br />
Please inform us of any special needs you have related to mobility and wheelchair access to ensure appropriate accommodations.</p>
<hr />
<p>USEFUL LINKS<br />
Scholarships Information Page - key updates will be posted here<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Scholarships'>http://wikimania2013.wikimedia.org/wiki/Scholarships</a></p>
<p>Wikimania 2013 Page<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Main_Page'>http://wikimania2013.wikimedia.org/wiki/Main_Page</a></p>
<p>Registration<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Registration'>http://wikimania2013.wikimedia.org/wiki/Registration</a></p>
<p>Hong Kong's Immigration Department Website (Visa information)<br />
<a href='http://www.immd.gov.hk/en/services/hk-visas/visit-transit/visit-visa-entry-permit.html'>http://www.immd.gov.hk/en/services/hk-visas/visit-transit/visit-visa-entry-permit.html</a></p>
<p>Additional information on travel to Hong Kong<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Getting_to_Hong_Kong'>http://wikimania2013.wikimedia.org/wiki/Getting_to_Hong_Kong</a></p>
<p>General Information on Hong Kong and Wikimania<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Hong_Kong'>http://wikimania2013.wikimedia.org/wiki/Hong_Kong</a> <br />
See who's attending Wikimania 2013!<br />
<a href='http://wikimania2013.wikimedia.org/wiki/Attendees'>http://wikimania2013.wikimedia.org/wiki/Attendees</a></p>
<p>Questions? Contact wikimania-scholarships@wikimedia.org</p>
";
		$this->sendMail($name, $email, $subject, $body);
		}*/
		
		/*function p2WMFWaitlist($id, $name, $email){
		$subject = "Waitlist letter";
   		$body = "<p>Dear ".$name.",</p>
		<p><br />
The Wikimania 2013 Scholarships Program Committee has made its final decision on scholarship awardees for Wikimania 2013 in Hong Kong, August 7-11. Over 1,200 applications for scholarships were received this year. Although you were not granted a scholarship, your application received a high score, and you have been placed on a short waiting list. Should any scholarship grantees not be able to accept it, you will have a chance to be selected. We will notify you if you have been accepted by the end of April.</p>
<p>We highly value your participation on the Wikimedia projects, and encourage your continued dedication and participation. Thank you for your interest in Wikimania.</p>
<p><br />
Sincerely,</p>
<p>Simon Shek, Wikimania Scholarships Committee<br />
Jessie Wild, Wikimedia Foundation<br />
wikimania-scholarships@wikimedia.org</p>
";
		$this->sendMail($name, $email, $subject, $body);
		}*/
		
		/*function p2WMFDecline($id, $name, $email){
		$subject = "Decline letter";
   		$body = "<p>Dear Applicant:</p>
<p><br />
The Wikimania 2013 Scholarships Program Committee has made its final decision on scholarship recipients. We regret to tell you that you were not selected to receive a scholarship to attend Wikimania 2013 in Hong Kong.</p>
<p><br />
The Committee judged over 1200 applications from individuals around the world on the basis of their contributions to the Wikimedia projects, participation as a volunteer in both on and off-line activities, and/or involvement in initiatives related to open source, education, free culture, etc. We wish that we could send every applicant to Wikimania, but we are only able to support to a small percentage of those who applied.</p>
<p><br />
If you can make other arrangements to attend Wikimania 2013, we strongly encourage you to do so! Conference registration can be found at &lt;<a href='http://wikimania2013.wikimedia.org/wiki/Registration'>http://wikimania2013.wikimedia.org/wiki/Registration</a>&gt;.</p>
<p><br />
We value your participation on Wikimedia's projects and encourage your future participation. If you need help finding out what you can do to contribute, please do not hesitate to contact us at wikimania-scholarshipwikimedia.org. Thank you for your interest in Wikimania and for your involvement in the Wikimedia movement.</p>
<p><br />
Sincerely,<br />
Simon Shek, Wikimania Scholarships Committee<br />
Jessie Wild, Wikimdedia Foundation<br />
wikimania-scholarships@wikimedia.org<br /></p>
";
		$this->sendMail($name, $email, $subject, $body);
		}*/
		
		/*function p2WMFChapOpp($id, $name, $email){
		$subject = "Chapter Scholarship letter";
   		$body = "<p>Dear ".$name.",</p>
		<p>The Wikimania 2013 Scholarships Program Committee has finished the application reviews for Wikimania 2013 in Hong Kong, August 7 - 11! Your application qualifies for a Wikimedia chapter scholarship, and we would like to inform you that your application information has been shared with a Wikimedia chapter and for the final selection process.</p>
<p>The final decision will come soon. We highly value your participation on the Wikimedia projects, and encourage your continued dedication and participation. Thank you for your patience, and your interest in Wikimania!</p>
<p>Sincerely,<br />
Simon Shek, Wikimania Scholarships Committee<br />
Jessie Wild, Wikimedia Foundation<br />
wikimania-scholarships@wikimedia.org</p>
";
		$this->sendMail($name, $email, $subject, $body);
		}*/
		
	/*function p2FullRemind($id, $name, $email, $discountCode){
		$subject = "Full Scholarship Reminder Letter";
   		$body = "<p>Dear ".$name.",</p>
<p>Earlier this month we received your acceptance of Wikimedia Foundation¡¦s offer that will pay for your air travel, registration, and dormitory accommodations to attend Wikimania 2013 in Hong Kong, Aug 7-11, 2013. Our records indicate that you have not yet made arrangements to attend. We very much would like to encourage you to complete the steps below soon.</p>
<p>DISCOUNT CODE You will use the discount code to indicate you are a scholarship recipient on the registration form. Use this for registering for the conference and accommodations.</p>
<p>Discount code: &lt;".$discountCode."&gt; Your Scholarship ID is &lt;".$id."&gt;</p>
<p>Please do the following soon:</p>
<p>1. CONFERENCE REGISTRATION</p>
<p>Please register for the conference at Registration web site using the above discount code.</p>
<p>2. ACCOMMODATIONS:</p>
<p>Dormitory accommodations for 4 nights will be covered for scholarship recipients. Please fill in the registration form: <a href='http://wikimania2013.wikimedia.org/wiki/Accommodation'>http://wikimania2013.wikimedia.org/wiki/Accommodation</a></p>
<p>If you do not wish to stay in the dorms, please let eyoung@wikimedia.org know.</p>
<p>3. TRAVEL ARRANGEMENTS:</p>
<p>Please contact our travel agency to book your travel, which will be paid for directly by the Wikimedia Foundation:</p>
<p>Email: wikigroup@tandt.com</p>
<p>Toll Free Tel: +1-877-504-1896</p>
<p>Travel arrangements must be confirmed no later than June 15.</p>
<p>4. SPECIAL NEEDS:</p>
<p>Please inform us of any special needs you have related to mobility and wheelchair access to ensure appropriate accommodations.</p>
<p>If I can be of further assistance, please let me know.</p>
<p>Ellie Young</p>
<p>Conference Coordinator</p>
<p>Wikimedia Foundation</p>
<p>eyoung@wikimedia.org</p>
";
		$this->sendMail($name, $email, $subject, $body);
	}*/
	
	/*function p2PartialRemind($id, $name, $email){
		$subject = "Partial Scholarship Reminder letter";
   		$body = "<p>Dear ".$name.",</p>
<p>Earlier this month, we received your acceptance of a partial scholarship to attend Wikimania 2013 in Hong Kong from August 7 - 11, 2013. This is a reminder that the Wikimedia Foundation will reimburse you for either 50% of the exact cost (converted to USD from your local currency based on the exchange rate obtained via Oanda, WMF¡¦s preferred exchange website) of your travel expenses to/from Hong Kong, or the amount listed below*.</p>
<table>
<tr>
<td align='center' style='background:#f0f0f0;'><b>Region</b></td>
<td align='center' style='background:#f0f0f0;'><b>Est. Flight</b></td>
</tr>
<tr>
<td>Africa</td>
<td>$660</td>
</tr>
<tr>
<td>Asia-Pacific</td>
<td></td>
</tr>
<tr>
<td>
<dl>
<dd>Australia</dd>
</dl>
</td>
<td>$685</td>
</tr>
<tr>
<td>
<dl>
<dd>India</dd>
</dl>
</td>
<td>$475</td>
</tr>
<tr>
<td>
<dl>
<dd>Other Asia-Pacific</dd>
</dl>
</td>
<td>$300</td>
</tr>
<tr>
<td>Central &amp; South America</td>
<td>$1,130</td>
</tr>
<tr>
<td>East Europe &amp; Central Asia</td>
<td>$600</td>
</tr>
<tr>
<td>Europe</td>
<td>$600</td>
</tr>
<tr>
<td>North Africa &amp; West Asia</td>
<td>$675</td>
</tr>
<tr>
<td>North America</td>
<td>$820</td>
</tr>
</table>
<ul>
<li>This amount is based on our best estimate for 50% of the travel expenses to/from Hong Kong based on your region of origin.</li>
</ul>
<p>We encourage you to make your arrangements soon.</p>
<p>In order to reimburse you for this, we will need you to provide us with the following:</p>
<p>Copy of your air ticket /receipt for our records</p>
<p>Preferred form of reimbursement (three options laid out below and fees may be assessed to you by PayPal or your bank for bank transfer):</p>
<p><br />
1. PayPal: please provide your PayPal Account</p>
<p>2. Bank Transfer: please provide the following</p>
<p>Full legal name of beneficiary:</p>
<p>Address of beneficiary:</p>
<p>Telephone # of beneficiary:</p>
<p>Address of bank:</p>
<p>Name of bank:</p>
<p>Account number:</p>
<p>IBAN (outside US):</p>
<p>SWIFT or BIC (required outside of US):</p>
<p>ABA/Routing Number (US Only)</p>
<p>IFSC (India only):</p>
<p>Tax ID (Brazil only):</p>
<p>3. Check (U.S. recipients only): your name and address</p>
<p>Please send all of this information to:</p>
<p>Ellie Young</p>
<p>eyoung@wikimedia.org TEL: +1 415 839 6885 x6862 FAX: +1 415 882 0495</p>
";
		$this->sendMail($name, $email, $subject, $body);
	}*/
	
		function p2TravelRemind($id, $name, $email, $discountCode){
		$subject = "Travel Arrangements for your Wikimania Scholarship";
   		$body = "<p>Dear ".$name.",</p>
<p>Our records indicate that as of this date, you have not yet contacted
our Travel Agent (see below) to make your arrangements for travel to
attend Wikimania 2013 in Hong Kong, Aug 7-11, 2013.   In our previous
emails we encouraged you to do this by June 15th.   We would strongly
urge you to act now.   Also if you have not yet registered for the
conference and/or dorms, please do so as well.</p>
<p></p>
<p>Here are the instructions again and we look forward to your
participation in Wikimania!</p>
<p></p>
<p>DISCOUNT CODE You will use the discount code to indicate you are a
scholarship recipient on the registration form. Use this for
registering for the conference and accommodations.</p>
<p></p>
<p>Discount code: ".$discountCode." Your Scholarship ID is ".$id."</p>
<p></p>
<p>Please do the following soon:</p>
<p></p>
<p>1. CONFERENCE REGISTRATION</p>
<p></p>
<p>Please register for the conference at Registration web site using the
above discount code.</p>
<p></p>
<p>2. ACCOMMODATIONS:</p>
<p></p>
<p>Dormitory accommodations for 4 nights will be covered for scholarship
recipients. Please fill in the registration form:
http://wikimania2013.wikimedia.org/wiki/Accommodation</p>
<p></p>
<p>If you do not wish to stay in the dorms, please let eyoung@wikimedia.org know.</p>
<p></p>
<p>3. TRAVEL ARRANGEMENTS:</p>
<p></p>
<p>Please contact our travel agency to book your travel, which will be
paid for directly by the Wikimedia Foundation:</p>
<p></p>
<p>Email: wikigroup@tandt.com</p>
<p></p>
<p>Toll Free Tel: +1-877-504-1896</p>
<p></p>
<p>Travel arrangements must be confirmed no later than June 15.</p>
<p></p>
<p>4. SPECIAL NEEDS:</p>
<p></p>
<p>Please inform us of any special needs you have related to mobility and
wheelchair access to ensure appropriate accommodations.</p>
<p></p>
<p>If I can be of further assistance, please let me know.</p>
<p></p>
<p>Ellie Young</p>
<p>Conference Coordinator</p>
<p>Wikimedia Foundation</p>
<p></p>
<p>eyoung@wikimedia.org</p>
";
		$this->sendMail($name, $email, $subject, $body);
	}
		
		function sendMail($name, $email, $subject, $body){
			$this->mail->Subject = $subject;
			$this->mail->Body = $body;
			$this->mail->IsHTML(true);
   			$this->mail->AddAddress($email, $name);
   			
			if(!$this->mail->Send()) {
		    	echo "<p>Mailer Error: " . $this->mail->ErrorInfo . "</p>";
		   	}
		}
	}
	
	//$dal = new DataAccessLayer;
	//p1 reject
	/*$schols = $dal->GetPhase1EarlyRejects();
	$schols = $dal->GetPhase1EarlyRejectsTemp();*/
	//p1 success
	//$schols = $dal->GetPhase1Success();

	//$file = fopen("test.txt", "r") or exit( 'Unable to open file! '.getcwd() );//testing
	//$file = fopen("Full - WMF.txt", "r") or exit( 'Unable to open file! '.getcwd() );//p2WMFFull
	//$file = fopen("Full - WMDE.txt", "r") or exit( 'Unable to open file! '.getcwd() );//p2WMDEFull
	//$file = fopen("Partial - WMF.txt", "r") or exit( 'Unable to open file! '.getcwd() );//p2WMFPartial
	//$file = fopen("Waitlist - WMF.txt", "r") or exit( 'Unable to open file! '.getcwd() );//p2WMFWaitlist
	//$file = fopen("Decline.txt", "r") or exit( 'Unable to open file! '.getcwd() );//p2WMFDecline
	//$file = fopen("Chapter opportunity.txt", "r") or exit( 'Unable to open file! '.getcwd() );//p2WMFChapOpp
	$file = fopen("FullScholarshipService.txt", "r") or exit( 'Unable to open file! '.getcwd() );//p2TravelRemind
	
	
	//Output a line of the file until the end is reached
	$schols = array();
	$temp = "";
	while(!feof($file)){
		$temp = fgets($file);
		$tempSplit = preg_split("/,/", $temp);
		$tempArr = array("id"=>$tempSplit[0],"name"=>$tempSplit[1],"email"=>$tempSplit[2], "scholar"=>$tempSplit[3]);
		array_push($schols, $tempArr);
		$temp = "";
	}
	fclose($file);
	
	$cnt=1;
	foreach ($schols as $row) {
		$sendMail = new mailing;
		
		//p1 reject
		/*$sendMail -> p1reject($row['fname'] . ' ' . $row['lname'], $row['email']);
		echo "<p>".$cnt.": ".$row['fname'] . ' ' . $row['lname']." email:".$row['email']."</p>";*/
		
		//p1 success
		/*$sendMail -> p1success($row['fname'] . ' ' . $row['lname'], $row['email']);
		echo "<p>".$cnt.": ".$row['fname'] . ' ' . $row['lname']." email:".$row['email']."</p>";
		$cnt++;*/
		
		//p2WMFFull
		/*$sendMail -> p2WMFFull($row['id'], $row['name'], $row['email']);
		echo "<p>".$cnt.": id=>".$row['id'] . ',name=>' . $row['name'].",email=>".$row['email']."</p>";
		$cnt++;*/
		
		//p2WMDEFull
		/*$sendMail -> p2WMDEFull($row['id'], $row['name'], $row['email']);
		echo "<p>".$cnt.": id=>".$row['id'] . ',name=>' . $row['name'].",email=>".$row['email']."</p>";
		$cnt++;*/
		
		//p2WMFPartial
		/*$sendMail -> p2WMFPartial($row['id'], $row['name'], $row['email']);
		echo "<p>".$cnt.": id=>".$row['id'] . ',name=>' . $row['name'].",email=>".$row['email']."</p>";
		$cnt++;*/
		
		//p2WMFWaitlist
		/*$sendMail -> p2WMFWaitlist($row['id'], $row['name'], $row['email']);
		echo "<p>".$cnt.": id=>".$row['id'] . ',name=>' . $row['name'].",email=>".$row['email']."</p>";
		$cnt++;*/
		
		//p2WMFDecline
		/*$sendMail -> p2WMFDecline($row['id'], $row['name'], $row['email']);
		echo "<p>".$cnt.": id=>".$row['id'] . ',name=>' . $row['name'].",email=>".$row['email']."</p>";
		$cnt++;*/
		
		//p2WMFChapOpp
		/*$sendMail -> p2WMFChapOpp($row['id'], $row['name'], $row['email']);
		echo "<p>".$cnt.": id=>".$row['id'] . ',name=>' . $row['name'].",email=>".$row['email']."</p>";
		$cnt++;*/
		
		//p2FullRemind
		/*$sendMail -> p2FullRemind($row['id'], $row['name'], $row['email'], "WMDE4929ac");
		echo "<p>".$cnt.": id=>".$row['id'] . ',name=>' . $row['name'].",email=>".$row['email']."</p>";
		$cnt++;*/
		
		//p2PartialRemind
		/*$sendMail -> p2PartialRemind($row['id'], $row['name'], $row['email']);
		echo "<p>".$cnt.": id=>".$row['id'] . ',name=>' . $row['name'].",email=>".$row['email']."</p>";
		$cnt++;*/
		
		//p2TravelRemind
		$sendMail -> p2TravelRemind($row['id'], $row['name'], $row['email'], $row['scholar']);
		echo "<p>".$cnt.": id=>".$row['id'] . ',name=>' . $row['name'].",email=>".$row['email'].", scholar=>".$row['scholar']."</p>";
		$cnt++;
	}
?>
</body>
</html>