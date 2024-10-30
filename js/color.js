(function( $ ) {
 
    // Add Color Picker to all inputs that have 'color-field' class
    $(function() {
        $('.pbackground').wpColorPicker();
        $('.ptext').wpColorPicker();
        $('.bbackground').wpColorPicker();
        $('.btext').wpColorPicker();
    });
     
})( jQuery );