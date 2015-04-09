var $ = jQuery;
$.fn.equalCols = function(){
  return this.outerHeight( Math.max.apply(this, jQuery(this).map(function(i,e){ return jQuery(e).height('auto').outerHeight() }).get() ) )
};

$(function() {

 init();

});

function init(){
	$('.print-link').click(function(e){
		e.preventDefault();
		 window.print();
	});
	resizeElements();
    $(".date, .date_arrivee, .date_depart").datepicker( { dateFormat: "dd/mm/yy" } );
    $( ".errors-list" ).dialog({
        title: "Erreurs :",
        appendTo: ".main-content",
        closeOnEscape: true,
        position: { my: "left top", at: "left bottom", of: '.main-content' }
    });
    $( ".success-list" ).dialog({
    	title: "Validations :",
    	appendTo: ".main-content",
    	closeOnEscape: true,
    	position: { my: "left top", at: "left bottom", of: '.main-content' }
    });
    $( ".keywords" ).autocomplete({
      source: [ "Paris", "Marseille", "Lokisalle", "Merzbow" ]
    });
    $( "select" ).selectmenu();
    $('#cart-review-table').footable();

    $( ".sub-menu-buton" ).hide();
    $( ".sub-menu-buton" ).menu({
         position: {  my: "left top", at: "left bottom", of: '.sub-menu' },
     });
    $('.sub-menu > a').click(function(e){
        e.preventDefault();
        $('.sub-menu').bind('hover');
    });
    $( ".sub-menu " ).hover(
        function() {
            $( ".sub-menu-buton" ).fadeToggle( "fast", "linear" );
          }, function() {
            $( ".sub-menu-buton" ).fadeToggle( "fast", "linear" );
  });


}

function resizeElements(){
	$(".offer .image").equalCols();
    $(".products-grid .article").equalCols();
    $(".account .field").equalCols();
	$(".promo-wrapper .field").equalCols();
}