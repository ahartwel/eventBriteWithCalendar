<?php

/*
Plugin Name: Eventbrite API with Calendar
Plugin URI: 
Description: No different than Automattic's eventbrite plugin, except this one displays a calendar, modified April 14th, 2015
Version: 1
Author: SWARM NYC, thanks Automattic!
Author URI: http://swarmnyc.com
License: GPL v2 or newer <https://www.gnu.org/licenses/gpl.txt>
*/

/**
 * Set our token option on activation if an Eventbrite connection already exists in Keyring.
 */
function eventbrite_check_existing_token() {
	// Bail if Keyring isn't activated.
	if ( ! class_exists( 'Keyring_SingleStore' ) ) {
		return;
	}

	// Get any Eventbrite tokens we may already have.
	$tokens = Keyring_SingleStore::init()->get_tokens( array( 'service'=>'eventbrite' ) );

	// If we have one, just use the first.
	if ( ! empty( $tokens[0] ) ) {
		update_option( 'eventbrite_api_token', $tokens[0]->unique_id );
	}
}
register_activation_hook( __FILE__, 'eventbrite_check_existing_token' );

/**
 * Load our API on top of the Keyring Eventbrite service.
 */
function eventbrite_api_load_keyring_service() {
	require_once( 'inc/class-eventbrite-api.php' );
}
add_action( 'keyring_load_services', 'eventbrite_api_load_keyring_service', 11 );

/**
 * Load classes.
 */
function eventbrite_api_init() {
	// Load Eventbrite_Requirements.
	require_once( 'inc/class-eventbrite-requirements.php' );

	// No point loading unless we have an active Eventbrite connection.
	if ( Eventbrite_Requirements::has_active_connection() ) {
		require_once( 'inc/class-eventbrite-manager.php' );
		require_once( 'inc/class-eventbrite-query.php' );
		require_once( 'inc/class-eventbrite-templates.php' );
		require_once( 'inc/class-eventbrite-event.php' );
		require_once( 'inc/functions.php' );


	}
}
add_action( 'init', 'eventbrite_api_init' );







function generateCalendar($theTime, $events, $numm) {

 //This gets today's date 
 $date =$theTime ; 
 //This puts the day, month, and year in seperate variables 
 $day = date('d', $date) ; 
 $month = date('m', $date) ; 
 $year = date('Y', $date) ;
 //Here we generate the first day of the month 
 $first_day = mktime(0,0,0,$month, 1, $year) ; 
 //This gets us the month name 
 $title = date('F', $first_day) ;
 //Here we find out what day of the week the first day of the month falls on 
 $day_of_week = date('D', $first_day) ; 
 //Once we know what day of the week it falls on, we know how many blank days occure before it. If the first day of the week is a Sunday then it would be zero
 switch($day_of_week){ 
 case "Sun": $blank = 0; break; 
 case "Mon": $blank = 1; break; 
 case "Tue": $blank = 2; break; 
 case "Wed": $blank = 3; break; 
 case "Thu": $blank = 4; break; 
 case "Fri": $blank = 5; break; 
 case "Sat": $blank = 6; break; 
 }

echo '<script type="text/javascript">';
echo 'events = [];';
for ($ii = 0; $ii<count($events->posts); $ii++) {
		
		
		echo 'events[' . $ii .'] = ' . json_encode($events->posts[$ii]) . ";";
	}
echo '</script>';



 //We then determine how many days are in the current month
 $days_in_month = cal_days_in_month(0, $month, $year) ; 
 //Here we start building the table heads 
 echo "<table border=1 width=294 id='calendar' class='theCal' num='" . $numm . "' style='z-index: " . (6-$numm) . ";'>";
 echo "<tr class='month'><th colspan=7> <h1> $title $year </h1> </th></tr>";
 echo "<tr><td width=42>S</td><td width=42>M</td><td 
width=42>T</td><td width=42>W</td><td width=42>T</td><td 
width=42>F</td><td width=42>S</td></tr>";
 //This counts the days in the week, up to 7
 $day_count = 1;
 echo "<tr class='date'>";
 //first we take care of those blank days
 while ( $blank > 0 ) 
 { 
 echo "<td></td>"; 
 $blank = $blank-1; 
 $day_count++;
 } 
 //sets the first day of the month to 1 
 $day_num = 1;
 //count up the days, untill we've done all of them in the month
 while ( $day_num <= $days_in_month ) 
 { 
 echo "<td><div class='dayNum'>" . $day_num . '</div>';
	for ($ii = 0; $ii<count($events->posts); $ii++) {
		
		$theDate = explode("T",$events->posts[$ii]->start->utc );
		$theTime = str_split($theDate[1],5)[0];
		$theTime  = date("g:i a", strtotime($theTime));
		if ($day_num<10) {
		if ($theDate[0] == $year . "-" . $month . "-0" . $day_num) {
		echo '<div class="event" eventNum="' . $ii . '"><a href="' . $events->posts[$ii]->url  . '"><h2>' . $events->posts[$ii]->post_title . "</h2></a><h4>" .  $theTime . "</h4><span>See more details</span></div>";
		}
		} else {
		if ($theDate[0] == $year . "-" . $month . "-" . $day_num) {
		echo '<div class="event" eventNum="' . $ii . '"><a href="' . $events->posts[$ii]->url  . '"><h2>' . $events->posts[$ii]->post_title . "</h2></a><h4>" .  $theTime . "</h4><span>See more details</span></div>";
		}

		}	
		
		
				
	}
 echo "</td>"; 
 $day_num++; 
 $day_count++;
 //Make sure we start a new row every week
 if ($day_count > 7)
 {
 echo "</tr><tr class='date'>";
 $day_count = 1;
 }
 } 
 //Finaly we finish out the table with some blank details if needed
 while ( $day_count >1 && $day_count <=7 ) 
 { 
 echo "<td> </td>"; 
 $day_count++; 
 } 
 
 echo "</tr></table>"; 
    
echo "<script src='" . site_url() . "/wp-content/plugins/eventbrite-api/inc/calendar.js'>";


}