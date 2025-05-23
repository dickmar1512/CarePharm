$(document).ready(function () {
    $("#searchForm #product_code2").on("input", function () {
        let searchTerm = $(this).val().trim();
        // if (searchTerm == "") {
        //     //$("#show_search_results").html("");
        //     return; // Salimos de la función
        // }
        
        if (searchTerm.length >= 5) {
            $("#searchForm").submit(); // Envía el formulario automáticamente
        }
	});
});