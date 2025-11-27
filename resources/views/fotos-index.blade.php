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
            </div>
        </div>
    </div>
</div>

<!-- Filtro datos por fecha -->
<div class="row">
    <div class="container mt-1">
        <div class="row g-3 align-items-end">
        <!-- Rango de Fechas en (izquierda) -->
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label for="rangoFechasComm" class="form-label">Rango de fechas</label>
                        <input id="rangoFechasComm" type="text" class="form-control text-center">
                </div>
            </div>

            <!-- Buscador en (derecha) -->
            <div class="col-12 col-md-6">
                <div class="form-group">
                    <label class="form-label">Búsqueda Ord. SIT / P.O / O.C</label>
                        <div class="input-group">
                            <input type="text" class="form-control text-center" placeholder="Buscar..." id="searchInput">
                            <button id="searchButton" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- =======>>>>>>>>>>>> Datos Tabla <<<<<<<<<========== -->
<div class="row mt-3">
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
                                    <i class="fas me-1"></i>
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
                                    TIPO
                                </th>
                                <th data-column="acciones">
                                    <i class="fas fa-cogs me-1"></i>
                                    ACCIONES
                                </th>
                            </tr>
                            <!-- =======>>>>>>>>>>>> Fila de filtros <<<<<<<<<========== -->
                            <tr class="bg-light">
                                <td data-column="imagen">
                                </td>
                                <td data-column="orden-sit">
                                    <div class="autocomplete-wrapper">

                                    </div>
                                </td>
                                <td data-column="po">
                                    <div class="autocomplete-wrapper">

                                    </div>
                                </td>
                                <td data-column="oc">
                                    <div class="autocomplete-wrapper">

                                    </div>
                                </td>
                                <td data-column="descripcion">
                                    <div class="autocomplete-wrapper">
                                        <input type="text"
                                            class="form-control form-control-sm predictive-filter"
                                            placeholder="Buscar"
                                            id="filterDescripcion"
                                            data-column="descripcion"
                                            autocomplete="off">
                                        <div class="autocomplete-suggestions" id="suggestionsDescripcion"></div>
                                    </div>
                                </td>
                                <td data-column="tipo-fotografia">
                                    <div class="btn-group w-100 dropdown-container">
                                        <button class="btn btn-buscar dropdown-toggle w-100"
                                                type="button"
                                                id="tipoFotografiaDropdown"
                                                data-bs-toggle="dropdown"
                                                data-bs-auto-close="outside"
                                                aria-expanded="false"
                                                style="background-color: white; border: 1px solid #ced4da; text-align: left;">
                                            <i class="fas me-1"></i>
                                            <span id="tipoFotografiaLabel">Buscar</span>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="tipoFotografiaDropdown" id="tipoFotografiaMenu">
                                            <!-- Header del filtro -->
                                            <li class="dropdown-header d-flex align-items-center">
                                                <i class="fas fa-filter me-2"></i>
                                                Filtrar por Tipo
                                            </li>

                                            <!-- Opciones con checkboxes -->
                                            <li>
                                                <label class="dropdown-item d-flex align-items-center" for="filtroMuestra">
                                                        <input type="checkbox"
                                                            class="form-check-input me-2"
                                                            id="filtroMuestra"
                                                            value="MUESTRA"
                                                            onchange="filterByTipoFotografia()">
                                                        <span class="grow">Muestra</span>
                                            </label>
                                            </li>

                                            <li>
                                                <label class="dropdown-item d-flex align-items-center" for="filtroPrendaFinal">
                                                        <input type="checkbox"
                                                            class="form-check-input me-2"
                                                            id="filtroPrendaFinal"
                                                            value="PRENDA FINAL"
                                                            onchange="filterByTipoFotografia()">
                                                        <span class="grow">Prenda Final</span>
                                                </label>
                                            </li>

                                            <li>
                                                <label class="dropdown-item d-flex align-items-center" for="filtroValidacionAC">
                                                        <input type="checkbox"
                                                            class="form-check-input me-2"
                                                            id="filtroValidacionAC"
                                                            value="VALIDACION AC"
                                                            onchange="filterByTipoFotografia()">
                                                        <span class="grow">Validación AC</span>
                                                </label>
                                            </li>

                                            <!-- Separador -->
                                            <li><hr class="dropdown-divider"></li>

                                            <!-- NUEVO: Controles del filtro -->
                                            <li class="dropdown-item-text p-2">
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-sm btn-outline-primary grow"
                                                            onclick="selectAllTipoFotografia()"
                                                            title="Seleccionar todos">
                                                        <i class="fas fa-check-double"></i>
                                                        <span class="d-none d-sm-inline ms-1">Todos</span>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger grow"
                                                            onclick="clearTipoFotografiaFilter()"
                                                            title="Limpiar filtro">
                                                        <i class="fas fa-times"></i>
                                                        <span class="d-none d-sm-inline ms-1">Limpiar</span>
                                                    </button>
                                                </div>
                                            </li>

                                            <!-- NUEVO: Indicador de filtro activo -->
                                            <li id="filterStatusIndicator" class="dropdown-item-text text-center" style="display: none;">
                                                <small class="text-primary">
                                                    <i class="fas fa-filter me-1"></i>
                                                    Filtro activo
                                                </small>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td data-column="acciones">
                                </td>
                            </tr>
                        </thead>
                        <tbody id="imagesTableBody">

                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- ==========>>>>>>>> Lightbox para visualizar imágenes <<<<<<<<<<<========== -->
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

<!-- =========>>>>>>>> Container de notificaciones <<<<<<<<<<========= -->
<div id="notificationContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;"></div>

<!-- ESPACIO PARA MODAL DE COMENTARIOS (==========) -->


<!-- ==========>>>>>>>> Modal para ingresar datos(Descripcion - Tipo fotografia) de la imagen <<<<<<<<<<<========== -->
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
              <option value="MUESTRA">MUESTRA</option>
              <option value="VALIDACION AC">VALIDACIÓN AC</option>
              <option value="PRENDA FINAL">PRENDA FINAL</option>
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

<!-- ==========>>>>>>>> Modal para Editar Información de la Prenda(BTN EDITAR) <<<<<<<<<<<========== -->
<div class="modal fade" id="editImageModal" tabindex="-1" aria-labelledby="editImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editImageModalLabel">
                    <i class="fas fa-edit me-2"></i>
                    Editar Información de la Prenda
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Columna izquierda - Imagen con herramientas -->
                    <div class="col-md-6">
                        <div class="image-edit-container">
                            <!-- Imagen principal -->
                            <div id="imageDisplayContainer" class="image-display-container">
                                <img id="editModalImage" src="" alt="" class="img-fluid">

                                <!-- Overlay para recorte (oculto inicialmente) -->
                                <div id="cropOverlay" class="crop-overlay d-none">
                                    <canvas id="cropCanvas"></canvas>
                                </div>
                            </div>

                            <!-- Herramientas de imagen -->
                            <div class="image-tools mt-3">
                                <div class="btn-group w-100" role="group">
                                    <button type="button" class="btn btn-outline-primary" id="cropImageBtn" title="Recortar Imagen">
                                        <i class="fas fa-crop"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="resetImageBtn" title="Sin cambios que restablecer">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                </div>

                                <!-- Botones de recorte (ocultos inicialmente) -->
                                <div id="cropControls" class="crop-controls mt-2 d-none">
                                    <div class="btn-group w-100">
                                        <button type="button" class="btn btn-success" id="applyCropBtn">
                                            <i class="fas fa-check me-1"></i>
                                            Recortar Imagen
                                        </button>
                                        <button type="button" class="btn btn-secondary" id="cancelCropBtn">
                                            <i class="fas fa-times me-1"></i>
                                            Cancelar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha - Formulario de edición -->
                    <div class="col-md-6">
                        <form id="editImageForm">
                            <input type="hidden" id="editImageId" name="image_id">

                            <!-- Tipo de Fotografía -->
                            <div class="mb-3">
                                <label for="editTipoFotografia" class="form-label fw-bold">Tipo de Fotografía</label>
                                <select class="form-select" id="editTipoFotografia" name="tipo_fotografia" required>
                                    <option value="PRENDA FINAL">PRENDA FINAL</option>
                                    <option value="MUESTRA">MUESTRA</option>
                                    <option value="VALIDACION AC">VALIDACIÓN AC</option>
                                </select>
                                <div class="form-text">Selecciona el tipo de fotografía según la etapa del producto</div>
                            </div>

                            <!-- Descripción -->
                            <div class="mb-3">
                                <label for="editDescripcion" class="form-label fw-bold">Descripción</label>
                                <textarea class="form-control" id="editDescripcion" name="descripcion" rows="3"
                                          placeholder="Ej: Polo de algodón. Color azul marino, talla M."></textarea>
                                <div class="form-text">Describe las características principales del producto</div>
                            </div>

                            <!-- Información adicional (solo lectura) -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Información de la Orden</label>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label text-muted small">Orden SIT</label>
                                        <input type="text" class="form-control-plaintext" id="editOrdenSit" readonly>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label text-muted small">P.O</label>
                                        <input type="text" class="form-control-plaintext" id="editPO" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <label class="form-label text-muted small">O.C</label>
                                        <input type="text" class="form-control-plaintext" id="editOC" readonly>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label text-muted small">Fecha de subida</label>
                                        <input type="text" class="form-control-plaintext" id="editFechaSubida" readonly>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección de gestión de archivo -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">Gestión de Archivo</label>
                                <div class="row g-2 mb-3">
                                    <!-- Subir nueva foto desde archivo -->
                                    <div class="col-6">
                                        <input type="file" class="form-control d-none" id="newPhotoInput" accept="image/*">
                                        <button type="button" class="btn btn-outline-primary btn-sm w-100" id="uploadNewPhotoBtn">
                                            <i class="fas fa-folder me-1"></i>
                                            Subir Foto
                                        </button>
                                    </div>

                                    <!-- Tomar foto con cámara -->
                                    <div class="col-md-4">
                                        <input type="file" class="form-control d-none" id="newCameraInput" accept="image/*" capture="camera">
                                        <button type="button" class="btn btn-outline-success btn-sm w-100" id="takeCameraPhotoBtn">
                                            <i class="fas fa-camera me-1"></i>
                                            Tomar Foto
                                        </button>
                                    </div>

                                    <!-- Borrar foto -->
                                    <div class="col-6">
                                        <button type="button" class="btn btn-outline-danger btn-sm w-100" id="deletePhotoBtn">
                                            <i class="fas fa-trash me-1"></i>
                                            Borrar Foto
                                        </button>
                                    </div>
                                </div>
                                <!-- Contenedor para múltiples fotos -->
                                <div id="multiplePhotosContainer" class="multiple-photos-container"></div>
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
                <button type="button" class="btn btn-primary" id="saveChangesBtn">
                    <i class="fas fa-save me-1"></i>
                    Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

<!-- =======>>>>>>>>>>>> MODAL DE HISTORIAL DE LA PRENDA <<<<<<<<<========== -->
<div class="modal fade" id="historialModal" tabindex="-1" aria-labelledby="historialModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="historialModalLabel">
                    <i class="fas fa-history me-2 text-success"></i>
                    Historial de la Prenda
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Información de la orden -->
                <div class="mb-4">
                    <p class="mb-1 text-muted">Orden de estados para revisión</p>
                </div>

                <!-- Progress Steps -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-center align-items-center position-relative">
                            <!-- Step 1: Muestra -->
                            <div class="text-center step-container">
                                <div class="step-circle step-muestra completed" id="stepMuestra">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div class="step-number">1</div>
                                <div class="step-label">Muestra</div>
                            </div>

                            <!-- Arrow 1 -->
                            <div class="step-arrow">
                                <i class="fas fa-arrow-right text-muted"></i>
                            </div>

                            <!-- Step 2: Validación AC -->
                            <div class="text-center step-container">
                                <div class="step-circle step-validacion pending" id="stepValidacionAC">
                                    <i class="fas fa-search"></i>
                                </div>
                                <div class="step-number">2</div>
                                <div class="step-label">Validación AC</div>
                            </div>

                            <!-- Arrow 2 -->
                            <div class="step-arrow">
                                <i class="fas fa-arrow-right text-muted"></i>
                            </div>

                            <!-- Step 3: Prenda Final -->
                            <div class="text-center step-container">
                                <div class="step-circle step-final pending" id="stepPrendaFinal">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="step-number">3</div>
                                <div class="step-label">Prenda Final</div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <!-- Fotografías por Estado -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="mb-3">
                            <i class="fas fa-images me-2"></i>
                            Fotografías por Estado
                        </h6>
                    </div>
                </div>

                <!-- Muestra -->
                <div class="row mb-4" id="muestraSection">
                    <div class="col-12">
                        <div class="d-flex align-items-center mb-2">
                            <div class="status-indicator status-muestra me-2"></div>
                            <strong>Muestra</strong>
                            <span class="badge bg-info ms-2" id="muestraCount">2 fotos</span>
                        </div>
                        <div class="photos-container" id="muestraPhotos">
                            <!-- Fotos de muestra se cargarán aquí -->
                        </div>
                    </div>
                </div>

                <!-- Validación AC -->
                <div class="row mb-4" id="validacionSection">
                    <div class="col-12">
                        <div class="d-flex align-items-center mb-2">
                            <div class="status-indicator status-validacion me-2"></div>
                            <strong>Validación AC</strong>
                            <span class="badge bg-warning ms-2" id="validacionCount">1 foto</span>
                        </div>
                        <div class="photos-container" id="validacionPhotos">
                            <!-- Fotos de validación se cargarán aquí -->
                        </div>
                    </div>
                </div>

                <!-- Prenda Final -->
                <div class="row mb-4" id="finalSection">
                    <div class="col-12">
                        <div class="d-flex align-items-center mb-2">
                            <div class="status-indicator status-final me-2"></div>
                            <strong>Prenda Final</strong>
                            <span class="badge bg-success ms-2" id="finalCount">0 fotos</span>
                        </div>
                        <div class="photos-container" id="finalPhotos">
                            <!-- Fotos finales se cargarán aquí -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cerrar
                </button>
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

{{-- ARCHIVO Javascript para manejo de la logica de fotos-index.blade --}}
<script src="{{ asset('js/fotos-index.js') }}"></script>

<script src="{{ asset('js/pagination.js') }}"></script>

<script>

    //DETECTAR TIPO DE USUARIO SIMPLE
    const urlParams = new URLSearchParams(window.location.search);
    const isUserAccess = urlParams.get('user_access') === 'true';
    const isAdminAccess = urlParams.get('admin_access') === 'true';
    const shouldReloadData = urlParams.get('reload_data') === 'true';
    const ordenSitParam = urlParams.get('orden_sit');

    //Determinar tipo de usuario
    let isReallyAdmin;

    if (isUserAccess === true) {
        // Explícitamente marcado como usuario normal
        isReallyAdmin = false;
    } else if (isAdminAccess === true) {
        // Explícitamente marcado como administrador
        isReallyAdmin = true;
    } else {
        //Verificar si viene desde fotos-sit-add
        const transferredData = localStorage.getItem('newUploadedImages');
        const hasTransferredImages = transferredData && JSON.parse(transferredData)?.images?.length > 0;

        if (hasTransferredImages) {
            // Si hay imágenes transferidas, probablemente es usuario normal
            isReallyAdmin = false;
        } else {
            // Acceso directo sin contexto -> Admin por defecto
            isReallyAdmin = true;
        }
    }

    //CONFIGURAR PERMISO GLOBAL SIMPLE
    window.showDeleteButtons = isReallyAdmin;
    window.showEditButtons = isReallyAdmin;

    console.log('Tipo de acceso:', {
        isUserAccess,
        isAdminAccess,
        isReallyAdmin,
        ordenSitParam,
        showDeleteButtons: window.showDeleteButtons,
        showEditButtons: window.showEditButtons,
        accessType: isReallyAdmin ? 'ADMINISTRADOR' : 'USUARIO NORMAL'
    });

    if (isAdminAccess && !ordenSitParam) {
        setTimeout(() => {
            if (typeof loadPhotosFromBackend === 'function') {
                loadPhotosFromBackend();
            }
        }, 800);
    }

    //Procesar datos transferidos desde fotos-sit-add
    document.addEventListener('DOMContentLoaded', function() {
        //Procesar datos transferidos primero
        const transferredData = localStorage.getItem('newUploadedImages');
        let hasTransferredData = false;
        let processedTransferredImages = 0;

        if (transferredData) {
            try {
                const data = JSON.parse(transferredData);

                if (data.images && data.images.length > 0) {
                    hasTransferredData = true;

                    //Procesar cada imagen transferida
                    data.images.forEach((imageData, index) => {
                        console.log(`Imagen transferida ${index + 1}:`, {
                            id: imageData.id,
                            url: imageData.imagen_url || imageData.url,
                            descripcion: imageData.descripcion,
                            fromSitAdd: imageData.fromSitAdd
                        });

                        //Normalizar datos para compatibilidad
                        const normalizedImageData = {
                            id: imageData.id || imageData.backendId,
                            imagen_url: imageData.imagen_url || imageData.url,
                            url: imageData.imagen_url || imageData.url,
                            orden_sit: imageData.orden_sit,
                            po: imageData.po,
                            oc: imageData.oc,
                            descripcion: imageData.descripcion,
                            tipo: imageData.tipo,
                            created_at: imageData.created_at,
                            fecha_subida: imageData.created_at,
                            fromSitAdd: true,
                            transferTimestamp: Date.now()
                        };

                        //Agregar con delay para animación
                        setTimeout(() => {
                            addBackendImageToTable(normalizedImageData);
                            processedTransferredImages++;
                        }, index * 100);
                    });
                }

                //Limpiar localStorage después de procesar
                localStorage.removeItem('newUploadedImages');

            } catch (error) {
                console.error('Error procesando datos transferidos:', error);
                localStorage.removeItem('newUploadedImages');
            }
        } else {
            console.log('No hay datos transferidos');
        }

        // Cargar backend después de transferidas
        const delayBackendLoad = hasTransferredData ? 2000 : 800; // 2s si hay transferidos, 0.8s si no

        setTimeout(() => {
            console.log('niciando carga del backend...');
            if (typeof loadPhotosFromBackend === 'function') {
                loadPhotosFromBackend();
            } else {
                console.error('Función loadPhotosFromBackend no está definida');
            }
        }, delayBackendLoad);
    });
  /*=================================================================================================================================*/

    // === Funcion crear filas de imagen ===
    function createImageRowHTML(data, source = 'frontend') {
        const isBackend = source === 'backend';

        // Mapear datos según la fuente
        const imageUrl = isBackend ? data.imagen_url : data.url;
        const altText = isBackend ? data.descripcion : (data.name || data.descripcion || 'Imagen');
        const ordenSit = isBackend ? data.orden_sit : (data.ordenSit || 'N/A');
        const po = isBackend ? data.po : (data.po || 'N/A');
        const oc = isBackend ? (data.oc || '-') : (data.oc || 'N/A');
        const descripcion = data.descripcion || 'Sin descripción';
        const tipo = isBackend ? data.tipo : data.normalizedType;



        //USAR LA MISMA LÓGICA QUE FOTOS-SIT-ADD
        const safeImageUrl = (imageUrl || '').replace(/'/g, "\\'");
        const safeDescripcion = (descripcion || '').replace(/'/g, "\\'");
        const safeTipo = (tipo || '').replace(/'/g, "\\'");

        // Funciones de eliminacion
        const deleteFunction = isBackend ? `deleteBackendImage(${data.id}, this)` : 'deleteImage(this)';

        // Funciones de edición
        const editFunction = isBackend ? `editBackendImage(${data.id}, this)` : 'editImage(this)';

        return `
            <td data-column="imagen">
                <img src="${safeImageUrl}"
                    alt="${altText}"
                    class="img-thumbnail preview-image"
                    style="width: 60px; height: 60px; cursor: pointer; object-fit: cover; background-color: #f8f9fa;"
                    onclick="openLightbox('${safeImageUrl}', '${safeDescripcion}', '${safeTipo}')"
                    onerror="this.src='https://picsum.photos/id/535/400/600'">
            </td>
            <td data-column="orden-sit">${ordenSit}</td>
            <td data-column="po">${po}</td>
            <td data-column="oc">${oc}</td>
            <td data-column="descripcion">${descripcion}</td>
            <td data-column="tipo-fotografia">${tipo}</td>
            <td data-column="acciones">
                ${generateActionButtons(data, source)}
            </td>
        `;
    }

    /*===========================================================================================================*/
    // Agregar esta función para crear u ocultar botones segun corresponda:
    function generateActionButtons(data, source) {
        //Verificar permisos globales
        const showDelete = window.showDeleteButtons === true;
        const showEdit = window.showEditButtons === true;
        const isBackend = source === 'backend';

        let buttonsHTML = '';

        //BOTÓN ELIMINAR - Solo para administradores
        if (showDelete) {
            const deleteFunction = isBackend ? `deleteBackendImage(${data.id}, this)` : 'deleteImage(this)';
            buttonsHTML += `
                <button class="btn btn-danger btn-sm me-1 btn-delete" onclick="${deleteFunction}" title="Eliminar imagen">
                    <i class="fas fa-trash"></i>
                </button>
            `;
        }

        //BOTÓN EDITAR - Solo para administradores
        if (showEdit) {
            const editFunction = isBackend ? `editBackendImage(${data.id}, this)` : 'editImage(this)';
            buttonsHTML += `
                <button class="btn btn-warning btn-sm me-1 btn-edit" onclick="${editFunction}" title="Editar información">
                    <i class="fas fa-edit"></i>
                </button>
            `;
        }

        //BOTONES SIEMPRE VISIBLES - Para todos los usuarios
        buttonsHTML += `
            <button class="btn btn-info btn-sm comment-btn me-1" onclick="openCommentsModal(this)" title="Ver comentarios">
                <i class="fas fa-comments"></i>
            </button>
            <button class="btn btn-success btn-sm btn-historial" onclick="openHistorialModal(this)" title="Historial">
                <i class="fas fa-history"></i>
            </button>
        `;

        return buttonsHTML;
    }

  /*==============================================================================================================*/
    // ===== FUNCIONES DE LIGHTBOX CORREGIDAS =====
    function openLightbox(imageUrl, description, type) {
        //VALIDA QUE LA URL NO ESTÉ VACÍA
        if (!imageUrl || imageUrl === '' || imageUrl === 'undefined' || imageUrl === 'null') {
            console.error('URL de imagen inválida:', imageUrl);
            showNotification('Error: La imagen no está disponible', 'error');
            return;
        }

        const lightbox = document.getElementById('imageLightbox');
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxDescription = document.getElementById('lightboxDescription');
        const lightboxType = document.getElementById('lightboxType');

        console.log('Elementos encontrados:', {
            lightbox: !!lightbox,
            lightboxImage: !!lightboxImage,
            lightboxDescription: !!lightboxDescription,
            lightboxType: !!lightboxType
        });

        //VALIDACIÓN CORRECTA
        if (lightbox && lightboxImage) {
            //ASIGNAR DIRECTAMENTE SIN VALIDACIONES ADICIONALES
            lightboxImage.src = imageUrl;
            lightboxImage.alt = description || 'Imagen';

            if (lightboxDescription) {
                lightboxDescription.textContent = description || 'Sin descripción';
            }

            if (lightboxType) {
                lightboxType.textContent = type || 'Sin tipo especificado';
            }

            //MOSTRAR LIGHTBOX INMEDIATAMENTE
            lightbox.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            //VERIFICAR QUE LA IMAGEN SE CARGÓ CORRECTAMENTE
            lightboxImage.onload = function() {
                console.log('Imagen cargada exitosamente en lightbox');
            };

            lightboxImage.onerror = function() {
                showNotification('Error: No se pudo cargar la imagen', 'error');
            };

        } else {
            console.error('Elementos del lightbox no encontrados:', {
                lightbox: !!lightbox,
                lightboxImage: !!lightboxImage
            });
            showNotification('Error: Lightbox no disponible', 'error');
        }
    }

    function closeLightbox() {
        const lightbox = document.getElementById('imageLightbox');
        if (lightbox) {
            lightbox.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

  /*==========================================================================================================================*/
    function downloadImage() {
        const lightboxImage = document.getElementById('lightboxImage');
        if (lightboxImage && lightboxImage.src && lightboxImage.src !== '') {
            //Obtener numero de orden sit para nombre de archivo
            let ordenSit = null;
            let fileName = 'imagen.jpg'; // Fallback

            try {
                // Método 1: Desde currentImageData global
                if (window.currentImageData && window.currentImageData.ordenSit) {
                    ordenSit = window.currentImageData.ordenSit;
                }

                // Método 2: Desde descripción del lightbox
                if (!ordenSit) {
                    const lightboxDescription = document.getElementById('lightboxDescription');
                    if (lightboxDescription) {
                        const descText = lightboxDescription.textContent;
                        // Buscar número de 6+ dígitos (según orden SIT)
                        const match = descText.match(/\b\d{6,}\b/);
                        if (match) {
                            ordenSit = match[0];
                        }
                    }
                }

                // Método 3: Desde la tabla (buscar fila visible actual)
                if (!ordenSit) {
                    const tableBody = document.getElementById('imagesTableBody');
                    if (tableBody) {
                        const visibleRows = tableBody.querySelectorAll('tr:not([style*="display: none"])');
                        for (let row of visibleRows) {
                            const img = row.querySelector('img');
                            const ordenCell = row.querySelector('[data-column="orden-sit"]');

                            // Si la imagen coincide con la del lightbox
                            if (img && img.src === lightboxImage.src && ordenCell) {
                                ordenSit = ordenCell.textContent.trim();
                                break;
                            }
                        }
                    }
                }

                //CONSTRUIR NOMBRE: imagen_[ordenSit].jpg
                if (ordenSit && ordenSit !== '' && ordenSit !== 'N/A') {
                    // Limpiar orden SIT (solo números)
                    const cleanOrdenSit = ordenSit.replace(/[^0-9]/g, '');
                    if (cleanOrdenSit) {
                        fileName = `imagen_${cleanOrdenSit}.jpg`;
                    }
                } else {
                    // Fallback con timestamp
                    fileName = `imagen_${Date.now()}.jpg`;
                }

            } catch (error) {
                console.warn('Error obteniendo orden SIT:', error);
                fileName = `imagen_${Date.now()}.jpg`;
            }

            //Crear enlace de descarga
            const link = document.createElement('a');
            link.href = lightboxImage.src;
            link.download = fileName;
            link.style.display = 'none';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            console.log(`Descarga iniciada: ${fileName}`);
        } else {
            console.error('No hay imagen para descargar');
        }
    }

    // ===== SCRIPT FUNCIONALIDAD SUBIDA DE IMAGENES =====
    function initializeUploadButtons() {
        const cameraUpload = document.getElementById('cameraUpload');
        const fileUpload = document.getElementById('fileUpload');
        const cameraInput = document.getElementById('cameraInput');
        const fileInput = document.getElementById('fileInput');

        // Camera upload click
        if (cameraUpload && cameraInput) {
            cameraUpload.addEventListener('click', function() {
                console.log('Activando cámara...');
                cameraInput.click();
            });

            cameraInput.addEventListener('change', function(e) {
                handleImageUpload(e.target.files, 'camera');
            });
        }

        // File upload click
        if (fileUpload && fileInput) {
            fileUpload.addEventListener('click', function() {
                fileInput.click();
            });

            fileInput.addEventListener('change', function(e) {
                handleImageUpload(e.target.files, 'file');
            });
        }

        // Drag and drop functionality
        initializeDragAndDrop();
    }

/*======================================================================================================================*/
    function handleImageUpload(files, source) {
        if (!files || files.length === 0) {
            showNotification('No se seleccionaron archivos', 'warning');
            return;
        }

        //Validar archivos
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

  /*====================================================================================================*/
    function processMultipleImageAtOnce(files, source) {
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
        return;
    }

    //Mostrar estado de carga (ADAPTAR para fotos-index)
    const uploadBtn = source === 'camera'
        ? document.getElementById('cameraUpload')
        : document.getElementById('fileUpload');

    setUploadState(uploadBtn, 'uploading');

    //PROCESAR ARCHIVOS DE FORMA ASÍNCRONA Y ROBUSTA
    const imageDataArray = [];
    let processedCount = 0;
    let hasErrors = false;

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

            // VERIFICAR SI TODOS LOS ARCHIVOS ESTÁN PROCESADOS
            if (processedCount === validFiles.length) {
                if (imageDataArray.length > 0) {
                    // ORDENAR por índice para mantener orden original
                    imageDataArray.sort((a, b) => a.index - b.index);
                    // Pequeño delay para asegurar que todo esté listo
                    setTimeout(() => {
                        showBatchImageModal(imageDataArray, uploadBtn);
                    }, 200);
                } else {
                    setUploadState(uploadBtn, 'normal');
                }
            }
        };

        reader.onerror = function(error) {
            console.error(`Error leyendo archivo ${index + 1} (${file.name}):`, error);

            hasErrors = true;
            processedCount++;

            // CONTINUAR AUNQUE HAYA ERRORES
            if (processedCount === validFiles.length) {
                if (imageDataArray.length > 0) {

                    imageDataArray.sort((a, b) => a.index - b.index);

                    setTimeout(() => {
                        showBatchImageModal(imageDataArray, uploadBtn);
                    }, 200);
                } else {
                    setUploadState(uploadBtn, 'normal');
                }
            }
        };

        // INICIAR LECTURA CON LOG
        reader.readAsDataURL(file);
    });

    // TIMEOUT DE SEGURIDAD
    setTimeout(() => {
        if (processedCount < validFiles.length) {
            if (imageDataArray.length > 0) {
                //showNotification(`Solo se procesaron ${imageDataArray.length} de ${validFiles.length} archivos`, 'warning');
                imageDataArray.sort((a, b) => a.index - b.index);
                showBatchImageModal(imageDataArray, uploadBtn);
            } else {
                //showNotification('Timeout: No se pudo procesar ningún archivo', 'error');
                setUploadState(uploadBtn, 'normal');
            }
        }
    }, 15000); // 15 segundos timeout
}

/*=======================================================================================================================*/
//Copia de fotos-sit-add adaptado para fotos-index
function showBatchImageModal(imageDataArray, uploadBtn) {
    //VALIDACIÓN ADICIONAL
    if (!imageDataArray || imageDataArray.length === 0) {
        setUploadState(uploadBtn, 'normal');
        return;
    }

    const modalEl = document.getElementById('imageDataModal');
    if (!modalEl) {
        setUploadState(uploadBtn, 'normal');
        return;
    }

    const modal = new bootstrap.Modal(modalEl);

    //Mostrar titulo simple
    const modalTitle = document.getElementById('imageDataModalLabel');
    if (modalTitle) {
        modalTitle.textContent = `Detalles para ${imageDataArray.length} imagen(es)`;
    }

    //Limpiar formulario
    const descripcionInput = document.getElementById('descripcionInput');
    const tipoSelect = document.getElementById('tipoFotografiaSelect');

    if (descripcionInput) descripcionInput.value = '';
    if (tipoSelect) tipoSelect.selectedIndex = 0;

    //Solo mostrar cantidad
    addSimpleInfo(imageDataArray.length);

    //Configurar modal para no cerrarse
    modalEl.setAttribute('data-bs-backdrop', 'static');
    modalEl.setAttribute('data-bs-keyboard', 'false');

    //Manejar guardado para TODAS las imágenes
    const saveBtn = document.getElementById('saveImageData');
    const newSaveBtn = saveBtn.cloneNode(true);
    saveBtn.parentNode.replaceChild(newSaveBtn, saveBtn);

    //CONFIGURAR NUEVO EVENT LISTENER
    newSaveBtn.addEventListener('click', async function handleBatchSave() {
        const descripcionVal = descripcionInput ? descripcionInput.value.trim() : '';
        const tipoFotografia = tipoSelect ? tipoSelect.value : '';

        //Validación
        if (!descripcionVal || !tipoFotografia) {
            showNotification("Por favor complete todos los campos", 'warning');
            return;
        }

        console.log(`Iniciando procesamiento de ${imageDataArray.length} imágenes con:`, {
            descripcion: descripcionVal,
            tipo: tipoFotografia
        });

        //Desactivar botón durante procesamiento
        newSaveBtn.disabled = true;
        newSaveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Procesando...';

        try {
            const savedImages = [];

            //OBTENER ORDEN SIT PARA FOTOS-INDEX
            const ordenSitActual = getCurrentOrdenSit();
            const poUnificado = generatePONumber();
            const ocUnificado = generateOCNumber();

            //VALIDAR que se generaron correctamente
            if (!DESARROLLO_MODE && (!ordenSitActual || !poUnificado || !ocUnificado)) {
                throw new Error('Error: Faltan datos de orden válidos para producción');
            }

            //USAR valores por defecto si es necesario en producción
            const ordenFinal = ordenSitActual || 'SIN_ORDEN';
            const poFinal = poUnificado || 'SIN_PO';
            const ocFinal = ocUnificado || 'SIN_OC';

            //COPIAR LÓGICA SECUENCIAL DE FOTOS-SIT-ADD
            for (let i = 0; i < imageDataArray.length; i++) {
                const imageData = imageDataArray[i];

                try {
                    // Convertir base64 a File
                    const response = await fetch(imageData.base64);
                    const blob = await response.blob();
                    //Generar Timestamp único por imagen
                    const uniqueTimestamp = Date.now() + (i * 1000) + Math.random() * 100;
                    const fileName = imageData.name || `imagen_${uniqueTimestamp}_${i}.jpg`;
                    const file = new File([blob], fileName, { type: blob.type });

                    //CREAR FormData CON ORDEN UNIFICADA (IGUAL QUE FOTOS-SIT-ADD)
                    const formData = new FormData();
                    formData.append('imagen', file);
                    formData.append('orden_sit', ordenFinal); //MISMA ORDEN PARA TODAS
                    formData.append('po', poFinal);           //MISMO PO PARA TODAS
                    formData.append('oc', ocFinal);           //MISMO OC PARA TODAS
                    formData.append('descripcion', descripcionVal);
                    formData.append('tipo', tipoFotografia);      //SIN .toUpperCase()
                    formData.append('origen_vista', 'fotos-index');
                    formData.append('timestamp', new Date(uniqueTimestamp).toISOString());
                    formData.append('batch_index', i.toString());
                    formData.append('batch_total', imageDataArray.length.toString());
                    formData.append('unique_identifier', `${uniqueTimestamp}_${i}_${Math.random().toString(36).substring(2, 9)}`);
                    formData.append('file_original_name', file.name);
                    formData.append('processing_order', i.toString());

                    console.log(`FormData para imagen ${i + 1} con datos únicos:`);
                    for (let pair of formData.entries()) {
                        if (pair[1] instanceof File) {
                            console.log(`${pair[0]}: File(${pair[1].name}, ${pair[1].size} bytes, ${pair[1].type})`);
                        } else {
                            console.log(`${pair[0]}: ${pair[1]}`);
                        }
                    }

                    //SUBIR AL BACKEND (USAR FUNCIÓN EXISTENTE DE FOTOS-INDEX)
                    const backendResponse = await uploadToBackendIndex(formData);

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
                            saved: true,
                            imagen_path: backendResponse.data.imagen_path //Verificar hashes únicos
                        });
                        console.log(`Imagen ${i + 1}/${imageDataArray.length} guardada: ID ${backendResponse.data.id}`);

                        //DELAY ENTRE SUBIDAS (más tiempo para lotes pequeños)
                        if (i < imageDataArray.length - 1) {
                            let delay;
                            if (imageDataArray.length <= 3) {
                                delay = 2000 + (i * 500); // 2 o 3 seg para lotes pequeños
                            } else if (imageDataArray.length <= 5) {
                                delay = 1500 + (i * 300); // 1.5 seg para lotes medianos
                            } else {
                                delay = 1000 + (i * 200); // 1 seg para lotes grandes
                            }
                            await new Promise(resolve => setTimeout(resolve, delay)); // Usar delay calculado para fotos-index
                        }
                    } else {
                        throw new Error(backendResponse.message || 'Error en respuesta');
                    }

                } catch (imageError) {
                    console.error(`Error procesando imagen ${i + 1}:`, imageError);
                    // Continuar con las siguientes imágenes
                    await new Promise(resolve => setTimeout(resolve, 1000));
                }
            }

            //FINALIZACIÓN (DIFERENTE A FOTOS-SIT-ADD)
            if (savedImages.length > 0) {
                modal.hide();

                // Verificar Hashes únicos
                const hashesUnicos = verifyUniqueHashes(savedImages);

                if (!hashesUnicos) {
                    console.warn('Se encontraron hashes duplicados en el lote');
                }

                //AGREGAR A TABLA SECUENCIALMENTE (ESPECÍFICO DE FOTOS-INDEX)
                savedImages.forEach((imageData, index) => {
                    setTimeout(() => {
                        addBackendImageToTable(imageData);
                    }, index * 200);
                });

                // Refrescar paginación
                setTimeout(() => {
                    if (typeof manualRefreshPagination === 'function') {
                        manualRefreshPagination();
                    }
                }, (savedImages.length * 200) + 500);

                // Mostrar resultado
                const mensaje = savedImages.length === imageDataArray.length
                ? `${savedImages.length} imagen(es) subida(s) con hashes ${hashesUnicos ? 'únicos' : 'duplicados'}`
                : `${savedImages.length} de ${imageDataArray.length} imagen(es) subida(s)`;
            } else {
                throw new Error('No se pudo guardar ninguna imagen');
            }

        } catch (error) {
            console.error('Error durante el procesamiento:', error);
        } finally {
            //CLEANUP
            newSaveBtn.disabled = false;
            newSaveBtn.innerHTML = 'Guardar';
            setUploadState(uploadBtn, 'normal');
            removeSimpleInfo();
        }
    });

    // Mostrar modal
    modal.show();
}

/*======================================================================================================================*/
// ==>> Función para verificar hashes únicos después de la subida

function verifyUniqueHashes(savedImages) {
    const hashes = savedImages.map(img => {
        if (img.imagen_path) {
            // Extraer hash del nombre del archivo
            const fileName = img.imagen_path.split('/').pop();
            const hash = fileName.split('.')[0];
            return {
                id: img.id,
                hash: hash,
                imagen_path: img.imagen_path
            };
        }
        return null;
    }).filter(Boolean);

    // Verificar duplicados
    const hashCounts = {};
    const duplicates = [];

    hashes.forEach(item => {
        if (hashCounts[item.hash]) {
            hashCounts[item.hash]++;
            duplicates.push(item);
        } else {
            hashCounts[item.hash] = 1;
        }
    });

    if (duplicates.length > 0) {
        console.error('HASH DUPLICADO DETECTADO:', duplicates);
        // Log detallado de duplicados
        duplicates.forEach(dup => {
            console.error(`Duplicado: ID ${dup.id}, Hash: ${dup.hash}, Path: ${dup.imagen_path}`);
        });
    } else {
        console.log('Todos los hash son únicos');
    }

    return duplicates.length === 0;
}

/*======================================================================================================================*/
//Funciones auxiliares de fotos-sit-add
function addSimpleInfo(imageCount) {
    // Buscar si ya existe info container
    let infoContainer = document.getElementById('modalSimpleInfo');
    if (!infoContainer) {
        // Crear contenedor simple
        infoContainer = document.createElement('div');
        infoContainer.id = 'modalSimpleInfo';
        infoContainer.className = 'mb-3';

        // Insertar al inicio del modal-body
        const modalBody = document.querySelector('#imageDataModal .modal-body');
        if (modalBody) {
            modalBody.insertAdjacentElement('afterbegin', infoContainer);
            console.log('Info container creado');
        }
    }

    //OBTENER ORDEN SIT ACTUAL PARA MOSTRAR
    const ordenSitActual = getCurrentOrdenSit();

    const infoHTML = `
        <div class="alert alert-primary p-3 text-center">
            <h5 class="mb-2">
                <i class="fas fa-images me-2"></i>
                ${imageCount} imagen(es) seleccionada(s)
            </h5>
            <p class="mb-1">
                <strong>Orden SIT:</strong> ${ordenSitActual}
            </p>
        </div>
    `;

    infoContainer.innerHTML = infoHTML;
}

function removeSimpleInfo() {
    const infoContainer = document.getElementById('modalSimpleInfo');
    if (infoContainer) {
        infoContainer.remove();
    }
}

// =====>>> Buscar en tabla existente
function getCurrentOrdenSit() {
    // 1. Buscar en tabla existente
    const tableRows = document.querySelectorAll('#imagesTableBody tr[data-image-id]');
    if (tableRows.length > 0) {
        const firstRow = tableRows[0];
        const ordenSitCell = firstRow.querySelector('td[data-column="orden-sit"]');
        if (ordenSitCell && ordenSitCell.textContent.trim() !== 'N/A') {
            return ordenSitCell.textContent.trim();
        }
    }

    // 2. Buscar en localStorage
    const lastOrdenSit = localStorage.getItem('lastOrdenSit');
    if (lastOrdenSit) {
        return lastOrdenSit;
    }

    // 3. Generar nueva orden SIT solo si esta en modo desarrollo
    if (DESARROLLO_MODE) {
        const newOrdenSit = generateOrderNumber();
        if (newOrdenSit) {
            localStorage.setItem('lastOrdenSit', newOrdenSit);
            return newOrdenSit;
        }
    }

}

function uploadToBackendIndex(formData) {
    return new Promise((resolve, reject) => {

        $.ajax({
            url: '/api/fotografias',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            timeout: 30000,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Origen-Vista': 'fotos-index',
                'Accept': 'application/json'
            },
            success: function(response) {
                if (response.success && response.data) {
                    //Refrescar paginación
                    setTimeout(() => {
                        if (typeof manualRefreshPagination === 'function') {
                            manualRefreshPagination();
                        }
                    }, 200);

                    resolve({
                        success: true,
                        data: response.data
                    });
                } else {
                    reject(new Error(response.message || 'Respuesta inválida'));
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = `Error ${xhr.status}: `;

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = Object.values(xhr.responseJSON.errors).flat();
                    errorMessage += errors.join(', ');
                } else {
                    errorMessage += error || 'Error de conexión';
                }

                reject(new Error(errorMessage));
            }
        });
    });
}


/*======================================================================================================================*/
    //let currentUploadSession = null;
    function uploadSingleImage(file) {
    return new Promise((resolve, reject) => {
        console.log('Subiendo imagen desde fotos-index:', file.name);

        const formData = new FormData();
        formData.append('imagen', file);
        formData.append('orden_sit', generateOrderNumber());
        formData.append('po', generatePONumber());
        formData.append('oc', generateOCNumber());
        formData.append('timestamp', new Date().toISOString());
        formData.append('origen_vista', 'fotos-index');

        //MOSTRAR MODAL PARA DATOS ADICIONALES
        const modalEl = document.getElementById('imageDataModal');
        const modal = new bootstrap.Modal(modalEl);

        // Limpiar y mostrar modal
        document.getElementById('descripcionInput').value = '';
        document.getElementById('tipoFotografiaSelect').selectedIndex = 0;
        modal.show();

        //CONFIGURAR BOTÓN GUARDAR
        const saveBtn = document.getElementById('saveImageData');
        const newSaveBtn = saveBtn.cloneNode(true);
        saveBtn.parentNode.replaceChild(newSaveBtn, saveBtn);

        newSaveBtn.addEventListener('click', function() {
            const descripcion = document.getElementById('descripcionInput').value.trim();
            const tipoFotografia = document.getElementById('tipoFotografiaSelect').value;

            //VALIDACIÓN MÁS ESTRICTA
            if (!descripcion || descripcion.length < 3) {
                showNotification("La descripción debe tener al menos 3 caracteres", 'warning');
                return;
            }

            if (!tipoFotografia) {
                showNotification("Seleccione un tipo de fotografía", 'warning');
                return;
            }

            formData.append('descripcion', descripcion);
            formData.append('tipo', tipoFotografia); //SIN .toUpperCase() - dejar como está

            modal.hide();

            //SUBIR AL BACKEND usando AJAX existente
            $.ajax({
                url: '/api/fotografias',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 30000, // 30 segundos timeout
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Origen-Vista': 'fotos-index'
                },
                beforeSend: function() {
                    console.log('Enviando al servidor...');
                },
                success: function(response) {
                    if (response.success && response.data) {
                        //Refrescar paginación
                        setTimeout(() => {
                            if (typeof manualRefreshPagination === 'function') {
                                manualRefreshPagination();
                            }
                        }, 200);


                        resolve(response.data);
                    } else {
                        reject(new Error(response.message || 'Respuesta inválida del servidor'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText
                    });

                    let errorMessage = `Error ${xhr.status}: `;

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage += xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage += errors.join(', ');
                    } else {
                        errorMessage += error || 'Error de conexión';
                    }

                    reject(new Error(errorMessage));
                }
            });
        });

        //MANEJAR CANCELACIÓN DEL MODAL
        modalEl.addEventListener('hidden.bs.modal', function() {
            // Si el modal se cierra sin guardar, rechazar la promesa
            if (!newSaveBtn.disabled) {
                reject(new Error('Operación cancelada por el usuario'));
            }
        }, { once: true });
    });
}

/*=====================================================================================================================*/
    // ====>>>> Al cargar la página, verificar si hay imágenes nuevas(agregadas) En fotos-sit-add
    document.addEventListener("DOMContentLoaded", function() {
            //LIMPIAR cualquier resto de localStorage sin procesar
            const transferredData = localStorage.getItem('newUploadedImages');
            if (transferredData) {
                try {
                    const data = JSON.parse(transferredData);

                    if (data.images && data.images.length > 0) {

                        //PROCESAR CADA IMAGEN TRANSFERIDA
                        data.images.forEach((imageData, index) => {
                            console.log(`Procesando imagen ${index + 1}:`, {
                                id: imageData.id,
                                url: imageData.url,
                                origen: imageData.origenVista,
                                fromSitAdd: imageData.fromSitAdd
                            });

                            //VERIFICAR QUE VIENE DE FOTOS-SIT-ADD
                            if (imageData.fromSitAdd === true && imageData.displayOnly === true) {
                                //AGREGAR A TABLA SIN SUBIR DE NUEVO
                                setTimeout(() => {
                                    addBackendImageToTable(imageData);
                                }, index * 200); // Delay escalonado para animación
                            } else {
                                console.log(`Imagen ${index + 1} no tiene marcadores correctos`);
                            }
                        });
                    }

                    //LIMPIAR localStorage después de procesar
                    localStorage.removeItem('newUploadedImages');

                } catch (error) {
                    localStorage.removeItem('newUploadedImages'); // Limpiar en caso de error
                }
            } else {
                console.log('No hay imágenes transferidas');
            }

            //CARGAR IMÁGENES DEL BACKEND (después de las transferidas)
            setTimeout(() => {
                loadPhotosFromBackend();
            }, 1000);

            // Resto de inicializaciones...
            initializeUploadButtons();
        });

/*=====================================================================================================================*/
    function getDefaultImageByType(tipo) {
        const defaultImages = {
            'MUESTRA': 'https://picsum.photos/id/535/200/300',
            'PRENDA FINAL': 'https://picsum.photos/id/535/200/300',
            'VALIDACION AC': 'https://picsum.photos/id/535/200/300',
            'Muestra': 'https://picsum.photos/id/535/200/300',
            'Prenda Final': 'https://picsum.photos/id/535/200/300',
            'Validación AC': 'https://picsum.photos/id/535/200/300'
        };

        const defaultUrl = defaultImages[tipo] || 'https://picsum.photos/id/535/200/300';
        return defaultUrl;
    }

    // Agregar función para refrescar cards cuando se agregue nueva imagen
    function addImageToTable(imageData) {
        const tableBody = document.getElementById('imagesTableBody');
        if (!tableBody) {
            return;
        }

        // Generar ID único si no existe
        const imageId = imageData.id || 'img_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
        const fechaCreacion = new Date().toISOString();

        const row = document.createElement('tr');
        row.setAttribute('data-image-id', imageId);
        row.setAttribute('data-fecha-creacion', fechaCreacion);

        // Crear imagen con manejo de errores mejorado
        const imgSrc = imageData.url;
        const imgAlt = imageData.name || imageData.descripcion || 'Imagen';
        const imgDesc = imageData.descripcion || imgAlt;

        // Normalizar tipos para compatibilidad con filtros
        let normalizedType = imageData.tipoFotografia || imageData.tipo;
        if (normalizedType === 'Muestra') normalizedType = 'MUESTRA';
        if (normalizedType === 'Prenda Final') normalizedType = 'PRENDA FINAL';
        if (normalizedType === 'Validación AC') normalizedType = 'VALIDACION AC';

        // Validar URL antes de crear la fila
        if (!imgSrc || imgSrc === '' || imgSrc === 'undefined') {
            imageData.url = getDefaultImageByType(normalizedType);
        }

        // Agregar al objeto de datos
        imageData.normalizedType = normalizedType;

        // Usar función unificada
        row.innerHTML = createImageRowHTML(imageData, 'frontend');

        // === INSERCION INTELIGENTE ===
        if (imageData.isNew && imageData.source === 'edit-multiple') {
            // Para imágenes nuevas desde edición: insertar después de la fila actual
            if (currentEditingRow && currentEditingRow.nextSibling) {
                tableBody.insertBefore(row, currentEditingRow.nextSibling);
            } else {
                tableBody.appendChild(row);
            }
            console.log('Fila agregada a la tabla después de la fila actual');
        } else {
            // Para otras imágenes: agregar al inicio de la tabla
            tableBody.insertBefore(row, tableBody.firstChild);
            console.log('Fila agregada al inicio de la tabla');
        }

        // Añadir animación de entrada
        row.style.opacity = '0';
        row.style.transform = 'translateY(-10px)';

        // Color de fondo según origen
        if (imageData.isNew && imageData.source === 'edit-multiple') {
            row.style.backgroundColor = '#e3f2fd'; // Azul claro para nuevas desde edición
        } else {
            row.style.backgroundColor = '#d4edda'; // Verde claro para otras
        }

        setTimeout(() => {
            row.style.transition = 'all 0.5s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, 100);

        // Quitar el fondo verde después de 3 segundos
        setTimeout(() => {
            row.style.backgroundColor = '';
        }, 3000);

        // Mostrar mensaje si es imagen por defecto
        if (imageData.isDefaultImage) {
            console.log('Se usó imagen por defecto para:', imageData.descripcion);
        }
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

    /*==============================================================================================*/
    //==>> Booleano cambiar a conveniencia
    const DESARROLLO_MODE = true; // Cambiar a False en producción - True funciona con datos generados de prueba

    function generateOrderNumber() {
        if (!DESARROLLO_MODE) {
            return null;
        }
        return '100' + Math.floor(Math.random() * 90000 + 10000);
    }

    function generatePONumber() {
        if (!DESARROLLO_MODE) {
            return null;
        }
        return '6000' + Math.floor(Math.random() * 900000 + 100000);
    }

    function generateOCNumber() {
        if (!DESARROLLO_MODE) {
            return null;
        }
        return '4200' + Math.floor(Math.random() * 9000000 + 1000000);
    }

    // En caso de querer descargar la imagen
    function downloadImageFromRow(button) {
        const row = button.closest('tr');
        const img = row.querySelector('img');
        if (img) {
            const link = document.createElement('a');
            link.href = img.src;
            link.download = img.alt || 'imagen';
            link.click();
        }
    }
</script>

<script>
    //FUNCIÓN PARA CARGAR DATOS DEL BACKEND AL INICIAR
    function loadPhotosFromBackend() {
        //Obtener parametros de url
        const urlParams = new URLSearchParams(window.location.search);
        const ordenSitParam = urlParams.get('orden_sit');
        const isAdminAccess = urlParams.get('admin_access') === 'true';

        //Preparar datos de consulta - siempre cargar todas las fotos
        const queryData = {
            per_page: 100 // Cargar todas las fotografías disponibles
        };

        $.ajax({
            url: '/api/fotografias',
            type: 'GET',
            data: queryData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            timeout: 15000,
            success: function(response) {
                if (response.success) {
                    //Extraer datos correctamente
                    let fotografias = [];

                    if (response.data && Array.isArray(response.data.data)) {
                        fotografias = response.data.data; // Paginación Laravel
                    } else if (response.data && Array.isArray(response.data)) {
                        fotografias = response.data; // Array directo
                    } else if (Array.isArray(response)) {
                        fotografias = response; // Respuesta directa
                    }

                    if (fotografias.length > 0) {
                        //Verificar duplicados
                        const tableBody = document.getElementById('imagesTableBody');
                        const existingIds = new Set();

                        if (tableBody) {
                            tableBody.querySelectorAll('tr[data-image-id]').forEach(row => {
                                const imageId = row.dataset.imageId;
                                if (imageId) {
                                    existingIds.add(imageId.replace('backend_', ''));
                                }
                            });
                        }

                        //Filtrar solo duplicados, no por orden Sit
                        const fotografiasNuevas = fotografias.filter(foto => {
                            const isNew = !existingIds.has(foto.id?.toString());
                            if (!isNew) {
                            }
                            return isNew;
                        });

                        //Agregar todas las fotografías nuevas
                        if (fotografiasNuevas.length > 0) {
                            fotografiasNuevas.forEach((foto, index) => {
                                setTimeout(() => {
                                    addBackendImageToTable(foto);
                                }, index * 50);
                            });
                        }
                    } else {
                        // Solo mostrar mensaje si no hay datos transferidos
                        const tableBody = document.getElementById('imagesTableBody');
                        const hasExistingData = tableBody && tableBody.children.length > 0;

                        if (!hasExistingData && typeof showNotification === 'function') {
                            showNotification('No hay fotografías disponibles en la base de datos', 'info', 2000);
                        }
                    }
                } else {
                    if (typeof showNotification === 'function') {
                        showNotification('Error obteniendo datos del servidor', 'warning', 3000);
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error cargando fotografías del backend:', {
                    status: xhr.status,
                    statusText: xhr.statusText,
                    error: error
                });

                if (typeof showNotification === 'function') {
                    showNotification('Error de conexión con el servidor', 'error', 4000);
                }
            }
        });
    }
  /*=============================================================================================================*/
    //FUNCIÓN PARA AGREGAR IMAGEN DEL BACKEND A LA TABLA
    function addBackendImageToTable(fotografiaData) {
        //VALIDACIÓN MEJORADA DE URL
        if (!fotografiaData.imagen_url && !fotografiaData.url) {
            console.error('Imagen sin URL válida:', fotografiaData);
            return;
        }

        //NORMALIZAR URL - PRIORIZAR imagen_url
        if (!fotografiaData.imagen_url && fotografiaData.url) {
            fotografiaData.imagen_url = fotografiaData.url;
        }

        //VERIFICACIÓN FINAL DE URL
        const finalUrl = fotografiaData.imagen_url || fotografiaData.url;
        if (!finalUrl || finalUrl === '' || finalUrl === 'undefined') {
            fotografiaData.imagen_url = 'https://picsum.photos/id/535/400/600';
        }

        const tableBody = document.getElementById('imagesTableBody');
        if (!tableBody) {
            return;
        }

        const imageId = `backend_${fotografiaData.id}`;
        const existingRow = tableBody.querySelector(`tr[data-image-id="${imageId}"]`);
        if (existingRow) {
            return;
        }

        console.log('Datos válidos para crear fila:', {
            id: fotografiaData.id,
            imagen_url: fotografiaData.imagen_url,
            descripcion: fotografiaData.descripcion,
            tipo: fotografiaData.tipo
        });

        const row = document.createElement('tr');
        row.setAttribute('data-image-id', imageId);
        row.setAttribute('data-fecha-creacion', fotografiaData.fecha_subida || fotografiaData.created_at);

        //Marcar origen en el DOM
        row.setAttribute('data-origen-vista', fotografiaData.origenVista || 'backend');

        // Usar función unificada
        row.innerHTML = createImageRowHTML(fotografiaData, 'backend');

        // Insertar al inicio de la tabla
        tableBody.insertBefore(row, tableBody.firstChild);

        // Animación de entrada
        row.style.opacity = '0';
        row.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            row.style.transition = 'all 0.5s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, 100);

        // Refrescar paginacion
        setTimeout(() => {
            if (typeof manualRefreshPagination === 'function') {
                manualRefreshPagination();
            }
        }, 300);
    }

/*===================================================================================================*/
    //FUNCIÓN PARA ELIMINAR IMAGEN DEL BACKEND
    function deleteBackendImage(fotografiaId, button) {
        const row = button.closest('tr');
        if (!row) {
            //showNotification('Error: No se encontró la fila', 'error');
            return;
        }

        // Extraer datos usando la función de fotos-index.js
        const imageData = extractImageDataFromRow(row);
        if (!imageData) {
            //showNotification('Error: No se pudieron extraer los datos de la imagen', 'error');
            return;
        }

        // Agregar ID de backend para eliminación
        imageData.backendId = fotografiaId;
        imageData.isBackendImage = true;

        showDeleteConfirmation(imageData, row);
    }

 /*===================================================================================================*/
    //FUNCIÓN PARA EDITAR IMAGEN DEL BACKEND
    function editBackendImage(fotografiaId, button) {
        // Reutilizar la función existente de edición
        editImage(button);

        // Guardar ID del backend para uso posterior
        if (window.currentImageData) {
            window.currentImageData.backendId = fotografiaId;
        }
    }

    //CARGAR DATOS AL INICIAR PÁGINA
    document.addEventListener('DOMContentLoaded', function() {
        // Esperar a que se inicialice todo
        setTimeout(() => {
            loadPhotosFromBackend();
        }, 1000);
    });
</script>

<!-- Adicionales para el uso del selector rango de fechas -->
<!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <!-- Moment.js -->
  <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>

  <!-- Date Range Picker -->
  <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<!-- SCRIPTS para manejo de selector del rango de fechas -->
<!--Si se requiere usar en otro lugar (export const setRangeDates...) -->
<script>
    const setRangeDates = (options) => {
      const {
        element,
        startDate = moment(),
        endDate = moment(),
        ranges = {
          Hoy: [moment(), moment()],
          Ayer: [moment().subtract(1, "days"), moment().subtract(1, "days")],
          "Últimos 7 Días": [moment().subtract(6, "days"), moment()],
          "Últimos 30 Días": [moment().subtract(29, "days"), moment()],
          "Este Mes": [moment().startOf("month"), moment().endOf("month")],
          "Ultimo Mes": [
            moment().subtract(1, "month").startOf("month"),
            moment().subtract(1, "month").endOf("month"),
          ],
          Todo: [moment().subtract(20, "years"), moment()],
        },
        eventDateRange = function (start, end) {
          let fecha1 = start.format("YYYY-MM-DD");
          let fecha2 = end.format("YYYY-MM-DD");
          console.log("Rango seleccionado:", fecha1, fecha2);
        }
      } = options;

      $(element).daterangepicker({
        showWeekNumbers: true,
        showDropdowns: true,
        autoApply: true,
        ranges,
        locale: {
          format: "DD-MM-YYYY",
          separator: " - ",
          applyLabel: "Aplicar",
          cancelLabel: "Cancelar",
          fromLabel: "Desde",
          toLabel: "Hasta",
          customRangeLabel: "Personalizado",
          weekLabel: "W",
          daysOfWeek: ["Do", "Lu", "Mar", "Mie", "Jue", "Vie", "Sab"],
          monthNames: [
            "Enero","Febrero","Marzo","Abril","Mayo","Junio",
            "Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre",
          ],
          firstDay: 1,
        },
        alwaysShowCalendars: true,
        startDate,
        endDate,
        opens: "center",
        cancelClass: "btn-danger",
      }, eventDateRange);
    };

    // Inicialización cuando cargue la página
    $(document).ready(function () {
      setRangeDates({
        element: '#rangoFechasComm',
        startDate: moment().startOf('year'),
        endDate: moment(),
      });
    });
</script>

<script>
// Función para aplicar filtro automático basado en la selección del daterangepicker
function setupAutomaticFiltering() {
    // Interceptar cuando se selecciona una opción del menú
    $(document).on('click', '.ranges li', function() {
        const rangeText = $(this).text().trim();

        // Pequeño delay para que el daterangepicker procese la selección
        setTimeout(() => {
            const fechaInicio = $('#rangoFechasComm').data('daterangepicker').startDate.format('YYYY-MM-DD');
            const fechaFin = $('#rangoFechasComm').data('daterangepicker').endDate.format('YYYY-MM-DD');

            // Aplicar filtro inmediatamente
            applyDateFilter(fechaInicio, fechaFin, rangeText);
        }, 100);
    });

    // También interceptar cuando se aplica un rango personalizado
    $('#rangoFechasComm').on('apply.daterangepicker', function(ev, picker) {
        const fechaInicio = picker.startDate.format('YYYY-MM-DD');
        const fechaFin = picker.endDate.format('YYYY-MM-DD');
        applyDateFilter(fechaInicio, fechaFin, 'Personalizado');
    });
}

// Función principal para aplicar el filtro
function applyDateFilter(fechaInicio, fechaFin, rangeType) {
    const tableBody = document.getElementById('imagesTableBody');
    if (!tableBody) {
        return;
    }

    const rows = tableBody.querySelectorAll('tr[data-image-id]');
    let visibleCount = 0;
    let hiddenCount = 0;

    rows.forEach(row => {
        const fechaCreacion = getFechaCreacionFromRow(row);

        if (fechaCreacion) {
            const fechaRow = moment(fechaCreacion).format('YYYY-MM-DD');
            const inRange = moment(fechaRow).isBetween(fechaInicio, fechaFin, 'day', '[]');

            if (inRange) {
                // Mostrar fila con animación suave
                row.style.display = '';
                row.style.opacity = '0.3';
                setTimeout(() => {
                    row.style.transition = 'opacity 0.3s ease';
                    row.style.opacity = '1';
                }, 50);
                visibleCount++;
            } else {
                // Ocultar fila con animación
                row.style.transition = 'opacity 0.2s ease';
                row.style.opacity = '0';
                setTimeout(() => {
                    row.style.display = 'none';
                }, 200);
                hiddenCount++;
            }
        } else {
            // Si no tiene fecha, mostrar por defecto
            row.style.display = '';
            row.style.opacity = '1';
            visibleCount++;
        }
    });

    // Mostrar notificación del resultado
    showFilterNotification(visibleCount, hiddenCount, rangeType, fechaInicio, fechaFin);
}

// Función para obtener fecha de creación de una fila
function getFechaCreacionFromRow(row) {
    // Opción 1: Si tienes un atributo data-fecha-creacion
    if (row.dataset.fechaCreacion) {
        return row.dataset.fechaCreacion;
    }

    // Opción 2: Extraer del ID de la imagen si tiene timestamp
    const imageId = row.dataset.imageId;
    if (imageId && imageId.includes('_')) {
        const parts = imageId.split('_');
        const timestamp = parts[1];
        if (timestamp && !isNaN(timestamp) && timestamp.length >= 10) {
            return new Date(parseInt(timestamp));
        }
    }

    // Opción 3: Fecha actual para nuevas imágenes sin fecha específica
    return new Date();
}

// Función para mostrar notificación del resultado del filtro de fechas
function showFilterNotification(visible, hidden, rangeType, fechaInicio, fechaFin) {
    const message = `
        <strong>${rangeType}</strong><br>
            ${fechaInicio} a ${fechaFin}<br>
            ${visible} foto(s) mostrada(s)<br>
            ${hidden} foto(s) oculta(s)
    `;

    // Usar la función de notificación existente
    if (typeof showNotification === 'function') {
        const tipo = visible > 0 ? 'success' : 'warning';
    } else {
        // Crear notificación temporal
        createTemporaryNotification(message, visible > 0 ? 'success' : 'warning');
    }
}

// Función para limpiar filtros
function clearDateFilter() {
    const tableBody = document.getElementById('imagesTableBody');
    if (tableBody) {
        const rows = tableBody.querySelectorAll('tr[data-image-id]');
        rows.forEach(row => {
            row.style.display = '';
            row.style.opacity = '1';
            row.style.transition = '';
        });
    }

    // Limpiar selector de fechas
    $('#rangoFechasComm').val('');

    showFilterNotification(rows.length, 0, 'Filtro Limpiado', '', '');
}

// Inicialización cuando el DOM esté listo
$(document).ready(function() {
    // inicialización existente del daterangepicker...

    // Agregar el filtrado automático
    setupAutomaticFiltering();
});

</script>
@endsection
