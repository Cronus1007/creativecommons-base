<?php if ( is_active_sidebar( 'homepage-sidebar' ) ) : ?>
	<section class="homepage-sidebars">
		<div class="container mobile-no-padding">
			<?php dynamic_sidebar( 'homepage-sidebar' ); ?>
		</div>
	</section>
<?php endif; ?>
