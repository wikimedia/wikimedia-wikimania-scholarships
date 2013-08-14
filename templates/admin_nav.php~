<?php
$isadmin = $dal->IsSysAdmin($_SESSION['user_id']);
?>
<div id="tabsnav" class="clearfix">
<ul id="review-tabs">
	<li><a href="<?php echo $BASEURL; ?>review/phase1">Phase 1</a></li>
	<li><a href="<?php echo $BASEURL; ?>review/phase2">Phase 2</a></li>
<?
if ( $isadmin ) : ?>
        <li><a href="<?php echo $BASEURL; ?>review/grid/score?partial=0">Final: Full</a></li>
        <li><a href="<?php echo $BASEURL; ?>review/grid/score?partial=1">Final: Partial</a></li>
<? endif; ?>
	<li>|</li>
	<li><a href="<?php echo $BASEURL; ?>review/view">Review</a></li>
	<li>|</li>
	<li><a href="<?php echo $BASEURL; ?>review/search">Search</a></li>
	<li><a href="<?php echo $BASEURL; ?>review/country/grid">By Country</a></li>
	<li><a href="<?php echo $BASEURL; ?>review/region">By Region</a></li>
</ul>
</div>
