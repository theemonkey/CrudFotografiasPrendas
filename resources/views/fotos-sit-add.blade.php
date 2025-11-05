@extends('layout/plantilla')

@section('tituloPagina', 'Agregar Foto a Orden SIT')

@section('contenido')

<div class="container mt-4">
    <h3 class="mb-4">Agregar fotos de la prenda</h3>

    <!-- Buscar Orden SIT -->
    <div class="mb-3">
        <div class="input-group">
            <input type="text"
                id="ordenSitInput"
                class="form-control"
                placeholder="Buscar orden SIT"
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

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

        //BUSCAR EN EL BACKEND
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
                        //ORDEN EXISTE - Mostrar datos existentes
                        const primeraFoto = fotografias[0];
                        mostrarOrdenExistente(primeraFoto, fotografias.length);
                    } else {
                        //ORDEN NO EXISTE - Permitir crear nueva
                        mostrarOrdenNueva(value);
                    }
                } else {
                    console.error('Error en respuesta:', response.message);
                    mostrarOrdenNueva(value); // Fallback a nueva orden
                }
            },
            error: function(xhr, status, error) {
                console.error('Error buscando orden:', error);
                //FALLBACK: Permitir crear nueva orden
                mostrarOrdenNueva(value);
            }
        });
    }

    //NUEVA FUNCIÓN: Mostrar orden existente
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

    //NUEVA FUNCIÓN: Mostrar orden nueva
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

        //showNotification(`Nueva orden ${numeroOrden} lista para fotografías`, 'info', 2000);
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
    function guardarFoto(savedImages) {
        console.log('Iniciando guardado automático...', savedImages);

        if (!savedImages || savedImages.length === 0) {
            showNotification("No hay imágenes guardadas para procesar", 'warning');
            return;
        }

        //PREPARAR datos para mostrar (No subir de nuevo a la tabla)
        const dataToTransfer = {
            images: savedImages.map(img => ({
                //Datos del backend real (ya subido)
                id: img.id,
                backendId: img.id,
                url: img.url,
                imagen_url: img.url, // Para compatibilidad con backend
                orden_sit: img.orden_sit,
                po: img.po,
                oc: img.oc,
                descripcion: img.descripcion,
                tipo: img.tipo,
                created_at: img.created_at,
                fecha_subida: img.created_at,

                //MARCADORES DE CONTROL
                origenVista: 'fotos-sit-add',
                procesadoPor: 'fotos-sit-add',
                displayOnly: true,        //Solo para mostrar
                uploaded: true,           //Ya subida
                isBackendImage: true,     //Es imagen de backend
                source: 'backend-confirmed', //Confirmada en backend
                fromSitAdd: true
            })),
            timestamp: Date.now(),
            totalImages: savedImages.length
        };

        //GUARDAR para mostrar en fotos-index
        localStorage.setItem('newUploadedImages', JSON.stringify(dataToTransfer));

        //REDIRECCIÓN AUTOMÁTICA
        //showNotification(`${savedImages.length} imagen(es) guardadas. Redirigiendo...`, 'success', 1500);

        setTimeout(() => {
            console.log('Redirigiendo a fotos-index...');
            window.location.href = "{{ route('fotos-index') }}";
        }, 1500);
    }

    // Lightbox functions
    function openLightbox(imageUrl, description, type) {
        console.log('Abriendo lightbox:', { imageUrl, description, type });

        const lightbox = document.getElementById('imageLightbox');
        const lightboxImage = document.getElementById('lightboxImage');
        const previewDescripcion = document.getElementById('previewDescripcion');
        const previewTipo = document.getElementById('previewTipo');

        if (lightbox && lightboxImage) {
            lightboxImage.src = imageUrl;
            lightboxImage.alt = description || 'Imagen';

            if (previewDescripcion) {
                previewDescripcion.textContent = description || 'Sin descripción';
            }

            if (previewTipo) {
                previewTipo.textContent = type || 'Sin tipo';
            }

            lightbox.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            console.log('Lightbox abierto correctamente');
        } else {
            console.error('No se encontraron elementos del lightbox');
        }
    }

    function closeLightbox() {
        const lightbox = document.getElementById('imageLightbox');
        if (lightbox) {
            lightbox.style.display = 'none';
            document.body.style.overflow = '';
            console.log('Lightbox cerrado');
        }
    }

    function downloadImage() {
        const lightboxImage = document.getElementById('lightboxImage');
        if (lightboxImage && lightboxImage.src) {
            const link = document.createElement('a');
            link.href = lightboxImage.src;
            link.download = 'imagen.jpg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            console.log('Descarga iniciada');
        }
    }

    window.openLightbox = openLightbox;
    window.closeLightbox = closeLightbox;
    window.downloadImage = downloadImage;

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

            //VALIDAR archivos antes de procesar
            const validFiles = Array.from(files).filter(file => {
                if (!file.type.startsWith('image/')) {
                    showNotification(`"${file.name}" no es una imagen válida`, 'error');
                    return false;
                }
                if (file.size > 10 * 1024 * 1024) {
                    showNotification(`"${file.name}" es demasiado grande (máx 10MB)`, 'error');
                    return false;
                }
                return true;
            });

            if (validFiles.length === 0) {
                showNotification('No hay archivos válidos para procesar', 'warning');
                return;
            }

            console.log(`${validFiles.length} archivos válidos de ${files.length} total`);

            // Mostrar estado de carga
            const uploadBtn = source === 'camera'
                ? document.getElementById('cameraUpload')
                : document.getElementById('fileUpload');

            setUploadState(uploadBtn, 'uploading');

            //PROCESAR ARCHIVOS DE FORMA ASÍNCRONA Y ROBUSTA
            const imageDataArray = [];
            let processedCount = 0;
            let hasErrors = false;

            // Mostrar progreso inicial
            //showNotification(`Procesando ${validFiles.length} archivo(s)...`, 'info', 2000);

            //PROCESAR CADA ARCHIVO CON MANEJO DE ERRORES
            validFiles.forEach((file, index) => {
                const reader = new FileReader();

                reader.onload = function(e) {
                    console.log(`Archivo ${index + 1}/${validFiles.length} leído: ${file.name}`);

                    const imageData = {
                        id: 'temp_' + Date.now() + '_' + index,
                        name: file.name,
                        size: file.size,
                        type: file.type,
                        base64: e.target.result,
                        file: file,
                        timestamp: Date.now(),
                        index: index
                    };

                    imageDataArray.push(imageData);
                    processedCount++;

                    console.log(`Progreso: ${processedCount}/${validFiles.length}`);

                    //VERIFICAR SI TODOS LOS ARCHIVOS ESTÁN PROCESADOS
                    if (processedCount === validFiles.length) {
                        console.log('Todos los archivos procesados');

                        if (imageDataArray.length > 0) {
                            //ORDENAR por índice para mantener orden original
                            imageDataArray.sort((a, b) => a.index - b.index);

                            console.log(`Orden de imágenes confirmado: ${imageDataArray.map(img => img.name).join(', ')}`);

                            // Pequeño delay para asegurar que todo esté listo
                            setTimeout(() => {
                                showBatchImageModal(imageDataArray, uploadBtn);
                            }, 200);
                        } else {
                            console.error('No se procesaron imágenes válidas');
                            showNotification('No se pudo procesar ningún archivo válido', 'error');
                            setUploadState(uploadBtn, 'normal');
                        }
                    }
                };

                reader.onerror = function(error) {
                    console.error(`Error leyendo archivo ${index + 1} (${file.name}):`, error);
                    showNotification(`Error leyendo ${file.name}`, 'error', 2000);

                    hasErrors = true;
                    processedCount++;

                    //CONTINUAR AUNQUE HAYA ERRORES
                    if (processedCount === validFiles.length) {
                        if (imageDataArray.length > 0) {
                            console.log(`Procesamiento completado con errores. ${imageDataArray.length} imágenes válidas.`);

                            // Ordenar y mostrar las imágenes que sí se procesaron
                            imageDataArray.sort((a, b) => a.index - b.index);

                            setTimeout(() => {
                                showBatchImageModal(imageDataArray, uploadBtn);
                            }, 200);
                        } else {
                            console.error('No se pudo procesar ningún archivo');
                            showNotification('No se pudo procesar ningún archivo', 'error');
                            setUploadState(uploadBtn, 'normal');
                        }
                    }
                };

                //INICIAR LECTURA CON LOG
                console.log(`Iniciando lectura del archivo ${index + 1}: ${file.name} (${(file.size / 1024 / 1024).toFixed(2)}MB)`);
                reader.readAsDataURL(file);
            });

            //TIMEOUT DE SEGURIDAD
            setTimeout(() => {
                if (processedCount < validFiles.length) {
                    console.error(`Timeout: Solo se procesaron ${processedCount}/${validFiles.length} archivos`);

                    if (imageDataArray.length > 0) {
                        showNotification(`Solo se procesaron ${imageDataArray.length} de ${validFiles.length} archivos`, 'warning');
                        imageDataArray.sort((a, b) => a.index - b.index);
                        showBatchImageModal(imageDataArray, uploadBtn);
                    } else {
                        showNotification('Timeout: No se pudo procesar ningún archivo', 'error');
                        setUploadState(uploadBtn, 'normal');
                    }
                }
            }, 15000); // 15 segundos timeout
        }

        // ================= FUNCIÓN: Modal para lote de imágenes ===================
        function showBatchImageModal(imageDataArray, uploadBtn) {
            console.log(`Abriendo modal para ${imageDataArray.length} imágenes`);

            //VALIDACIÓN ADICIONAL
            if (!imageDataArray || imageDataArray.length === 0) {
                console.error('No hay imágenes para procesar en modal');
                showNotification('No hay imágenes para procesar', 'warning');
                setUploadState(uploadBtn, 'normal');
                return;
            }

            const modalEl = document.getElementById('imageDataModal');
             if (!modalEl) {
                console.error('Modal no encontrado');
                showNotification('Error del sistema: Modal no disponible', 'error');
                setUploadState(uploadBtn, 'normal');
                return;
            }

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
            const newSaveBtn = saveBtn.cloneNode(true);
            saveBtn.parentNode.replaceChild(newSaveBtn, saveBtn);

            //CONFIGURAR NUEVO EVENT LISTENER
            newSaveBtn.addEventListener('click', async function handleBatchSave() {
                const descripcionVal = descripcionInput ? descripcionInput.value.trim() : '';
                const tipoFotografia = tipoSelect ? tipoSelect.value : '';

                // Validación
                if (!descripcionVal || !tipoFotografia) {
                    showNotification("Por favor complete todos los campos", 'warning');
                    return;
                }

                console.log(`Iniciando procesamiento de ${imageDataArray.length} imágenes con:`, {
                    descripcion: descripcionVal,
                    tipo: tipoFotografia
                });

                // Desactivar botón durante procesamiento
                newSaveBtn.disabled = true;
                newSaveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Procesando...';

                try {
                    const savedImages = [];
                    const ordenSitValue = document.getElementById('ordenSitValue')?.textContent || 'N/A';

                    if (!ordenSitValue || ordenSitValue === 'N/A') {
                        throw new Error('Debe buscar una orden SIT válida antes de subir imágenes');
                    }

                    //PROCESAR SECUENCIALMENTE PARA EVITAR SOBRECARGA
                    for (let i = 0; i < imageDataArray.length; i++) {
                        const imageData = imageDataArray[i];

                        //showNotification(`Guardando imagen ${i + 1} de ${imageDataArray.length}...`, 'info', 1000);

                        try {
                            // Convertir base64 a File
                            const response = await fetch(imageData.base64);
                            const blob = await response.blob();
                            const fileName = imageData.name || `imagen_${Date.now()}_${i}.jpg`;
                            const file = new File([blob], fileName, { type: blob.type });

                            // Crear FormData
                            const formData = new FormData();
                            formData.append('imagen', file);
                            formData.append('orden_sit', ordenSitValue);
                            formData.append('po', generatePONumber());
                            formData.append('oc', generateOCNumber());
                            formData.append('descripcion', descripcionVal);
                            formData.append('tipo', tipoFotografia.toUpperCase());
                            formData.append('origen_vista', 'fotos-sit-add');
                            formData.append('timestamp', new Date().toISOString());
                             formData.append('batch_index', i.toString());
                            formData.append('batch_total', imageDataArray.length.toString());

                            console.log(`FormData para imagen ${i + 1}:`);
                            for (let pair of formData.entries()) {
                                if (pair[1] instanceof File) {
                                    console.log(`${pair[0]}: File(${pair[1].name}, ${pair[1].size} bytes, ${pair[1].type})`);
                                } else {
                                    console.log(`${pair[0]}: ${pair[1]}`);
                                }
                            }

                            // Subir al backend
                            const backendResponse = await uploadToBackend(formData);

                            if (backendResponse.success) {
                                savedImages.push({
                                    id: backendResponse.data.id,
                                    url: backendResponse.data.imagen_url,
                                    orden_sit: backendResponse.data.orden_sit,
                                    po: backendResponse.data.po,
                                    oc: backendResponse.data.oc,
                                    descripcion: backendResponse.data.descripcion,
                                    tipo: backendResponse.data.tipo,
                                    created_at: backendResponse.data.created_at,
                                    source: 'backend-real',
                                    saved: true
                                });
                                console.log(`Imagen ${i + 1}/${imageDataArray.length} guardada: ID ${backendResponse.data.id}`);

                                // Delay mas largo entre subidas
                                if (i < imageDataArray.length - 1) {
                                    await new Promise(resolve => setTimeout(resolve, 2000)); // 2 segundos
                                }
                            } else {
                                throw new Error(backendResponse.message || 'Error en respuesta');
                            }

                        } catch (imageError) {
                            console.error(`Error procesando imagen ${i + 1}:`, imageError);
                            showNotification(`Error en imagen ${i + 1}: ${imageError.message}`, 'error', 2000);
                            // Continuar con las siguientes imágenes

                            // Continuar con delay
                            await new Promise(resolve => setTimeout(resolve, 1000)); // 1 segundo
                        }
                    }

                    //FINALIZACIÓN
                    if (savedImages.length > 0) {
                        uploadedImages.push(...savedImages);
                        modal.hide();

                        // Actualizar vista previa con la primera imagen subida
                        updateCardPreview(savedImages[0]);

                        console.log(`Procesamiento completado: ${savedImages.length}/${imageDataArray.length} imágenes guardadas`);

                        // Mostrar resultado
                        if (savedImages.length === imageDataArray.length) {
                            //showNotification(`${savedImages.length} imagen(es) guardada(s) correctamente`, 'success', 2000);
                        } else {
                            //showNotification(`${savedImages.length} de ${imageDataArray.length} imagen(es) guardada(s)`, 'warning', 3000);
                        }

                        // Guardado automático y redirección
                        setTimeout(() => {
                            guardarFoto(savedImages);
                        }, 1000);

                    } else {
                        throw new Error('No se pudo guardar ninguna imagen');
                    }

                } catch (error) {
                    console.error('Error durante el procesamiento:', error);
                    showNotification(`Error general: ${error.message}`, 'error', 5000);
                } finally {
                    //CLEANUP
                    newSaveBtn.disabled = false;
                    newSaveBtn.innerHTML = 'Guardar';
                    setUploadState(uploadBtn, 'normal');
                }
            });

            // Mostrar modal
           modal.show();
        }

        // =======================================================================================
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

            //CREAR FormData para enviar al backend
            const formData = new FormData();
            formData.append('imagen', file);
            formData.append('orden_sit', document.getElementById('ordenSitValue').textContent || '');
            formData.append('po', generatePONumber());
            formData.append('oc', generateOCNumber());
            formData.append('timestamp', new Date().toISOString());

            //Mostrar modal para datos adicionales ANTES de enviar
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

                //AGREGAR campos adicionales al FormData
                formData.append('descripcion', descripcion);
                formData.append('tipo', tipoFotografia.toUpperCase());

                modal.hide();
                saveBtn.removeEventListener('click', handleSave);

                //ENVIAR AL BACKEND con AJAX
                uploadToBackend(formData)
                    .then(response => {
                        console.log('Imagen subida al backend:', response);

                        //GUARDAR para transferir a fotos-index
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

    //NUEVA FUNCIÓN: Subir al backend
    function uploadToBackend(formData) {
        return new Promise((resolve, reject) => {
            console.log('Subiendo imagen al backend...');

            //VERIFICAR datos antes del envío
            console.log('FormData contents:');
            for (let pair of formData.entries()) {
                console.log(`${pair[0]}: ${pair[1]}`);
            }

            //AGREGAR campos obligatorios que podrían faltar
            if (!formData.has('descripcion')) {
                formData.append('descripcion', 'Imagen subida desde fotos-sit-add');
            }

            if (!formData.has('tipo')) {
                formData.append('tipo', 'MUESTRA');
            }

            if (!formData.has('orden_sit')) {
                const ordenSit = document.getElementById('ordenSitValue')?.textContent || 'N/A';
                formData.append('orden_sit', ordenSit);
            }

            //ENVÍO CON MANEJO MEJORADO DE ERRORES
            $.ajax({
                url: '/api/fotografias',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 30000, // 30 segundos timeout
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Origen-Vista': 'fotos-sit-add',
                    'Accept': 'application/json'
                },
                beforeSend: function() {
                    console.log('Enviando imagen al servidor...');
                },
                success: function(response) {
                    console.log('Respuesta del servidor:', response);

                    if (response.success && response.data) {
                        //ESTRUCTURAR respuesta correctamente
                        resolve({
                            success: true,
                            data: {
                                id: response.data.id,
                                imagen_url: response.data.imagen_url || response.data.url,
                                orden_sit: response.data.orden_sit,
                                po: response.data.po,
                                oc: response.data.oc,
                                descripcion: response.data.descripcion,
                                tipo: response.data.tipo,
                                created_at: response.data.created_at,
                                fecha_subida: response.data.fecha_subida || response.data.created_at
                            }
                        });
                    } else {
                        reject(new Error(response.message || 'Respuesta inválida del servidor'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });

                    let errorMessage = 'Error de conexión con el servidor';

                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            // Error de validación Laravel
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage = errors.join(', ');
                        }
                    } else if (xhr.status === 422) {
                        errorMessage = 'Error de validación: Verifique que todos los campos estén completos';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Error interno del servidor';
                    } else if (xhr.status === 0) {
                        errorMessage = 'No se pudo conectar al servidor';
                    }

                    reject(new Error(errorMessage));
                }
            });
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

    // Inicialización principal fotos-sit-add
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

        console.log('Sistema fotos-sit-add listo');
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
