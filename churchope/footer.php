		</div>
		
		<footer>
	    <?php if (get_option(SHORTNAME."_footer_widgets_enable") != '') {  
	switch ( get_option(SHORTNAME."_footer_widgets_columns") ) {
		case '1' : {
			$column_class = 'grid_12';
			break;
			}	
		case '2' : {
			$column_class = 'grid_6';
			break;
			}	
		case '3' : {
			$column_class = 'grid_4';
			break;
			}	
		case '4' : {
			$column_class = 'grid_3';
			break;
			}		
	}
 
 ?>
<section id="footer_widgets" class="clearfix row">


<?php
$i = 1;
while ($i <= (int)get_option(SHORTNAME."_footer_widgets_columns")) { ?>
    <aside class="<?php echo $column_class ?>">
   	<?php dynamic_sidebar("footer-".$i)  ?>
    </aside>
   
    
    <?php
	$i++;
	 } ?>
	<div class="grid_12 dotted"></div>  
</section> 
  <?php } ?>        
        
        
        
        <div class="row" id="copyright">
   			 <div class="grid_5"><p><?php  echo wpml_t('churchope', 'copyright', stripslashes(get_option(SHORTNAME."_copyright"))); ?></p></div>			 
		 <?php wp_nav_menu( array( 'theme_location' => 'footer-menu', 'container_class' => 'grid_7 clearfix',  'fallback_cb' => '','container'  => 'nav' ) ); ?>
		 </div>
		<?php  wp_footer(); ?>
		<?php  echo stripslashes(get_option(SHORTNAME."_GA")); ?>
        </footer>
	</body>
</html>