var myDropzone;
var dz;

Dropzone.autoDiscover = false;

document.addEventListener('DOMContentLoaded', function() {

    /*var previewNode = document.querySelector("#dropzone-preview-template");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);*/

    myDropzone = new Dropzone("#dropzone-139-10", {
        url: "#",
        autoProcessQueue: false,
        maxFilesize: 10, // 10 MB
        acceptedFiles: ".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.ppt,.pptx,.mp4",
        addRemoveLinks: true,
        dictDefaultMessage: "Arrastra archivos aquí o haz clic para seleccionar",
        //previewTemplate: previewTemplate,
        previewsContainer: "#previews",
        thumbnailWidth: 120,
        thumbnailHeight: 120,
        init: function() {
            dz = this;
            var form = document.getElementById('wpforms-form-139');            
            var fileInput = document.getElementById('wpforms-139-field_10');


            //Los cambios principales que he realizado son:
            //updateFileInput en una función asíncrona que devuelve una promesa
            //procesamiento en paralelo de los archivos usando Promise.all
            //Mejoré el manejo de errores y la verificación de archivos nulos
            //Modifiqué el evento submit para esperar a que se complete el procesamiento de archivos
            //Agregué verificaciones adicionales y logging para depuración

            // Función para convertir URL a File
            async function urlToFile(url, filename, mimeType) {
                try {
                    const response = await fetch(url);
                    const blob = await response.blob();
                    return new File([blob], filename, { type: mimeType });
                } catch (error) {
                    console.error('Error converting URL to File:', error);
                    return null;
                }
            }

            // Función para crear un archivo temporal desde una URL
            async function createTempFileFromUrl(fileData) {
                const tempFile = await urlToFile(fileData.url, fileData.name, fileData.type);
                if (tempFile) {
                    tempFile.serverID = fileData.id;
                    tempFile.url = fileData.url;
                    tempFile.thumbnail = fileData.thumbnail;
                    return tempFile;
                }
                return null;
            }

            // Función asíncrona para actualizar el input de archivos
            async function updateFileInput(files) {
                const dataTransfer = new DataTransfer();
                
                // Procesar todos los archivos de manera asíncrona
                const processedFiles = await Promise.all(files.map(async (file) => {
                    if (file.url) {
                        return await createTempFileFromUrl(file);
                    } else if (file instanceof File) {
                        return file;
                    }
                    return null;
                }));

                // Filtrar archivos nulos y agregarlos al DataTransfer
                processedFiles
                    .filter(file => file !== null)
                    .forEach(file => dataTransfer.items.add(file));

                fileInput.files = dataTransfer.files;
                return dataTransfer.files;
            }

            form.addEventListener('submit', async function(e) {

                
                try {
                    e.preventDefault();
                    e.stopPropagation();

                    // Esperar a que se procesen todos los archivos
                    await updateFileInput(dz.files);

                    // Verificar si hay archivos para enviar
                    if (fileInput.files.length > 0) {
                        //console.log('Archivos a enviar:', fileInput.files);
                        
                        // Crear input para archivos existentes
                        /*const existingFiles = dz.files
                            .filter(file => file.serverID)
                            .map(file => ({
                                id: file.serverID,
                                name: file.name,
                                url: file.url,
                                thumbnail: file.thumbnail
                            }));

                        if (existingFiles.length > 0) {
                            const container = document.getElementById('wpforms-139-field_10-container');
                            let existingFilesInput = document.getElementById('wpforms-139-field_10_existing');
                            
                            if (!existingFilesInput) {
                                existingFilesInput = document.createElement('input');
                                existingFilesInput.type = 'hidden';
                                existingFilesInput.name = 'wpforms[fields][10_existing]';
                                existingFilesInput.id = 'wpforms-139-field_10_existing';
                                container.appendChild(existingFilesInput);
                            }
                            
                            existingFilesInput.value = JSON.stringify(existingFiles);
                        }
                        */
                        // Proceder con el envío del formulario
                        //form.submit();
                    } else {
                        console.warn('No hay archivos para enviar');
                        // Decidir si continuar con el envío del formulario sin archivos
                        //form.submit();
                    }
                } catch (error) {
                    console.error('Error al procesar los archivos:', error);
                }                
               

            });

            this.on("addedfile", async function(file) {
                //console.log("addedfile");
                
                if (file.type.startsWith('video/')) {
                    file.previewElement.querySelector(".dz-image img").remove();

                    var video = document.createElement('video');
                    video.style.width = '120px';
                    video.style.height = '120px';
                    video.setAttribute('controls', 'controls');

                    if (file.url) {
                        var source = document.createElement('source');
                        source.src = file.url;
                        source.type = file.type;
                        video.appendChild(source);
                        file.previewElement.querySelector(".dz-image").appendChild(video);
                        
                        // Si ya tiene thumbnail guardado, usarlo
                        if (file.thumbnail) {
                            var img = new Image();
                            img.src = file.thumbnail;
                            file.previewElement.querySelector(".dz-image").appendChild(img);
                        }
                            
                    } 
                    // Si es un archivo nuevo
                    else if (file instanceof File) {
                        var source = document.createElement('source');
                        source.src = URL.createObjectURL(file);
                        source.type = file.type;
                        video.appendChild(source);
                        file.previewElement.querySelector(".dz-image").appendChild(video);

                        generateVideoThumbnail(file, function(thumbnail) {
                            file.thumbnail = thumbnail;
                            updateThumbnailsField();
                        });
                    }                    
                }

                await updateFileInput(dz.files);

            });

            this.on("removedfile", async function(file) {
                //console.log('removedfile');                
                
                if (file.type.startsWith('video/')){
                    updateThumbnailsField();
                }
                await updateFileInput(dz.files);
            });

            this.on("sendingmultiple", function(files, xhr, formData) {
                //console.log('sendingmultiple');
                formData.append('nonce', ajax_params.nonce); 
                // Añade los campos del formulario a formData
                Array.from(form.elements).forEach(function(field) {
                    formData.append(field.name, field.value);
                });
            });

            this.on("successmultiple", function(files, response) {
                console.log('successmultiple');
                form.submit();
            });

            this.on("errormultiple", function(files, response) {
                console.error("Error al subir archivos:", response);
            });

            // Función para convertir URL a File
            /*async function urlToFile(url, filename, mimeType) {
                try {
                    const response = await fetch(url);
                    const blob = await response.blob();
                    return new File([blob], filename, { type: mimeType });
                } catch (error) {
                    console.error('Error converting URL to File:', error);
                    return null;
                }
            }

            // Función para crear un archivo temporal desde una URL
            async function createTempFileFromUrl(fileData) {
                const tempFile = await urlToFile(fileData.url, fileData.name, fileData.type);
                if (tempFile) {
                    tempFile.serverID = fileData.id;
                    tempFile.url = fileData.url;
                    tempFile.thumbnail = fileData.thumbnail;
                    return tempFile;
                }
                return null;
            }*/

            function generateVideoThumbnail(file, callback) {
                console.log("generateVideoThumbnail");

                if (!(file instanceof File)) return;                
                var reader = new FileReader();
                reader.onload = function(e) {
                    var video = document.createElement('video');
                    video.preload = 'metadata';
                    video.onloadedmetadata = function() {
                        window.URL.revokeObjectURL(video.src);
                        var canvas = document.createElement('canvas');
                        canvas.width = video.videoWidth;
                        canvas.height = video.videoHeight;
                        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                        var thumbnail = canvas.toDataURL();
                        callback(thumbnail);
                    };
                    video.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }

            function updateThumbnailsField() {
                var thumbnailsData = dz.files.filter(function(file) {
                    return file.type.startsWith('video/') && file.thumbnail;
                }).map(function(file) {
                    return {
                        filename: file.name,
                        thumbnail: file.thumbnail
                    };
                });

                var thumbnailField = document.getElementById('wpforms-139-field_thumbnails');
                if (thumbnailField) {
                    thumbnailField.value = JSON.stringify(thumbnailsData);
                }
            }

        },
        // Personaliza la previsualización de imágenes
        transformFile: function(file, done) {
            if (file.type.startsWith('image/')) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = new Image();
                    img.onload = function() {
                        var canvas = document.createElement('canvas');
                        var ctx = canvas.getContext('2d');
                        canvas.width = 120;
                        canvas.height = 120;
                        ctx.drawImage(img, 0, 0, 120, 120);
                        var thumbnail = canvas.toDataURL(file.type);
                        done(thumbnail);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                done(file);
            }
        }
    });

    //document.getElementById('hide-139-10').style.display = 'none';

});

window.addEventListener('load', function() {
   const dropzone = document.getElementById('dropzone-container');

    if (dropzone) {
        const previousElement = dropzone.previousElementSibling;
        if (previousElement) {
            previousElement.style.display = 'none';
        }
    }
});

jQuery(document).ready(function($) {
    // Función para obtener el nonce de WordPress
    function getWPNonce() {
        return typeof wpApiSettings !== 'undefined' ? wpApiSettings.nonce : '';
    }    

    function loadExistingFiles() {
        let data_record_=$('.record').find('input').val();
        //console.log(getWPNonce());
        if(data_record_.length>0){
            $.ajax({
                url: ajax_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'get_existing_files', 
                    record: data_record_,
                    //nonce: getWPNonce()
                },        
                success: function(response) {
                    //console.log(response);
                    if (response.success) {
                        response.data.forEach(function(fileData) {
                            let mockFile = {
                                name: fileData.name,
                                size: fileData.size,
                                accepted: true,
                                status: Dropzone.ADDED,
                                serverID: fileData.id,
                                type: fileData.type,
                                url: fileData.url,           // Añadir la URL del archivo
                                thumbnail: fileData.thumbnail // Añadir el thumbnail si existe                                
                              };
                            //console.log("ajax::success::mockFile::",mockFile);
                            dz.files.push(mockFile);                              
                            dz.emit("addedfile", mockFile);
                            // Si es una imagen, crear la miniatura
                            if (fileData.type && fileData.type.startsWith('image/')) {
                                // Usar la URL directamente sin createObjectURL
                                dz.emit("thumbnail", mockFile, fileData.url);
                            }
                            dz.emit("complete", mockFile);
                            
                        });
                    }
                    
          
                    
                },
                error: function() {
                }
            });    
        }
    }
    loadExistingFiles();
});