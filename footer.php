<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package mattys
 */

?>

	<footer id="colophon" class="site-footer">
		<div class="site-info">
			
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->
<div class="modal fade" id="mattysprodmenumodal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
		<div id="mattysmainbody" class="modal-content">
			<div id="stickyclosewrap">
				<button type="button" class="btn-close btn-mattysclose" data-bs-dismiss="modal" aria-label="Close">
					<img src="<?php echo get_template_directory_uri().'/assets/images/close.svg'; ?>" alt="">
				</button>
			</div>
			<div id="mattysmodalbody" class="modal-body"></div>
		</div>
	</div>
</div>
<?php wp_footer(); ?>

</body>
</html>
