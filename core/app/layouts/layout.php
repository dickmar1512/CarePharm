<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>..::CarePharm::..</title>
    <meta content='width=device-width, initial-scale=1' name='viewport'>
    <link rel="shortcut icon" href="dist/img/favicon.ico">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="dist/css/fonts.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="plugins/fontawesome-free/css/v4-shims.min.css">
    <link rel="stylesheet" href="plugins/font-awesome6.0.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

    <!-- Ionicons -->
    <link rel="stylesheet" href="plugins/ionicons/ionicons.min.2.0.1.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="plugins/jqvmap/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="plugins/summernote/summernote-bs4.min.css">

    <!-- Select2 -->
    <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

    <!-- style para login -->
    <link href="dist/css/boxicons.min.css" rel="stylesheet">
    <link href="dist/css/style.css" rel="stylesheet">

    <!-- SweetAlert2   -->
    <link rel="stylesheet" href="plugins/sweetalert2/css/sweetalert2.min.css">
    <script src="plugins/sweetalert2/js/sweetalert2@11.js"></script>

    <!-- jsPDF -->
    <script src="plugins/jspdf/jspdf.umd.min.js"></script>
    <!-- jspdf-autotable -->
    <script src="plugins/jspdf/jspdf.plugin.autotable.min.js"></script>

    <!-- QR CODE -->
    <script src="plugins/jsqrcode/qrcode.min.js"></script>

    <link rel="stylesheet" href="dist/css/modal.css">

    <style type="text/css">
        /* Estilos para el botón */
        .btn-full {
            width: 100%;
            /* Ocupa el 100% del ancho del contenedor */
            height: 100%;
            /* Ocupa el 100% del alto del contenedor */
            display: flex;
            /* Usa flexbox para centrar el contenido */
            justify-content: center;
            /* Centra horizontalmente */
            align-items: center;
            /* Centra verticalmente */
            background-color: #888;
            /* Color de fondo */
            color: #FFF;
            /* Color del texto */
            border: none;
            /* Sin borde */
            padding: 0;
            /* Sin padding */
            margin: 0;
            /* Sin margen */
            text-align: center;
            /* Centra el texto */
            cursor: pointer;
            /* Cambia el cursor al pasar sobre el botón */
        }

        /* Contenedor del ícono */
        .icon-container {
            position: relative;
            display: inline-block;
        }

        /* Estilo del ícono */
        .icon-container .icon-inbox {
            font-size: 48px;
            /* Tamaño del ícono */
            color: #007bff;
            /* Color del ícono (azul de Bootstrap) */
            position: relative;
            z-index: 2;
            /* Asegura que el ícono esté por encima de la sombra */
        }

        /* Sombra circular */
        .icon-container::after {
            content: '';
            position: absolute;
            top: 80%;
            left: 50%;
            transform: translate(-50%, -60%);
            width: 60px;
            /* Tamaño de la sombra */
            height: 20px;
            /* Tamaño de la sombra */
            background-color: rgba(0, 123, 255, 0.2);
            /* Color de la sombra (azul claro) */
            border-radius: 50%;
            /* Hace que la sombra sea circular */
            z-index: 1;
            /* Coloca la sombra detrás del ícono */
            box-shadow: 0 0 20px rgba(0, 123, 255, 0.3);
            /* Sombra adicional */
        }

        /* Efecto hover (opcional) */
        .icon-container:hover .icon-inbox {
            color: #0056b3;
            /* Cambia el color del ícono al pasar el mouse */
        }

        .icon-container:hover::after {
            background-color: rgba(0, 86, 179, 0.3);
            /* Cambia el color de la sombra al pasar el mouse */
        }

        /* Contenedor relativo para posicionar el ícono */
        .input-icon-container {
            position: relative;
            width: 100%;
            /* Ajusta el ancho según sea necesario */
        }

        /* Estilo para el contenedor del ícono */
        .input-icon-container .icon-wrapper {
            position: absolute;
            right: 0;
            /* Alinea a la derecha */
            top: 0;
            height: 100%;
            /* Misma altura que el input */
            width: 30px;
            /* Ancho del área del ícono */
            background-color: #007bff;
            /* Color de fondo del ícono */
            display: flex;
            align-items: center;
            justify-content: center;
            border-top-right-radius: 4px;
            /* Bordes redondeados */
            border-bottom-right-radius: 4px;
            pointer-events: none;
            /* Evita que el ícono interfiera con el input */
        }


        /* Estilo para el ícono */
        .input-icon-container .glyphicon {
            position: absolute;
            right: 10px;
            /* Distancia desde el borde derecho */
            top: 50%;
            /* Centra verticalmente */
            transform: translateY(-50%);
            /* Ajuste fino para centrar */
            pointer-events: none;
            /* Evita que el ícono interfiera con el input */
            color: rgb(247, 244, 244);
            /* Color del ícono */
        }

        /* Ajuste del padding del input para que el texto no se superponga con el ícono */
        .input-icon-container input {
            padding-right: 30px;
            /* Espacio para el ícono */
        }

        .custom-file-input {
            opacity: 0; /* Hace el input invisible */
            position: absolute; /* Lo saca del flujo normal del documento */
            left: 0; /* Lo posiciona a la izquierda */
            top: 0; /* Lo alinea en la parte superior */
            width: auto; /* Ajusta el ancho automáticamente */
            height: 100%; /* Ocupa toda la altura del contenedor */
            cursor: pointer; /* Cambia el cursor a pointer para indicar que es clickeable */
        }

        /**Estilo para el modal de carga */
        #loadingModal .modal-dialog {
            max-width: 350px;
        }

        #loadingModal .modal-content {
            background-color: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            padding: 20px;
        }

        #loadingModal .modal-body {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px;
        } 
    </style>

</head>

<body
    style="<?php if (isset($_SESSION["user_id"]) || isset($_SESSION["client_id"])): ?> <?php else: ?>background-image: url('img/fondo3.jpg') !important; background-repeat: no-repeat;  background-size: cover !important;<?php endif; ?>"
    class="<?php if (isset($_SESSION["user_id"]) || isset($_SESSION["client_id"])): ?> hold-transition sidebar-mini layout-fixed  <?php else: ?>login-page<?php endif; ?>">
    <div class="wrapper">
        <?php if (isset($_SESSION["user_id"]) || isset($_SESSION["client_id"])): ?>
            <!-- Navbar -->
            <nav class="main-header navbar navbar-expand navbar-white navbar-light">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </li>
                    <li class="nav-item d-none d-sm-inline-block">
                        <a href="./?" class="nav-link">Inicio</a>
                    </li>
                </ul>

                <!-- Right navbar links -->
                <ul class="navbar-nav ml-auto">
                    <!-- Usuario conectado -->
                    <li class="nav-item dropdown">
                        <a class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fa fa-user-lock"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <a href="#" class="dropdown-item">
                                <!-- Dato de usuario -->
                                <div class="media">
                                    <img src="dist/img/avatar5.png" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                                    <div class="media-body">
                                        <h3 class="dropdown-item-title">
                                            <?php if (isset($_SESSION["user_id"])) {
                                                echo UserData::getById($_SESSION["user_id"])->name;
                                            } ?>
                                            <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                                        </h3>
                                    </div>
                                </div>
                                <!---fin  dato usuario -->
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="./logout.php" class="dropdown-item">
                                <!-- Cerrar sesion -->
                                <div class="media">
                                    <i class="fa fa-power-off img-circle mr-3"></i>
                                    <div class="media-body">
                                        <h3 class="dropdown-item-title">
                                            Cerrar Sesión
                                        </h3>
                                    </div>
                                </div>
                                <!-- fin cerrar sesion -->
                            </a>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.navbar -->

            <!-- Main Sidebar Container -->
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="./?view=home" class="brand-link">
                    <img src="dist/img/AdminLTELogo.png" alt="CarePharm Logo" class="brand-image img-circle elevation-3"
                        style="opacity: .8">
                    <span class="brand-text font-weight-light">CarePharm</span>
                </a>
                <!-- sidebar: style can be found in sidebar.less -->
                <div class="sidebar">
                    <!-- Sidebar user panel (optional) -->
                    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                        <div class="image">
                            <img src="dist/img/avatar5.png" class="img-circle elevation-2" alt="User Image">
                        </div>
                        <div class="info">
                            <?php if (isset($_SESSION["user_id"])) { ?>
                                <a href="#" class="d-block"><?= UserData::getById($_SESSION["user_id"])->name ?></a>
                            <?php } ?>

                        </div>
                    </div>

                    <!-- SidebarSearch Form -->
                    <div class="form-inline">
                        <div class="input-group" data-widget="sidebar-search">
                            <input class="form-control form-control-sidebar" type="search" placeholder="Search"
                                aria-label="Search">
                            <div class="input-group-append">
                                <button class="btn btn-sidebar">
                                    <i class="fas fa-search fa-fw"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Menu -->
                    <nav class="mt-2">
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                            data-accordion="false">
                            <?php
                            $admin = UserData::getById($_SESSION["user_id"])->is_admin;
                            $dirtec = UserData::getById($_SESSION["user_id"])->is_dirtec;
                            $caja = UserData::getById($_SESSION["user_id"])->is_caja;
                            $motonmax = UserData::getById($_SESSION["user_id"])->montomax;
                            if (isset($_SESSION["user_id"])): ?>
                                <li class="nav-item">
                                    <a href="./?view=home" class="nav-link">
                                        <i class='fa fa-home'></i>
                                        <p>Inicio</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class='fa fa-shopping-cart'></i>
                                        <p>Venta</p>
                                        <i class="right fas fa-angle-left"></i>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="./?view=sell" class="nav-link" data-view="sell">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Generar Venta</p>
                                            </a>
                                        </li>                                       
                                        <li class="nav-item">
                                            <a href="./?view=sells" class="nav-link" data-view="sells">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Registro Venta</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="./?view=sellsnew" class="nav-link" data-view="sellsnew">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Registro Venta NEW</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="./?view=sellreportsProducts" class="nav-link" data-view="sellreportsProducts">
                                                <i class="nav-icon far fa-circle text-info"></i> 
                                                Ventas x Producto
                                            </a>
                                        </li>     
                                    </ul>
                                </li>
                                <?php if ($admin == 1 || $dirtec == 1) { ?>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class="bx bxs-shopping-bag"></i>
                                            <p>Compras</p>
                                            <i class="right fas fa-angle-left"></i>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="./?view=re" class="nav-link" data-view="re">
                                                    <i class="nav-icon far fa-circle text-info"></i>
                                                    <p>Reabastecer</p>
                                                </a>
                                            </li>                                            
                                            <li class="nav-item">
                                                <a href="./?view=res" class="nav-link" data-view="res">
                                                    <i class="nav-icon far fa-circle text-info"></i>
                                                    <p>Registro Compra</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                <?php } ?>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class='fa fa-cube'></i>
                                        <p>Caja</p>
                                        <i class="right fas fa-angle-left"></i>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="./?view=box" class="nav-link" data-view="box">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Cierre de Caja</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="./?view=expenseentry" class="nav-link" data-view="expenseentry">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Ingreso gastos</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <?php if ($admin == 1 || $dirtec == 1) { ?>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class='fas fa-tag'></i>
                                            <p>Productos/Servicios</p>
                                            <i class="right fas fa-angle-left"></i>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li>
                                                <a href="./?view=products" class="nav-link" data-view="products">
                                                    <i class="nav-icon far fa-circle text-info"></i>
                                                    <p>Listado Producto</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="./?view=importarexcel" class="nav-link" data-view="importarexcel">
                                                    <i class="nav-icon far fa-circle text-info"></i>
                                                    <p>Importar Excel</p>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="./?view=paquetes" class="nav-link" data-view="paquetes">
                                                    <i class="nav-icon far fa-circle text-info"></i>
                                                    <p>Listado Presentacion</p>
                                                </a>
                                            </li>
                                        </ul>  
                                    </li>
                                <?php } ?>
                                <?php if ($admin == 1 || $dirtec == 1) { ?>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class='fa fa-database'></i>
                                            <p>Catalogos</p>
                                            <i class="right fas fa-angle-left"></i>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="./?view=unidades" class="nav-link" data-view="unidades">
                                                    <i class="nav-icon far fa-circle text-info"></i>
                                                    <p>Unidades</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="./?view=categories" class="nav-link" data-view="categories">
                                                    <i class="nav-icon far fa-circle text-info"></i>
                                                    <p>Categorias</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class='fa fa-area-chart'></i>
                                            <p>Inventarios</p>
                                            <i class="right fas fa-angle-left"></i>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="./?view=inventary" class="nav-link" data-view="inventary">
                                                    <i class="nav-icon far fa-circle text-info"></i>
                                                    <p>Inventario</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="./?view=reports" class="nav-link" data-view="reports">
                                                    <i class="nav-icon far fa-circle text-info"></i>
                                                    <p>Reporte Inventario</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="./?view=kardexbyproducto" class="nav-link">
                                                    <i class="nav-icon far fa-circle text-info"></i>
                                                    <p>Kardex</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                <?php } ?>
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class='fa fa-bar-chart'></i>
                                        <p>Reportes</p>
                                        <i class="right fas fa-angle-left"></i>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="./?view=sellreports" class="nav-link" data-view="sellreports">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Ventas x Cliente</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="./?view=sellsRepostNotaVenta" class="nav-link" data-view="sellsRepostNotaVenta">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>Registro Nota Venta</p>
                                            </a>
                                        </li>
                                        <?php if($admin ==1 || $dirtec ==1) {?>
                                        <li class="nav-item">
                                            <a href="./?view=reportsboleta" class="nav-link" data-view="reportsboleta">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                Boletas
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="./?view=reportsfactura" class="nav-link" data-view="reportsfactura">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                Factura
                                            </a>
                                        </li>
                                        <?php }?>
                                        <li class="nav-item">
                                            <a href="./?view=reportsnotascredito" class="nav-link" data-view="reportsnotascredito">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>N.C. Factura</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="./?view=reportsnotascreditoboleta" class="nav-link" data-view="reportsnotascreditoboleta">
                                                <i class="nav-icon far fa-circle text-info"></i>
                                                <p>N.C. Boleta</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <?php if ($admin == 1 || $dirtec == 1) { ?>
                                    <li class="nav-item">
                                        <a href="#" class="nav-link">
                                            <i class='fa fa-cog'></i>
                                            <p>Administracion</p>
                                            <i class="right fas fa-angle-left"></i>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <?php if ($admin == 1) { ?>
                                            <li class="nav-item">
                                                <a href="./?view=users" class="nav-link" da>
                                                    <i class="nav-icon far fa-circle text-info"></i>
                                                    <p>Usuarios</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="./?view=settings" class="nav-link">
                                                    <i class="nav-icon far fa-circle text-info"></i>
                                                    <p>Empresa</p>
                                                </a>
                                            </li> 
                                            
                                            <?php } ?>                                           
                                            <li class="nav-item">
                                                <a href="./?view=clients" class="nav-link">
                                                    <i class="nav-icon far fa-circle text-info"></i>
                                                    <p>Clientes</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="./?view=providers" class="nav-link">
                                                    <i class="nav-icon far fa-circle text-info"></i>
                                                    <p>Proveedores</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                <?php } ?>
                            <?php endif; ?>

                        </ul>
                    </nav>
                    <!-- /.sidebar-menu -->
                </div>
                <!-- /.sidebar -->
            </aside>
        <?php endif; ?>

        <!-- Content Wrapper. Contains page content -->
        <?php if (isset($_SESSION["user_id"]) || isset($_SESSION["client_id"])): ?>
            <div class="content-wrapper">
                <?php View::load("index"); ?>
            </div>
            <!-- /.content-wrapper -->
            <footer class="main-footer">
                <strong>Copyright &copy; 2022-2025 CAREPHARM</strong>
                All rights reserved.
                <div class="float-right d-none d-sm-inline-block">
                    <b>Version</b> 3.0
                </div>
            </footer>            
        <?php else: ?>
            <div class="bodyLogin">
                <div class="containerLogin">
                    <div class="form-box login">
                        <!-- <form action="./?action=processlogin" method="post"> -->
                        <form id="loginForm" method="post">    
                            <h1>Care<b>Pharm</b></h1>
                            <p>Use su usuario y contraseña</p>
                            <!-- <div id="loginMessage" class="alert alert-danger" style="display:none;"></div> -->
                            <div class="input-box">
                                <input type="text" id="username" name="username" placeholder="Username"
                                    autocomplete="username" required>
                                <i class="bx bxs-user"></i>
                            </div>
                            <div class="input-box">
                                <input type="password" id="password" name="password" placeholder="Password"
                                    autocomplete="current-password" required>
                                <i class="bx bxs-lock-alt"></i>
                            </div>
                            <button type="submit" class="btnLogin">Iniciar</button>
                            <p>Plataformas sociales</p>
                            <div class="social-icons">
                                <a href="#"><i class="bx bxl-google"></i></a>
                                <a href="#"><i class="bx bxl-facebook"></i></a>
                                <a href="#"><i class="bx bxl-github"></i></a>
                                <a href="#"><i class="bx bxl-linkedin"></i></a>
                                <a href="#"><i class='bx bxl-twitter'></i></a>
                            </div>
                        </form>
                    </div>

                    <div class="toggle-box">
                        <div class="toggle-panel toggle-left">
                            <h1>Hola, ¡Bienvenido!</h1>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <!-- ./wrapper -->
     <!-- Modal de carga con tu loader angular personalizado -->
    <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="background-color: transparent; border: none;">
                <div class="modal-body text-center">
                    <style type='text/css'>
                        /**Estilo loader tipo angular */
                        .angular-loader {
                            width: 100px;
                            height: 100px;
                            border-radius: 50%;
                            display: inline-block;
                            border-top: 4px solid #FF3D00;
                            border-right: 4px solid transparent;
                            box-sizing: border-box;
                            animation: rotation 1s linear infinite;
                            position: relative;
                            margin: 0 auto;
                        }

                        .angular-loader::before {
                            content: '';
                            box-sizing: border-box;
                            position: absolute;
                            left: 0;
                            top: 0;
                            width: 80px;
                            height: 80px;
                            border-radius: 50%;
                            border-left: 4px solid #808080;
                            border-bottom: 4px solid transparent;
                            animation: rotation 0.5s linear infinite reverse;
                            margin: 10px;
                        }

                        .angular-loader::after {
                            content: '';
                            box-sizing: border-box;
                            position: absolute;
                            left: 0;
                            top: 0;
                            width: 60px;
                            height: 60px;
                            border-radius: 50%;
                            border-right: 4px solid #00BFFF;
                            border-top: 4px solid transparent;
                            animation: rotation 1s linear infinite;
                            margin: 20px;
                        }

                        @keyframes rotation {
                            0% { transform: rotate(0deg); }
                            100% { transform: rotate(360deg); }
                        }
                        
                        .loading-text {
                            color: white;
                            margin-top: 20px;
                            font-size: 1.2rem;
                            font-weight: bold;
                        }
                    </style>
                    
                    <div class="angular-loader"></div>
                    <p class="loading-text">Autenticando...</p>
                    <div id="loginMessage" class="alert alert-danger" style="display:none;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- REQUIRED JS SCRIPTS -->
    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="plugins/jquery-ui/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 -->
    <script src="plugins/select2/js/select2.full.min.js"></script>
    <!-- ChartJS -->
    <script src="plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="plugins/sparklines/sparkline.js"></script>
    <!-- JQVMap -->
    <script src="plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="plugins/moment/moment.min.js"></script>
    <script src="plugins/moment/locale/es.js"></script>
    <script src="plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="plugins/summernote/summernote-bs4.min.js"></script>
    <!-- overlayScrollbars -->
    <script src="plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="plugins/jszip/jszip.min.js"></script>
    <script src="plugins/pdfmake/pdfmake.min.js"></script>
    <script src="plugins/pdfmake/vfs_fonts.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>

    <!-- dropzonejs -->
    <script src="plugins/dropzone/min/dropzone.min.js"></script>

    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.js"></script>
    <script src="dist/js/demo.js"></script>

    <!-- Scripts home -->
    <!-- <script src="dist/js/pages/home.js"></script> -->

    <!-- Scripts sell -->
    <!-- <script src="dist/js/pages/sell.js"></script> -->

    <!-- Scripts sells -->
   <!-- <script src="dist/js/pages/sells.js"></script> -->

    <!-- Scripts products -->
    <!-- <script src="dist/js/pages/products.js"></script> -->

    <!-- Scripts providers -->
    <!-- <script src="dist/js/pages/providers.js"></script> -->
    
    <!-- Scripts providers -->
    <!-- <script src="dist/js/pages/clients.js"></script> -->

    <!-- Scripts registro de compra-->
    <!-- <script src="dist/js/pages/res.js"></script> -->

    <!-- Scripts reabastecer compra-->
    <!-- <script src="dist/js/pages/re.js"></script> -->

     <!-- Scripts nota de credito factura-->
    <!-- <script src="dist/js/pages/nocfactura.js"></script> -->

    <!-- Scripts nota de credito boleta-->
    <!-- <script src="dist/js/pages/nocboleta.js"></script> -->

    <!-- Scripts unidades-->
    <!-- <script src="dist/js/pages/unidades.js"></script> -->

    <!-- Scripts categorias-->
    <!-- <script src="dist/js/pages/categories.js"></script> -->

    <!-- Scripts Box-->
    <!-- <script src="dist/js/pages/box.js"></script> -->

    <!-- Scripts gastos-->
    <!-- <script src="dist/js/pages/expenseentry.js"></script> -->
     
    <script src="dist/js/pages/main.js"></script>

    <!-- Scripts onesell -->     
    <script src="dist/js/imprimir50.js" type="text/javascript"></script>
    <script src="dist/js/imprimir80.js" type="text/javascript"></script>
    <script src="dist/js/imprimirA5.js" type="text/javascript"></script>
    <script src="dist/js/imprimirA4.js" type="text/javascript"></script>

    <script type="text/javascript">
         $(function () {
            //Initialize Select2 Elements
            //$('.select2').select2();

            //Initialize Select2 Elements
            $('.select2bs4').select2({
            theme: 'bootstrap4'
            })
        });
        moment.updateLocale('es', {
            week: {
                dow: 0,  // Domingo como primer día
            }
        });

        var parTooltip = {
            today: 'Ir a hoy',
            clear: 'Limpiar selección',
            close: 'Cerrar',
            selectMonth: 'Seleccionar mes',
            prevMonth: 'Mes anterior',
            nextMonth: 'Mes siguiente',
            selectYear: 'Seleccionar año',
            prevYear: 'Año anterior',
            nextYear: 'Año siguiente',
            selectDecade: 'Seleccionar década',
            prevDecade: 'Década anterior',
            nextDecade: 'Década siguiente',
            prevCentury: 'Siglo anterior',
            nextCentury: 'Siglo siguiente'
        };

        $('#fechaini').datetimepicker({
            format: 'L',
            locale: 'es',
            buttons: {
                showToday: true,
                showClose: true
            },
            tooltips: parTooltip
        });

        $('#fechafin').datetimepicker({
            format: 'L',
            locale: 'es',
            buttons: {
                showToday: true,
                showClose: true
            },
            tooltips: parTooltip
        });

        // Inicializar select2
        // $('.select2').select2({
        //     theme: 'bootstrap4'
        // }); 

        $(document).ready(function () {
                $(".datatable").DataTable({
                    "responsive": true, 
                    "lengthChange": true, 
                    "autoWidth": false,
                    "dom": '<"row"<"col-md-3"l><"col-md-6 text-center"B><"col-md-3"f>>rtip',
                    "buttons": [
                        { extend: "copy", text: "Copiar" }, 
                        "csv", 
                        "excel", 
                        "pdf", 
                        "print", 
                        { extend: "colvis", text: "Visible" }
                    ],
                    "language": {
                        "sProcessing": "Procesando...",
                        "sLengthMenu": "Mostrar _MENU_ registros",
                        "sZeroRecords": "No se encontraron resultados",
                        "sEmptyTable": "Ningún dato disponible en esta tabla",
                        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                        "sInfoPostFix": "",
                        "sSearch": "Buscar:",
                        "sUrl": "",
                        "sInfoThousands": ",",
                        "sLoadingRecords": "Cargando...",
                        "oPaginate": {
                            "sFirst": "Primero",
                            "sLast": "Último",
                            "sNext": "Siguiente",
                            "sPrevious": "Anterior"
                        },
                        "oAria": {
                            "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                        }
                    },
                    
                });
             /**
            * Script que revisa el monto máximo que debe tener en caja para hacer cierre
            */ 

            <?php if (isset($_SESSION["user_id"])): ?>
                let iduser = '<?= $_SESSION["user_id"] ?>';
                <?php 
                $motonmax = 0; // Valor por defecto
                if (isset($_SESSION["user_id"])) {
                    $user = UserData::getById($_SESSION["user_id"]);
                    $motonmax = $user ? $user->montomax : 0;
                }
                ?>
                let motonmax = <?= $motonmax ?>;
                
                if(iduser && motonmax > 0){
                    let checkInterval;
                    const CHECK_DELAY = 600000; // 10 minutos en milisegundos (1000 * 60 * 10)                     
                    // Iniciar el intervalo
                    checkMonto(motonmax);
                    
                    function checkMonto(montoMaximo) {
                        // Tu código existente para checkMonto
                    }
                }
            <?php endif; ?>
            
            $('#imprimir').click(function() {
                $('#imprimir').hide();
                $('#div_opciones').hide();
                $('.main-footer').hide();
                $('.card-header').hide();
                window.print();

                $('#imprimir').show();
                $('#div_opciones').show();
                $('.main-footer').show();
                $('.card-header').show();  
            });
            

             $('#loginForm').on('submit', function(e) {
                e.preventDefault();
                
                // Mostrar modal de carga
                $('#loadingModal').modal({
                    backdrop: 'static',
                    keyboard: false
                }).modal('show');
                
                // Ocultar mensaje de error si existe
                $('#loginMessage').hide();
                
                // Limpiar mensajes anteriores
                $('#loginMessage').removeClass('alert-success alert-danger');
                
                // Enviar datos por AJAX
                $.ajax({
                    url: './?action=processlogin',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        // Cerrar el modal primero
                        $('#loadingModal').modal('hide');
                        
                        if(response.success) {
                            // Mostrar mensaje de éxito brevemente antes de redirigir
                            $('#loginMessage').addClass('alert-success')
                                            .text('Autenticación exitosa, redirigiendo...')
                                            .show();
                            
                            // Redirigir después de 2 segundo
                            setTimeout(function() {
                                window.location.href = './?view=home';
                            }, 2000);
                        } else {                            
                            // Redirigir después de 2 segundo
                             setTimeout(function() {
                                 window.location.href = './?view=login';
                             }, 2000);
                            // Mostrar error específico
                            $('#loginMessage').addClass('alert-danger')
                                            .text(response.message || 'Credenciales incorrectas')
                                            .show();                            
                            // Enfocar el campo de usuario
                            $('#username').focus();
                        }
                    },
                    error: function(xhr, status, error) {
                        // Cerrar el modal en caso de error
                        $('#loadingModal').modal('hide');
                        
                        // Mostrar mensaje de error de conexión
                        $('#loginMessage').addClass('alert-danger')
                                        .text('Error de conexión: ' + (xhr.responseJSON?.message || 'Intente nuevamente'))
                                        .show();
                        
                        console.error('Error en login:', status, error);
                    },
                    complete: function() {
                        // Esta función se ejecuta siempre, después de success o error
                        // Podemos usarla para limpieza si es necesario
                    }
                });
            });
        });
        
        // Función para ejecutar la consulta AJAX
        function checkMonto(montomax) {                                
            $.ajax({
                url: './?action=getMontoMax',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.montocaja >= montomax) {
                        showModal(response.montocaja, montomax);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error en la consulta:", error);
                }
            });
        }

        // Función para mostrar el modal con SweetAlert2
        function showModal(monto,montomax) {
            Swal.fire({
                title: `Monto en caja es : ${monto}<br> lo permitido es : ${montomax}`,
                html: '¿Qué deseas hacer?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Cerrar caja',
                cancelButtonText: 'Posponer 10 minutos',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                 // Guardar el momento del redireccionamiento
                    lastRedirectTime = Date.now();
                    window.location.href = './?view=box';
                } else {
                                        // Si pospone, simplemente continuar con el intervalo existente
                }
            });
        }   

        // Función para volver a la página anterior
        function goBack() {
            window.history.back();
        }

        /**
         * Formatea una fecha en formato dd/mm/yyyy
         * @param {string} fechaString - La fecha en formato ISO 8601 (ejemplo: "2023-10-01")
         * @returns {string} La fecha formateada en formato dd/mm/yyyy o un mensaje de error si la fecha es inválida
         */
        function formatearFecha(fechaString) {
            try {
                const fechaObj = new Date(fechaString);
                
                if (isNaN(fechaObj.getTime())) {
                throw new Error('Fecha inválida');
                }
                
                const dia = fechaObj.getDate().toString().padStart(2, '0');
                const mes = (fechaObj.getMonth() + 1).toString().padStart(2, '0');
                const año = fechaObj.getFullYear();
                
                return `${dia}/${mes}/${año}`;
            } catch (error) {
                console.error('Error al formatear fecha:', error);
                return 'Fecha inválida';
            }
        }

        function initDataTable(tableSelector) {

            $(tableSelector).DataTable({
                    "responsive": true, 
                    "lengthChange": true, 
                    "autoWidth": false,
                    "dom": '<"row"<"col-md-3"l><"col-md-6 text-center"B><"col-md-3"f>>rtip',
                    "buttons": [
                        { extend: "copy", text: "Copiar" }, 
                        "csv", 
                        "excel", 
                        "pdf", 
                        "print", 
                        { extend: "colvis", text: "Visible" }
                    ],
                    "language": {
                        "sProcessing": "Procesando...",
                        "sLengthMenu": "Mostrar _MENU_ registros",
                        "sZeroRecords": "No se encontraron resultados",
                        "sEmptyTable": "Ningún dato disponible en esta tabla",
                        "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                        "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                        "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                        "sInfoPostFix": "",
                        "sSearch": "Buscar:",
                        "sUrl": "",
                        "sInfoThousands": ",",
                        "sLoadingRecords": "Cargando...",
                        "oPaginate": {
                            "sFirst": "Primero",
                            "sLast": "Último",
                            "sNext": "Siguiente",
                            "sPrevious": "Anterior"
                        },
                        "oAria": {
                            "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                            "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                        }
                    },
                    
                });
            }   
    </script>

</body>
</html>