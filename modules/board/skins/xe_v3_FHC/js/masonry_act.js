// <![CDATA[
(function($){
jQuery(function($){

    var $container = $('.FHCGallery');
  
    $container.imagesLoaded( function(){
      $container.masonry({
        itemSelector : '.box'
      });
    });
  
});
})(jQuery);
// ]]>