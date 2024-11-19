jQuery(document).ready(function($) {

    

    let dt = new DataTable('#user-offers-table', {
        ajax: {
            url: ajax_params.ajax_url,
            type: 'POST',
            data: function(d) {
                return {
                    action: 'load_user_offers',
                    id: ajax_params.id,
                    draw: d.draw,
                    start: d.start,
                    length: d.length,
                    search: d.search
                };
            },
            dataSrc: function(response) {
                if (!response.recordsTotal) response.recordsTotal = 0;
                if (!response.recordsFiltered) response.recordsFiltered = 0;
                if (!response.data) response.data = [];
                return response.data;
            }
        },
        processing: true,
        serverSide: true,
        pageLength: 10,
        dom: '<"top"Bfl>rt<"bottom"ip>', // Personalización del layout
        buttons: [
            {
                extend: 'collection',
                text: 'Exportar',
                buttons: [
                    {
                        extend: 'copy',
                        exportOptions: {
                            columns: [0, 1, 2],
                            customizeData: function(data) {
                                console.log(data);
                                return clearData(data);
                            }
                        }
                    },
                    {
                        extend: 'csv',
                        exportOptions: {
                            columns: [0, 1, 2],
                            /*customizeData: function(data) {
                                return clearData(data);
                            }*/
                        },
                        /*action: function(e, dt, button, config) {
                            // Crear una copia temporal de los datos
                            var data = dt.data().toArray();
            
                            // Modificar los datos para la exportación
                            var exportData = data.map(function(row) {
                                return {
                                    ...row,
                                    content: typeof row.content === 'object' ? row.content.content : row.content || ''
                                };
                            });
            
                            // Generar el CSV manualmente
                            var csvContent = "data:text/csv;charset=utf-8,";
                            csvContent += exportData.map(function(row) {
                                //console.log(row);
                                //return Object.values(row).join(",");
                                return row.index+','+convertToPlain(row.content)+','+convertToPlain(row.groups);
                            }).join("\n");
            
                            var encodedUri = encodeURI(csvContent);
                            var link = document.createElement("a");
                            link.setAttribute("href", encodedUri);
                            link.setAttribute("download", "export.csv");
                            document.body.appendChild(link);
                            link.click();
                            document.body.removeChild(link);
                        }*/
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: [0, 1, 2],
                            customizeData: function(data) {
                                return clearData(data);
                            }
                        }
                    },
                    {
                        extend: 'pdf',
                        exportOptions: {
                            columns: [0, 1, 2],
                            customizeData: function(data) {
                                return clearData(data);
                            }
                        }
                    },
                    {
                        extend: 'print',
                        exportOptions: {
                            columns: [0, 1, 2],
                            customizeData: function(data) {
                                return clearData(data);
                            }
                        }
                    }
                ]
            },
            {
                extend: 'colvis',
                text: 'Mostrar columnas'
            }
        ],
        columns: [
            { 
                data: 'index',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return data || '';
                }
            },
            { 
                data: 'content',
                orderable: false,
                render: {
                    /*_: function(data, type, row, meta) {
                        //if (typeof row === 'object') {
                            //return row.content || '';
                        //}
                            console.log('orthogonal:type',meta);
                        return data || '';
                        
                    },*/
                    display: function(data, type, row, meta) {
                        //console.log('display:type',type,'::meta::', meta);
                    //console.log("data::"+data,"type::"+type,"row::"+row,"meta::",meta);
                    //console.log("data::",data,"type::",type,"row::",row);
                    //console.log("data::"+data,"type::"+type);
                    //console.log(row);
                    /*
                    if (type === 'export') {
                        console.log('export');
                        // Para exportación, mostrar contenido completo
                        //return typeof data === 'object' ? 
                            //[data.title, data.description].filter(Boolean).join(' - ') : 
                            //data || '';
                        return typeof row === 'object' ? 
                            row.content : 
                            data || '';
                    }*/
                    //console.log('window.location.href',window.location.href);
                    
                    //if (type === 'display') {
                        //console.log('display',typeof data,typeof row, row);
                        if (typeof row === 'object') {
                            let description = row.content || '';
                            let shortContent = '';
                            
                            if (description) {
                                // Limitar la descripción a 100 caracteres
                                /*let shortDesc = description.length > 100 ? 
                                    description.substring(0, 100) + '...' : 
                                    description;
                                shortContent += shortDesc;*/

                                shortContent=recortarCadena(description, 2, ["<br>", "<br/>"])
                            }
                            
                            // Crear contenedor con el contenido completo oculto
                            return `
                                <div class="content-wrapper">
                                    <div class="short-content">${shortContent}</div>
                                    <div class="full-content" style="display:none">                            
                                        ${description}
                                    </div>
                                    <button class="toggle-content" style="border:none;background:none;color:blue;cursor:pointer">... (ver detalles)</button>
                                </div>
                            `;
                        }
                        return data || '';
                    //}
                    
                    //return data || '';
                    }
                }
            },
            { 
                data: 'groups',
                orderable: false,
                render: function(data) {
                    return data || '';
                }
            },
            { 
                data: 'reference',
                orderable: false,
                searchable: false,
                className: 'noexport' // Clase para excluir de exportación
            }
        ],
        language: {
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            infoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            infoFiltered: "(filtrado de un total de _MAX_ registros)",
            infoPostFix: "",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron resultados",
            emptyTable: "Ningún dato disponible en esta tabla",
            paginate: {
                first: "Primero",
                previous: "Anterior",
                next: "Siguiente",
                last: "Último"
            },
            buttons: {
                colvis: 'Mostrar columnas',
                copy: 'Copiar',
                csv: 'CSV',
                excel: 'Excel',
                pdf: 'PDF',
                print: 'Imprimir'
            }
        }
    });

    function convertToPlain(html){

        // Create a new div element
        let tempDivElement = document.createElement("div");
    
        // Set the HTML content with the given value
        tempDivElement.innerHTML = html;
    
        // Retrieve the text property of the element 
        return tempDivElement.textContent || tempDivElement.innerText || "";
    }

    // Manejador para expandir/contraer contenido
    $('#user-offers-table').on('click', '.toggle-content', function(e) {
        e.preventDefault();
        const wrapper = $(this).closest('.content-wrapper');
        const shortContent = wrapper.find('.short-content');
        const fullContent = wrapper.find('.full-content');
        
        if (fullContent.is(':visible')) {
            fullContent.hide();
            shortContent.show();
            $(this).text('... (ver detalles)');
        } else {
            shortContent.hide();
            fullContent.show();
            $(this).text('▲ (ocultar detalles)');
        }
    });

    // Manejador para el botón de actualizar
    dt.on('click', '.updater', function(e) {
        e.preventDefault();
        //console.log($(this));
        var recordId = $(this).data('record');
        //console.log(recordId)
        $.ajax({
            url: ajax_params.ajax_url,
            type: 'POST',
            data: {
                action: 'load_offer',
                record: recordId
            },
            success: function(response) {
                const result = JSON.parse(response.data[0]).reduce((acc, curr) => {
                    acc[curr.name] = curr.value;
                    return acc;
                    }, {});
                result.action = "fill";
                result.record = recordId;
                redirectByPost($(location).attr("origin")+'/offer/',result,false);
            },
            error: function() {
                alert('Error de conexión');
            }
        });
    });

    dt.on('click', '.remover', function(e) {
        e.preventDefault();
        
        var record = $(this).data('record');
        
        if ( confirm( BP_Confirm.are_you_sure ) ) {
            $.ajax({
                url: ajax_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'remove_record',
                    record: record
                },
                success: function(response) {
                    //console.log(response);
                    dt.ajax.reload(null, false);
                },
                error: function() {
                    alert('Error de conexión');
                }
            });           
            //return true;
       }/* else {

            return false;
       }*/

    });

    function clearData(data){
        //console.log(data);
        data.body.forEach(function(row) {
            // Asumiendo que content está en el índice 1
            //console.log(row);
            /*if (typeof row[1] === 'object') {
                row[1] = row[1].content || '';
            }*/
            if(row[1].length>0){
                //console.log(row[1]);
                row[1]=row[1].replace("... (ver detalles)",'')|| '';
                //console.log(row[1]);
            }
        });

        return data;
    }

    //$('.updater').click(updater);

/*
    function updater(e){
        data_record=e.currentTarget.getAttribute('data-record')

        $.ajax({
            url: ajax_params.ajax_url,
            type: 'POST',
            data: {
                action: 'load_offer', 
                record: data_record
            },        
            success: function(response) {
                //console.log(response.data[0]);
                //console.log(JSON.parse(response.data[0]));
                //let arrData=new array(response.data[0]);
                const result = JSON.parse(response.data[0]).reduce((acc, curr) => {
                    acc[curr.name] = curr.value;
                    return acc;
                  }, {});
                result.action = "fill";
                result.record = data_record;

                //console.log(result);
                redirectByPost($(location).attr("origin")+'/offer/',result,false);
            },
            error: function() {
            }
        });    
        //var info={"action":"fill","nombre":e.currentTarget.getAttribute('data-ix')};       
        //redirectByPost($(location).attr("origin")+'/offer/',info,false);        
    }*/


    function redirectByPost(url, parameters, inNewTab) {
        parameters = parameters || {};
        inNewTab = inNewTab === undefined ? true : inNewTab;
      
        var form = document.createElement("form");
        form.id = "reg-form";
        form.name = "reg-form";
        form.action = url;
        form.method = "post";
        form.enctype = "multipart/form-data";
      
        if (inNewTab) {
          form.target = "_blank";
        }
      
        Object.keys(parameters).forEach(function (key) {
          var input = document.createElement('input');
          input.type = "text";
          input.name = key;
          input.value = parameters[key];
          form.appendChild(input);
        });
      
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
      
        return false;
    }

    function recortarCadena(htmlString, maxOccurrences, delimiters) {
        // Usar una expresión regular para considerar múltiples delimitadores
        let regex = new RegExp(delimiters.join('|'), 'g');
        let parts = htmlString.split(regex);
        
        if (parts.length <= maxOccurrences) {
            return htmlString;
        }
    
        let result = parts.slice(0, maxOccurrences).join(delimiters[0]) + delimiters[0]; // Usa el primer delimitador para unir
    
        return result;
    }    

});


