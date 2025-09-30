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
                        src="https://picsum.photos/400/600"
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

        // Simular orden encontrada(Eliminar luego) - Imagen más grande
        ordenSitValue.textContent = value;
        prendaPreview.src = "https://picsum.photos/400/600";

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

    // PREPARAR datos con información adicional para historial
    const dataToTransfer = {
        images: imagenesCompletas.map(img => ({
            ...img,
            transferTimestamp: Date.now(),
            transferDate: new Date().toISOString(),
            readyForHistorial: true // Marca para que el historial sepa que están listas
        })),
        timestamp: new Date().toISOString(),
        source: 'fotos-sit-add',
        totalImages: imagenesCompletas.length,
        metadata: {
            ordenSit: imagenesCompletas[0]?.ordenSit || 'N/A',
            uploadSession: Date.now().toString(36) // ID único de sesión de subida
        }
    };

    console.log('Datos preparados para transferencia:', dataToTransfer);

    localStorage.setItem('newUploadedImages', JSON.stringify(dataToTransfer));
    console.log('Datos guardados en localStorage con metadatos de historial');

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

                    // cerrar modal y Resolver la promesa con los datos
                    modal.hide();
                    resolve(completeData);

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
    }

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

<!--/=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=//=/=/=/=/=/=/=/ -->
<script>
// ================================================================================================
// FUNCIONALIDAD BOTÓN CANCELAR - fotos-sit-add (VERSIÓN COMPLETA)
// ================================================================================================

// Variables globales para control de cancelación
let uploadInProgress = false;
let currentOrderData = null;
let uploadedFiles = [];

// ===== FUNCIONALIDAD DEL BOTÓN CANCELAR =====
function setupCancelButton() {
    console.log('🔍 Buscando botón Cancelar...');

    // Método 1: Buscar por ID específicos
    let cancelButton = document.getElementById('cancelBtn') ||
                      document.getElementById('btnCancelar') ||
                      document.getElementById('cancelButton');

    // Método 2: Buscar por clases comunes
    if (!cancelButton) {
        const possibleButtons = document.querySelectorAll('.btn-secondary, .btn-outline-secondary, .btn[data-action="cancel"]');
        cancelButton = Array.from(possibleButtons).find(btn =>
            btn.textContent.includes('Cancelar') ||
            btn.innerText.includes('Cancelar') ||
            btn.getAttribute('data-action') === 'cancel'
        );
    }

    // Método 3: Buscar todos los botones y filtrar por texto
    if (!cancelButton) {
        const allButtons = document.querySelectorAll('button, .btn');
        cancelButton = Array.from(allButtons).find(btn => {
            const text = btn.textContent.toLowerCase().trim();
            const innerText = btn.innerText.toLowerCase().trim();
            return text.includes('cancelar') || innerText.includes('cancelar');
        });
    }

    // Método 4: Buscar en el área específica (footer, etc.)
    if (!cancelButton) {
        const footerArea = document.querySelector('.modal-footer, .card-footer, .action-buttons, .button-group');
        if (footerArea) {
            const footerButtons = footerArea.querySelectorAll('button, .btn');
            cancelButton = Array.from(footerButtons).find(btn =>
                btn.textContent.includes('Cancelar') || btn.innerText.includes('Cancelar')
            );
        }
    }

    if (cancelButton) {
        console.log('✅ Botón Cancelar encontrado:', cancelButton);

        // Remover event listeners existentes para evitar duplicados
        const newCancelButton = cancelButton.cloneNode(true);
        cancelButton.parentNode.replaceChild(newCancelButton, cancelButton);

        // Agregar nuevo event listener
        newCancelButton.addEventListener('click', handleCancelUpload);

        console.log('🔧 Event listener de cancelación configurado');
        return newCancelButton;
    } else {
        console.warn('⚠️ Botón Cancelar no encontrado, configurando listener global...');

        // Configurar listener global como fallback
        setupGlobalCancelListener();
        return null;
    }
}

// ===== CONFIGURAR LISTENER GLOBAL COMO FALLBACK =====
function setupGlobalCancelListener() {
    document.addEventListener('click', function(e) {
        const target = e.target;

        // Verificar si es un botón de cancelar
        if (target.tagName === 'BUTTON' || target.classList.contains('btn')) {
            const text = target.textContent.toLowerCase().trim();
            const innerText = target.innerText.toLowerCase().trim();
            const dataAction = target.getAttribute('data-action');

            if (text.includes('cancelar') ||
                innerText.includes('cancelar') ||
                dataAction === 'cancel' ||
                target.id.toLowerCase().includes('cancel')) {

                console.log('🎯 Botón Cancelar detectado via listener global');
                handleCancelUpload(e);
            }
        }
    });

    console.log('🌐 Listener global de cancelación configurado');
}

// ===== MANEJAR CANCELACIÓN DE SUBIDA =====
function handleCancelUpload(event) {
    event.preventDefault();
    event.stopPropagation();

    console.log('🚫 Botón Cancelar presionado');

    // Verificar si hay una subida en progreso o datos que perder
    const hasData = uploadInProgress || uploadedFiles.length > 0 || currentOrderData || hasFormData();

    if (hasData) {
        showCancelConfirmation();
    } else {
        // Si no hay nada que cancelar, simplemente limpiar y cerrar
        performCancelAction(false); // false = no mostrar confirmación de éxito
    }
}

// ===== VERIFICAR SI HAY DATOS EN EL FORMULARIO =====
function hasFormData() {
    // Verificar campos de texto
    const textInputs = document.querySelectorAll('input[type="text"], textarea, input[type="search"]');
    const hasTextData = Array.from(textInputs).some(input => input.value.trim() !== '');

    // Verificar selects
    const selects = document.querySelectorAll('select');
    const hasSelectData = Array.from(selects).some(select => select.value !== '' && select.value !== select.options[0].value);

    // Verificar archivos
    const fileInputs = document.querySelectorAll('input[type="file"]');
    const hasFileData = Array.from(fileInputs).some(input => input.files.length > 0);

    return hasTextData || hasSelectData || hasFileData;
}

// ===== MOSTRAR CONFIRMACIÓN DE CANCELACIÓN =====
function showCancelConfirmation() {
    const hasUploadedFiles = uploadedFiles.length > 0;
    const hasOrderData = currentOrderData !== null;
    const hasFormContent = hasFormData();

    let message = '¿Estás seguro de que deseas cancelar?';
    let details = [];

    if (hasUploadedFiles) {
        details.push(`• Se perderán ${uploadedFiles.length} imagen(es) subida(s)`);
    }

    if (hasOrderData && currentOrderData.ordenSit) {
        details.push(`• Se perderán los datos de la orden SIT: ${currentOrderData.ordenSit}`);
    }

    if (hasFormContent) {
        details.push('• Se perderá la información ingresada en el formulario');
    }

    if (uploadInProgress) {
        details.push('• Se cancelará la subida en progreso');
    }

    if (details.length > 0) {
        details.push('', 'Esta acción no se puede deshacer.');
        message += '\n\n' + details.join('\n');
    }

    Swal.fire({
        title: '¿Cancelar subida?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, cancelar todo',
        cancelButtonText: 'Continuar',
        reverseButtons: true,
        focusCancel: true
    }).then((result) => {
        if (result.isConfirmed) {
            performCancelAction(true); // true = mostrar confirmación de éxito
        } else {
            console.log('📤 Usuario decidió continuar con la subida');
        }
    });
}

// ===== FUNCIÓN ESPECÍFICA PARA OCULTAR EL CARD DE ORDEN =====
function hideOrderCard() {
    console.log('🔍 Buscando card de orden para ocultar...');

    // Método 1: Buscar por contenido específico de la orden
    const orderElements = document.querySelectorAll('*');
    let orderCard = null;

    // Buscar elementos que contengan "Orden SIT:" o "Tipo:" o "Descripción:"
    Array.from(orderElements).forEach(element => {
        const text = element.textContent;
        if (text.includes('Orden SIT:') && text.includes('Tipo:') && text.includes('Descripción:')) {
            // Encontrar el contenedor padre más apropiado
            let parent = element;
            while (parent && parent !== document.body) {
                if (parent.classList.contains('card') ||
                    parent.classList.contains('card-body') ||
                    parent.classList.contains('order-info') ||
                    parent.classList.contains('order-card') ||
                    parent.style.border ||
                    parent.style.padding) {
                    orderCard = parent;
                    break;
                }
                parent = parent.parentElement;
            }

            // Si no encuentra un contenedor específico, usar el elemento padre directo
            if (!orderCard && element.parentElement) {
                orderCard = element.parentElement;
            }
        }
    });

    // Método 2: Buscar por estructura HTML específica
    if (!orderCard) {
        // Buscar div que contenga la imagen y la información de la orden
        const possibleCards = document.querySelectorAll('div');
        orderCard = Array.from(possibleCards).find(div => {
            const children = div.children;
            let hasImage = false;
            let hasOrderInfo = false;

            Array.from(children).forEach(child => {
                if (child.tagName === 'IMG' || child.querySelector('img')) {
                    hasImage = true;
                }
                if (child.textContent.includes('Orden SIT:') ||
                    child.textContent.includes('Tipo:')) {
                    hasOrderInfo = true;
                }
            });

            return hasImage && hasOrderInfo;
        });
    }

    // Método 3: Buscar el contenedor que tenga los elementos característicos
    if (!orderCard) {
        const containers = document.querySelectorAll('div, section, article');
        orderCard = Array.from(containers).find(container => {
            const hasOrderSit = container.textContent.includes('Orden SIT:');
            const hasUploadButtons = container.textContent.includes('Cámara') && container.textContent.includes('Archivo');
            return hasOrderSit && hasUploadButtons;
        });
    }

    if (orderCard) {
        console.log('✅ Card de orden encontrado:', orderCard);

        // Aplicar animación de salida
        orderCard.style.transition = 'all 0.5s ease';
        orderCard.style.opacity = '0';
        orderCard.style.transform = 'translateY(-20px)';

        setTimeout(() => {
            orderCard.style.display = 'none';
            console.log('📦 Card de orden ocultado');
        }, 500);

        return true;
    } else {
        console.warn('⚠️ No se pudo encontrar el card de orden específico');
        return false;
    }
}

// ===== FUNCIÓN ALTERNATIVA PARA OCULTAR TODA LA SECCIÓN =====
function hideOrderSection() {
    console.log('🔍 Intentando ocultar sección completa de orden...');

    // Buscar elementos que contengan la información de la orden
    const textToSearch = ['Orden SIT:', 'Tipo:', 'Descripción:', 'Subir Imágenes'];
    let sectionToHide = null;

    // Buscar desde el elemento más específico hacia arriba
    textToSearch.forEach(searchText => {
        if (!sectionToHide) {
            const elements = document.querySelectorAll('*');
            Array.from(elements).forEach(element => {
                if (element.textContent.includes(searchText) && !sectionToHide) {
                    // Subir en el DOM hasta encontrar un contenedor apropiado
                    let current = element;
                    let attempts = 0;

                    while (current && current.parentElement && attempts < 10) {
                        current = current.parentElement;
                        attempts++;

                        // Verificar si este elemento es un buen candidato para ocultar
                        const hasMultipleOrderElements = textToSearch.filter(text =>
                            current.textContent.includes(text)
                        ).length >= 3;

                        if (hasMultipleOrderElements &&
                            current.children.length > 1 &&
                            current !== document.body) {
                            sectionToHide = current;
                            break;
                        }
                    }
                }
            });
        }
    });

    if (sectionToHide) {
        console.log('✅ Sección de orden encontrada:', sectionToHide);

        // Animación de ocultamiento
        sectionToHide.style.transition = 'all 0.6s ease';
        sectionToHide.style.opacity = '0';
        sectionToHide.style.transform = 'scale(0.95)';
        sectionToHide.style.maxHeight = sectionToHide.offsetHeight + 'px';

        setTimeout(() => {
            sectionToHide.style.maxHeight = '0';
            sectionToHide.style.padding = '0';
            sectionToHide.style.margin = '0';
        }, 200);

        setTimeout(() => {
            sectionToHide.style.display = 'none';
            console.log('📦 Sección de orden completamente ocultada');
        }, 600);

        return true;
    }

    return false;
}

// ===== FUNCIÓN PARA RESETEAR LA VISTA COMPLETAMENTE =====
function resetToInitialState() {
    console.log('🔄 Reseteando a estado inicial...');

    // Limpiar el campo de búsqueda
    const searchInput = document.querySelector('input[placeholder*="orden"], input[placeholder*="SIT"]');
    if (searchInput) {
        searchInput.value = '';
        searchInput.placeholder = 'Buscar orden SIT';
    }

    // Asegurar que solo el campo de búsqueda sea visible
    const searchContainer = searchInput ? searchInput.closest('div, form, section') : null;

    if (searchContainer) {
        // Mantener visible solo el contenedor de búsqueda
        const allMainContainers = document.querySelectorAll('main > div, .container > div, .content > div');
        allMainContainers.forEach(container => {
            if (container !== searchContainer &&
                !container.contains(searchContainer) &&
                container.textContent.includes('Orden SIT:')) {
                container.style.display = 'none';
            }
        });
    }

    console.log('✅ Vista reseteada a estado inicial');
}

// ===== FUNCIÓN PARA MOSTRAR NUEVAMENTE EL FORMULARIO DE BÚSQUEDA =====
function showSearchForm() {
    console.log('🔍 Asegurando que el formulario de búsqueda esté visible...');

    const searchElements = [
        document.querySelector('input[placeholder*="orden"]'),
        document.querySelector('input[placeholder*="SIT"]'),
        document.querySelector('#searchInput'),
        document.querySelector('[name="orden_sit"]')
    ];

    searchElements.forEach(element => {
        if (element) {
            // Asegurar que el elemento y sus padres sean visibles
            let current = element;
            while (current && current !== document.body) {
                if (current.style.display === 'none') {
                    current.style.display = '';
                }
                if (current.style.visibility === 'hidden') {
                    current.style.visibility = 'visible';
                }
                current = current.parentElement;
            }

            // Enfocar el campo de búsqueda
            setTimeout(() => {
                element.focus();
            }, 100);
        }
    });
}

// ===== CERRAR/OCULTAR CARD (VERSIÓN MEJORADA) =====
function closeUploadCard() {
    console.log('📦 Intentando cerrar card de subida...');

    // Intentar múltiples métodos para ocultar el card
    let success = false;

    // Método 1: Ocultar card específico de orden
    success = hideOrderCard();

    // Método 2: Si el primero falla, intentar ocultar toda la sección
    if (!success) {
        success = hideOrderSection();
    }

    // Método 3: Si ambos fallan, resetear a estado inicial
    if (!success) {
        resetToInitialState();
        success = true;
    }

    // Método 4: Fallback - buscar y ocultar cualquier contenedor con información de orden
    if (!success) {
        const fallbackContainers = document.querySelectorAll('div');
        Array.from(fallbackContainers).forEach(container => {
            const text = container.textContent;
            if (text.includes('Orden SIT:') &&
                text.includes('Subir Imágenes') &&
                container.offsetHeight > 100) {

                container.style.transition = 'opacity 0.5s ease';
                container.style.opacity = '0';

                setTimeout(() => {
                    container.style.display = 'none';
                }, 500);

                success = true;
            }
        });
    }

    if (success) {
        console.log('✅ Card cerrado exitosamente');
    } else {
        console.warn('⚠️ No se pudo cerrar el card automáticamente');
    }

    return success;
}

// ===== REALIZAR ACCIÓN DE CANCELACIÓN (ACTUALIZADA) =====
function performCancelAction(showSuccess = true) {
    console.log('🧹 Ejecutando cancelación completa...');

    // 1. Detener cualquier subida en progreso
    stopCurrentUploads();

    // 2. Limpiar archivos subidos
    clearUploadedFiles();

    // 3. Limpiar datos de la orden
    clearOrderData();

    // 4. Resetear interfaz
    resetInterface();

    // 5. MEJORADO: Cerrar/ocultar card específicamente
    const cardClosed = closeUploadCard();

    // 6. Mostrar formulario de búsqueda
    setTimeout(() => {
        showSearchForm();
    }, cardClosed ? 600 : 100);

    // 7. Mostrar mensaje de confirmación si se solicita
    if (showSuccess) {
        setTimeout(() => {
            showCancellationSuccess();
        }, 300);
    }

    // 8. Permitir empezar de nuevo
    setTimeout(() => {
        enableNewUpload();
    }, 700);
}

// ===== DETENER SUBIDAS EN PROGRESO =====
function stopCurrentUploads() {
    uploadInProgress = false;

    // Cancelar requests AJAX en progreso
    if (window.currentUploadRequests && Array.isArray(window.currentUploadRequests)) {
        window.currentUploadRequests.forEach(request => {
            if (request && typeof request.abort === 'function') {
                try {
                    request.abort();
                } catch (error) {
                    console.warn('Error cancelando request:', error);
                }
            }
        });
        window.currentUploadRequests = [];
    }

    // Limpiar timeouts/intervals de upload
    if (window.uploadTimeouts && Array.isArray(window.uploadTimeouts)) {
        window.uploadTimeouts.forEach(timeout => clearTimeout(timeout));
        window.uploadTimeouts = [];
    }

    console.log('⏹️ Subidas en progreso detenidas');
}

// ===== LIMPIAR ARCHIVOS SUBIDOS =====
function clearUploadedFiles() {
    uploadedFiles = [];

    // Limpiar input de archivos
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        try {
            input.value = '';
        } catch (error) {
            console.warn('Error limpiando input de archivo:', error);
        }
    });

    // Limpiar previews de imágenes
    const imagePreviewSelectors = [
        '#imagePreview',
        '#uploadedImages',
        '.uploaded-images-container',
        '.image-preview-container',
        '.preview-container',
        '[data-preview]'
    ];

    imagePreviewSelectors.forEach(selector => {
        const container = document.querySelector(selector);
        if (container) {
            container.innerHTML = '';
            container.style.display = 'none';
        }
    });

    // Limpiar localStorage relacionado con imágenes
    const storageKeys = [
        'uploadedImages',
        'currentUploadSession',
        'newUploadedImages',
        'pendingImages',
        'tempImages'
    ];

    storageKeys.forEach(key => {
        try {
            localStorage.removeItem(key);
        } catch (error) {
            console.warn(`Error limpiando localStorage key ${key}:`, error);
        }
    });

    console.log('🗑️ Archivos subidos limpiados');
}

// ===== LIMPIAR DATOS DE LA ORDEN =====
function clearOrderData() {
    currentOrderData = null;

    // Limpiar campos del formulario por selector específico
    const fieldSelectors = [
        'input[name="orden_sit"]',
        'input[placeholder*="orden"]',
        'input[placeholder*="SIT"]',
        'select[name="tipo_fotografia"]',
        'textarea[name="descripcion"]',
        '#ordenSitInput',
        '#tipoFotografiaSelect',
        '#descripcionInput',
        '#searchInput'
    ];

    fieldSelectors.forEach(selector => {
        const field = document.querySelector(selector);
        if (field) {
            if (field.type === 'checkbox' || field.type === 'radio') {
                field.checked = false;
            } else {
                field.value = '';
            }
        }
    });

    // Limpiar displays de información de orden
    const displaySelectors = [
        '#orderInfo',
        '#orderDisplay',
        '.order-info-display',
        '.order-card',
        '[data-order-info]'
    ];

    displaySelectors.forEach(selector => {
        const display = document.querySelector(selector);
        if (display) {
            display.style.display = 'none';
            display.innerHTML = '';
        }
    });

    console.log('📋 Datos de orden limpiados');
}

// ===== RESETEAR INTERFAZ =====
function resetInterface() {
    // Resetear estado de botones comunes
    const buttonSelectors = [
        '.btn-primary',
        '.btn-success',
        '#guardarBtn',
        '#subirBtn',
        'button[type="submit"]',
        '[data-action="save"]',
        '[data-action="upload"]'
    ];

    buttonSelectors.forEach(selector => {
        const buttons = document.querySelectorAll(selector);
        buttons.forEach(btn => {
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('loading', 'disabled', 'uploading');

                // Resetear texto de botones que puedan haber sido modificados
                if (btn.innerHTML.includes('fa-spin') || btn.textContent.includes('...')) {
                    const originalTexts = {
                        'Guardar': '<i class="fas fa-save me-1"></i>Guardar',
                        'Subir': '<i class="fas fa-upload me-1"></i>Subir',
                        'Procesar': '<i class="fas fa-cog me-1"></i>Procesar'
                    };

                    Object.entries(originalTexts).forEach(([text, html]) => {
                        if (btn.textContent.includes(text)) {
                            btn.innerHTML = html;
                        }
                    });
                }
            }
        });
    });

    // Resetear progress bars
    const progressElements = document.querySelectorAll('.progress-bar, .upload-progress, [data-progress]');
    progressElements.forEach(element => {
        element.style.width = '0%';
        element.textContent = '';
        element.setAttribute('aria-valuenow', '0');

        const parentProgress = element.closest('.progress');
        if (parentProgress) {
            parentProgress.style.display = 'none';
        }
    });

    // Remover mensajes de estado temporales
    const statusSelectors = [
        '.upload-status',
        '.status-message',
        '.alert-info',
        '.alert-warning',
        '.alert-success',
        '[data-status]'
    ];

    statusSelectors.forEach(selector => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            if (element.classList.contains('temporary') ||
                element.textContent.includes('Subiendo') ||
                element.textContent.includes('Procesando')) {
                element.remove();
            }
        });
    });

    console.log('🔄 Interfaz reseteada');
}

// ===== MOSTRAR ÉXITO DE CANCELACIÓN =====
function showCancellationSuccess() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '✅ Cancelado',
            text: 'La subida ha sido cancelada correctamente. Puedes empezar de nuevo.',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    } else {
        // Fallback si SweetAlert no está disponible
        console.log('✅ Cancelación completada exitosamente');

        // Crear notificación simple
        const notification = document.createElement('div');
        notification.innerHTML = `
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
                ✅ Cancelado correctamente. Puedes empezar de nuevo.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }
}

// ===== HABILITAR NUEVA SUBIDA =====
function enableNewUpload() {
    // Resetear flags globales
    uploadInProgress = false;
    currentOrderData = null;
    uploadedFiles = [];

    // Habilitar inputs de subida
    const uploadInputs = document.querySelectorAll('input[type="file"]');
    uploadInputs.forEach(input => {
        input.disabled = false;
    });

    // Habilitar botones de subida
    const uploadButtonSelectors = [
        '#cameraUpload',
        '#fileUpload',
        '.upload-btn',
        '[data-upload]'
    ];

    uploadButtonSelectors.forEach(selector => {
        const btn = document.querySelector(selector);
        if (btn) {
            btn.disabled = false;
            btn.classList.remove('disabled');
        }
    });

    // Enfocar el primer campo relevante
    const firstFieldSelectors = [
        'input[placeholder*="orden"]',
        'input[placeholder*="SIT"]',
        '#searchOrderInput',
        'input[name="orden_sit"]',
        'input[type="search"]'
    ];

    for (const selector of firstFieldSelectors) {
        const field = document.querySelector(selector);
        if (field) {
            setTimeout(() => {
                field.focus();
            }, 500);
            break;
        }
    }

    console.log('🆕 Sistema listo para nueva subida');
}

// ===== FUNCIÓN PARA DETECTAR ESTADO DE SUBIDA =====
function updateUploadState(files = [], orderData = null, inProgress = false) {
    uploadedFiles = files || [];
    currentOrderData = orderData;
    uploadInProgress = inProgress;

    console.log('📊 Estado actualizado:', {
        files: uploadedFiles.length,
        hasOrder: !!currentOrderData,
        inProgress: uploadInProgress
    });
}

// ===== FUNCIÓN DE DEBUG PARA IDENTIFICAR EL CARD =====
function debugFindOrderCard() {
    console.log('🐛 DEBUG: Buscando card de orden...');

    const allDivs = document.querySelectorAll('div');
    console.log(`Total de divs encontrados: ${allDivs.length}`);

    const candidateCards = [];

    Array.from(allDivs).forEach((div, index) => {
        const text = div.textContent;
        const hasOrderSit = text.includes('Orden SIT:');
        const hasTipo = text.includes('Tipo:');
        const hasDescripcion = text.includes('Descripción:');
        const hasSubirImagenes = text.includes('Subir Imágenes');

        if (hasOrderSit && hasTipo && hasDescripcion) {
            candidateCards.push({
                element: div,
                index: index,
                height: div.offsetHeight,
                width: div.offsetWidth,
                classList: Array.from(div.classList),
                hasUploadSection: hasSubirImagenes
            });
        }
    });

    console.log('Candidatos encontrados:', candidateCards);

    // Resaltar visualmente los candidatos (temporal para debug)
    candidateCards.forEach((candidate, i) => {
        candidate.element.style.outline = `3px solid ${i === 0 ? 'red' : 'blue'}`;
        candidate.element.title = `Candidato ${i + 1}`;
    });

    return candidateCards;
}

// ===== DETECTAR CAMBIOS DE ESTADO =====
function setupStateDetection() {
    // Detectar cuando se seleccionan archivos
    document.addEventListener('change', function(e) {
        if (e.target.type === 'file' && e.target.files.length > 0) {
            updateUploadState(Array.from(e.target.files), currentOrderData, false);
        }
    });

    // Detectar cuando se llenan campos importantes
    document.addEventListener('input', function(e) {
        const relevantFields = ['orden_sit', 'ordenSit'];
        const isRelevantField = relevantFields.some(field =>
            e.target.name === field ||
            e.target.id === field ||
            e.target.placeholder?.toLowerCase().includes('orden')
        );

        if (isRelevantField && e.target.value.trim()) {
            const orderData = { ordenSit: e.target.value.trim() };
            updateUploadState(uploadedFiles, orderData, uploadInProgress);
        }
    });
}

// ===== INICIALIZACIÓN =====
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚫 Inicializando funcionalidad de cancelación completa...');

    // Usar setTimeout para asegurar que el DOM esté completamente cargado
    setTimeout(() => {
        setupCancelButton();
        setupStateDetection();
    }, 100);

    console.log('✅ Funcionalidad de cancelación completa inicializada');
});

// ===== FUNCIONES GLOBALES =====
window.handleCancelUpload = handleCancelUpload;
window.performCancelAction = performCancelAction;
window.updateUploadState = updateUploadState;
window.debugFindOrderCard = debugFindOrderCard;
window.hideOrderCard = hideOrderCard;
window.hideOrderSection = hideOrderSection;
window.resetToInitialState = resetToInitialState;

// ===== ATAJO DE TECLADO =====
document.addEventListener('keydown', function(e) {
    // Ctrl+Esc para cancelar rápidamente
    if (e.ctrlKey && e.key === 'Escape') {
        e.preventDefault();
        handleCancelUpload(e);
    }
});

console.log('🚫 Módulo de cancelación completo cargado');
</script>

@endsection
