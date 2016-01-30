<?php
   global $idx_plus_user;
   $data = self::get_global('results_listings');
   $i = 0;
?>

 <?php 
    $ul_class = "large-block-grid-3 small-block-grid-2";
    $layout = get_theme_mod('theme_layout');
    if($layout == '1c') {
      $ul_class= "large-block-grid-4 small-block-grid-2";
    }
 ?>
 <ul id="dsidx-listings" class="listings <?php echo $ul_class; ?>">

  <?php 
  if (!is_array($data)) { 
    echo $data;
  } 
  else {
    
    foreach ($data as $listing) {
      extract($listing); 

      $title = $Address . ', (MLS #'.$MlsNumber.')';

      $classes = array(
      'listing-' . $mls
      );                
      
      $status_string = '';
      $status_class = '';            
      if( isset( $ListingStatusID )) {
        switch ($ListingStatusID) {

          case '1':
            $status_string = 'Active';
            $status_class = 'for-sale';
            break;

          case '2':
            $status_string = 'Conditional';
            $status_class = 'for-rent';
            break;

          case '3':
            $status_string = 'Pending';
            $status_class = 'reduced';
            break;

          case '4':
            $status_string = 'Sold';
            $status_class = 'sold';
            break;

        }
      }
      ?>
      <li>

        <article>
          <header class="entry-header">
            <?php echo '<a href="' . $url . '" rel="nofollow" alt="'.$title.'">' . self::process_photo($photo, $listing, array('width'=>400, 'height' => 400)) . '</a>';?>
          </header>
          <div class="entry-summary">
            <h1 itemprop="name" class="entry-title"><?php echo '<a href="' . $url . '" rel="nofollow" alt="'.$title.'">' . $title . '</a>'; ?></h1>
            <div class="entry-meta">
              <div class="icon bed">
                <i class="sha-bed"></i>
                <span><?php echo $BedsTotal;?></span>
              </div>
              <div class="icon bath">
                <i class="sha-bath"></i>
                <span><?php echo $BathsTotal;?></span>
              </div>
              <div class="icon size">
                <i class="sha-ruler"></i>
                <span><?php echo $ImprovedSqFt;?></span>
              </div>
            </div>
          </div>

          <footer class="entry-footer">
            <div class="property-price">
                <span itemprop="price"><?php echo $price; ?></span>
            </div>
          </footer>

        </article>
      </li>

    <?php $i++; } ?>

  </ul>