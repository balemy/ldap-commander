$(document).ready(function () {
    $('#entityform-objectclass').select2({
        theme: 'bootstrap-5'
    });
    $('#entityform-objectclass').on('change', function (evt) {
        console.log("changexxx");
        rebuildForm();
    });

    $('#entityform-rdnattribute').select2({
        theme: 'bootstrap-5'
    });
    $('#add-attribute-picker').select2({
        theme: 'bootstrap-5',
    });
    $('#add-attribute-picker').on('select2:select', function (evt) {
        var data = evt.params.data;
        var element = evt.params.data.element;
        var $element = $(element);
        $element.detach();
        $el = $('[data-attribute="' + data.id.toLowerCase() + '"');
        $el.detach().appendTo("#attributeList").slideDown();

        $('#add-attribute-picker').val('');
        $('#add-attribute-picker').trigger('change');
    });

    rebuildForm();
});

$(".add-input").click(function () {
    $inputsRow = $(this).closest('.attribute-row').find('.attribute-row-inputs');
    currentInputCount = $inputsRow.find('input').length;

    $input = $(this).closest('.attribute-row').find('.attribute-row-inputs').find('input').first().clone();
    $input.attr("name", $(this).data('input-name').replace('replace-with-id', currentInputCount));
    $input.attr("id", $(this).data('input-id').replace('replace-with-id', currentInputCount));
    $input.attr("value", '');

    $inputsRow.append($input).slideDown();
});


function rebuildForm() {
    var selectedRdnAttribute = $('#entityform-rdnattribute').val();
    $('#entityform-rdnattribute').find("option").remove();
    $('#entityform-rdnattribute').val(null).trigger('change');

    // Empty Attribute Picker List
    $('#add-attribute-picker').val(null).trigger('change');

    $('.attribute-row').hide();
    $("[data-attribute='objectclass']").show();

    // Get current object classes
    var activeObjectClasses = [];
    $.each($('#entityform-objectclass').select2('data'), function (index, obj) {
        activeObjectClasses.push(obj.id)
        $.each(ldapSchema.objectClasses[obj.id].sups, function (index, subObjectClassName) {
            activeObjectClasses.push(subObjectClassName.toLowerCase());
        });
    });

    $.each(activeObjectClasses, function (index, objectClass) {
        $.each(ldapSchema.objectClasses[objectClass].must, function (index, attributeLabel) {
            $el = $("[data-attribute='" + attributeLabel.toLowerCase() + "']");
            $el.show();
            addAttributeToDnPicker($el.data('attribute'), $el.data('attribute-label'), selectedRdnAttribute)
        });
        $.each(ldapSchema.objectClasses[objectClass].may, function (index, attributeLabel) {
            var attributeName = attributeLabel.toLowerCase();
            var $attribute = $("[data-attribute='" + attributeName + "']");
            if ($attribute.find('input').val()) {
                $attribute.show();
            } else {
                addAttributeToAddPicker($attribute.data('attribute'), $attribute.data('attribute-label'));
            }
        });
    });
}

function addAttributeToAddPicker(id, label) {
    $picker = $('#add-attribute-picker');

    if (!$picker.find("option[value='" + id + "']").length) {
        var newOption = new Option(label, id, false, false);
        $picker.append(newOption);
        $picker.val('');
        $picker.trigger('change');
    }
}

function addAttributeToDnPicker(id, label, current) {
    var isSelected = (id === current);
    $picker = $('#entityform-rdnattribute');

    if (!$picker.find("option[value='" + id + "']").length) {
        var newOption = new Option(label, id, false, isSelected);
        $picker.append(newOption).trigger('change');
    }
}
