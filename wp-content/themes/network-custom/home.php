<?php
/*
Template Name: Home
*/
?>

<?php get_header(); ?>

 <?php
/**
 * Detect plugin
 */
if ( ! function_exists( 'is_plugin_active_for_network' ) )
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

?> 

			<div id="content">

				<div id="inner-content" class="wrap clearfix">

						<div id="main" class="twelvecol first clearfix" role="main">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

								<header class="article-header">

									<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>

								</header>

								<section class="entry-content clearfix" itemprop="articleBody">
									<?php the_content(); ?>
								</section>

								<footer class="article-footer">
									<?php the_tags( '<span class="tags">' . __( 'Tags:', 'bonestheme' ) . '</span> ', ', ', '' ); ?>

								</footer>

								<?php// comments_template(); ?>

								<section class="sites-list">
									<article itemscope class="site-entry">
										<header>
											<h3 class="site-title" itemprop="site-name"><a href="">Typewriter</a></h3>
											<h4 class="site-description" itemprop="site-description">Not much to say about this</h4>
										</header>
										<section>
											<h3 class="section-head" role="label">Recent Posts</h3>
											<ul class="posts-list" itemprop="posts">
												<li class="post-item" itemprop="post"><a href="" class="post-title">Title</a> - <span class="date" itemprop="publish-date">Date</span></li>
											</ul>
										</section>
										<footer class="post-meta">
											<ul class="tags-list tags entypo-tag" itemprop="networks">
												<li class="tag-item tag" itemprop="network">Technology</li>
												<li class="tag-item tag" itemprop="network">Social Justice</li>
												<li class="tag-item tag" itemprop="network">Environment</li>
												<li class="tag-item tag" itemprop="network">Recovery</li>
											</ul>
											<div itemprop="date-created" class="date entypo-calendar">Jan. 1, 2014</div>
										</footer>
									</article>
								</section>

								<?php
								// check for plugin using plugin name
								if ( is_plugin_active_for_network( 'networks/networks.php' ) && is_multisite() ) { ?>

								<?php wp_get_sites( 'offset=1'); 



								?>







								<?php } else {
									echo "<p>Multisite and the Networks plugin are required for this theme to work.</p>";
								} ?>

							</article>

							

							<?php endwhile; else : ?>

									<article id="post-not-found" class="hentry clearfix">
										<header class="article-header">
											<h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
										</header>
										<section class="entry-content">
											<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
										</section>
										<footer class="article-footer">
												<p><?php _e( 'This is the error message in the page.php template.', 'bonestheme' ); ?></p>
										</footer>
									</article>

							<?php endif; ?>

						</div>

						<?php// get_sidebar(); ?>

				</div>

			</div>

<?php get_footer(); ?>
