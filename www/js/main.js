$(function(){
  // confirmation
  $('[data-confirm]').click(function(){
    var el = $(this);
    var text = el.attr('data-confirm');
    return confirm(text);
  });

  // bootstrap select
  $('select').selectpicker();
});
