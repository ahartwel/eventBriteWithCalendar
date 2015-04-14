<?php
/**
 * Template Name: Eventbrite Events
 */

get_header(); ?>


<style>
#primary {
display: inline-block;

}
</style>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<header class="page-header">
				<h1 class="page-title">
					<!-- <?php the_title(); ?> -->
				</h1>
			</header><!-- .page-header -->


			<?php
				// Set up and call our Eventbrite query. eventbrite_get_events( $params, $force ) Eventbrite_Query
				$events = new Eventbrite_Query( apply_filters( 'eventbrite_query_args', array(
					// 'display_private' => false, // boolean
					'limit' => 200,            // integer
					'organizer_id' => 8032556033,     // integer
					 'p' => false,                // integer
					// 'post__not_in' => null,     // array of integers
					// 'venue_id' => null,         // integer
				) ) );

				//$events = eventbrite_get_events([ ], true);
				print_r($events);
				$theDate = explode("T",$events->posts[0]->start->utc );
				//echo $theDate[0];
				//echo "   " . count($events->posts);


				?>
			
<?php 
echo '<div id="eventDetails"><p id="closeDetails">X</p><p id="eventDetailsPar"></p></div>';
echo '<div class="arrow-left dim" id="leftIt"></div><div class="arrow-right" id="rightIt"></div>';
for ($i = 0; $i<6; $i++) {
generateCalendar(time() + ($i * 2678400), $events, $i); 
}
?>

				<?php
				echo "<div class='columner'>";
				//echo json_decode($events);
				if ( $events->have_posts() ) :
					while ( $events->have_posts() ) : $events->the_post(); ?>

						<article id="event-<?php the_ID(); ?>" <?php post_class(); ?>>
							<header class="entry-header">
								<?php the_post_thumbnail(); ?>

								<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

								<div class="entry-meta">
									<?php eventbrite_event_meta(); ?>
								</div><!-- .entry-meta -->
							</header><!-- .entry-header -->

							<div class="entry-content">
								<?php eventbrite_ticket_form_widget(); ?>
							</div><!-- .entry-content -->

							<footer class="entry-footer">
								<?php eventbrite_edit_post_link( __( 'Edit', 'eventbrite_api' ), '<span class="edit-link">', '</span>' ); ?>
							</footer><!-- .entry-footer -->
						</article><!-- #post-## -->

					<?php endwhile;

					// Previous/next post navigation.
					eventbrite_paging_nav( $events );

				else :
					// If no content, include the "No posts found" template.
					get_template_part( 'content', 'none' );

				endif;

				// Return $post to its rightful owner.
				wp_reset_postdata();

			echo "</div>";

			?>

		</main><!-- #main -->

	


	</div><!-- #primary -->
<script type="text/javascript" src="<?php bloginfo('wpurl'); ?>/wp-includes/js/calendarScript.js"></script>

<?php get_sidebar(); ?>
<?php get_footer(); ?>