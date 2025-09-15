@extends('layout/plantilla')

@section('tituloPagina', 'Index fotos')

@section('contenido')


<!-- Modal de Gestión de Imágenes -->
<div class="modal fade" id="imagesManagementModal" tabindex="-1" aria-labelledby="imagesManagementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-custom">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="imagesManagementModalLabel">
                    <i class="fas fa-camera me-2"></i>
                    Fotografias de prendas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Sección Superior Sticky -->
                <div class="sticky-header">
                    <!-- Fecha de Creación de Registro -->
                    <div class="row align-items-end mb-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    Fecha creación de registro
                                </label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="dateRange" placeholder="01-09-2025 - 30-09-2025" readonly>
                                    <button class="btn btn-outline-secondary" type="button" id="editDateRange" title="Editar fecha">
                                        <i class="fas fa-calendar-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <!-- Botones de Subida -->
                                <label class="form-label">Subir Imágenes</label>
                                <div class="upload-buttons">
                                    <div class="upload-btn" id="cameraUpload">
                                        <i class="fas fa-camera"></i>
                                        <span>Cámara</span>
                                        <input type="file" accept="image/*" capture="camera" style="display: none;" id="cameraInput">
                                    </div>
                                    <div class="upload-btn" id="fileUpload">
                                        <i class="fas fa-folder"></i>
                                        <span>Archivo</span>
                                        <input type="file" accept="image/*" multiple style="display: none;" id="fileInput">
                                    </div>
                                </div>
                            </div>
                    </div>

                    <!-- Búsqueda Global -->
                    <div class="row align-items-end search-actions-row">
                        <div class="col-md-7">
                            <label class="form-label">Buscar Ord. SIT / P.O / O.C</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="globalSearch" placeholder="Buscar por SIT, P.O, O.C, descripción...">
                                <button class="btn btn-primary" type="button" id="searchBtn">
                                    <i class="fas fa-search"></i>
                                </button>
                                 <button class="btn btn-danger" type="button" id="clearSearch">
                                        <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <!-- Botones de Acción -->
                            <div class="action-buttons-container">
                                <button class="btn btn-pink" id="exportAllBtn">
                                    <i class="fas fa-download me-1"></i>
                                    Exportar todo
                                </button>
                                <button class="btn btn-danger" id="clearFiltersBtn">
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
                                <button class="btn btn-success" id="exportSelectedBtn">
                                    <i class="fas fa-file-export me-1"></i>
                                    Exportar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Filtros Activos -->
                    <div id="activeFilters" class="mb-2" style="display: none;">
                        <strong>Filtros activos:</strong>
                        <div id="filterBadges" class="d-inline"></div>
                        <button class="btn btn-sm btn-outline-danger ms-2" id="clearAllFilters">
                            <i class="fas fa-times"></i> Limpiar todos
                        </button>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="table-container">
                    <table class="table table-hover images-table mb-0">
                        <thead>
                            <tr>
                                <th width="4%">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th width="8%" data-column="imagen">IMAGEN</th>
                                <th width="12%" data-column="orden-sit">
                                    ORDEN SIT
                                    <i class="fas fa-sort ms-1 text-muted" data-sort="orden_sit"></i>
                                </th>
                                <th width="12%" data-column="po">
                                    P.O
                                    <i class="fas fa-sort ms-1 text-muted" data-sort="po"></i>
                                </th>
                                <th width="12%" data-column="oc">
                                    O.C
                                    <i class="fas fa-sort ms-1 text-muted" data-sort="oc"></i>
                                </th>
                                <th width="20%" data-column="descripcion">
                                    DESCRIPCIÓN
                                    <i class="fas fa-sort ms-1 text-muted" data-sort="descripcion"></i>
                                </th>
                                <th width="15%" data-column="tipo-fotografia">
                                    TIPO FOTOGRAFÍA
                                    <i class="fas fa-sort ms-1 text-muted" data-sort="tipo_fotografia"></i>
                                </th>
                                <th width="17%" data-column="acciones">ACCIONES</th>
                            </tr>
                            <!-- Fila de filtros -->
                            <tr class="bg-light">
                                <td></td>
                                <td data-column="imagen">
                                    <small class="text-muted"></small>
                                </td>
                                <td data-column="orden-sit">
                                    <input type="text" class="form-control search-input" placeholder="Buscar" data-filter="orden_sit">
                                </td>
                                <td data-column="po">
                                    <input type="text" class="form-control search-input" placeholder="Buscar" data-filter="po">
                                </td>
                                <td data-column="oc">
                                    <input type="text" class="form-control search-input" placeholder="Buscar" data-filter="oc">
                                </td>
                                <td data-column="descripcion">
                                    <input type="text" class="form-control search-input" placeholder="Buscar" data-filter="descripcion">
                                </td>
                                <td data-column="tipo-fotografia">
                                    <select class="form-select search-input custom-select" data-filter="tipo_fotografia">
                                        <option value="">Todos</option>
                                        <option value="PRENDA FINAL">PRENDA FINAL</option>
                                        <option value="MUESTRA">MUESTRA</option>
                                        <option value="PROCESO">PROCESO</option>
                                        <option value="DEFECTO">DEFECTO</option>
                                    </select>
                                </td>
                                <td data-column="acciones">
                                    <small class="text-muted"></small>
                                </td>
                            </tr>
                        </thead>
                        <tbody id="imagesTableBody">
                            <!-- Datos de ejemplo -->
                            <tr>
                                <td><input type="checkbox" class="form-check-input row-select"></td>
                                <td data-column="imagen">
                                    <img src="https://via.placeholder.com/60x60/4CAF50/white?text=" alt="Prenda" class="image-thumbnail" data-bs-toggle="modal" data-bs-target="#imagePreviewModal">
                                </td>
                                <td data-column="orden-sit">10006482</td>
                                <td data-column="po">6000101385</td>
                                <td data-column="oc">42000020624</td>
                                <td data-column="descripcion">CAM FORM UNIC</td>
                                <td data-column="tipo-fotografia">PRENDA FINAL</td>
                                <td data-column="acciones">
                                    <button class="btn btn-danger btn-sm btn-action" title="Eliminar" onclick="deleteImage(1)">
                                        <i class="fas fa-times"></i> Eliminar
                                    </button>
                                    <button class="btn btn-warning btn-sm btn-action" title="Editar" onclick="editImage(1)">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-success btn-sm btn-action" title="Comentario" onclick="addComment(1)">
                                        <i class="fas fa-comment"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="form-check-input row-select"></td>
                                <td data-column="imagen">
                                    <img src="https://via.placeholder.com/60x60/2196F3/white?text=" alt="Prenda" class="image-thumbnail" data-bs-toggle="modal" data-bs-target="#imagePreviewModal">
                                </td>
                                <td data-column="orden-sit">10001600</td>
                                <td data-column="po">3000001545</td>
                                <td data-column="oc">-</td>
                                <td data-column="descripcion">Muestra Validación</td>
                                <td data-column="tipo-fotografia">MUESTRA</td>
                                <td data-column="acciones">
                                    <button class="btn btn-danger btn-sm btn-action" title="Eliminar" onclick="deleteImage(2)">
                                        <i class="fas fa-times"></i> Eliminar
                                    </button>
                                    <button class="btn btn-warning btn-sm btn-action" title="Editar" onclick="editImage(2)">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-success btn-sm btn-action" title="Comentario" onclick="addComment(2)">
                                        <i class="fas fa-comment"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="form-check-input row-select"></td>
                                <td data-column="imagen">
                                    <img src="https://via.placeholder.com/60x60/FF9800/white?text=" alt="Prenda" class="image-thumbnail" data-bs-toggle="modal" data-bs-target="#imagePreviewModal">
                                </td>
                                <td data-column="orden-sit">10031630</td>
                                <td data-column="po">6000057975</td>
                                <td data-column="oc">4000066739</td>
                                <td data-column="descripcion">GORRA NO</td>
                                <td data-column="tipo-fotografia">PRENDA FINAL</td>
                                <td data-column="acciones">
                                    <button class="btn btn-danger btn-sm btn-action" title="Eliminar" onclick="deleteImage(3)">
                                        <i class="fas fa-times"></i> Eliminar
                                    </button>
                                    <button class="btn btn-warning btn-sm btn-action" title="Editar" onclick="editImage(3)">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-success btn-sm btn-action" title="Comentario" onclick="addComment(3)">
                                        <i class="fas fa-comment"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="form-check-input row-select"></td>
                                <td data-column="imagen">
                                    <img src="https://via.placeholder.com/60x60/E91E63/white?text=" alt="Prenda" class="image-thumbnail" data-bs-toggle="modal" data-bs-target="#imagePreviewModal">
                                </td>
                                <td data-column="orden-sit">10032970</td>
                                <td data-column="po">6000060366</td>
                                <td data-column="oc">5511000299</td>
                                <td data-column="descripcion">BERMUDA UNIC</td>
                                <td data-column="tipo-fotografia">PRENDA FINAL</td>
                                <td data-column="acciones">
                                    <button class="btn btn-danger btn-sm btn-action" title="Eliminar" onclick="deleteImage(4)">
                                        <i class="fas fa-times"></i> Eliminar
                                    </button>
                                    <button class="btn btn-warning btn-sm btn-action" title="Editar" onclick="editImage(4)">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-success btn-sm btn-action" title="Comentario" onclick="addComment(4)">
                                        <i class="fas fa-comment"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" class="form-check-input row-select"></td>
                                <td data-column="imagen">
                                    <img src="https://via.placeholder.com/60x60/9C27B0/white?text=" alt="Prenda" class="image-thumbnail" data-bs-toggle="modal" data-bs-target="#imagePreviewModal">
                                </td>
                                <td data-column="orden-sit">10047396</td>
                                <td data-column="po">6000081373</td>
                                <td data-column="oc">4000065347</td>
                                <td data-column="descripcion">POLO BUSINESS</td>
                                <td data-column="tipo-fotografia">PRENDA FINAL</td>
                                <td data-column="acciones">
                                    <button class="btn btn-danger btn-sm btn-action" title="Eliminar" onclick="deleteImage(5)">
                                        <i class="fas fa-times"></i> Eliminar
                                    </button>
                                    <button class="btn btn-warning btn-sm btn-action" title="Editar" onclick="editImage(5)">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                    <button class="btn btn-success btn-sm btn-action" title="Comentario" onclick="addComment(5)">
                                        <i class="fas fa-comment"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="row mt-3 align-items-center">
                    <div class="col-md-6">
                        <div class="pagination-info">
                            Mostrando registros del <strong>1</strong> al <strong>5</strong> de un total de <strong>5</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="Paginación">
                            <ul class="pagination justify-content-end mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Anterior</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item"><a class="page-link" href="#">4</a></li>
                                <li class="page-item"><a class="page-link" href="#">5</a></li>
                                <li class="page-item disabled"><a class="page-link" href="#">...</a></li>
                                <li class="page-item"><a class="page-link" href="#">4514</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Siguiente</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary btn-sm">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Vista Previa de Imagen -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewModalLabel">Vista Previa de Imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" alt="Vista previa" class="img-fluid" style="max-height: 70vh;">
                <div class="mt-3">
                    <p><strong>Descripción:</strong> <span id="previewDescription"></span></p>
                    <p><strong>Tipo:</strong> <span id="previewType"></span></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Edición -->
<div class="modal fade" id="editImageModal" tabindex="-1" aria-labelledby="editImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editImageModalLabel">Editar Imagen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editForm">
                    <div class="mb-3">
                        <label for="editOrdenSit" class="form-label">Orden SIT</label>
                        <input type="text" class="form-control" id="editOrdenSit">
                    </div>
                    <div class="mb-3">
                        <label for="editPO" class="form-label">P.O</label>
                        <input type="text" class="form-control" id="editPO">
                    </div>
                    <div class="mb-3">
                        <label for="editOC" class="form-label">O.C</label>
                        <input type="text" class="form-control" id="editOC">
                    </div>
                    <div class="mb-3">
                        <label for="editDescripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="editDescripcion" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="editTipoFotografia" class="form-label">Tipo Fotografía</label>
                        <select class="form-select" id="editTipoFotografia">
                            <option value="">Seleccionar tipo</option>
                            <option value="PRENDA FINAL">PRENDA FINAL</option>
                            <option value="MUESTRA">MUESTRA</option>
                            <option value="PROCESO">PROCESO</option>
                            <option value="DEFECTO">DEFECTO</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveChanges()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Comentarios -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commentModalLabel">Agregar Comentario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="commentForm">
                    <div class="mb-3">
                        <label for="commentText" class="form-label">Comentario</label>
                        <textarea class="form-control" id="commentText" rows="4" placeholder="Escribe tu comentario o sugerencia..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveComment()">Guardar Comentario</button>
            </div>
        </div>
    </div>
</div>

<!-- Botón para abrir el modal (ejemplo de uso) -->
<div class="container mt-5">
    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#imagesManagementModal">
        <i class="fas fa-camera me-2"></i>
        Fotografias de prendas
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

<script>
    // Función mejorada para inicializar el selector de fechas - Filtro por fechas (fecha creacion registro)
    function initializeDatePicker() {
        let flatpickrInstance = null;

        // Verificar si ya existe una instancia
        if (document.getElementById('dateRange')._flatpickr) {
            document.getElementById('dateRange')._flatpickr.destroy();
        }

        // Configurar Flatpickr
        flatpickrInstance = flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "d-m-Y",
            locale: "es",
            defaultDate: ["{{ date('d-m-Y', strtotime('-30 days')) }}", "{{ date('d-m-Y') }}"],
            clickOpens: false, // No abrir automáticamente al hacer click en el input
            allowInput: false,  // No permitir escritura manual
            onOpen: function() {
                document.querySelector('.date-input-container')?.classList.add('date-editing');
            },
            onClose: function(selectedDates, dateStr, instance) {
                // Remover clase de edición cuando se cierre
                document.querySelector('.date-input-container')?.classList.remove('date-editing');
                if (dateStr) {
                    console.log('Fechas seleccionadas: ', dateStr);
                    // ==>> Espacio para logica de filtrado por fechas
                    updateActiveFilters('dateRange', dateStr);
                }
            }
        });

        // Evento para el botón de editar fecha
        document.getElementById('editDateRange').addEventListener('click', function() {
            const container = document.querySelector('.date-input-container');

            if (flatpickrInstance.isOpen) {
                // Si está abierto, cerrarlo
                flatpickrInstance.close();
                container?.classList.remove('date-editing');
            } else {
                // Si está cerrado, abrirlo
                flatpickrInstance.open();
                container?.classList.add('date-editing');
            }
        });

        // También permitir click en el input para abrir
        document.getElementById('dateRange').addEventListener('click', function() {
            flatpickrInstance.open();
        });

        // Funcion para actualizar filtros activos (placeholder)
        function updateActiveFilters(key, value) {
            console.log('Filtro actualizado: ', key, value);
            // Espacio para implementar logica de filtros aqui
        }

        // Función para limpiar fechas (si la necesitas en otro lugar)
        window.clearDateRange = function() {
            flatpickrInstance.clear();
            document.querySelector('.date-input-container')?.classList.remove('date-editing');
            updateActiveFilters('dateRange', '');
        };

        // Función para establecer fechas programáticamente
        window.setDateRange = function(startDate, endDate) {
            flatpickrInstance.setDate([startDate, endDate]);
            updateActiveFilters('dateRange', `${startDate} - ${endDate}`);
        };
    }

@endsection
