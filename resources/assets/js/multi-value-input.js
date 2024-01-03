$(".add-input").click(function () {
    $lastInput = $(this).siblings('div.inputRow').last();
    currentInputCount = $(this).siblings('div.inputRow').length;

    $newInput = $lastInput.clone();
    $newInput.find('input').attr('value', '');
    $newInput.find('input').attr('name', $(this).data('input-name').replace('replace-with-id', currentInputCount));

    // In case of file input: do not show download/delete button
    $newInput.find('.download-binary-button').hide();
    $newInput.find('.delete-binary-button').hide();
    $newInput.find('input').show();
    $newInput.find('input').prop("disabled", false);

    $newInput.insertAfter($lastInput);
});
