require('../../../bower_components/foundation/js/foundation/foundation.alert.js');
console.dir(fileupload);

var dateValidate = require('../modules/datebuilder.js');
var countriesBuilder = require('../modules/countrybuilder.js');

$(function () {
    "use strict";

    var $fileuploadWrap;
    var $fileupload;
    var date;

    date = new dateValidate(['#day_sign_up_', '#month_sign_up_', '#year_sign_up_']);

    $(document).foundation({
        abide: {
            patterns: {

                password: /^(.){6,}$/
            },
            error_labels: false,
            validators: {
                isDay: date.isDay
            }
        }
    });


    // Countries
    countriesBuilder($('#country_sign_up_'));

    $('#form_sign_up_')
        .on("valid.fndtn.abide", function (g) {
            console.info('valid!');
            /*todo*/
        })
        .on("invalid.fndtn.abide", function (g) {
            console.warn('Invalid!');
            /*todo*/
        });


    /* File upload */

    $fileupload = $('#fileupload');
    $fileuploadWrap = $('.fileupload_');
    $fileupload.fileupload({
            url: '//jquery-file-upload.appspot.com/',
            dataType: 'json',
            dropZone: $('.dropzone'),
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
            maxFileSize: 10000,
            done: function (e, data) {
                if (data.result.files[0].error) {
                    fileuploadMessage(data.result.files[0].error);
                    $fileuploadWrap.removeClass('loading');
                    return;
                }
                $('.picture_ img')
                    .one('load', function () {
                        $fileuploadWrap.removeClass('loading');
                    })
                    .attr('src', data.result.files[0].url);
            },
            fail: function (e, data) {
                fileuploadMessage('Server Error');
                $fileuploadWrap.removeClass('loading');
            }
        })
        .on('fileuploadadd', function (e, data) {
            var fileType, allowdtypes;

            $fileuploadWrap.next('.alert-box').remove();
            fileType = data.files[0].name.split('.').pop(),
                allowdtypes = 'jpeg,jpg,png,gif';
            if (allowdtypes.indexOf(fileType) < 0) {
                fileuploadMessage('Tipo de arquivo inválido');
                return false;
            } else {
                $fileuploadWrap.addClass('loading');
            }
        });

    $('body')
        .one('dragover', dragoverHandler)
        .on('drop', function () {
            $('.dropzone').removeClass('dropover');
            $('body').one('dragover', dragoverHandler);
        });

    function dragoverHandler() {
        $('.dropzone').addClass('dropover');
    }

    function fileuploadMessage(message, type) {
        var template = Handlebars.compile($('#tpl_fileupload_sign_up_').html());
        $fileuploadWrap.after(template({
            'class': type || 'alert',
            message: message
        }));
        $(document).foundation('alert', 'reflow');
    }
});