$('.btn-ingredient-delete').click(function() {
    var parent = this.parentElement.parentElement;
    $(parent).remove();
});

