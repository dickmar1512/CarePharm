<?php
// autoload.php
// 10 octubre del 2014
// esta funcion elimina el hecho de estar agregando los modelos manualmente


// function __autoload($modelname){
// 	if(Model::exists($modelname)){
// 		include Model::getFullPath($modelname);
// 	} 

// 	if(Form::exists($modelname)){
// 		include Form::getFullPath($modelname);
// 	}
// }

// Reemplaza la función __autoload con spl_autoload_register
spl_autoload_register(function ($modelname) {
    if (Model::exists($modelname)) {
        include Model::getFullPath($modelname);
    }

	if(Form::exists($modelname)){
		include Form::getFullPath($modelname);
	}
});

?>