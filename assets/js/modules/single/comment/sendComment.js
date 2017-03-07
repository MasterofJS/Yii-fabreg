import React from 'react';
export default function (idPage, data, success, error) {
    $.ajax({
        url: `/data/comments_${idPage}.json`,
        dataType: 'json',
        stype: 'POST',
        data: data,
        success: success,
        error: error
    });
}






