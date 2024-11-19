jQuery(document).ready(function($) {
    //console.log();
    data_record=$('.record').find('input').val();
    if(data_record.length>0){
        //initButton();
        initToast();
        initFields();
    }

    function initFields(){
        $.ajax({
            url: ajax_params.ajax_url,
            type: 'POST',
            data: {
                action: 'load_record', 
                record: data_record
            },        
            success: function(response) {
                //console.log(response);

                const content = JSON.parse(response.data[0].content).reduce((acc, curr) => {
                    acc[curr.name] = curr.value;
                    return acc;
                  }, {});
                const parcel = JSON.parse(response.data[0].parcel);
                $('#wpforms-139-field_1').val(content["Tipo de producto"]);
                $('#wpforms-139-field_4').val(content["Tipo de oferta"]);
                $('#wpforms-139-field_6').val(content["Entidad Federativa"]);

                parcel.forEach((group)=>{
                    checkWithValue(group);
                });
                
            },
            error: function() {
            }
        });    
    }

    function initButton(){
        btnSb=$('.wpforms-submit')
        btnSb.hide();
        //$btn=$('<button class="updater" data-alt-text="Enviando..." data-submit-text="Actualizar" aria-live="assertive" style="border-radius: 100px;background-color: #9F2241;color: #fff;border: 1px solid #9F2241;font-size: 15px;padding: 10px 20px;">Actualizar</button>');
        $btn=$('<button class="b-updater" data-alt-text="Enviando..." data-submit-text="Actualizar" aria-live="assertive" style="border-radius: 100px;background-color: #9F2241;color: #fff;border: 1px solid #9F2241;font-size: 15px;padding: 10px 20px;">Actualizar</button>');
        $btn.appendTo(btnSb.parent());
        $('.b-updater').click(b_updater);
    }

    function initToast(){
        body=$("body")


        toast='<div id="alerta-toast" class="toast" style="position: fixed; bottom: 20px; right: 20px; display: none;">';
        toast+='<div class="toast-body">Mensaje de exito</div></div>';

        $tst=$(toast);
        $tst.appendTo(body);
        
    }    
    function b_updater(e){
        //console.log(e);
        e.preventDefault();
        values=getWPFormsFieldValues('139');

        record=$('.record').find('input').val();
        if(record.length>0){
            //values.push({name:'record',value:`${record}`})
            //console.log(JSON.stringify(values));
            //console.log(JSON.parse(JSON.stringify(values)));

            $.ajax({
                url: ajax_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'update_record', 
                    values: JSON.parse(JSON.stringify(values))                    
                },        
                success: function(response) {
                    console.log(response);
                    mostrarToast('¡La actividad fue completada con éxito!', 'success');
                },
                error: function(response) {
                    console.log("ERROR:::",response);
                }
            }); 
        }

        
    }

    function checkWithValue(val) {
        $(":checkbox").filter(function() {
            return this.value == val;
        }).prop("checked", "true");
    }

    function getWPFormsFieldValues(formId) {
        let fieldValues = [];
        let form = $(`#wpforms-form-${formId}`);
        let checkval = [];
        // Obtener todos los campos del formulario
        form.find('[name^="wpforms[fields]"]').each(function() {
            //console.log(this);
            let field = $(this);
            //let fieldName = field.attr('name').match(/\[fields\]\[(\d+)\]/)[1];
            let match = field.attr('name').match(/\[fields\]\[(\d+)\]/);
            //console.log(match);
            if(match){
                let value = field.val();
                let fieldName = match[1];

                //console.log(fieldName);
                //var fieldIndex = parseInt(fieldName, 10);
                //if(fieldIndex)
                //console.log(fieldIndex);
                //let fieldLabel = $(`#wpforms-${formId}-field_${fieldName}-label`).text() || `Campo ${fieldName}`;

                // Encuentra el label asociado usando el atributo 'for'
                let label = $('label[for="wpforms-'+formId+'-field_'+fieldName+'"]');

                // Obtener el texto del label
                let fieldLabel = label.text();
                
                // Manejar diferentes tipos de campos
                if (field.is(':checkbox') || field.is(':radio')) {                    
                    if (field.is(':checked')) {
                        checkval.push(`${value}`);
                    }
                } else if (field.is('select')) {
                    let selectedText = field.find('option:selected').text();
                    if (value) {
                        fieldValues.push({"name":`${fieldLabel}`,"value":`${selectedText}`});
                    }
                } else {
                    if (value) {
                        fieldValues.push({"name":`${fieldLabel}`,"value":`${value}`});
                    }
                }
            }    
        });
        if(checkval.length>0){fieldValues.push({"name":"places","value":`${checkval}`});}
        
        return fieldValues;
    }


    function mostrarToast(mensaje, tipo) {
        var $toast = $('#alerta-toast');
    
        $toast.removeClass('toast-success toast-danger'); // Quita clases anteriores
        if (tipo === 'success') {
            $toast.addClass('toast-success');
        } else if (tipo === 'error') {
            $toast.addClass('toast-danger');
        }
    
        // Cambiar el contenido del mensaje
        $toast.find('.toast-body').html(mensaje);
    
        // Mostrar el toast
        $toast.fadeIn();
    
        // Ocultar el toast después de 3 segundos
        setTimeout(function() {
            $toast.fadeOut();
        }, 3000);
    }
});
