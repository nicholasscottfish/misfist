	</div> <!-- #main.row -->

</div> <!-- #page.grid -->

<footer id="footer" role="contentinfo">

	<div id="footer-content" class="grid wfull">

		<div class="row">

			<p class="copyright c12">
				<span class="fl"><?php printf( __( 'Copyright &copy; %s %s. All Rights Reserved.', 'carton' ), date( 'Y' ), ' <a href="' . home_url() . '">' . get_bloginfo( 'name' ) .'</a>' ); ?></span>
				<span class="credit-link fr"><i class="icon-leaf"></i><?php printf( __( '%s created by %s.', 'carton' ), BAVOTASAN_THEME_NAME, '<a href="https://themes.bavotasan.com/2012/carton/">c.bavota</a>' ); ?></span>
			</p><!-- .c12 -->

		</div><!-- .row -->

	</div><!-- #footer-content.grid -->

</footer><!-- #footer -->

<?php wp_footer(); ?>
</body>
</html>