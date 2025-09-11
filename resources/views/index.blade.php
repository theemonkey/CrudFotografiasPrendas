@extends('layout/plantilla')

@section('tituloPagina', 'Index')

@section('contenido')

    <div class="card">
        <div class="card-body">
            <div class="col-12">
                <h1 class="mb-3">
                    <i class="fas fa-images"></i> Galería de Imágenes
                </h1>

                <!-- Filtros -->
                <div class="card mb-3">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label for="stage" class="form-label">Etapa de Prenda</label>
                                <select name="stage" id="stage" class="form-select">
                                    <option value="">Todas las etapas</option>
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage }}" {{ request('stage') == $stage ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('_', ' ', $stage)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-custom btn-primary-custom">
                                    <i class="fas fa-filter"></i> Filtrar
                                </button>
                            </div>
                            <div class="col-md-6 d-flex align-items-end justify-content-end">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-custom btn-secondary-custom" id="toggleSort">
                                        <i class="fas fa-sort"></i> Ordenar
                                    </button>
                                    <button type="button" class="btn btn-custom btn-primary-custom" onclick="window.location.href='{{ route('images.create') }}'">
                                        <i class="fas fa-plus"></i> Agregar Imágenes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Galería -->
        <div class="row" id="imageGallery">
            @forelse($images as $image)
                <div class="col-6 col-md-3 col-lg-2 mb-4" data-image-id="{{ $image->id }}">
                    <div class="image-card">
                        <div class="order-badge">
                            <span class="badge bg-primary">{{ $image->order_position }}</span>
                        </div>
                        <div class="stage-badge">
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $image->clothing_stage)) }}</span>
                        </div>

                        <img src="{{ $image->thumbnail_url }}"
                             alt="{{ $image->description }}"
                             class="img-fluid w-100"
                             style="height: 200px; object-fit: cover;"
                             onclick="showImageModal({{ $image->id }})">

                        <div class="image-overlay">
                            <p class="text-center mb-2">{{ Str::limit($image->description, 50) }}</p>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-light btn-sm" onclick="editImage({{ $image->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-warning btn-sm" onclick="cropImage({{ $image->id }})">
                                    <i class="fas fa-crop"></i>
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="deleteImage({{ $image->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <h3 class="text-muted">No hay imágenes</h3>
                        <p class="text-muted">Comienza agregando algunas imágenes a tu galería</p>
                        <a href="{{ route('images.create') }}" class="btn btn-custom btn-primary-custom">
                            <i class="fas fa-plus"></i> Agregar Primera Imagen
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Paginación -->
        @if($images->hasPages())
            <div class="row">
                <div class="col-12 d-flex justify-content-center">
                    {{ $images->appends(request()->query())->links() }}
                </div>
            </div>
        @endif
    </div>

    <!-- Botón flotante para agregar -->
    <a href="{{ route('images.create') }}" class="btn btn-primary btn-lg fab-button rounded-circle">
        <i class="fas fa-plus"></i>
    </a>

    <!-- Modal para vista completa -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vista Completa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="" class="img-fluid">
                    <div class="mt-3" id="modalDescription"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        let sortable = null;
        let sortMode = false;

        // Configurar CSRF token para AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Toggle modo ordenamiento
        document.getElementById('toggleSort').addEventListener('click', function() {
            sortMode = !sortMode;
            const gallery = document.getElementById('imageGallery');

            if (sortMode) {
                this.innerHTML = '<i class="fas fa-save"></i> Guardar Orden';
                this.classList.remove('btn-outline-secondary');
                this.classList.add('btn-success');

                // Activar sortable
                sortable = Sortable.create(gallery, {
                    animation: 150,
                    ghostClass: 'sortable-ghost'
                });

                // Mostrar indicadores de orden
                document.querySelectorAll('.order-badge').forEach(badge => {
                    badge.style.display = 'block';
                });

            } else {
                this.innerHTML = '<i class="fas fa-sort"></i> Ordenar';
                this.classList.remove('btn-success');
                this.classList.add('btn-outline-secondary');

                // Guardar nuevo orden
                if (sortable) {
                    saveOrder();
                    sortable.destroy();
                    sortable = null;
                }
            }
        });

        // Guardar orden
        function saveOrder() {
            const gallery = document.getElementById('imageGallery');
            const items = gallery.children;
            const orderData = [];

            for (let i = 0; i < items.length; i++) {
                const imageId = items[i].getAttribute('data-image-id');
                orderData.push({
                    id: imageId,
                    order_position: i + 1
                });
            }

            fetch('{{ route("images.update-order") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ images: orderData })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Orden guardado exitosamente', 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error al guardar el orden', 'danger');
            });
        }

        // Mostrar imagen en modal
        function showImageModal(imageId) {
            // Aquí deberías hacer una llamada AJAX para obtener los datos de la imagen
            fetch(`/images/${imageId}`)
                .then(response => response.text())
                .then(html => {
                    // Parsear la respuesta y extraer los datos necesarios
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const img = doc.querySelector('.full-size-image');
                    const description = doc.querySelector('.image-description');

                    document.getElementById('modalImage').src = img.src;
                    document.getElementById('modalDescription').innerHTML = description.innerHTML;

                    new bootstrap.Modal(document.getElementById('imageModal')).show();
                });
        }

        // Editar imagen
        function editImage(imageId) {
            window.location.href = `/images/${imageId}/edit`;
        }

        // Recortar imagen
        function cropImage(imageId) {
            window.location.href = `/images/${imageId}/edit#crop`;
        }

        // Eliminar imagen
        function deleteImage(imageId) {
            if (confirm('¿Estás seguro de que quieres eliminar esta imagen?')) {
                fetch(`/images/${imageId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Imagen eliminada exitosamente', 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error al eliminar la imagen', 'danger');
                });
            }
        }

        // Mostrar alertas
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.top = '20px';
            alertDiv.style.right = '20px';
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);

            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    </script>

@endsection
