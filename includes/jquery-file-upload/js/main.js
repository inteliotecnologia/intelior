/*
 * jQuery File Upload Plugin JS Example 8.3.0
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, regexp: true */
/*global $, window, blueimp */

$(function () {
    'use strict';
	
    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: 'form.php?uploadArquivos',
        //done: function (e, data) {
            //$.each(data.result.files, function (index, file) {
                //$('<p/>').text(file.name).appendTo('#files');
                //alert(file.name);
            //});
            
            //$('.checkbox_delete_toggle').fadeIn();
            //$('.delete').fadeIn();
            
            //alert('todos arquivos selecionados');
        //},
        /*change: function (e, data) {
		    $.each(data.files, function (index, file) {
		       // alert('Selected file: ' + file.name);
		    });
		    //$('.start').trigger('click');         
            //alert('todos arquivos selecionados');
        },*/
        //5 mega
        maxFileSize: 5000000,
        autoUpload: true
    });

    // Enable iframe cross-domain access via redirect option:
    /*$('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );*/
	
	//$('#fileupload').fileupload('destroy');
	
	// Load existing files:
    $('#fileupload').addClass('fileupload-processing');
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('#fileupload').fileupload('option', 'url'),
        dataType: 'json',
        context: $('#fileupload')[0]
    }).always(function () {
        $(this).removeClass('fileupload-processing');
    }).done(function (result) {
    	//alert(result);
        $(this).fileupload('option', 'done').call(this, null, {result: result});
    });


});
