$(document).ready(function () {

    $('#entityform-objectclass').select2({
        theme: 'bootstrap-5'
    });

    $('#entityform-objectclass').on('change', function (evt) {
        var activeObjectClasses = [];

        // Collect also sub objectclasses
        $.each($('#entityform-objectclass').select2('data'), function (index, obj) {
            activeObjectClasses = collectSupObjectClasses(obj.id);
        });
        activeObjectClasses = activeObjectClasses.filter(onlyUnique);

        // Only rebuild the form if all activeObjectClasses are selected in the select2 field
        // If not, change the value of the select 2 field, and trigger change. (This recalls this method)
        if (activeObjectClasses.length == $('#entityform-objectclass').val().length) {
            rebuildForm();
        } else {
            $('#entityform-objectclass').val(activeObjectClasses);
            $('#entityform-objectclass').trigger('change');
        }
    });


    $('#add-attribute-picker').select2({
        theme: 'bootstrap-5',
    });

    $('#add-attribute-picker').on('select2:select', function (evt) {
        var data = evt.params.data;
        var element = evt.params.data.element;
        var $element = $(element);
        $element.detach();

        $el = $('.attribute-row[data-attribute="' + data.id + '"');
        $el.detach().appendTo("#attribute-list").slideDown();

        $('#add-attribute-picker').val('');
        $('#add-attribute-picker').trigger('change');
    });


    $('#entityform-rdnattribute').select2({
        theme: 'bootstrap-5'
    });
    $('#add-attribute-picker').on('select2:select', function (evt) {
        var data = evt.params.data;
        var element = evt.params.data.element;
        var $element = $(element);
        $element.detach();
        $el = $('[data-attribute="' + data.id.toLowerCase() + '"');
        $el.detach().appendTo("#attribute-list").slideDown();

        $('#add-attribute-picker').val('');
        $('#add-attribute-picker').trigger('change');
    });

    rebuildForm();
});

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

$(".delete-binary-button").click(function () {
    $inputsRow = $(this).closest('.input-group');
    $inputsRow.find('.download-binary-button').hide();
    $inputsRow.find('.delete-binary-button').hide();

    $input = $inputsRow.find('input').first();
    $input.show();
    $input.prop("disabled", false);
});

function rebuildForm() {
    var activeObjectClasses = $('#entityform-objectclass').val();
    if (activeObjectClasses === null) {
        activeObjectClasses = [];
    }
    console.log(activeObjectClasses)

    var selectedRdnAttribute = $('#entityform-rdnattribute').val();
    $('#entityform-rdnattribute').find("option").remove();
    $('#entityform-rdnattribute').val(null).trigger('change');

    // Empty Attribute Picker List
    $('#add-attribute-picker').val(null).trigger('change');

    $('.attribute-row').hide();
    $(".attribute-row[data-attribute='objectclass']").show();


    $.each(activeObjectClasses, function (index, objectClass) {
        $.each(ldapSchema.objectClasses[objectClass].must, function (index, attributeId) {
            var $el = $(".attribute-row[data-attribute='" + attributeId + "']");
            $el.show();
            addAttributeToDnPicker($el.data('attribute'), $el.data('attribute-label'), selectedRdnAttribute)
        });
        $.each(ldapSchema.objectClasses[objectClass].may, function (index, attributeId) {
            var $el = $(".attribute-row[data-attribute='" + attributeId + "']");
            if ($el.find('input').val() || $el.find('a.download-binary-button').length) {
                // May Attribute is not empty
                $el.show();
            } else {
                // Add Attribute to picker
                addAttributeToAddPicker($el.data('attribute'), $el.data('attribute-label'));
            }
        });
    });

}

function onlyUnique(value, index, self) {
    return self.indexOf(value) === index;
}

function collectSupObjectClasses(objectClass) {
    res = [objectClass.toLowerCase()];
    $.each(ldapSchema.objectClasses[objectClass.toLowerCase()].sups, function (index, subObjectClass) {
        res.push(subObjectClass.toLowerCase());
        res = res.concat(collectSupObjectClasses(subObjectClass))
    });
    return res;
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

$('#setPassButton').on('click', function () {
    var newPassword = $('#new-password-input').val();
    sha256(newPassword).then(function (result) {
        $('#setPassButton').data('target-input').val(result);
    })

    $('#staticSetPassword').modal('hide');
});

var setPasswordModal = document.getElementById('staticSetPassword')
setPasswordModal.addEventListener('show.bs.modal', function (event) {
    // Button that triggered the modal
    var button = event.relatedTarget
    $('#setPassButton').data('target-input', $(button).prev());
})

async function sha256(m) {
    const hash = await crypto.subtle.digest("SHA-256", (new TextEncoder()).encode(m))
    return "{sha256}" + btoa(String.fromCharCode(...new Uint8Array(hash)))
}
