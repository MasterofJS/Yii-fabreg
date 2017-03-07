export default function (errors, type = 'refs') {
    errors.forEach(function (e) {
        $(ReactDOM.findDOMNode(this[type][e.field]))
            .addClass('error')
            .find('.error')
            .text(e.message);
    }.bind(this))
}