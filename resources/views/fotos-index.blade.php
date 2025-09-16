@extends('layout/plantilla')

@section('tituloPagina', 'Index fotos')

@section('contenido')

<!-- Contenido de pagina -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-camera me-2 text-primary"></i>
                    Fotografías de Prendas
                </h2>
                <p class="text-muted mb-0">
                    Gestión y visualización de fotografías de productos
                </p>
            </div>

            <!-- Botones de accion -->
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <!-- Botones de Subida de Imágenes -->
                <div class="upload-section me-3">
                    <label class="form-label mb-2 d-block text-center">
                        <small class="text-muted">Subir Imágenes</small>
                    </label>
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

                <!-- Separador visual -->
                <div class="vr me-3" style="height: 60px;"></div>

                <button class="btn btn-success" onclick="exportAll()">
                    <i class="fas fa-download me-1"></i>
                    Exportar todo
                </button>

                <button class="btn btn-danger" onclick="showFilters()">
                    <i class="fas fa-filter me-1"></i>
                    Filtros
                </button>

                <div class="btn-group">
                    <button class="btn btn-warning dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-columns me-1"></i>
                        Columnas
                    </button>
                    <ul class="dropdown-menu" id="columnsDropdown">
                        <li><label class="dropdown-item"><input type="checkbox" checked class="me-2" data-column="imagen">Imagen</label></li>
                        <li><label class="dropdown-item"><input type="checkbox" checked class="me-2" data-column="orden-sit">Orden SIT</label></li>
                        <li><label class="dropdown-item"><input type="checkbox" checked class="me-2" data-column="po">P.O</label></li>
                        <li><label class="dropdown-item"><input type="checkbox" checked class="me-2" data-column="oc">O.C</label></li>
                        <li><label class="dropdown-item"><input type="checkbox" checked class="me-2" data-column="descripcion">Descripción</label></li>
                        <li><label class="dropdown-item"><input type="checkbox" checked class="me-2" data-column="tipo-fotografia">Tipo Fotografía</label></li>
                        <li><label class="dropdown-item"><input type="checkbox" checked class="me-2" data-column="acciones">Acciones</label></li>
                    </ul>
                </div>

                <button class="btn btn-success" onclick="exportSelected()">
                    <i class="fas fa-file-export me-1"></i>
                    Exportar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filtro datos por fecha -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Fecha creación de registro</label>
                        <div class="input-group">
                            <input type="date" class="form-control" id="fechaInicio">
                            <span class="input-group-text">-</span>
                            <input type="date" class="form-control" id="fechaFin">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Buscar Ord. SIT / P.O / O.C</label>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Buscar..." id="searchInput">
                            <button class="btn btn-primary" onclick="searchRecords()">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="clearSearch()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-info w-100" onclick="applyDateFilter()">
                            <i class="fas fa-calendar-check me-1"></i>
                            Aplicar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Datos Tabla -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover images-table">
                        <thead class="table-light">
                            <tr>
                                <th data-column="imagen">
                                    <i class="fas fa-image me-1"></i>
                                    IMAGEN
                                </th>
                                <th data-column="orden-sit">
                                    <i class="fas fa-hashtag me-1"></i>
                                    ORDEN SIT
                                </th>
                                <th data-column="po">
                                    <i class="fas fa-file-alt me-1"></i>
                                    P.O
                                </th>
                                <th data-column="oc">
                                    <i class="fas fa-clipboard me-1"></i>
                                    O.C
                                </th>
                                <th data-column="descripcion">
                                    <i class="fas fa-align-left me-1"></i>
                                    DESCRIPCIÓN
                                </th>
                                <th data-column="tipo-fotografia">
                                    <i class="fas fa-camera me-1"></i>
                                    TIPO FOTOGRAFÍA
                                </th>
                                <th data-column="acciones">
                                    <i class="fas fa-cogs me-1"></i>
                                    ACCIONES
                                </th>
                            </tr>
                            <!-- Fila de filtros -->
                            <tr class="bg-light">
                                <td data-column="imagen">
                                    <input type="text" class="form-control form-control-sm" placeholder="Buscar">
                                </td>
                                <td data-column="orden-sit">
                                    <input type="text" class="form-control form-control-sm" placeholder="Buscar">
                                </td>
                                <td data-column="po">
                                    <input type="text" class="form-control form-control-sm" placeholder="Buscar">
                                </td>
                                <td data-column="oc">
                                    <input type="text" class="form-control form-control-sm" placeholder="Buscar">
                                </td>
                                <td data-column="descripcion">
                                    <input type="text" class="form-control form-control-sm" placeholder="Buscar">
                                </td>
                                <td data-column="tipo-fotografia">
                                    <input type="text" class="form-control form-control-sm" placeholder="Buscar">
                                </td>
                                <td data-column="acciones">
                                    <input type="text" class="form-control form-control-sm" placeholder="Buscar">
                                </td>
                            </tr>
                        </thead>
                        <tbody id="imagesTableBody">
                            <!-- Fila de ejemplo 1 - CORREGIDA -->
                            <tr data-image-id="img_example_1">
                                <td data-column="imagen">
                                    <img src="images/shirt-blue.jpg"
                                         alt="Camisa azul"
                                         class="img-thumbnail preview-image"
                                         style="width: 60px; height: 60px; cursor: pointer;"
                                         onclick="openImageLightbox(this.src, this.alt, 'Camisa azul clásica', 'PRENDA FINAL')">
                                </td>
                                <td data-column="orden-sit">10060482</td>
                                <td data-column="po">6000101385</td>
                                <td data-column="oc">4200020624</td>
                                <td data-column="descripcion">CAM FORM UNIC</td>
                                <td data-column="tipo-fotografia">PRENDA FINAL</td>
                                <td data-column="acciones">
                                    <button class="btn btn-danger btn-sm me-1" onclick="deleteImage(this)" title="Eliminar imagen">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                    <button class="btn btn-warning btn-sm me-1" onclick="editImage(this)" title="Editar información">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-info btn-sm comment-btn" onclick="openCommentsModal(this)" title="Ver/Agregar comentarios">
                                        <i class="fas fa-comments"></i>
                                        <span class="comment-count" data-count="2">2</span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Fila de ejemplo 2 - CORREGIDA -->
                            <tr data-image-id="img_example_2">
                                <td data-column="imagen">
                                    <img src="images/shirt-green.jpg"
                                         alt="Camisa verde"
                                         class="img-thumbnail preview-image"
                                         style="width: 60px; height: 60px; cursor: pointer;"
                                         onclick="openImageLightbox(this.src, this.alt, 'Camisa verde clásica', 'MUESTRA')">
                                </td>
                                <td data-column="orden-sit">10001600</td>
                                <td data-column="po">3000001545</td>
                                <td data-column="oc">-</td>
                                <td data-column="descripcion">Muestra Validación</td>
                                <td data-column="tipo-fotografia">MUESTRA</td>
                                <td data-column="acciones">
                                    <button class="btn btn-danger btn-sm me-1" onclick="deleteImage(this)" title="Eliminar imagen">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                    <button class="btn btn-warning btn-sm me-1" onclick="editImage(this)" title="Editar información">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-info btn-sm comment-btn" onclick="openCommentsModal(this)" title="Ver/Agregar comentarios">
                                        <i class="fas fa-comments"></i>
                                        <span class="comment-count" data-count="0"></span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Fila de ejemplo 3 - CORREGIDA -->
                            <tr data-image-id="img_example_3">
                                <td data-column="imagen">
                                    <img src="images/shirt-white.jpg"
                                         alt="Camisa Blanca"
                                         class="img-thumbnail preview-image"
                                         style="width: 60px; height: 60px; cursor: pointer;"
                                         onclick="openImageLightbox(this.src, this.alt, 'Camisa polo', 'PRENDA FINAL')">
                                </td>
                                <td data-column="orden-sit">10047396</td>
                                <td data-column="po">6000081373</td>
                                <td data-column="oc">4000065347</td>
                                <td data-column="descripcion">POLO BUSINESS</td>
                                <td data-column="tipo-fotografia">PRENDA FINAL</td>
                                <td data-column="acciones">
                                    <button class="btn btn-danger btn-sm me-1" onclick="deleteImage(this)" title="Eliminar imagen">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </button>
                                    <button class="btn btn-warning btn-sm me-1" onclick="editImage(this)" title="Editar información">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-info btn-sm comment-btn" onclick="openCommentsModal(this)" title="Ver/Agregar comentarios">
                                        <i class="fas fa-comments"></i>
                                        <span class="comment-count" data-count="0"></span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginacion -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <span class="text-muted">Mostrando registros del 1 al 3 de un total de 3</span>
                    </div>
                    <nav>
                        <ul class="pagination mb-0">
                            <li class="page-item disabled">
                                <span class="page-link">Anterior</span>
                            </li>
                            <li class="page-item active">
                                <span class="page-link">1</span>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">2</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">3</a>
                            </li>
                            <li class="page-item">
                                <a class="page-link" href="#">Siguiente</a>
                            </li>
                        </ul>
                    </nav>
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
                        <strong>Descripción:</strong>
                        <p id="lightboxDescription">-</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Tipo:</strong>
                        <p id="lightboxType">-</p>
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

<!-- Container de notificaciones -->
<div id="notificationContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

<!-- MODAL DE COMENTARIOS -->
<div class="modal fade" id="commentsModal" tabindex="-1" aria-labelledby="commentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentsModalLabel">
                    <i class="fas fa-comments me-2"></i>
                    Comentarios y Observaciones
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Información de la imagen -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <img id="commentImagePreview" src="" alt="" class="img-fluid rounded border">
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Orden SIT:</strong>
                                <p id="commentOrdenSit" class="mb-1">-</p>
                            </div>
                            <div class="col-md-6">
                                <strong>P.O:</strong>
                                <p id="commentPO" class="mb-1">-</p>
                            </div>
                            <div class="col-md-6">
                                <strong>O.C:</strong>
                                <p id="commentOC" class="mb-1">-</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Tipo:</strong>
                                <p id="commentTipo" class="mb-1">-</p>
                            </div>
                            <div class="col-12">
                                <strong>Descripción:</strong>
                                <p id="commentDescripcion" class="mb-0">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Agregar nuevo comentario -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>
                            Agregar Nuevo Comentario
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="commentForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipo de Observación</label>
                                    <select class="form-select" id="commentType" required>
                                        <option value="">Seleccionar tipo</option>
                                        <option value="quality">Calidad</option>
                                        <option value="technical">Técnico</option>
                                        <option value="production">Producción</option>
                                        <option value="design">Diseño</option>
                                        <option value="general">General</option>
                                        <option value="urgent">Urgente</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Prioridad</label>
                                    <select class="form-select" id="commentPriority" required>
                                        <option value="low">Baja</option>
                                        <option value="medium" selected>Media</option>
                                        <option value="high">Alta</option>
                                        <option value="critical">Crítica</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Comentario/Observación</label>
                                    <textarea class="form-control" id="commentText" rows="3"
                                              placeholder="Escribe tu comentario o observación aquí..." required></textarea>
                                    <div class="form-text">
                                        <small><span id="charCount">0</span>/500 caracteres</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Agregar Comentario
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary ms-2" onclick="clearCommentForm()">
                                        <i class="fas fa-eraser me-1"></i>
                                        Limpiar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de comentarios existentes -->
                <div class="card">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Comentarios Existentes
                            <span class="badge bg-secondary ms-2" id="totalCommentsCount">0</span>
                        </h6>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="sortComments('newest')" title="Más recientes primero">
                                <i class="fas fa-sort-amount-down"></i>
                            </button>
                            <button class="btn btn-outline-primary" onclick="sortComments('oldest')" title="Más antiguos primero">
                                <i class="fas fa-sort-amount-up"></i>
                            </button>
                            <button class="btn btn-outline-primary" onclick="filterCommentsByPriority()" title="Filtrar por prioridad">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="commentsList" class="comments-list">
                            <!-- Los comentarios se cargarán aquí dinámicamente -->
                            <div class="text-center text-muted p-4" id="noCommentsMessage">
                                <i class="fas fa-comment-slash fa-2x mb-2"></i>
                                <p>No hay comentarios para esta imagen</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cerrar
                </button>
                <button type="button" class="btn btn-success" onclick="exportComments()">
                    <i class="fas fa-file-export me-1"></i>
                    Exportar Comentarios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Meta tag para el usuario actual (para detección automática) -->
<meta name="current-user" content="{{ auth()->user()->name ?? 'Usuario Sistema' }}">

{{-- ARCHIVO Javascript para manejo de filtrado por fechas --}}
<script src="{{ asset('js/fotos-index.js') }}"></script>
<script src="{{ asset('js/comentarios.js') }}"></script>

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

                // Agregar imágenes a la tabla
                results.forEach(imageData => {
                    addImageToTable(imageData);
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
            const formData = new FormData();
            formData.append('image', file);
            formData.append('timestamp', new Date().toISOString());

            // Simular subida (reemplazar con tu endpoint real)
            setTimeout(() => {
                // Crear URL temporal para vista previa
                const imageUrl = URL.createObjectURL(file);

                resolve({
                    id: Date.now() + Math.random(),
                    url: imageUrl,
                    name: file.name,
                    size: file.size,
                    uploadDate: new Date().toISOString(),
                    ordenSit: generateOrderNumber(),
                    po: generatePONumber(),
                    oc: generateOCNumber(),
                    descripcion: 'Imagen subida',
                    tipoFotografia: 'SUBIDA MANUAL'
                });
            }, 1000 + Math.random() * 2000); // Simular tiempo de subida variable
        });
    }

    function addImageToTable(imageData) {
        const tableBody = document.getElementById('imagesTableBody');
        if (!tableBody) return;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td data-column="imagen">
                <img src="${imageData.url}"
                    alt="${imageData.name}"
                    class="img-thumbnail preview-image"
                    style="width: 60px; height: 60px; cursor: pointer;"
                    onclick="openImageLightbox('${imageData.url}', '${imageData.name}', '${imageData.descripcion}', '${imageData.tipoFotografia}')">
            </td>
            <td data-column="orden-sit">${imageData.ordenSit}</td>
            <td data-column="po">${imageData.po}</td>
            <td data-column="oc">${imageData.oc}</td>
            <td data-column="descripcion">${imageData.descripcion}</td>
            <td data-column="tipo-fotografia">
                <span class="badge bg-info">${imageData.tipoFotografia}</span>
            </td>
            <td data-column="acciones">
                <button class="btn btn-danger btn-sm me-1" onclick="deleteImage(this)">
                    <i class="fas fa-trash"></i> Eliminar
                </button>
                <button class="btn btn-warning btn-sm me-1" onclick="editImage(this)">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button class="btn btn-success btn-sm" onclick="downloadImageFromRow(this)">
                    <i class="fas fa-comment"></i>
                </button>
            </td>
        `;

        // Agregar al inicio de la tabla
        tableBody.insertBefore(row, tableBody.firstChild);

        // Añadir animación
        row.style.opacity = '0';
        row.style.transform = 'translateY(-10px)';

        setTimeout(() => {
            row.style.transition = 'all 0.5s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, 100);
    }

    function setUploadState(button, state) {
        if (!button) return;

        // Remover clases anteriores
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
            // Prevenir comportamiento por defecto
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                btn.addEventListener(eventName, preventDefaults, false);
            });

            // Highlight en drag over
            ['dragenter', 'dragover'].forEach(eventName => {
                btn.addEventListener(eventName, () => btn.classList.add('active'), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                btn.addEventListener(eventName, () => btn.classList.remove('active'), false);
            });

            // Handle drop
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

    // Utility functions para generar números
    function generateOrderNumber() {
        return '100' + Math.floor(Math.random() * 90000 + 10000);
    }

    function generatePONumber() {
        return '6000' + Math.floor(Math.random() * 900000 + 100000);
    }

    function generateOCNumber() {
        return '4200' + Math.floor(Math.random() * 9000000 + 1000000);
    }

    function downloadImageFromRow(button) {
        const row = button.closest('tr');
        const img = row.querySelector('img');
        if (img) {
            const link = document.createElement('a');
            link.href = img.src;
            link.download = img.alt || 'imagen';
            link.click();
            showNotification('Descarga iniciada', 'success');
        }
    }

    // Agregar a la inicialización principal
    document.addEventListener("DOMContentLoaded", function() {
        // ... otras inicializaciones
        initializeUploadButtons();
    });
</>


@endsection
