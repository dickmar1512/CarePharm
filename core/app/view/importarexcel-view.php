<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark"><i class='fas fa-file-excel text-success mr-2'></i> Importar Nueva compra vía Excel</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="./">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="./?view=importarexcel">Compras</a></li>
                    <li class="breadcrumb-item active">Nueva Compra Excel</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-9">
                
                <!-- Mensajes de Estado -->
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible shadow-sm border-0 mb-4">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-check"></i> ¡Éxito!</h5>
                        Se importaron <strong><?php echo $_GET['rows'] ?></strong> productos correctamente a la base de datos.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible shadow-sm border-0 mb-4">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h5><i class="icon fas fa-ban"></i> Error en la Importación</h5>
                        <?php echo htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <!-- Instrucciones -->
                    <div class="col-md-5">
                        <div class="card card-outline card-info shadow-sm h-100">
                            <div class="card-header py-2">
                                <h3 class="card-title text-sm font-weight-bold uppercase">Instrucciones de Uso</h3>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled text-sm">
                                    <li class="mb-3 d-flex">
                                        <div class="mr-3 text-info"><i class="fas fa-1 fa-2x"></i></div>
                                        <div>Descargue la plantilla oficial de Excel para asegurar el formato correcto.</div>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <div class="mr-3 text-info"><i class="fas fa-2 fa-2x"></i></div>
                                        <div>Complete los campos obligatorios: Nombre, Código, Precio de Venta y Stock Inicial.</div>
                                    </li>
                                    <li class="mb-3 d-flex">
                                        <div class="mr-3 text-info"><i class="fas fa-3 fa-2x"></i></div>
                                        <div>Suba el archivo y el sistema procesará los datos automáticamente.</div>
                                    </li>
                                </ul>
                                <hr>
                                <a href="dist/templates/plantilla_productos.xlsx" class="btn btn-outline-info btn-block btn-sm shadow-sm" download>
                                    <i class="fas fa-download mr-1"></i> Descargar Plantilla Modelo
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Panel de Carga -->
                    <div class="col-md-7">
                        <div class="card card-outline card-success shadow-sm h-100">
                            <div class="card-header py-2">
                                <h3 class="card-title text-sm font-weight-bold uppercase">Subir Archivo Excel</h3>
                            </div>
                            <div class="card-body py-4">
                                <form class="form-horizontal" method="post" id="importForm" action="./?view=addproductxls" role="form" enctype="multipart/form-data">
                                    <div class="upload-zone border-dashed p-5 text-center mb-4" id="drop-zone">
                                        <div class="mb-3">
                                            <i class="fas fa-cloud-upload-alt fa-4x text-success opacity-3"></i>
                                        </div>
                                        <p class="text-muted font-weight-bold mb-1">Arrastre el archivo aquí o haga clic para buscar</p>
                                        <p class="text-xs text-muted">Formatos permitidos: .xls, .xlsx</p>
                                        
                                        <input type="file" name="image" id="excelFile" class="custom-file-input" style="display:none;" required>
                                        <label class="btn btn-success mt-3" for="excelFile" id="fileLabel">
                                            <i class="fas fa-file-excel mr-2"></i> Seleccionar archivo
                                        </label>
                                        <div id="file-info" class="mt-2 font-weight-bold text-success text-sm" style="display:none;"></div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary btn-lg btn-block shadow-sm py-3 font-weight-bold" id="btnImport" disabled>
                                        <i class="fas fa-upload mr-2"></i> INICIAR PROCESAMIENTO
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .border-dashed {
        border: 2px dashed #28a745;
        border-radius: 10px;
        background-color: rgba(40, 167, 69, 0.02);
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .border-dashed:hover {
        background-color: rgba(40, 167, 69, 0.05);
        border-color: #218838;
    }
    .opacity-3 { opacity: 0.3; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
</style>

<script>
    document.getElementById('excelFile').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const fileName = file.name;
            const fileInfo = document.getElementById('file-info');
            fileInfo.innerHTML = `<i class="fas fa-check-circle mr-1"></i> Archivo listo: ${fileName}`;
            fileInfo.style.display = 'block';
            document.getElementById('fileLabel').innerHTML = '<i class="fas fa-sync-alt mr-2"></i> Cambiar archivo';
            document.getElementById('btnImport').disabled = false;
            document.getElementById('drop-zone').classList.add('bg-light');
        }
    });

    // Simple Drag & Drop visual effect
    const dropZone = document.getElementById('drop-zone');
    dropZone.onclick = () => document.getElementById('excelFile').click();
    
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
            e.preventDefault();
            dropZone.style.backgroundColor = '';
        }, false);
    });
</script>