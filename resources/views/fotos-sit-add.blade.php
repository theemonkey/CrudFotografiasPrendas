@extends('layout/plantilla')

@section('tituloPagina', 'Agregar Foto a Orden SIT')

@section('contenido')


{{--INDICADOR DE ROL --}}
<script>
    const isAdmin = true; // false -> usuario normal, true -> administrador
</script>

<div class="container mt-4">
    {{-- Header con información de permisos --}}
   <h3 class="mb-3">Agregar fotos de la prenda</h3>

    <!-- Buscar Orden SIT -->
    <div class="mb-3">
       <div class="input-group">
            <input type="text"
                id="ordenSitInput"
                class="form-control"
                placeholder="Buscar orden SIT"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')">

            <button id="limpiarBoton" class="btn btn-outline-danger" style="display: none;">
                <i class="fas fa-times"></i>
            </button>

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

</div>

{{-- AGREGAR MINI CARD DE HISTORIAL --}}
<div id="historialFotosCard" class="card p-3 mb-3" style="display:none;">
    <h6 class="mb-3">
        <i class="fas fa-history me-2 text-muted"></i>
        Historial de fotos cargadas
    </h6>

    <div id="historialContainer" class="row g-2">
        <!-- Aquí se mostrarán las miniaturas dinámicamente -->
    </div>

    <div id="historialEmpty" class="text-center text-muted py-3" style="display: block;">
        <i class="fas fa-camera me-2"></i>
        No hay fotos cargadas para esta orden
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
              <option value="Validacion AC">Validación AC</option>
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

                        //Comportamiento según ROL
                        if (isAdmin) {
                            //Redirección directa a tabla
                            redirectToTableAdmin(primeraFoto.orden_sit);
                        } else {
                            //Usuario normal -> mostrar interfaz de subida
                            mostrarOrdenExistente(primeraFoto, fotografias.length);
                        }
                    } else {
                        //ORDEN NO EXISTE
                        if (isAdmin) {
                            //Redirección directa a tabla
                            redirectToTableAdmin(value);
                        } else {
                            //Usuario normal -> permitir crear nueva orden
                            mostrarOrdenNueva(value);
                        }
                    }
                } else {
                    console.error('Error en respuesta:', response.message);

                    if (isAdmin) {
                        //Redirección directa a tabla
                        redirectToTableAdmin(value);
                    } else {
                    mostrarOrdenNueva(value); // Fallback a nueva orden
                }
              }
            },
            error: function(xhr, status, error) {
                console.error('Error buscando orden:', error);

                if (isAdmin) {
                    //Redirección directa a tabla
                    redirectToTableAdmin(value);
                } else {
                    mostrarOrdenNueva(value); // Fallback a nueva orden
                }
            }
        });
    }

    //===>> Función redirección para administradores <<===
    function redirectToTableAdmin(ordenSit) {
        showNotification(`Redirigiendo a la tabla de la orden ${ordenSit}...`, 'info', 1500);

        setTimeout(() => {
                 window.location.href = `{{ route('fotos-index') }}?orden_sit=${encodeURIComponent(ordenSit)}&admin_access=true`;
        }, 1500);
    }

    //NUEVA FUNCIÓN: Mostrar orden existente
    function mostrarOrdenExistente(primeraFoto, totalFotos) {
        const ordenSitValue = document.getElementById('ordenSitValue');
        const tipoOrden = document.getElementById('tipoOrden');
        const descripcion = document.getElementById('descripcion');
        const prendaPreview = document.getElementById('prendaPreview');
        const ordenSitCard = document.getElementById('ordenSitCard');
        const limpiarBoton = document.getElementById('limpiarBoton');

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

        // AGREGAR: Mostrar botón X
        if (limpiarBoton) {
            limpiarBoton.style.display = 'inline-block';
        }

        // AGREGAR: Mostrar historial si hay imágenes
        if (uploadedImages.length > 0) {
            mostrarHistorialCard();
        }

        //showNotification(`Orden ${primeraFoto.orden_sit} encontrada con ${totalFotos} fotografía(s)`, 'success', 2000);
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

        // AGREGAR: Mostrar botón X
        const limpiarBoton = document.getElementById('limpiarBoton');
        if (limpiarBoton) {
            limpiarBoton.style.display = 'inline-block';
        }

        // AGREGAR: Mostrar historial si hay imágenes
        if (uploadedImages.length > 0) {
            mostrarHistorialCard();
        }

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

    /*=========================================================================================*/
   // Guardar y no redirigir a usuarios normales
    function guardarFoto(savedImages) {
        console.log('Iniciando guardado automático...', savedImages);

        if (!savedImages || savedImages.length === 0) {
            showNotification("No hay imágenes guardadas para procesar", 'warning');
            return;
        }

        //Comportamiento según ROL
        if (isAdmin) {
            //ADMIN: Guardar y redirigir a tabla
            guardarYRedirigirAdmin(savedImages);
        } else {
            //USUARIO NORMAL: Solo mostrar confirmación, No redirigir
            guardarUsuarioNormal(savedImages);
        }
    }

    /*=========================================================================================*/
    //Función para ADMINISTRADORES
    function guardarYRedirigirAdmin(savedImages) {

        //PREPARAR datos
        const dataToTransfer = {
            images: savedImages.map(img => ({
                //Datos del backend (ya subido)
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

        localStorage.setItem('newUploadedImages', JSON.stringify(dataToTransfer));

        //REDIRECCIÓN AUTOMÁTICA
        showNotification(`${savedImages.length} imagen(es) guardadas. Redirigiendo...`, 'success', 1500);

        setTimeout(() => {
            const ordenSit = document.getElementById('ordenSitValue').textContent || 'N/A';
            window.location.href = `{{ route('fotos-index') }}?orden_sit=${encodeURIComponent(ordenSit)}&admin_access=true`;
        }, 1500);
    }

    /*=========================================================================================*/
    //Función para USUARIOS NORMALES
    function guardarUsuarioNormal(savedImages) {
        console.log('👤 USUARIO NORMAL: Guardando sin redirigir');

        // Mostrar confirmación sin redirección
        showNotification(
            `${savedImages.length} imagen(es) guardada(s) correctamente. Puede continuar subiendo más fotos.`,
            'success',
            4000
        );

        // Actualizar historial de fotos
        mostrarHistorialCard();

        // Actualizar la interfaz para permitir más subidas
        resetUploadInterface();

        console.log('Usuario puede continuar subiendo fotos a la misma orden');
    }

    /*=========================================================================================*/
    //Función para RESETEAR interfaz (usuario normal)
    function resetUploadInterface() {
        // Limpiar inputs de archivo
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => input.value = '');

        // Resetear estado de botones
        const uploadButtons = document.querySelectorAll('.upload-btn');
        uploadButtons.forEach(btn => {
            btn.classList.remove('uploading', 'active');
        });

        console.log('Interfaz lista para más uploads');
    }

    /*=========================================================================================*/
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
                        //showNotification(`Solo se procesaron ${imageDataArray.length} de ${validFiles.length} archivos`, 'warning');
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
                const tipoFotografia = tipoSelect ? tipoSelect.value.trim() : '';

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
                        if (isAdmin) {
                            //ADMIN: Guardar y redirigir
                            setTimeout(() => {
                                guardarFoto(savedImages);
                            }, 1000);
                        } else {
                            //USUARIO NORMAL: Solo guardar sin redirigir
                            agregarImagenesHistorial(savedImages);

                            setTimeout(() => {
                                guardarUsuarioNormal(savedImages);
                            }, 1000);
                        }


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
                        // Refrescar paginación
                        setTimeout(() => {
                            if (typeof updatePaginationAfterChange === 'function') {
                                updatePaginationAfterChange('upload success fotos-sit-add');
                            }
                        }, 200);

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
                imageData.tipo
            );

            console.log('Vista previa actualizada');
        }

        // Actualizar información mostrada
        if (descripcion && imageData.descripcion) {
            descripcion.textContent = imageData.descripcion;
        }

        if (tipoOrden && imageData.tipo) {
            tipoOrden.textContent = imageData.tipo;
            tipoOrden.className = "badge badge-color-personalizado";
            tipoSeleccionado = imageData.tipo;
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

    /*=======================================================================================================================*/
    // ===>>> Funciones para el manejo del mini card de historial de imagenes cargadas

    function mostrarHistorialCard() {
        const historialCard = document.getElementById('historialFotosCard');
        if (historialCard && uploadedImages.length > 0) {
            historialCard.style.display = 'block';
            actualizarHistorialVisual();
        } else if (historialCard && uploadedImages.length === 0) {
            historialCard.style.display = 'none';
        }
    }

    function limpiarHistorialVisual() {
        const historialContainer = document.getElementById('historialContainer');
        const historialEmpty = document.getElementById('historialEmpty');

        if (historialContainer) {
            historialContainer.innerHTML = '';
        }

        if (historialEmpty) {
            historialEmpty.style.display = 'block';
        }
    }

    function actualizarHistorialVisual() {
        const historialContainer = document.getElementById('historialContainer');
        const historialEmpty = document.getElementById('historialEmpty');

        if (!historialContainer) return;

        // Limpiar contenido anterior
        historialContainer.innerHTML = '';

        if (uploadedImages.length === 0) {
            if (historialEmpty) {
                historialEmpty.style.display = 'block';
            }
            return;
        }

        // Ocultar mensaje vacío
        if (historialEmpty) {
            historialEmpty.style.display = 'none';
        }

        // Crear miniaturas para cada imagen
        uploadedImages.forEach((imagen, index) => {
            const miniCard = crearMiniaturaImagen(imagen, index);
            historialContainer.appendChild(miniCard);
        });

        console.log(`Historial actualizado: ${uploadedImages.length} imágenes`);
    }

   function crearMiniaturaImagen(imagen, index) {
    const col = document.createElement('div');
    col.className = 'col-6 col-md-4 col-lg-3';

    col.innerHTML = `
        <div class="card position-relative shadow-sm h-100" data-image-index="${index}">
            <div class="position-relative">
                <img src="${imagen.url}"
                    class="card-img-top"
                    style="height: 120px; object-fit: cover; cursor: pointer;"
                    onclick="abrirLightboxHistorial('${imagen.url}', '${imagen.descripcion}', '${imagen.tipo}')"
                    alt="${imagen.descripcion}">

               <!-- Botones de acción en esquina superior derecha -->
                <div class="position-absolute top-0 end-0 m-1" style="z-index: 2;">
                    <!-- Botón eliminar -->
                    <button class="btn btn-danger btn-sm mb-1 d-block"
                            style="padding: 2px 6px; font-size: 10px;"
                            onclick="eliminarImagenHistorial(${index}, event)"
                            title="Eliminar imagen">
                        <i class="fas fa-trash"></i>
                    </button>

                    <!-- Botón editar -->
                    <button class="btn btn-warning btn-sm d-block"
                            style="padding: 2px 6px; font-size: 10px;"
                            onclick="editarImagenHistorial(${index}, event)"
                            title="Editar información">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </div>

            <div class="card-body p-2">
                <p class="card-text small mb-1 text-truncate" title="Orden SIT: ${imagen.orden_sit}">
                    <strong>Orden SIT:</strong> ${imagen.orden_sit}
                </p>
                <p class="card-text small mb-1 text-truncate" title="Tipo: ${imagen.tipo}">
                    <strong>Tipo:</strong> ${imagen.tipo}
                </p>
                <p class="card-text small mb-1 text-truncate" title="Descripción: ${imagen.descripcion}">
                    <strong>Descripción:</strong> ${imagen.descripcion}
                </p>
            </div>
        </div>
    `;

    return col;
}

    function abrirLightboxHistorial(url, descripcion, tipo) {
        console.log('Abriendo lightbox desde historial:', { url, descripcion, tipo });
        openLightbox(url, descripcion, tipo);
    }

    function eliminarImagenHistorial(index, event) {
        event.stopPropagation(); // Evitar que se abra el lightbox

        if (index < 0 || index >= uploadedImages.length) {
            console.error('Índice inválido:', index);
            return;
        }

        const imagen = uploadedImages[index];

        // Confirmar eliminación mediante SweetAlert
        Swal.fire({
            title: '¿Eliminar esta imagen?',
            text: imagen.descripcion,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            focusCancel: true
        }).then((result) => {
            if (result.isConfirmed) {
                console.log(`Eliminando imagen ${index + 1}: ${imagen.descripcion}`);

                // MOSTRAR LOADING SIMPLE
                Swal.fire({
                    title: 'Eliminando...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Eliminar del backend si tiene ID
                if (imagen.id) {
                    eliminarImagenBackend(imagen.id, index);
                } else {
                    // Solo eliminar del array local
                    eliminarImagenLocal(index);
                }
            }
        });
    }

    function eliminarImagenBackend(imagenId, localIndex) {
        console.log(`Eliminando imagen del backend: ID ${imagenId}`);

        $.ajax({
            url: `/api/fotografias/${imagenId}`,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('Imagen eliminada del backend:', response);

                // Eliminar del array local
                eliminarImagenLocal(localIndex);

                // Mostrar success con SweetAlert
                Swal.fire({
                    icon: 'success',
                    title: 'Eliminada',
                    text: 'Imagen eliminada correctamente',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            error: function(xhr, status, error) {
                console.error('Error eliminando imagen:', error);

                // Aún así, eliminar del array local como fallback
                eliminarImagenLocal(localIndex);

                // Mostrar warning con SweetAlert
                Swal.fire({
                    icon: 'warning',
                    title: 'Eliminada localmente',
                    text: 'La imagen se eliminó localmente (error en servidor)',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        });
    }

    function eliminarImagenLocal(index) {
        console.log(`Eliminando imagen local en índice ${index}`);

        // Eliminar del array
        uploadedImages.splice(index, 1);

        // Actualizar interfaz
        actualizarHistorialVisual();

        // Ocultar historial si no hay imágenes
        if (uploadedImages.length === 0) {
            const historialCard = document.getElementById('historialFotosCard');
            if (historialCard) {
                historialCard.style.display = 'none';
            }
        }

        console.log(`Imagen eliminada. Quedan ${uploadedImages.length} imágenes`);
    }

    /*==========================================================================================================================*/
    // Función para modal de edición en historial de fotos cargadas

    function editarImagenHistorial(index, event) {
        event.stopPropagation(); // Evitar que se abra el lightbox

        if (index < 0 || index >= uploadedImages.length) {
            console.error('Índice inválido:', index);
            return;
        }

        const imagen = uploadedImages[index];
        console.log('Editando imagen del historial:', imagen);

        // Modal de edición para fotos cargadas
        mostrarModalEdicionHistorial(imagen, index);
    }

    function mostrarModalEdicionHistorial(imagen, index) {
    // Crear contenido HTML del modal
    const modalHTML = `
        <div class="modal fade" id="editHistorialModal" tabindex="-1" aria-labelledby="editHistorialModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editHistorialModalLabel">
                            <i class="fas fa-edit me-2"></i>
                            Editar Información de la Fotografía
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <!-- Columna izquierda - Imagen -->
                            <div class="col-md-5">
                                <div class="text-center mb-3">
                                    <h6>Vista Previa</h6>
                                    <div class="image-preview-container">
                                        <img id="historialModalImage"
                                             src="${imagen.url}"
                                             alt="${imagen.descripcion}"
                                             class="img-fluid rounded border"
                                             style="max-height: 200px; cursor: pointer;"
                                             onclick="openLightbox('${imagen.url}', '${imagen.descripcion}', '${imagen.tipo}')">
                                    </div>
                                </div>

                                <!-- Información de solo lectura -->
                                <div class="bg-light p-3 rounded">
                                    <h6 class="mb-2">Información de la Fotografía</h6>
                                    <div class="mb-2">
                                        <strong>Orden SIT:</strong>
                                        <span class="text-primary">${imagen.orden_sit}</span>
                                    </div>
                                    <div class="mb-0">
                                        <strong>Fecha:</strong>
                                        <span>${new Date(imagen.created_at).toLocaleDateString('es-ES')}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Columna derecha - Formulario -->
                            <div class="col-md-7">
                                <form id="editHistorialForm">
                                    <div class="mb-3">
                                        <label for="historialTipoFotografia" class="form-label">
                                            <strong>Tipo de Fotografía</strong>
                                        </label>
                                        <select class="form-select" id="historialTipoFotografia" required>
                                            <option value="Muestra" ${imagen.tipo === 'MUESTRA' ? 'selected' : ''}>Muestra</option>
                                            <option value="Validacion AC" ${imagen.tipo === 'VALIDACION AC' || imagen.tipo === 'VALIDACIÓN AC' ? 'selected' : ''}>Validación AC</option>
                                            <option value="Prenda Final" ${imagen.tipo === 'PRENDA FINAL' ? 'selected' : ''}>Prenda Final</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="historialDescripcion" class="form-label">
                                            <strong>Descripción</strong>
                                        </label>
                                        <textarea class="form-control"
                                                  id="historialDescripcion"
                                                  rows="3"
                                                  required
                                                  placeholder="Descripción de la fotografía">${imagen.descripcion}</textarea>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>
                            Cancelar
                        </button>
                        <button type="button" class="btn btn-primary" id="guardarHistorialBtn" onclick="guardarCambiosHistorial(${index})">
                            <i class="fas fa-save me-1"></i>
                            Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

    //INSERTAR modal en el DOM
    const existingModal = document.getElementById('editHistorialModal');
    if (existingModal) {
        existingModal.remove();
    }

    document.body.insertAdjacentHTML('beforeend', modalHTML);

    //MOSTRAR modal
    const modal = new bootstrap.Modal(document.getElementById('editHistorialModal'));
    modal.show();

    console.log('Modal de edición de historial abierto');
}

//FUNCIÓN PARA GUARDAR CAMBIOS DEL HISTORIAL
function guardarCambiosHistorial(index) {
    const nuevoTipo = document.getElementById('historialTipoFotografia').value;
    const nuevaDescripcion = document.getElementById('historialDescripcion').value.trim();
    const historialImage = document.getElementById('historialModalImage');

    // Validar campos
    if (!nuevoTipo || !nuevaDescripcion) {
        showNotification('Por favor complete todos los campos', 'warning');
        return;
    }

    const imagen = uploadedImages[index];

    // Mostrar loading
    const guardarBtn = document.getElementById('guardarHistorialBtn');
    const originalText = guardarBtn.innerHTML;
    guardarBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';
    guardarBtn.disabled = true;

     const updateData = {
        tipo: nuevoTipo.toUpperCase(),
        descripcion: nuevaDescripcion,
        hasNewImage: false
    };

    //Crear FormData para envío al backend
    const formData = new FormData();
    formData.append('tipo', updateData.tipo);
    formData.append('descripcion', updateData.descripcion);
    formData.append('_method', 'PUT');

    //Enviar al backend
    enviarCambiosBackend(imagen.id, formData, index, updateData);
}

/*=========================================================================================================================*/
function enviarCambiosBackend(imagenId, formData, localIndex, updateData) {
    console.log(`Enviando cambios al backend para imagen ID: ${imagenId}`);

    //Agregar método PUT
    formData.append('_method', 'PUT');
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

    $.ajax({
        url: `/api/fotografias/${imagenId}`,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        timeout: 30000,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json',
            'X-Origen-Edicion': 'historial-fotos-sit-add'
        },
        success: function(response) {
            console.log('Cambios guardados en backend:', response);

            //ACTUALIZAR datos locales
            if (localIndex >= 0 && localIndex < uploadedImages.length) {
                uploadedImages[localIndex].tipo = updateData.tipo;
                uploadedImages[localIndex].descripcion = updateData.descripcion;

                console.log('Datos locales actualizados:', uploadedImages[localIndex]);
            }

            //ACTUALIZAR historial visual
            actualizarHistorialVisual();

            // Success
            mostrarEstadoGuardado(true);

            //Cerrar modal luego de éxito
            setTimeout(() => {
                cerrarModalEdicion();
            }, 1500);

        },
        error: function(xhr, status, error) {
            console.error('Error guardando cambios:', {
                status: xhr.status,
                responseText: xhr.responseText,
                error: error
            });

            let errorMessage = 'Error guardando cambios en el servidor';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.status === 422) {
                errorMessage = 'Error de validación: Verifique los datos';
            } else if (xhr.status === 404) {
                errorMessage = 'Imagen no encontrada en el servidor';
            }

            showNotification(`Error: ${errorMessage}`, 'error', 5000);
        }
    });
}

/*=========================================================================================================================*/
//===>>> Funciónes auxiliares
function mostrarEstadoGuardado(success) {
    const guardarBtn = document.getElementById('guardarHistorialBtn');

    if (guardarBtn) {
        guardarBtn.innerHTML = '<i class="fas fa-check me-1"></i>Guardado';
        guardarBtn.disabled = true;
        guardarBtn.classList.add('btn-success');
        guardarBtn.classList.remove('btn-primary');
    }

    showNotification('Cambios guardados correctamente', 'success', 2000);
}

function cerrarModalEdicion() {
    const modal = bootstrap.Modal.getInstance(document.getElementById('editHistorialModal'));
    if (modal) {
        modal.hide();
    }

    // Limpiar modal del DOM
    setTimeout(() => {
        const modalElement = document.getElementById('editHistorialModal');
        if (modalElement) {
            modalElement.remove();
        }
    }, 500);

    console.log('Modal de edición cerrado');
}

    /*=====================================================================================================================*/

    //AGREGAR función para prevenir duplicados:
    function agregarImagenesHistorial(nuevasImagenes) {
        console.log('Agregando imágenes al historial...', nuevasImagenes);

        nuevasImagenes.forEach(imagen => {
            //VERIFICAR si ya existe por ID
            const existeId = uploadedImages.find(img => img.id === imagen.id);
            const existeUrl = uploadedImages.find(img => img.url === imagen.url);

            if (!existeId && !existeUrl) {
                uploadedImages.push(imagen);
                console.log(`Imagen agregada: ID ${imagen.id}`);
            } else {
                console.log(`Imagen duplicada omitida: ID ${imagen.id}`);
            }
        });

        console.log(`Total imágenes en historial: ${uploadedImages.length}`);
    }

    /*========================================================================================================================*/

    // ====>>>>> Inicialización principal fotos-sit-add
    document.addEventListener("DOMContentLoaded", function() {
        initializeUploadButtons();

        const ordenSitInput = document.getElementById('ordenSitInput');
        const searchBoton = document.getElementById('searchBoton');
        const limpiarBoton = document.getElementById('limpiarBoton');

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

        // AGREGAR: Event listener para botón X
        if (limpiarBoton) {
            limpiarBoton.addEventListener('click', function(e) {
                e.preventDefault();
                limpiarOperacion();
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

function limpiarOperacion() {
    console.log('Limpiando operación...');

    // 1. Limpiar inputs de archivo
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => input.value = '');

    // 2. Limpiar campo de búsqueda
    const ordenSitInput = document.getElementById('ordenSitInput');
    if (ordenSitInput) {
        ordenSitInput.value = '';
    }

    // 3. Ocultar cards
    const ordenSitCard = document.getElementById('ordenSitCard');
    const historialCard = document.getElementById('historialFotosCard');
    const limpiarBoton = document.getElementById('limpiarBoton');

    if (ordenSitCard) {
        ordenSitCard.style.display = 'none';
    }

    if (historialCard) {
        historialCard.style.display = 'none';
    }

    if (limpiarBoton) {
        limpiarBoton.style.display = 'none';
    }

    // 4. Limpiar variables globales
    if (typeof uploadedImages !== 'undefined') {
        uploadedImages = [];
    }
    if (typeof currentImageData !== 'undefined') {
        currentImageData = null;
    }
    if (typeof tipoSeleccionado !== 'undefined') {
        tipoSeleccionado = null;
    }

    // 5. Limpiar localStorage
    localStorage.removeItem('newUploadedImages');
    localStorage.removeItem('uploadedImages');

    // 6. Limpiar historial visual
    limpiarHistorialVisual();

    showNotification('Operación cancelada. Listo para empezar de nuevo', 'info', 2000);
    console.log('Operación limpiada correctamente');
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
