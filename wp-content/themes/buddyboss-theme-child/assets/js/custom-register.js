jQuery(document).ready(function($) {
    var form = $('#signup-form');
    var fieldsOrder = [        
        'Nombre de la empresa',
        'Sector empresarial',
        'Breve descripción de la empresa',
        'Tipo de productos que podría ofrecer en venta, donación o préstamo',
        'Experiencias previas en economía circular e intercambio de bienes',
        'Nombre completo del representante de la empresa que realiza el registro',
        'Apellidos',
        'Alias',
        'signup_email',
        'signup_email_confirm',
        'signup_password',
        'signup_password_confirm',
        'register-privacy-info',
        'checkbox-options',
        'submit'
    ];
    
    fieldsOrder.forEach(function(fieldName) {
        var field;
        if (['signup_email', 'signup_email_confirm', 'signup_password', 'signup_password_confirm','checkbox-options','submit'].includes(fieldName)) {
            field = form.find('div.' + fieldName);
        } else if(fieldName=='register-privacy-info'){
            field = form.find('p.' + fieldName);
        } else {
            field = form.find('div.editfield:has(label:contains("' + fieldName + '"))');
        }
        if (field.length) {
            form.append(field);
        }
    });

    var $aliasField = $('#field_3');
    var $aliasUniqueField = $('#alias_is_unique');
    var $submitButton = form.find('input[type="submit"]');
    
    $aliasField.addClass('block-field');
    $aliasField.prop("readonly", true);

    function generateAlias() {
        console.log('custom-register::generateAlias::')        
        var firstName = $('#field_1').val(); // Asume que el campo de nombre es #field_1
        var lastName = $('#field_2').val();  // Asume que el campo de apellido es #field_2
        
        if (firstName && lastName) {
            var baseAlias = (firstName + lastName).toLowerCase().replace(/[^a-z0-9]/g, '');
            checkAliasUniqueness(baseAlias);
        }
    }

    function checkAliasUniqueness(alias) {
        //console.log('custom-register::checkAliasUniqueness::')
        $.ajax({
            url: registerAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'check_alias_uniqueness',
                alias: alias,
                nonce: registerAjax.nonce
            },
            success: function(response) {
                //console.log('custom-register::checkAliasUniqueness::response::',response)
                if (response.success) {
                    $aliasField.val(response.data.alias);
                    $aliasUniqueField.val('1');
                    $submitButton.prop('disabled', false);
                } else {
                    alert('No se pudo generar un alias único. Por favor, inténtalo de nuevo.');
                    $submitButton.prop('disabled', true);
                }
            }
        });
    }

    $('#field_1, #field_2').on('change', function() {
        //console.log('custom-register::generateAlias::')
        if ($aliasUniqueField.val() !== '1') {
            generateAlias();
        }else{
            if ($('#field_1').val() && $('#field_2').val()) {
                generateAlias();
            }
        }
    });

    form.on('submit', function(e) {
        if ($aliasUniqueField.val() !== '1') {
            e.preventDefault();
            alert('Por favor, espera mientras se genera un alias único.');
            generateAlias();
        }
    });
});