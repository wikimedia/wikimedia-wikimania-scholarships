(function( $, document, window ) {
  $(document).ready(function() {
    $('.langlist').each(function() {
      var $list = $(this),
        $select = $(document.createElement('select'))
          .addClass('langselect')
          .insertBefore($list.hide());
      $('>li a', this).each(function() {
        var $this = $(this);
        $(document.createElement('option'))
          .val(this.href)
          .html($this.html())
          .prop('selected',$this.attr('class')==='selected')
          .click(function() {
            window.location.href = $(this).val();
          })
          .appendTo($select);
      });
      $list.remove();
    });
    $('.langlabel').show();
  });
})( jQuery, document, window );
