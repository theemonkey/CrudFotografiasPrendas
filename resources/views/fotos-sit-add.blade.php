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
                <div class="col-md-3 text-center">
                    <!-- Miniatura -->
                    <img id="prendaPreview"
                        src="https://picsum.photos/200/300"
                        alt="Prenda"
                        class="img-thumbnail"
                        style="max-width:120px; cursor:pointer;"
                        onclick="openLightbox(this.src, tipoSeleccionado)">
                </div>
                <div class="col-md-9">
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
                    <!-- Estado -->
                    <div class="mb-3">
                        <label class="text-muted">Tipo del último cargue</label>
                        <div class="d-flex gap-2">
                            <button class="btn btn-personalizado" onclick="setTipoFoto('Muestra')">Muestra</button>
                            <button class="btn btn-personalizado" onclick="setTipoFoto('Prenda Final')">Prenda Final</button>
                            <button class="btn btn-personalizado" onclick="setTipoFoto('Validación AC')">Validación AC</button>
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
        <a class="btn btn-secondary">Cancelar</a>
        <button type="button" class="btn btn-primary" onclick="guardarFoto()">Guardar</button>
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

    // Validar y buscar (Simulacion resultados)
    function buscarOrdenSit() {
        const ordenSitInput = document.getElementById('ordenSitInput');
        const value = ordenSitInput.value.trim();

        if (value === "") {
            alert("Ingrese un número de orden");
            return;
        }

        // Simular orden encontrada(Eliminar luego)
        ordenSitValue.textContent = value;
        prendaPreview.src = "https://picsum.photos/200/300";

        // Limpiar datos previos
        descripcion.textContent = "";
        tipoOrden.textContent = "";
        tipoOrden.className = "";
        tipoSeleccionado = null;
        currentImageData = null;
        uploadedImages = [];

        ordenSitCard.style.display = 'block';
        alert(`Orden SIT ${value} encontrada`);
    }

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
    console.log('Iniciando guardado de fotos...');
    console.log('Imágenes a guardar:', uploadedImages);

    if (uploadedImages.length === 0) {
        alert("Debe subir al menos una imagen antes de guardar");
        return;
    }

    // Verificar que todas las imágenes tengan datos completos
    const imagenesCompletas = uploadedImages.filter(img =>
        img.url && img.descripcion && img.tipoFotografia
    );

    console.log('Imágenes con datos completos:', imagenesCompletas);

    if (imagenesCompletas.length === 0) {
        alert("No hay imágenes válidas para guardar");
        return;
    }

    // Guardar datos en localStorage para transferir a fotos-index
    const dataToTransfer = {
        images: imagenesCompletas,
        timestamp: new Date().toISOString()
    };

    console.log('Datos a transferir:', dataToTransfer);

    localStorage.setItem('newUploadedImages', JSON.stringify(dataToTransfer));
    console.log('Datos guardados en localStorage');

    alert(`Se guardaron ${imagenesCompletas.length} imagen(es) correctamente. Redirigiendo...`);

    // Redirigir a fotos-index
    console.log('Redirigiendo a fotos-index...');
    window.location.href = "{{ route('fotos-index') }}";
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

        console.log(' Sistema de subida inicializado');
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

        // Mostrar estado de carga
        const uploadBtn = source === 'camera'
            ? document.getElementById('cameraUpload')
            : document.getElementById('fileUpload');

        setUploadState(uploadBtn, 'uploading');

        // Procesar archivos
        const uploadPromises = validFiles.map(file => uploadSingleImage(file));

        Promise.all(uploadPromises)
            .then(results => {
                console.log(' Todas las imágenes subidas correctamente');
                showNotification(`${results.length} imagen(es) subida(s) correctamente`, 'success');

                // Agregar imágenes al array
                results.forEach(imageData => {
                    uploadedImages.push(imageData);
                    // Actualizar imagen de vista previa en el card
                    updateCardPreview(imageData);
                });

                setUploadState(uploadBtn, 'success');

                // Reset después de 2 segundos
                setTimeout(() => {
                    setUploadState(uploadBtn, 'normal');
                }, 2000);
            })
            .catch(error => {
                console.error(' Error subiendo imágenes:', error);
                showNotification('Error al subir las imágenes', 'error');
                setUploadState(uploadBtn, 'normal');
            });
    }

    function uploadSingleImage(file) {
        return new Promise((resolve, reject) => {
            console.log('Procesando archivo:', file.name);

            // Convertir archivo a Base64 para almacenamiento persistente
            const reader = new FileReader();

            reader.onload = function(e) {
                const base64Data = e.target.result;
                console.log('Imagen convertida a Base64');

                // Datos base de la imagen
                const tempData = {
                    id: Date.now() + Math.random(),
                    url: base64Data, // Usar Base64 en lugar de blob URL
                    name: file.name,
                    size: file.size,
                    uploadDate: new Date().toISOString(),
                    ordenSit: document.getElementById('ordenSitValue').textContent || 'N/A',
                    po: generatePONumber(),
                    oc: generateOCNumber()
                };

                console.log('Datos temporales preparados');

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

                    // Datos completos
                    const completeData = {
                        ...tempData,
                        descripcion: descripcionVal,
                        tipoFotografia
                    };

                    console.log('Datos completos preparados');

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

                    // cerrar modal y Resolver la promesa con los datos
                    modal.hide();
                    resolve(completeData);

                    // Remover listener
                    saveBtn.removeEventListener('click', handleSave);

                    // Habilitar boton nuevamente
                    saveBtn.disabled = false;

                    console.log('🎉 Imagen procesada exitosamente');
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
    }

    function updateCardPreview(imageData) {
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

    // Función para mostrar notificaciones
    function showNotification(message, type) {
        // Implementación básica con alert, puedes mejorar con toast notifications
        alert(message);
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

@endsection
