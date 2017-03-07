import paginationBuider from "../services/paginationBuilder";
require('es6-object-assign').polyfill();

export default function (url, type, assignData) {
    var request;
    this.setState({
        data: null,
        linkNextPage: null
    });
    
    request = $.ajax({
        url: url,
        dataType: 'json',
        cache: false,
        data: Object.assign({
            'per-page': 40
        }, assignData),
        success: function (respond, t, request) {
            let nextLink = paginationBuider(request.getResponseHeader('Link'));
            
            let data = {
                data: respond,
                linkNextPage: nextLink,
                perPage: request.getResponseHeader('X-Pagination-Per-Page')
            };
            
            if (type === 'search') {
                data.count = request.getResponseHeader('X-Pagination-Total-Count');
            }
            
            this.setState(data);
            
        }.bind(this),
        error: function (error) {
            console.warn(error);
        }
    });
    return request;
}
