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

                <!-- DESCOMENTAR SI SE REQUIEREN ESTOS BOTONES
                <button class="btn btn-pink" onclick="exportAll()">
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
                </button> -->

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
                                        <input type="text"
                                            class="form-control form-control-sm predictive-filter"
                                            placeholder="Buscar"
                                            id="filterOrdenSit"
                                            data-column="orden-sit"
                                            autocomplete="off">
                                        <div class="autocomplete-suggestions" id="suggestionsOrdenSit"></div>
                                    </div>
                                </td>
                                <td data-column="po">
                                    <div class="autocomplete-wrapper">
                                        <input type="text"
                                            class="form-control form-control-sm predictive-filter"
                                            placeholder="Buscar"
                                            id="filterPO"
                                            data-column="po"
                                            autocomplete="off">
                                        <div class="autocomplete-suggestions" id="suggestionsPO"></div>
                                    </div>
                                </td>
                                <td data-column="oc">
                                    <div class="autocomplete-wrapper">
                                        <input type="text"
                                            class="form-control form-control-sm predictive-filter"
                                            placeholder="Buscar"
                                            id="filterOC"
                                            data-column="oc"
                                            autocomplete="off">
                                        <div class="autocomplete-suggestions" id="suggestionsOC"></div>
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
                                                        <span class="flex-grow-1">Muestra</span>
                                            </label>
                                            </li>

                                            <li>
                                                <label class="dropdown-item d-flex align-items-center" for="filtroPrendaFinal">
                                                        <input type="checkbox"
                                                            class="form-check-input me-2"
                                                            id="filtroPrendaFinal"
                                                            value="PRENDA FINAL"
                                                            onchange="filterByTipoFotografia()">
                                                        <span class="flex-grow-1">Prenda Final</span>
                                                </label>
                                            </li>

                                            <li>
                                                <label class="dropdown-item d-flex align-items-center" for="filtroValidacionAC">
                                                        <input type="checkbox"
                                                            class="form-check-input me-2"
                                                            id="filtroValidacionAC"
                                                            value="VALIDACION AC"
                                                            onchange="filterByTipoFotografia()">
                                                        <span class="flex-grow-1">Validación AC</span>
                                                </label>
                                            </li>

                                            <!-- Separador -->
                                            <li><hr class="dropdown-divider"></li>

                                            <!-- NUEVO: Controles del filtro -->
                                            <li class="dropdown-item-text p-2">
                                                <div class="d-flex gap-1">
                                                    <button class="btn btn-sm btn-outline-primary flex-grow-1"
                                                            onclick="selectAllTipoFotografia()"
                                                            title="Seleccionar todos">
                                                        <i class="fas fa-check-double"></i>
                                                        <span class="d-none d-sm-inline ms-1">Todos</span>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger flex-grow-1"
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
                            <!-- Fila de ejemplo 1  ELIMINAR EN PRODUCCION -->
                            <tr data-image-id="img_example_1" data-fecha-creacion="2025-10-02">
                                <td data-column="imagen">
                                    <img src="https://picsum.photos/id/535/200/300"
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
                                    <button class="btn btn-danger btn-sm me-1 btn-delete" onclick="deleteImage(this)" title="Eliminar imagen">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm me-1 btn-edit" onclick="editImage(this)" title="Editar información">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-info btn-sm comment-btn me-1" onclick="openCommentsModal(this)" title="Ver/Agregar comentarios">
                                        <i class="fas fa-comments"></i>
                                        <span class="comment-count" data-count="0"></span>
                                    </button>
                                    <button class="btn btn-success btn-sm btn-historial" onclick="openHistorialModal(this)" title="Historial de la Prenda">
                                        <i class="fas fa-history"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Fila de ejemplo 2 -->
                            <tr data-image-id="img_example_2" data-fecha-creacion="2025-09-29">
                                <td data-column="imagen">
                                    <img src="https://picsum.photos/id/535/200/300"
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
                                    <button class="btn btn-danger btn-sm me-1 btn-delete" onclick="deleteImage(this)" title="Eliminar imagen">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm me-1 btn-edit" onclick="editImage(this)" title="Editar información">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-info btn-sm comment-btn" onclick="openCommentsModal(this)" title="Ver/Agregar comentarios">
                                        <i class="fas fa-comments"></i>
                                        <span class="comment-count" data-count="0"></span>
                                    </button>
                                </td>
                            </tr>

                            <!-- Fila de ejemplo 3 -->
                            <tr data-image-id="img_example_3" data-fecha-creacion="2025-09-27">
                                <td data-column="imagen">
                                    <img src="https://picsum.photos/id/535/200/300"
                                         alt="Camisa Blanca"
                                         class="img-thumbnail preview-image"
                                         style="width: 60px; height: 60px; cursor: pointer;"
                                         onclick="openImageLightbox(this.src, this.alt, 'Camisa polo', 'PRENDA FINAL')">
                                </td>
                                <td data-column="orden-sit">10047396</td>
                                <td data-column="po">6000081373</td>
                                <td data-column="oc">4000065347</td>
                                <td data-column="descripcion">POLO BUSINESS</td>
                                <td data-column="tipo-fotografia">VALIDACION AC</td>
                                <td data-column="acciones">
                                    <button class="btn btn-danger btn-sm me-1 btn-delete" onclick="deleteImage(this)" title="Eliminar imagen">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm me-1 btn-edit" onclick="editImage(this)" title="Editar información">
                                        <i class="fas fa-edit"></i>
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

                <!-- =========>>>> Paginacion <<<<<<============= -->
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

<!-- MODAL DE COMENTARIOS (ELIMINAR EN PRODUCCIÓN) -->
<!-- <div class="modal fade" id="commentsModal" tabindex="-1" aria-labelledby="commentsModalLabel" aria-hidden="true">
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
</div> -->

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

<!-- Meta tag para el usuario actual (para detección automática) -->
<meta name="current-user" content="{{ auth()->user()->name ?? 'Usuario Sistema' }}">

{{-- ARCHIVO Javascript para manejo de la logica de fotos-index.blade --}}
<script src="{{ asset('js/fotos-index.js') }}"></script>

<script>
    // ===== FUNCIONES DE LIGHTBOX CORREGIDAS =====
    function openImageLightbox(imageUrl, alt, description, type) {
        console.log('Intentando abrir lightbox:', { imageUrl, alt, description, type });

        // Validar que la URL existe y es válida
        if (!imageUrl || imageUrl === '' || imageUrl === 'undefined') {
            console.error('URL de imagen inválida:', imageUrl);
            alert('Error: La imagen no está disponible');
            return;
        }

        const lightbox = document.getElementById('imageLightbox');
        const lightboxImage = document.getElementById('lightboxImage');
        const lightboxDescription = document.getElementById('lightboxDescription');
        const lightboxType = document.getElementById('lightboxType');

        if (lightbox && lightboxImage) {
            // Verificar si la imagen se puede cargar
            const testImg = new Image();
            testImg.onload = function() {
                console.log('Imagen válida, mostrando lightbox');

                lightboxImage.src = imageUrl;
                lightboxImage.alt = alt || 'Imagen';

                if (lightboxDescription) {
                    lightboxDescription.textContent = description || alt || 'Sin descripción';
                }

                if (lightboxType) {
                    lightboxType.textContent = type || 'Sin tipo especificado';
                }

                lightbox.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            };

            testImg.onerror = function() {
                console.error('Error cargando imagen:', imageUrl);
                alert('Error: No se pudo cargar la imagen');
            };

            testImg.src = imageUrl;
        } else {
            console.error('Error: No se encontraron los elementos del lightbox');
            alert('Error al abrir la imagen');
        }
    }

    function closeLightbox() {
        const lightbox = document.getElementById('imageLightbox');
        if (lightbox) {
            lightbox.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function downloadImage() {
        const lightboxImage = document.getElementById('lightboxImage');
        if (lightboxImage && lightboxImage.src && lightboxImage.src !== '') {
            const link = document.createElement('a');
            link.href = lightboxImage.src;
            link.download = lightboxImage.alt || 'imagen.jpg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
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
                console.log('Abriendo selector de archivos...');
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

        console.log(`Subiendo ${files.length} archivo(s) desde ${source}`);

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
                console.log('Todas las imágenes subidas correctamente');
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
                console.error('Error subiendo imágenes:', error);
                showNotification('Error al subir las imágenes', 'error');
                setUploadState(uploadBtn, 'normal');
            });
    }

    let currentUploadSession = null;

    function uploadSingleImage(file) {
    return new Promise((resolve, reject) => {
        const formData = new FormData();
        formData.append('image', file);
        formData.append('timestamp', new Date().toISOString());

        setTimeout(() => {
            const imageUrl = URL.createObjectURL(file);

            // === Generar datos compartidos por sesión ====
            if (!currentUploadSession) {
                currentUploadSession = {
                    ordenSit: generateOrderNumber(),
                    po: generatePONumber(),
                    oc: generateOCNumber(),
                    sessionId: Date.now(),
                    timestamp: new Date().toISOString()
                };
                console.log('Nueva sesión de carga creada:', currentUploadSession);
            }

            // Datos base de la imagen con datos compartidos
            const tempData = {
                id: Date.now() + Math.random(),
                url: imageUrl,
                name: file.name,
                size: file.size,
                uploadDate: new Date().toISOString(),
                // ==== Se usan datos de la sesión actual====
                ordenSit: currentUploadSession.ordenSit,
                po: currentUploadSession.po,
                oc: currentUploadSession.oc,
                sessionId: currentUploadSession.sessionId
            };

            console.log('Imagen con datos compartidos:', tempData);

            // Abrir modal y esperar datos del usuario
            const modalEl = document.getElementById('imageDataModal');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();

            // Limpiar formulario
            document.getElementById('descripcionInput').value = '';
            document.getElementById('tipoFotografiaSelect').selectedIndex = 0;

            // Evento al guardar
            const saveBtn = document.getElementById('saveImageData');

            const handleSave = () => {
                const descripcion = document.getElementById('descripcionInput').value.trim();
                const tipoFotografia = document.getElementById('tipoFotografiaSelect').value;

                if (!descripcion || !tipoFotografia) {
                    showNotification("Por favor ingrese todos los campos.", 'warning');
                    return;
                }

                modal.hide();

                // Resolver con los datos completos
                resolve({
                    ...tempData,
                    descripcion,
                    tipoFotografia
                });

                // Eliminar listener para evitar duplicados
                saveBtn.removeEventListener('click', handleSave);
            };

            saveBtn.addEventListener('click', handleSave);
        }, 1000 + Math.random() * 2000);
    });
}

    // ====>>>> Al cargar la página, verificar si hay imágenes nuevas(agregadas) En fotos-sit-add
    document.addEventListener("DOMContentLoaded", function() {
        console.log('DOM cargado, verificando imágenes transferidas...');

        // Verificar si hay imágenes transferidas desde fotos-sit-add
        const transferredData = localStorage.getItem('newUploadedImages');
        if (transferredData) {
            try {
                const data = JSON.parse(transferredData);
                console.log('Datos transferidos encontrados:', data);

                if (data.images && data.images.length > 0) {
                    console.log('Procesando', data.images.length, 'imágenes transferidas');

                    // Verificar y recrear URLs de blob si es necesario
                    const validatedImages = data.images.map((imageData, index) => {
                        console.log(`Validando imagen ${index + 1}:`, imageData);

                        // Si la URL es un blob que ya no existe, usar imagen por defecto
                        if (imageData.url && imageData.url.startsWith('blob:')) {
                            console.warn('URL de blob detectada, usando imagen por defecto');
                            // Usar una imagen por defecto basada en el tipo
                            imageData.url = getDefaultImageByType(imageData.tipoFotografia);
                            imageData.isDefaultImage = true;
                        }

                        return imageData;
                    });

                    // Agregar las imágenes a la tabla con un pequeño delay
                    setTimeout(() => {
                        validatedImages.forEach((imageData, index) => {
                            console.log(`Procesando imagen ${index + 1}:`, imageData);
                            addImageToTable(imageData);
                        });

                        // Mostrar notificación de éxito
                        setTimeout(() => {
                            if (typeof showNotification === 'function') {
                                showNotification(`${validatedImages.length} imagen(es) agregada(s) correctamente`, 'success');
                            }
                        }, 500);
                    }, 200);

                    // Limpiar localStorage
                    localStorage.removeItem('newUploadedImages');
                    console.log('LocalStorage limpiado');
                }
            } catch (error) {
                console.error('Error procesando imágenes transferidas:', error);
                localStorage.removeItem('newUploadedImages');
            }
        } else {
            console.log('No hay imágenes transferidas');
        }

        // Inicialización del lightbox
        const lightbox = document.getElementById('imageLightbox');
        if (lightbox) {
            lightbox.onclick = function (e) {
                if (e.target === lightbox) {
                    closeLightbox();
                }
            };
            console.log('Lightbox inicializado');
        }

        // =====>>>>>>>>> INICIALIZACIÓN MEJORADA CON RESPONSIVE <<<<<<<<<<<<=====
        // Inicialización del responsive system
        if (window.responsiveSystem && !window.responsiveSystem.initialized) {
            window.responsiveSystem.init();
        }

        // Inicialización normal
        initializeUploadButtons();

        console.log('Sistema completamente inicializado');
    });

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
        console.log(`Imagen por defecto para "${tipo}": ${defaultUrl}`);
        return defaultUrl;
    }

    // Agregar función para refrescar cards cuando se agregue nueva imagen
    function addImageToTable(imageData) {
        console.log('Agregando imagen a la tabla:', imageData);

        const tableBody = document.getElementById('imagesTableBody');
        if (!tableBody) {
            console.error('No se encontró el tbody de la tabla');
            return;
        }

        // Generar ID único si no existe
        const imageId = imageData.id || 'img_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
        const fechaCreacion = new Date().toISOString();
        console.log('ID de imagen:', imageId);
        console.log('Fecha de creación:', fechaCreacion);

        const row = document.createElement('tr');
        row.setAttribute('data-image-id', imageId);
        row.setAttribute('data-fecha-creacion', fechaCreacion);

        // Normalizar tipos para compatibilidad con filtros
        let normalizedType = imageData.tipoFotografia;
        if (normalizedType === 'Muestra') normalizedType = 'MUESTRA';
        if (normalizedType === 'Prenda Final') normalizedType = 'PRENDA FINAL';
        if (normalizedType === 'Validación AC') normalizedType = 'VALIDACION AC';

        console.log('Tipo normalizado:', normalizedType);

        // Crear imagen con manejo de errores mejorado
        const imgSrc = imageData.url;
        const imgAlt = imageData.name || imageData.descripcion || 'Imagen';
        const imgDesc = imageData.descripcion || imgAlt;

        console.log('Datos de imagen:', { imgSrc, imgAlt, imgDesc, normalizedType });

        // Validar URL antes de crear la fila
        if (!imgSrc || imgSrc === '' || imgSrc === 'undefined') {
            console.error(' URL de imagen inválida, usando imagen por defecto');
            imageData.url = getDefaultImageByType(normalizedType);
        }

        row.innerHTML = `
            <td data-column="imagen">
                <img src="${imageData.url}"
                    alt="${imageData.name || imageData.descripcion || 'Imagen'}"
                    class="img-thumbnail preview-image"
                    style="width: 60px; height: 60px; cursor: pointer; object-fit: cover; background-color: #f8f9fa;"
                    onclick="openImageLightbox('${imageData.url}', '${imageData.name || imageData.descripcion}', '${imageData.descripcion}', '${normalizedType}')">
            </td>
            <td data-column="orden-sit">${imageData.ordenSit || 'N/A'}</td>
            <td data-column="po">${imageData.po || 'N/A'}</td>
            <td data-column="oc">${imageData.oc || 'N/A'}</td>
            <td data-column="descripcion">${imageData.descripcion || 'Sin descripción'}</td>
            <td data-column="tipo-fotografia">${normalizedType}</td>
            <td data-column="acciones">
                <button class="btn btn-danger btn-sm me-1 btn-delete" onclick="deleteImage(this)" title="Eliminar imagen">
                    <i class="fas fa-trash"></i>
                </button>
                <button class="btn btn-warning btn-sm me-1 btn-edit" onclick="editImage(this)" title="Editar información">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-info btn-sm comment-btn" onclick="openCommentsModal(this)" title="Ver/Agregar comentarios" data-comment-count="0">
                    <i class="fas fa-comments"></i>
                </button>
                <button class="btn btn-success btn-sm btn-historial" onclick="openHistorialModal(this)" title="Historial de la Prenda">
                    <i class="fas fa-history"></i>
                </button>
            </td>
        `;

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

        console.log('Animación aplicada');

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

    // Hacer las funciones globalmente disponibles
    window.openImageLightbox = openImageLightbox;
    window.closeLightbox = closeLightbox;
    window.downloadImage = downloadImage;

    console.log('Funciones de lightbox registradas globalmente');

    // =/=/=/=/=/=/=/=/=/=/=>>>>>>>>> Función para abrir el modal de historial
    function openHistorialModal(button) {
        console.log('Abriendo modal de historial...');

        const row = button.closest('tr');
        if (!row) {
            showNotification('Error: No se encontró la fila', 'error');
            return;
        }

        const imageData = extractImageDataFromRowReal(row);
        if (!imageData) {
            showNotification('Error: No se pudieron extraer datos', 'error');
            return;
        }

        console.log('Datos extraídos para historial:', imageData);

        // Cargar historial con datos REALES únicamente
        loadRealHistorialData(imageData.ordenSit, imageData);

        // Mostrar modal
        const modalElement = document.getElementById('historialModal');
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
    }

    function extractImageDataFromRowReal(row) {
        const img = row.querySelector('img');
        const ordenSitCell = row.querySelector('[data-column="orden-sit"]');
        const poCell = row.querySelector('[data-column="po"]');
        const ocCell = row.querySelector('[data-column="oc"]');
        const descripcionCell = row.querySelector('[data-column="descripcion"]');
        const tipoCell = row.querySelector('[data-column="tipo-fotografia"]');

        let imageId = row.dataset.imageId;
        if (!imageId) {
            imageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);
            row.dataset.imageId = imageId;
        }

        const data = {
            id: imageId,
            imageUrl: img ? img.src : '',
            imageAlt: img ? img.alt : '',
            ordenSit: ordenSitCell ? ordenSitCell.textContent.trim() : '',
            po: poCell ? poCell.textContent.trim() : '',
            oc: ocCell ? ocCell.textContent.trim() : '',
            descripcion: descripcionCell ? descripcionCell.textContent.trim() : '',
            tipo: tipoCell ? tipoCell.textContent.trim() : ''
        };

        console.log('Datos extraídos:', data);
        return data;
    }

    function loadRealHistorialData(ordenSit, currentImageData = null) {
        console.log('Cargando historial REAL para orden:', ordenSit);

        // Buscar TODAS las imágenes reales de la misma orden SIT
        const allRealImages = findAllImagesFromOrder(ordenSit);
        console.log('Imágenes reales encontradas:', allRealImages);

        // Generar historial basado SOLO en datos reales
        const historialData = generateRealOnlyHistorialData(ordenSit, allRealImages, currentImageData);

        console.log('Historial real generado:', historialData);

        // Actualizar interfaz con datos reales
        updateRealProgressSteps(historialData.estados);
        loadRealPhotosByCategory('muestra', historialData.fotos.muestra);
        loadRealPhotosByCategory('validacion', historialData.fotos.validacion);
        loadRealPhotosByCategory('final', historialData.fotos.final);

        console.log('Historial real cargado correctamente');
    }

    function findAllImagesFromOrder(ordenSit) {
        const allImages = [];
        const tableBody = document.getElementById('imagesTableBody');

        if (!tableBody) {
            console.warn('No se encontró la tabla');
            return allImages;
        }

        // Buscar en todas las filas de la tabla y comparar # Orden SIT
        const rows = tableBody.querySelectorAll('tr[data-image-id]');
        console.log(`Buscando en ${rows.length} filas para orden SIT: "${ordenSit}"`);

        rows.forEach((row, index) => {
            const ordenCell = row.querySelector('[data-column="orden-sit"]');
            const currentOrdenSit = ordenCell ? ordenCell.textContent.trim() : '';

            console.log(`Fila ${index}: "${currentOrdenSit}" vs "${ordenSit}"`);

            if (currentOrdenSit === ordenSit) {
                const imageData = extractImageDataFromRowReal(row);
                allImages.push({
                    ...imageData,
                    source: 'table',
                    timestamp: Date.now() - (Math.random() * 86400000)
                });
                console.log(`Imagen real encontrada:`, imageData);
            }
        });

        // También verificar en localStorage por imágenes recién transferidas
        try {
            const recentData = localStorage.getItem('newUploadedImages');
            if (recentData) {
                const parsed = JSON.parse(recentData);
                if (parsed.images) {
                    parsed.images.forEach(img => {
                        if (img.ordenSit === ordenSit) {
                            allImages.push({
                                id: img.id || 'localStorage_' + Date.now(),
                                imageUrl: img.url,
                                imageAlt: img.name || img.descripcion,
                                ordenSit: img.ordenSit,
                                po: img.po,
                                oc: img.oc,
                                descripcion: img.descripcion,
                                tipo: img.tipoFotografia,
                                source: 'localStorage-transfer',
                                timestamp: img.uploadTimestamp || Date.now()
                            });
                            console.log('Imagen encontrada en localStorage:', img);
                        }
                    });
                }
            }
        } catch (error) {
            console.warn('Error leyendo localStorage:', error);
        }

        console.log(`Total imágenes reales encontradas: ${allImages.length}`);
        return allImages;
    }

    function generateRealOnlyHistorialData(ordenSit, realImages, currentImage) {
        console.log('Generando historial con SOLO datos reales...');

        // Categorizar imágenes REALES por tipo
        const imagesByType = {
            muestra: [],
            validacion: [],
            final: []
        };

        // Estados basados SOLO en imágenes reales existentes
        const estados = {
            muestra: false,
            validacion: false,
            final: false
        };

        // Procesar cada imagen real
        realImages.forEach((imageData, index) => {
            console.log(`Procesando imagen real ${index + 1}:`, imageData);

            const tipo = imageData.tipo ? imageData.tipo.toUpperCase() : '';
            const imageForHistory = {
                url: imageData.imageUrl,
                fecha: new Date(imageData.timestamp || Date.now()).toISOString(),
                descripcion: imageData.descripcion || imageData.imageAlt || 'Sin descripción',
                ordenSit: imageData.ordenSit,
                po: imageData.po,
                oc: imageData.oc,
                source: imageData.source || 'unknown',
                isReal: true // Marcar como imagen real
            };

            // Categorizar POR TIPO REAL
            if (tipo.includes('MUESTRA')) {
                imagesByType.muestra.push(imageForHistory);
                estados.muestra = true;
                console.log('MUESTRA real agregada');
            } else if (tipo.includes('VALIDACION') || tipo.includes('VALIDACIÓN')) {
                imagesByType.validacion.push(imageForHistory);
                estados.validacion = true;
                console.log('VALIDACIÓN real agregada');
            } else if (tipo.includes('FINAL') || tipo.includes('PRENDA FINAL')) {
                imagesByType.final.push(imageForHistory);
                estados.final = true;
                console.log('PRENDA FINAL real agregada');
            } else {
                console.log(`Tipo no reconocido: "${tipo}" - Clasificando como general`);
                imagesByType.muestra.push(imageForHistory);
                estados.muestra = true;
            }
        });

        // SI NO hay imágenes reales, mantener estados en false
        console.log('Estados finales basados en imágenes reales:', estados);

        const result = {
            estados: estados,
            fotos: imagesByType,
            totalImages: realImages.length,
            metadata: {
                ordenSit,
                generatedAt: new Date().toISOString(),
                source: 'real-data-only',
                realImagesOnly: true
            }
        };

        console.log('Historial real final:', result);
        return result;
    }

    function updateRealProgressSteps(estados) {
        console.log('Actualizando pasos con datos REALES:', estados);

        const stepMuestra = document.getElementById('stepMuestra');
        const stepValidacionAC = document.getElementById('stepValidacionAC');
        const stepPrendaFinal = document.getElementById('stepPrendaFinal');

        // Reset classes
        [stepMuestra, stepValidacionAC, stepPrendaFinal].forEach(step => {
            if (step) {
                step.classList.remove('completed', 'pending');
            }
        });

        // Aplicar estados REALES con animación
        setTimeout(() => {
            if (stepMuestra) {
                stepMuestra.classList.add(estados.muestra ? 'completed' : 'pending');
                console.log(`MUESTRA: ${estados.muestra ? 'COMPLETADO (tiene imágenes )' : 'PENDIENTE (sin imágenes)'}`);
            }
        }, 100);

        setTimeout(() => {
            if (stepValidacionAC) {
                stepValidacionAC.classList.add(estados.validacion ? 'completed' : 'pending');
                console.log(`VALIDACIÓN: ${estados.validacion ? 'COMPLETADO (tiene imágenes )' : 'PENDIENTE (sin imágenes)'}`);
            }
        }, 200);

        setTimeout(() => {
            if (stepPrendaFinal) {
                stepPrendaFinal.classList.add(estados.final ? 'completed' : 'pending');
                console.log(`FINAL: ${estados.final ? 'COMPLETADO (tiene imágenes )' : 'PENDIENTE (sin imágenes)'}`);
            }
        }, 300);
    }

    function loadRealPhotosByCategory(categoria, fotos) {
        console.log(`Cargando ${fotos.length} fotos REALES para ${categoria}`);

        const photosContainer = document.getElementById(`${categoria}Photos`);
        const countBadge = document.getElementById(`${categoria}Count`);

        if (!photosContainer || !countBadge) {
            console.warn(`Contenedores no encontrados para: ${categoria}`);
            return;
        }

        // Actualizar contador
        countBadge.textContent = `${fotos.length} foto${fotos.length !== 1 ? 's' : ''}`;

        // Limpiar contenedor
        photosContainer.innerHTML = '';

        if (fotos.length === 0) {
            // Estado vacío REAL (sin fotos de este tipo)
            photosContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-image"></i>
                    <span>No hay fotografías en esta etapa</span>
                </div>
            `;
            console.log(` ${categoria}: Sin fotos reales`);
        } else {
            // Ordenar fotos por fecha (más recientes primero)
            const sortedFotos = fotos.sort((a, b) => new Date(b.fecha) - new Date(a.fecha));

            // Agregar fotos REALES únicamente
            sortedFotos.forEach((foto, index) => {
                setTimeout(() => {
                    const photoDiv = document.createElement('div');
                    photoDiv.className = 'photo-item';
                    photoDiv.innerHTML = `
                        <img src="${foto.url}"
                             alt="${foto.descripcion}"
                             title="Descripción: ${foto.descripcion}\n OrdenSIT: ${foto.ordenSit}\n Fuente: ${foto.source}"
                             onclick="openImageLightbox('${foto.url}', '${foto.descripcion}', '${foto.descripcion}', '${categoria.toUpperCase()}')">
                        <div class="photo-date">${formatRealDate(foto.fecha)}</div>
                    `;
                    photosContainer.appendChild(photoDiv);
                }, index * 100);
            });

            console.log(` ${categoria}: ${fotos.length} fotos REALES cargadas`);
        }
    }

    function formatRealDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Hacer función global
    window.openHistorialModal = openHistorialModal;
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
        console.log('Opción seleccionada:', rangeText);

        // Pequeño delay para que el daterangepicker procese la selección
        setTimeout(() => {
            const fechaInicio = $('#rangoFechasComm').data('daterangepicker').startDate.format('YYYY-MM-DD');
            const fechaFin = $('#rangoFechasComm').data('daterangepicker').endDate.format('YYYY-MM-DD');

            console.log('Aplicando filtro automático:', fechaInicio, 'a', fechaFin);

            // Aplicar filtro inmediatamente
            applyDateFilter(fechaInicio, fechaFin, rangeText);
        }, 100);
    });

    // También interceptar cuando se aplica un rango personalizado
    $('#rangoFechasComm').on('apply.daterangepicker', function(ev, picker) {
        const fechaInicio = picker.startDate.format('YYYY-MM-DD');
        const fechaFin = picker.endDate.format('YYYY-MM-DD');

        console.log('Rango personalizado aplicado:', fechaInicio, 'a', fechaFin);
        applyDateFilter(fechaInicio, fechaFin, 'Personalizado');
    });
}

// Función principal para aplicar el filtro
function applyDateFilter(fechaInicio, fechaFin, rangeType) {
    console.log(`Filtrando por ${rangeType}:`, fechaInicio, 'a', fechaFin);

    const tableBody = document.getElementById('imagesTableBody');
    if (!tableBody) {
        console.warn('Tabla no encontrada');
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

    // Opción 3: Para imágenes de ejemplo o existentes, usar fechas simuladas
    // Puedes ajustar estas fechas según tus datos reales
    const ordenSit = row.querySelector('[data-column="orden-sit"]')?.textContent.trim();
    if (ordenSit) {
        // Simular fechas basadas en el número de orden (esto es temporal)
        // En producción, estas fechas deben venir de la base de datos
        const simulatedDates = {
            '10060482': '2025-10-02',
            '10001600': '2025-09-29',
            '10047396': '2025-09-27'
        };

        if (simulatedDates[ordenSit]) {
            return simulatedDates[ordenSit];
        }
    }

    // Opción 4: Fecha actual para nuevas imágenes sin fecha específica
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
        //showNotification(message, tipo);
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

    console.log('Filtrado automático por fechas inicializado');
});
</script>
@endsection
