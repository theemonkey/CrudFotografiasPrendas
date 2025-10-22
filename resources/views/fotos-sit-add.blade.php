@extends('layout/plantilla')

@section('tituloPagina', 'Agregar Foto a Orden SIT')

@section('contenido')

<div class="container mt-4">
    <h3 class="mb-4">Agregar fotos de la prenda</h3>

    <!-- Buscar Orden SIT -->
    <div class="mb-3">
        <label for="ordenSitInput" class="form-label">Buscar orden SIT</label>
        <div class="input-group">
            <input type="text"
                id="ordenSitInput"
                class="form-control"
                placeholder="Ej: 12345678"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            <button id="searchBoton" class="btn btn-primary"><i class="fas fa-search"></i></button>
        </div>
    </div>

    <!-- Resultado de búsqueda -->
    <div id="ordenSitCard" class="card p-3 mb-3" style="display:none;">
        <div class="row align-items-center">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <!-- Miniatura más grande -->
                    <img id="prendaPreview"
                        src="https://picsum.photos/id/535/400/600"
                        alt="Prenda"
                        class="img-thumbnail"
                        style="max-width:200px; min-width:150px; height:auto; cursor:pointer; object-fit:cover; border-radius:8px;"
                        onclick="openLightbox(this.src, tipoSeleccionado)">
                </div>
                <div class="col-md-8">
                    <p class="mb-1"><strong>Orden SIT:</strong> <span id="ordenSitValue"></span></p>
                    <p class="mb-1"><strong>Tipo:</strong> <span id="tipoOrden"></span></p>
                    <p class="mb-1"><strong>Descripción:</strong> <span id="descripcion"></span></p>

                <!-- Subir imágenes -->
                    <div class="mb-3">
                        <label class="text-muted mb-3">Subir Imágenes</label>
                        <div class="upload-buttons d-flex gap-2">
                            <div class="upload-btn" id="cameraUpload" title="Tomar foto con cámara">
                                <i class="fas fa-camera"></i>
                                <span>Cámara</span>
                                <input type="file" accept="image/*" capture="camera" style="display: none;" id="cameraInput">
                            </div>
                            <div class="upload-btn" id="fileUpload" title="Seleccionar archivos">
                                <i class="fas fa-folder"></i>
                                <span>Archivo</span>
                                <input type="file" accept="image/*" multiple style="display: none;" id="fileInput">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

<!-- Lightbox para visualizar imágenes -->
<div id="imageLightbox" class="lightbox-overlay" style="display: none;">
    <div class="lightbox-content">
        <div class="lightbox-header">
            <h5 id="lightboxTitle">Vista Previa de Imagen</h5>
            <button onclick="closeLightbox()" class="btn-close-lightbox">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="lightbox-body">
            <img id="lightboxImage" src="" alt="" class="lightbox-image">
            <div class="lightbox-info">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Descripción:</strong> <span id="previewDescripcion"></span>
                        <p id="lightboxDescription"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Tipo:</strong> <span id="previewTipo"></span>
                        <p id="lightboxType"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="lightbox-footer">
            <button onclick="closeLightbox()" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>
                Cerrar
            </button>
            <button onclick="downloadImage()" class="btn btn-primary">
                <i class="fas fa-download me-1"></i>
                Descargar
            </button>
        </div>
    </div>
</div>

    <!-- Botones de acción -->
    <div class="text-end">
        <button type="button" class="btn btn-secondary" onclick="cancelarOperacion()">Cancelar</button>
        <!--<button type="button" class="btn btn-primary" onclick="guardarFoto()">Guardar</button>-->
    </div>
</div>

<!-- Modal para ingresar datos(Descripcion - Tipo fotografia) de la imagen -->
<div class="modal fade" id="imageDataModal" tabindex="-1" aria-labelledby="imageDataModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="imageDataModalLabel">Detalles de la imagen</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <form id="imageDataForm">
          <div class="mb-3">
            <label for="descripcionInput" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="descripcionInput" placeholder="Ej: CAMISA BLANCA" required>
          </div>
          <div class="mb-3">
            <label for="tipoFotografiaSelect" class="form-label">Tipo de Fotografía</label>
            <select class="form-select" id="tipoFotografiaSelect" required>
              <option value="" disabled selected>Seleccione un tipo</option>
              <option value="Muestra">Muestra</option>
              <option value="Validación AC">Validación AC</option>
              <option value="Prenda Final">Prenda Final</option>
            </select>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="saveImageData">Guardar</button>
      </div>

    </div>
  </div>
</div>


<!-- ======= Toast Container para notificaciones Toast ======= -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11000;">
    <div id="notificationToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center">
                <i id="toastIcon" class="me-2"></i>
                <span id="toastMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Scripts utiles-->
<!-- ==========Busqueda orden sit - Botones card agregar fotos prenda  ============0-->
<script>
    let estadoSeleccionado = null;
    let tipoSeleccionado = null;
    let uploadedImages = []; // Array para almacenar imágenes subidas
    let currentImageData = null; // Para almacenar datos de la imagen actual

    const ordenSitInput = document.getElementById('ordenSitInput');
    const ordenSitCard = document.getElementById('ordenSitCard');
    const ordenSitValue = document.getElementById('ordenSitValue');
    const tipoOrden = document.getElementById('tipoOrden');
    const prendaPreview = document.getElementById('prendaPreview');
    const imageLightbox = document.getElementById('imageLightbox');
    const descripcion = document.getElementById('descripcion');

    // Validar y buscar (conectar con Backend)
    function buscarOrdenSit() {
        const ordenSitInput = document.getElementById('ordenSitInput');
        const value = ordenSitInput.value.trim();

        if (value === "") {
            showNotification("Ingrese un número de orden", "warning");
            return;
        }

        // 🎯 BUSCAR EN EL BACKEND
        $.ajax({
            url: '/api/fotografias',
            type: 'GET',
            data: {
                orden_sit: value
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    const fotografias = response.data.data || [];

                    if (fotografias.length > 0) {
                        // 🎯 ORDEN EXISTE - Mostrar datos existentes
                        const primeraFoto = fotografias[0];
                        mostrarOrdenExistente(primeraFoto, fotografias.length);
                    } else {
                        // 🎯 ORDEN NO EXISTE - Permitir crear nueva
                        mostrarOrdenNueva(value);
                    }
                } else {
                    console.error('Error en respuesta:', response.message);
                    mostrarOrdenNueva(value); // Fallback a nueva orden
                }
            },
            error: function(xhr, status, error) {
                console.error('Error buscando orden:', error);
                // 🎯 FALLBACK: Permitir crear nueva orden
                mostrarOrdenNueva(value);
            }
        });
    }

    // 🎯 NUEVA FUNCIÓN: Mostrar orden existente
    function mostrarOrdenExistente(primeraFoto, totalFotos) {
        const ordenSitValue = document.getElementById('ordenSitValue');
        const tipoOrden = document.getElementById('tipoOrden');
        const descripcion = document.getElementById('descripcion');
        const prendaPreview = document.getElementById('prendaPreview');
        const ordenSitCard = document.getElementById('ordenSitCard');

        // Llenar datos existentes
        ordenSitValue.textContent = primeraFoto.orden_sit;
        tipoOrden.textContent = `${totalFotos} foto(s) existente(s)`;
        tipoOrden.className = "badge bg-success";
        descripcion.textContent = `Última: ${primeraFoto.descripcion}`;

        // Mostrar imagen existente
        prendaPreview.src = primeraFoto.imagen_url;
        prendaPreview.onclick = () => openLightbox(
            primeraFoto.imagen_url,
            primeraFoto.descripcion,
            primeraFoto.tipo
        );

        ordenSitCard.style.display = 'block';

        showNotification(`Orden ${primeraFoto.orden_sit} encontrada con ${totalFotos} fotografía(s)`, 'success', 2000);
    }

    // 🎯 NUEVA FUNCIÓN: Mostrar orden nueva
    function mostrarOrdenNueva(numeroOrden) {
        const ordenSitValue = document.getElementById('ordenSitValue');
        const tipoOrden = document.getElementById('tipoOrden');
        const descripcion = document.getElementById('descripcion');
        const prendaPreview = document.getElementById('prendaPreview');
        const ordenSitCard = document.getElementById('ordenSitCard');

        // Configurar para nueva orden
        ordenSitValue.textContent = numeroOrden;
        tipoOrden.textContent = "Nueva orden";
        tipoOrden.className = "badge bg-primary";
        descripcion.textContent = "Agregue fotografías para esta orden";

        // Imagen por defecto
        prendaPreview.src = "https://picsum.photos/id/535/400/600";
        prendaPreview.onclick = () => openLightbox(
            "https://picsum.photos/id/535/400/600",
            "Nueva orden",
            "NUEVA"
        );

        ordenSitCard.style.display = 'block';

        showNotification(`Nueva orden ${numeroOrden} lista para fotografías`, 'info', 2000);
    }


    /*function buscarOrdenSit() {
        const ordenSitInput = document.getElementById('ordenSitInput');
        const value = ordenSitInput.value.trim();

        if (value === "") {
            alert("Ingrese un número de orden", "warning");
            return;
        }

        // Simular orden encontrada(Eliminar luego) - Imagen más grande
        ordenSitValue.textContent = value;
        prendaPreview.src = "https://picsum.photos/id/535/400/600";

        // Limpiar datos previos
        descripcion.textContent = "";
        tipoOrden.textContent = "";
        tipoOrden.className = "";
        tipoSeleccionado = null;
        currentImageData = null;
        uploadedImages = [];

        ordenSitCard.style.display = 'block';
       showNotification(`Orden SIT ${value} encontrada`, 'success',2000);
    }*/

    // Cambiar estado
    function setTipoFoto(tipo) {
        tipoSeleccionado = tipo;
        tipoOrden.textContent = tipo;

        if (tipo === "Muestra") { tipoOrden.className = "badge badge-color-personalizado"; }
        if (tipo === "Prenda Final") { tipoOrden.className = "badge badge-color-personalizado"; }
        if (tipo === "Validación AC") { tipoOrden.className = "badge badge-color-personalizado"; }
    }

   // Guardar y redirigir a fotos-index
    function guardarFoto() {
        console.log('Iniciando guardado automático...');

        if (uploadedImages.length === 0) {
            showNotification("Debe subir al menos una imagen antes de continuar", 'warning');
            return;
        }

        // 🎯 TODAS LAS IMÁGENES YA ESTÁN EN EL BACKEND
        console.log(`${uploadedImages.length} imágenes ya guardadas en el backend`);

        // 🎯 PREPARAR datos para historial en fotos-index
        const dataToTransfer = {
            images: uploadedImages.map(img => ({
                ...img,
                source: 'backend-upload',
                transferTimestamp: Date.now(),
                readyForHistorial: true
            })),
            metadata: {
                totalUploaded: uploadedImages.length,
                uploadSession: Date.now().toString(36),
                source: 'fotos-sit-add'
                ordenSit: document.getElementById('ordenSitValue')?.textContent || 'Nueva'
            }
        };

        // 🎯 GUARDAR para mostrar en fotos-index
        localStorage.setItem('newUploadedImages', JSON.stringify(dataToTransfer));

        // 🎯 REDIRECCIÓN AUTOMÁTICA
        showNotification(`${uploadedImages.length} imagen(es) guardadas. Redirigiendo...`, 'success', 1500);

        setTimeout(() => {
            window.location.href = "{{ route('fotos-index') }}";
        }, 1500);
    }

    // Lightbox functions
    function openLightbox(imageUrl, description, type) {
        document.getElementById('lightboxImage').src = imageUrl;
        document.getElementById('previewDescripcion').textContent = description || '-';
        document.getElementById('previewTipo').textContent = type || '-';
        document.getElementById('imageLightbox').style.display = 'flex';
    }

    function closeLightbox() {
        document.getElementById('imageLightbox').style.display = 'none';
    }

    function downloadImage() {
        const imgSrc = document.getElementById('lightboxImage').src;
        const link = document.createElement('a');
        link.href = imgSrc;
        link.download = "prenda.jpg";
        link.click();
    }
</script>

<!--/=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=/ -->
<script>
    // ===== SCRIPT FUNCIONALIDAD SUBIDA DE IMAGENES =====

    function initializeUploadButtons() {
        const cameraUpload = document.getElementById('cameraUpload');
        const fileUpload = document.getElementById('fileUpload');
        const cameraInput = document.getElementById('cameraInput');
        const fileInput = document.getElementById('fileInput');

        // Camera upload click
        if (cameraUpload && cameraInput) {
            cameraUpload.addEventListener('click', function() {
                console.log(' Activando cámara...');
                cameraInput.click();
            });

            cameraInput.addEventListener('change', function(e) {
                handleImageUpload(e.target.files, 'camera');
            });
        }

        // File upload click
        if (fileUpload && fileInput) {
            fileUpload.addEventListener('click', function() {
                console.log(' Abriendo selector de archivos...');
                fileInput.click();
            });

            fileInput.addEventListener('change', function(e) {
                handleImageUpload(e.target.files, 'file');
            });
        }

        // Drag and drop functionality
        initializeDragAndDrop();

        console.log('Sistema de subida inicializado');
    }

    function handleImageUpload(files, source) {
        if (!files || files.length === 0) {
            showNotification('No se seleccionaron archivos', 'warning');
            return;
        }

        console.log(` Subiendo ${files.length} archivo(s) desde ${source}`);

        // Validar archivos
        const validFiles = Array.from(files).filter(file => {
            if (!file.type.startsWith('image/')) {
                showNotification(`Archivo "${file.name}" no es una imagen válida`, 'error');
                return false;
            }

            // Validar tamaño (máximo 10MB)
            if (file.size > 10 * 1024 * 1024) {
                showNotification(`Archivo "${file.name}" es demasiado grande (máximo 10MB)`, 'error');
                return false;
            }

            return true;
        });

        if (validFiles.length === 0) {
            return;
        }

        processMultipleImageAtOnce(validFiles, source);

    }
        function processMultipleImageAtOnce(files, source) {
            console.log(`Procesando ${files.length} imágenes en lote...`);

            // Mostrar estado de carga
            const uploadBtn = source === 'camera'
                ? document.getElementById('cameraUpload')
                : document.getElementById('fileUpload');

            setUploadState(uploadBtn, 'uploading');

            // Convertir todas las imagenes a base64 primero
            const filePromises = Array.from(files).map(file => {
                return new Promise((resolve, reject) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        resolve({
                            file: file,
                            base64: e.target.result,
                            name: file.name,
                            size: file.size
                        });
                    };
                    reader.onerror = () => reject(new Error(`Error leyendo ${file.name}`));
                    reader.readAsDataURL(file);
                });
            });

            // Cuando todas las imágenes estén convertidas, abrir UN SOLO modal
            Promise.all(filePromises)
                .then(imageDataArray => {
                    console.log('Todas las imágenes convertidas a Base64');
                    // Abrir modal para configurar datos para todas las imagenes
                    showBatchImageModal(imageDataArray, uploadBtn);
                })
                .catch(error => {
                    console.error('Error convirtiendo imágenes a Base64:', error);
                    showNotification('Error al convertir imágenes a Base64', 'error');
                    setUploadState(uploadBtn, 'normal');
                });
        }

        // ================= FUNCIÓN: Modal para lote de imágenes ===================
        function showBatchImageModal(imageDataArray, uploadBtn) {
            console.log(`Abriendo modal para ${imageDataArray.length} imágenes`);

            const modalEl = document.getElementById('imageDataModal');
            const modal = new bootstrap.Modal(modalEl);

            // Mostrar titulo simple
            const modalTitle = document.getElementById('imageDataModalLabel');
            if (modalTitle) {
                modalTitle.textContent = `Detalles para ${imageDataArray.length} imagen(es)`;
            }

            // Limpiar formulario
            const descripcionInput = document.getElementById('descripcionInput');
            const tipoSelect = document.getElementById('tipoFotografiaSelect');

            if (descripcionInput) descripcionInput.value = '';
            if (tipoSelect) tipoSelect.selectedIndex = 0;

            // Solo mostrar cantidad
            addSimpleInfo(imageDataArray.length);


            // Configurar modal para no cerrarse
            modalEl.setAttribute('data-bs-backdrop', 'static');
            modalEl.setAttribute('data-bs-keyboard', 'false');

            // Manejar guardado para TODAS las imágenes
            const saveBtn = document.getElementById('saveImageData');

            const handleBatchSave = () => {
                const descripcionVal = descripcionInput ? descripcionInput.value.trim() : '';
                const tipoFotografia = tipoSelect ? tipoSelect.value : '';

                if (!descripcionVal || !tipoFotografia) {
                    showNotification("Por favor ingrese todos los campos", 'warning');
                    return;
                }

                console.log(`Procesando ${imageDataArray.length} imágenes...`);

                // Desactivar botón mientras se procesa
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Procesando...';

                //showNotification(`Guardando ${imageDataArray.length} imagen(es)...`, 'info', 2000);

                // ====== Procesamiento Automatico ========
                try {
                    const completedImages = imageDataArray.map((imageData, index) => {
                        const ordenSitValue = document.getElementById('ordenSitValue').textContent || 'N/A';

                        return {
                            id: Date.now() + index + Math.random(),
                            url: imageData.base64,
                            name: imageData.name,
                            size: imageData.size,
                            uploadDate: new Date().toISOString(),
                            uploadTimestamp: Date.now() + index, // Timestamps únicos
                            ordenSit: ordenSitValue,
                            po: generatePONumber(),
                            oc: generateOCNumber(),
                            descripcion: descripcionVal,
                            tipoFotografia: tipoFotografia,
                            categoria: determineImageCategory(tipoFotografia),
                            completionTimestamp: Date.now(),
                            status: 'completed',
                            source: 'fotos-sit-add',
                            fileType: imageData.file.type,
                            batchId: Date.now(), // ID del lote
                            batchIndex: index + 1 // Posición en el lote
                        };
                    });

                    // Agregar TODAS las imágenes al array
                    uploadedImages.push(...completedImages);

                    // Actualizar vista previa con la primera imagen
                    if (completedImages.length > 0) {
                        updateCardPreview(completedImages[0]);
                    }

                    console.log(` ${completedImages.length}Procesamiento de imágenes completado.`);

                    // Cerrar modal inmediatamente
                    modal.hide();

                    // == Guardado y redireccion automatica ==
                    setTimeout(() => {
                        console.log('Guardado automatico iniciado...');
                        guardarFoto(); // Redireccion automatica
                    }, 500);
                } catch (error) {
                    console.error('Error durante el procesamiento automático:', error);
                } finally {
                    // Cleanup
                    saveBtn.removeEventListener('click', handleBatchSave);
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = 'Continuar';
                    setUploadState(uploadBtn, 'normal');
                }
            };

            // Agregar event listener
            saveBtn.addEventListener('click', handleBatchSave);

            // Cambiar texto del botón
            saveBtn.innerHTML = 'Continuar';

            // Mostrar modal
            modal.show();
            console.log('Modal mostrado - Listo para continuar');
        }

        //////////////////////////////////////////////////////////////////////
        // ===== FUNCIÓN AUXILIAR: Mostrar información basica =====
        function addSimpleInfo(imageCount) {
            // Buscar si ya existe info container
            let infoContainer = document.getElementById('modalSimpleInfo');
            if (!infoContainer) {
                // Crear contenedor simple
                infoContainer = document.createElement('div');
                infoContainer.id = 'modalSimpleInfo';
                infoContainer.className = 'mb-3';

                // Insertar al inicio del modal -body
                const modalBody = document.querySelector('#imageDataModal .modal-body');
                if (modalBody) {
                    modalBody.insertAdjacentElement('afterbegin', infoContainer);
                    console.log('Info container creado');
                }
            }

            // HTML simple
            const infoHTML = `
                <div class="alert alert-primary p-3 text-center">
                    <h5 class="mb-2">
                        <i class="fas fa-images me-2"></i>
                        ${imageCount} imagen(es) seleccionada(s)
                    </h5>
                    <p class="mb-0 text-muted">
                        Los datos que ingreses se aplicarán a todas las imágenes.
                    </p>
            </div>
        `;

        infoContainer.innerHTML = infoHTML;
        console.log(`Info mostrada para ${imageCount} imágenes`);
    }

    // Agregar cleanup automático cuando se cierre el modal:
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('imageDataModal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                // Limpiar info container cuando se cierre el modal
                const infoContainer = document.getElementById('modalSimpleInfo');
                if (infoContainer) {
                    infoContainer.remove();
                    console.log('Info container limpiado');
                }
            });
        }
    });

/*=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>*/
    function uploadSingleImage(file) {
        return new Promise((resolve, reject) => {
            console.log('Procesando archivo:', file.name);

            // 🎯 CREAR FormData para enviar al backend
            const formData = new FormData();
            formData.append('imagen', file);
            formData.append('orden_sit', document.getElementById('ordenSitValue').textContent || '');
            formData.append('po', generatePONumber());
            formData.append('oc', generateOCNumber());
            formData.append('timestamp', new Date().toISOString());

            // 🎯 Mostrar modal para datos adicionales ANTES de enviar
            const modalEl = document.getElementById('imageDataModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            // Limpiar formulario
            document.getElementById('descripcionInput').value = '';
            document.getElementById('tipoFotografiaSelect').selectedIndex = 0;

            // Event listener para guardar
            const saveBtn = document.getElementById('saveImageData');
            const handleSave = () => {
                const descripcion = document.getElementById('descripcionInput').value.trim();
                const tipoFotografia = document.getElementById('tipoFotografiaSelect').value;

                if (!descripcion || !tipoFotografia) {
                    showNotification("Por favor ingrese todos los campos.", 'warning');
                    return;
                }

                // 🎯 AGREGAR campos adicionales al FormData
                formData.append('descripcion', descripcion);
                formData.append('tipo', tipoFotografia.toUpperCase());

                modal.hide();
                saveBtn.removeEventListener('click', handleSave);

                // 🎯 ENVIAR AL BACKEND con AJAX
                uploadToBackend(formData)
                    .then(response => {
                        console.log('Imagen subida al backend:', response);

                        // 🎯 GUARDAR para transferir a fotos-index
                        const imageData = {
                            id: response.data.id,
                            url: response.data.imagen_url, // URL del servidor
                            orden_sit: response.data.orden_sit,
                            po: response.data.po,
                            oc: response.data.oc,
                            descripcion: response.data.descripcion,
                            tipo: response.data.tipo,
                            fecha_subida: response.data.fecha_subida,
                            source: 'backend'
                        };

                        resolve(imageData);
                    })
                    .catch(error => {
                        console.error('Error subiendo imagen:', error);
                        showNotification('Error al subir imagen', 'error');
                        reject(error);
                    });
            };

            saveBtn.addEventListener('click', handleSave);
        });
    }

    // 🎯 NUEVA FUNCIÓN: Subir al backend
    function uploadToBackend(formData) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/api/fotografias', // Ruta del backend
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        resolve(response);
                    } else {
                        reject(new Error(response.message || 'Error del servidor'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', xhr.responseText);
                    reject(new Error('Error de conexión con el servidor'));
                }
            });
        });
    }

    // 🎯 NUEVA FUNCIÓN: Subir al backend
    function uploadToBackend(formData) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/api/fotografias', // Ruta del backend
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        resolve(response);
                    } else {
                        reject(new Error(response.message || 'Error del servidor'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', xhr.responseText);
                    reject(new Error('Error de conexión con el servidor'));
                }
            });
        });
    }

    /*function uploadSingleImage(file) {
        return new Promise((resolve, reject) => {
            console.log('Procesando archivo:', file.name);

            // Convertir archivo a Base64 para almacenamiento persistente
            const reader = new FileReader();

            reader.onload = function(e) {
                const base64Data = e.target.result;
                console.log('Imagen convertida a Base64');

                // DATOS BASE MEJORADOS para sincronización con historial
                const tempData = {
                    id: Date.now() + Math.random(),
                    url: base64Data, // Base64 persistente
                    name: file.name,
                    size: file.size,
                    uploadDate: new Date().toISOString(),
                    uploadTimestamp: Date.now(), // Timestamp para ordenamiento
                    ordenSit: document.getElementById('ordenSitValue').textContent || 'N/A',
                    po: generatePONumber(),
                    oc: generateOCNumber(),
                    source: 'fotos-sit-add', // Identificador de origen
                    fileType: file.type
                };

                console.log('Datos temporales preparados para historial');

                // Abrir modal y esperar datos del usuario
                const modalEl = document.getElementById('imageDataModal');
                const modal = new bootstrap.Modal(modalEl);

                // Limpiar formulario antes de mostrar
                document.getElementById('descripcionInput').value = '';
                document.getElementById('tipoFotografiaSelect').selectedIndex = 0;

                // Prevenir que el modal se cierre al hacer clic fuera
                modalEl.setAttribute('data-bs-backdrop', 'static');
                modalEl.setAttribute('data-bs-keyboard', 'false');

                // Evento al guardar
                const saveBtn = document.getElementById('saveImageData');

                const handleSave = () => {
                    const descripcionVal = document.getElementById('descripcionInput').value.trim();
                    const tipoFotografia = document.getElementById('tipoFotografiaSelect').value;

                    console.log('Guardando datos:', { descripcionVal, tipoFotografia });

                    if (!descripcionVal || !tipoFotografia) {
                        alert("Por favor ingrese todos los campos.");
                        return;
                    }

                    // Desactivar el boton mientras se procesa
                    saveBtn.disabled = true;

                    // DATOS COMPLETOS con metadatos para historial
                    const completeData = {
                        ...tempData,
                        descripcion: descripcionVal,
                        tipoFotografia,
                        categoria: determineImageCategory(tipoFotografia), // Para agrupación en historial
                        completionTimestamp: Date.now(), // Cuando se completó el proceso
                        status: 'completed'
                    };

                    console.log('Datos completos preparados con metadatos de historial');

                    // SINCRONIZACIÓN: Actualizar elementos del card
                    descripcion.textContent = descripcionVal;
                    tipoOrden.textContent = tipoFotografia;
                    tipoOrden.className = "badge badge-color-personalizado";
                    tipoSeleccionado = tipoFotografia;

                    // Actualizar imagen de vista previa
                    prendaPreview.src = base64Data;
                    prendaPreview.onclick = () => openLightbox(base64Data, descripcionVal, tipoFotografia);

                    // Guardar datos actuales
                    currentImageData = completeData;

                    // Agregar imagen al array
                    uploadedImages.push(completeData);

                    console.log('Ejecutando guardarFoto() automáticamente...');

                    // cerrar modal y Resolver con los datos
                    modal.hide();
                    //resolve(completeData);

                    // Ejecutar guardarFoto después de un pequeño delay
                    setTimeout(() => {
                        guardarFoto();
                    }, 300);

                    // Remover listener
                    saveBtn.removeEventListener('click', handleSave);

                    // Habilitar boton nuevamente
                    saveBtn.disabled = false;

                    console.log('Imagen procesada exitosamente con datos de historial');
                };

                // Manejar cierre del modal con botón cancelar
                const cancelBtn = modalEl.querySelector('.btn-secondary');
                const handleCancel = () => {
                    console.log('Upload cancelado');
                    reject(new Error('Upload cancelled'));
                    saveBtn.removeEventListener('click', handleSave);
                    cancelBtn.removeEventListener('click', handleCancel);
                };
                cancelBtn.addEventListener('click', handleCancel);

                // Agregar event listener para el boton guardar
                saveBtn.addEventListener('click', handleSave);

                // Mostrar modal
                modal.show();
                console.log('Modal mostrado');
            };

            reader.onerror = function() {
                console.error('Error leyendo archivo:', file.name);
                reject(new Error('Error reading file'));
            };

            // Leer archivo como Base64
            reader.readAsDataURL(file);
        });
    }*/

    // NUEVA FUNCIÓN: Determinar categoría para historial
    function determineImageCategory(tipoFotografia) {
        const tipo = tipoFotografia.toUpperCase();

        if (tipo.includes('MUESTRA')) return 'Muestra';
        if (tipo.includes('VALIDACION AC') || tipo.includes('VALIDACION AC')) return 'Validación AC';
        if (tipo.includes('PRENDA FINAL') || tipo.includes('PRENDA FINAL')) return 'Prenda Final';

        return 'general'; // Categoría por defecto
    }

    function updateCardPreview(imageData) {
        console.log('Actualizando vista previa del card:', imageData);

        // Actualizar la imagen de vista previa en el card
        if (prendaPreview && imageData.url) {
            prendaPreview.src = imageData.url;
            prendaPreview.onclick = () => openLightbox(
                imageData.url,
                imageData.descripcion,
                imageData.tipoFotografia
            );

            console.log('Vista previa actualizada');
        }

        // Actualizar información mostrada
        if (descripcion && imageData.descripcion) {
            descripcion.textContent = imageData.descripcion;
        }

        if (tipoOrden && imageData.tipoFotografia) {
            tipoOrden.textContent = imageData.tipoFotografia;
            tipoOrden.className = "badge badge-color-personalizado";
            tipoSeleccionado = imageData.tipoFotografia;
        }

        //  SINCRONIZACIÓN: Asegurar que los datos incluyan timestamps y metadatos para historial
        imageData.uploadTimestamp = new Date().toISOString();
        imageData.source = 'fotos-sit-add';

        console.log('Datos de imagen preparados para historial:', imageData);
    }

    function setUploadState(button, state) {
        if (!button) return;

        button.classList.remove('active', 'uploading');

        switch (state) {
            case 'uploading':
                button.classList.add('uploading');
                break;
            case 'success':
                button.classList.add('active');
                break;
            case 'normal':
            default:
                // Estado normal, sin clases adicionales
                break;
        }
    }

    function initializeDragAndDrop() {
        const uploadBtns = document.querySelectorAll('.upload-btn');

        uploadBtns.forEach(btn => {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                btn.addEventListener(eventName, preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                btn.addEventListener(eventName, () => btn.classList.add('active'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                btn.addEventListener(eventName, () => btn.classList.remove('active'), false);
            });

            btn.addEventListener('drop', handleDrop, false);
        });
    }

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function handleDrop(e) {
        const files = e.dataTransfer.files;
        const isCamera = e.currentTarget.id === 'cameraUpload';
        handleImageUpload(files, isCamera ? 'camera' : 'file');
    }

    function generatePONumber() {
        return '6000' + Math.floor(Math.random() * 900000 + 100000);
    }

    function generateOCNumber() {
        return '4200' + Math.floor(Math.random() * 9000000 + 1000000);
    }

    // =====>>>>>> Función para mostrar notificaciones ======>>>>>
    function showNotification(message, type = 'info', duration = 4000) {
        console.log(`Notificación: ${message} (${type})`);

        const toastEl = document.getElementById('notificationToast');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');

        if (!toastEl || !toastMessage || !toastIcon) {
            // Fallback a console si no hay elementos de toast
            console.log(`NOTIFICACIÓN: ${message}`);
            return;
        }

        // Limpiar clases anteriores
        toastEl.className = 'toast align-items-center border-0';
        toastIcon.className = '';

        // Configurar según el tipo
        switch (type) {
            case 'success':
                toastEl.classList.add('text-bg-success');
                toastIcon.className = 'fas fa-check-circle text-white';
                break;
            case 'error':
            case 'danger':
                toastEl.classList.add('text-bg-danger');
                toastIcon.className = 'fas fa-exclamation-triangle text-white';
                break;
            case 'warning':
                toastEl.classList.add('text-bg-warning');
                toastIcon.className = 'fas fa-exclamation-circle text-dark';
                break;
            case 'info':
            default:
                toastEl.classList.add('text-bg-primary');
                toastIcon.className = 'fas fa-info-circle text-white';
                break;
        }

        // Configurar mensaje
        toastMessage.textContent = message;

        // Mostrar toast
        const toast = new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: duration
        });

        toast.show();

        // Auto-ocultar después del tiempo especificado
        setTimeout(() => {
            toast.hide();
        }, duration);
    }

    // Inicialización principal
    document.addEventListener("DOMContentLoaded", function() {
        initializeUploadButtons();

        const ordenSitInput = document.getElementById('ordenSitInput');
        const searchBoton = document.getElementById('searchBoton');

        if (ordenSitInput) {
            ordenSitInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    buscarOrdenSit();
                }
            });
        }

        if (searchBoton) {
            searchBoton.addEventListener('click', function(e) {
                e.preventDefault();
                buscarOrdenSit();
            });
        }
    });
</script>

<!--/=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=/ -->
<script>
// ================================================================================================
// FUNCIONALIDAD BOTÓN CANCELAR - fotos-sit-add
// ================================================================================================

function cancelarOperacion() {
    console.log('Cancelando operación...');

    // 1. Limpiar inputs de archivo
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => input.value = '');

    // 2. Limpiar campo de búsqueda
    const ordenSitInput = document.getElementById('ordenSitInput');
    if (ordenSitInput) {
        ordenSitInput.value = '';
    }

    // 3. Ocultar el card de resultados
    const ordenSitCard = document.getElementById('ordenSitCard');
    if (ordenSitCard) {
        ordenSitCard.style.display = 'none';
    }

    // 4. Limpiar variables globales si existen
    if (typeof uploadedImages !== 'undefined') {
        uploadedImages = [];
    }
    if (typeof currentImageData !== 'undefined') {
        currentImageData = null;
    }
    if (typeof tipoSeleccionado !== 'undefined') {
        tipoSeleccionado = null;
    }

    // 5. Limpiar localStorage de imágenes
    localStorage.removeItem('newUploadedImages');
    localStorage.removeItem('uploadedImages');

    console.log('Operación cancelada. Listo para empezar de nuevo.');
}

// Configurar el botón al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    // Buscar el botón de cancelar y agregar el evento
    const cancelButton = document.querySelector('.btn-secondary');
    if (cancelButton && cancelButton.textContent.includes('Cancelar')) {
        cancelButton.onclick = cancelarOperacion;
        console.log('Botón cancelar configurado');
    }
});

</script>

@endsection
