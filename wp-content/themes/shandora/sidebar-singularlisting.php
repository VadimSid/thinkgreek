<?php if ( is_active_sidebar( 'singularlisting' ) ) : ?>

	<aside id="sidebar-singularlisting" class="sidebar <?php echo shandora_column_class('large-4'); ?>">

		<?php dynamic_sidebar( 'singularlisting' ); ?>

	</aside><!-- #sidebar-primary .aside -->

<?php endif; ?>