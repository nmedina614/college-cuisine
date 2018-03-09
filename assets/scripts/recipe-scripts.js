$('.btn-ingredient-delete').click(function() {
    var listGroup = document.getElementsByClassName('ingredient-item');
    if(listGroup.length > 1) $(this).parent('.ingredient-item').remove();
    else $(this).parent('.ingredient-item').find('.input-ingredient').val('');
});