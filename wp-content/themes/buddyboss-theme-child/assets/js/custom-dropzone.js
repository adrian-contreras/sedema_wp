Dropzone.autoDiscover = false;

document.addEventListener('DOMContentLoaded', function() {

    /*var previewNode = document.querySelector("#dropzone-preview-template");
    previewNode.id = "";
    var previewTemplate = previewNode.parentNode.innerHTML;
    previewNode.parentNode.removeChild(previewNode);*/

    var myDropzone = new Dropzone("#dropzone-139-10", {
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
            var dz = this;
            var form = document.getElementById('wpforms-form-139');
            var fileInput = document.getElementById('wpforms-139-field_10');
            
            form.addEventListener('submit', function(e) {
                if (dz.getQueuedFiles().length > 0) {
                    e.preventDefault();
                    e.stopPropagation();
                    dz.processQueue();
                }
            });

            this.on("addedfile", function(file) {
                updateFileInput(dz.files);
                // Previsualización personalizada para videos
                //console.log('file.type',file.type);
                if (file.type.startsWith('video/')) {
                    file.previewElement.querySelector(".dz-image img").remove();

                    // Crear el elemento de video
                    var video = document.createElement('video');
                    video.style.width = '120px';
                    video.style.height = '120px';
                    video.setAttribute('controls', 'controls');

                    var source = document.createElement('source');
                    source.src = URL.createObjectURL(file);
                    source.type = file.type;
                    
                    // Añadir la fuente al video y el video a la previsualización
                    video.appendChild(source);
                    file.previewElement.querySelector(".dz-image").appendChild(video);

                    generateVideoThumbnail(file, function(thumbnail) {
                        file.thumbnail = thumbnail;
                        updateThumbnailsField();
                    });                    
                }

            });

            this.on("removedfile", function(file) {
                updateFileInput(dz.files);
                if (file.type.startsWith('video/')){
                    updateThumbnailsField();
                }
            });

            this.on("sendingmultiple", function(files, xhr, formData) {
                // Añade los campos del formulario a formData
                Array.from(form.elements).forEach(function(field) {
                    formData.append(field.name, field.value);
                });
            });

            this.on("successmultiple", function(files, response) {
                form.submit();
            });

            this.on("errormultiple", function(files, response) {
                console.error("Error al subir archivos:", response);
            });

            function updateFileInput(files) {
                var dataTransfer = new DataTransfer();
                files.forEach(function(file) {
                    dataTransfer.items.add(file);
                });
                fileInput.files = dataTransfer.files;
            }
            
            function generateVideoThumbnail(file, callback) {
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