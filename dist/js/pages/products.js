// Venta modal
function openProductModal(action, productData = null) {
    // Construir las opciones del select de categorías
    let categoryOptions = '<option value="">SELECCIONAR</option>';
    categories.forEach(category => {
        categoryOptions += `<option value="${category.id}" ${productData && productData.category_id == category.id ? 'selected' : ''}>${category.name}</option>`;
    });

    // Construir las opciones del select de unidades de medida
    let unidadesOptions = '<option value="">SELECCIONAR</option>';
    unidades.forEach(unidad => {
        unidadesOptions += `<option value="${unidad.id}" ${productData && productData.unit == unidad.id ? 'selected' : ''}>${unidad.name}</option>`;
    });
    //console.log("productData==>",productData);
    Swal.fire({
        title: action === 'edit' ? 'Editar Producto/Servicio' : 'Nuevo Producto/Servicio',
        html: `<hr>
                <form id="productForm">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="image" style="display: flex; justify-content: left;">Imagen*</label>
                                <div class="custom-file" style="display: flex; justify-content: left;">
                                    <input type="file" name="image" id="image" class="custom-file-input">
                                    <label class="btn btn-success" for="image">
                                        <i class="fas fa-upload"></i> Seleccionar archivo
                                    </label>
                                    <span id="file-name" class="ml-2">${productData && productData.image ? productData.image : ''}</span>
                                    ${productData && productData.image ? `<br><img src="storage/products/${productData.image}" class="img-responsive" style="max-width: 100px; margin-top: 10px;">` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="barcode" style="display: flex; justify-content: left;">Código de Barras*</label>
                                <input type="text" name="barcode" id="barcode" class="form-control" placeholder="Código de Barras" value="${productData ? productData.barcode : ''}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="category_id" style="display: flex; justify-content: left;">Categoría</label>
                                <select name="category_id" class="form-control">
                                    ${categoryOptions}
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="selUnidadMedida" style="display: flex; justify-content: left;">Unidad de Medida*</label>
                                <select name="selUnidadMedida" class="form-control" required>
                                    ${unidadesOptions}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" style="display: flex; justify-content: left;">Nombre*</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Nombre del Producto" value="${productData ? productData.name : ''}" required>
                    </div>
                    <div class="form-group">
                        <label for="prin_act" style="display: flex; justify-content: left;">Principio Activo</label>
                        <textarea name="prin_act" id="prin_act" class="form-control" placeholder="Principio Activo">${productData ? productData.principio_activo : '-'}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="description" style="display: flex; justify-content: left;">Descripción</label>
                        <textarea name="description" id="description" class="form-control" placeholder="Descripción del Producto">${productData ? productData.description : ''}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="presentacion" style="display: flex; justify-content: left;">Presentación</label>
                        <textarea name="presentacion" id="presentacion" class="form-control" placeholder="Presentación del Producto">${productData ? productData.presentation : ''}</textarea>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                               <label for="price_in" style="display: flex; justify-content: left;">Precio de Entrada*</label>
                               <input type="text" name="price_in" id="price_in" class="form-control" placeholder="Precio de Entrada" value="${productData ? productData.price_in : ''}" required>
                            </div>                            
                            <div class="col-md-4">
                                <label for="price_out" style="display: flex; justify-content: left;">Precio de Salida*</label>
                                <input type="text" name="price_out" id="price_out" class="form-control" placeholder="Precio de Salida" value="${productData ? productData.price_out : ''}" required>
                            </div>                            
                            <div class="col-md-4">
                                <label for="price_may" style="display: flex; justify-content: left;">Precio Por Mayor*</label>
                                <input type="text" name="price_may" id="price_may" class="form-control" placeholder="Precio Por Mayor" value="${productData ? productData.price_may : ''}" required>
                            </div>    
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="anaquel" style="display: flex; justify-content: left;">Anaquel*</label>
                                <input type="text" name="anaquel" id="anaquel" class="form-control" placeholder="Anaquel" value="${productData ? productData.anaquel : ''}" required>
                            </div>                                                                    
                            <div class="col-md-4">
                                <label for="inventary_min" style="display: flex; justify-content: left;">Mínima en Inventario</label>
                                <input type="text" name="inventary_min" id="inventary_min" class="form-control" placeholder="Mínima en Inventario (Default 10)" value="${productData ? productData.inventary_min : ''}">
                            </div>                          
                            <div class="col-md-4">
                                <label for="q" style="display: flex; justify-content: left;">Inventario Inicial</label>
                                <input type="text" name="q" id="q" class="form-control" placeholder="Inventario Inicial" value="${productData ? productData.stock : ''}">
                            </div>   
                        </div>   
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="icheck-primary d-inline">
                                    <input type="checkbox" name="is_stock" id="is_stock" ${((productData && productData.is_stock==1) || action =='add') ? 'checked' : ''}>
                                    <label for="is_stock">Con Stock</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="icheck-success d-inline">
                                    <input type="checkbox" name="is_active" id="is_active" ${((productData && productData.is_active==1)|| action =='add') ? 'checked' : ''}>
                                    <label for="is_active">Está Activo</label>
                                </div>
                            </div>                            
                            <div class="col-md-4">
                                <div class="icheck-warning d-inline">
                                    <input type="checkbox" name="is_may" id="is_may" ${(productData && productData.is_may==1) ? 'checked' : ''}>
                                    <label for="is_may">Precio por Mayor</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="product_id" value="${productData ? productData.id : ''}">
                    <input type="hidden" name="fecha_venc" value="${fechaVencimiento}">
                </form>`,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: action === 'edit' ? 'Actualizar Producto' : 'Agregar Producto',
        cancelButtonText: 'Cancelar',
        customClass: {
            container: 'custom-swal-container',
            popup: 'custom-swal-popup',
            header: 'custom-swal-header',
            title: 'custom-swal-title',
            content: 'custom-swal-content',
            closeButton: 'custom-swal-close-button'
        },
        width: '50%',
        didOpen: () => {
            if (!productData) {
                $('#barcode').val($('#genbarcode').val());
            }
            document.getElementById('image').addEventListener('change', function (e) {
                const fileName = e.target.files[0].name;
                document.getElementById('file-name').textContent = fileName;
            });
        },
        preConfirm: () => {
            const form = document.getElementById('productForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            if (!data.barcode || !data.name || !data.price_in || !data.price_out || !data.anaquel || !data.selUnidadMedida) {
                Swal.showValidationMessage('Por favor, completa todos los campos obligatorios');
                return false;
            }
            return data;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const url = action === 'edit' ? './?action=updateproduct' : './?action=addproduct';
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(result.value),
            })
                .then(response => response.json())
                .then(response => { console.log("response==>",response);
                    if (response.success) {
                        Swal.fire('Éxito!', `El producto ha sido ${action === 'edit' ? 'actualizado' : 'agregado'} correctamente.`, 'success')
                            .then(() => window.location = '?view=products');
                    } else {
                        Swal.fire('Error!', `Hubo un problema al ${action === 'edit' ? 'actualizar' : 'agregar'} el producto.`, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', `Hubo un problema al ${action === 'edit' ? 'actualizar' : 'agregar'} el producto.`, 'error');
                });
        }
    });
}

// Evento para abrir el modal de nuevo producto
$('#openModalNuevoProducto').on('click', function () {
    openProductModal('add');
});

// Evento para abrir el modal de edición de producto
$('.edit-product').on('click', function () {
    const productId = this.getAttribute('data-id');
    fetch(`./?action=getproduct&id=${productId}`)
        .then(response => response.json())
        .then(productData => {
            openProductModal('edit', productData);
        })
        .catch(error => {
            console.error('Error al obtener los datos del producto:', error);
        });
});