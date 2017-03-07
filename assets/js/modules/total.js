export default function () {

    $(document).ajaxError(function (event, request) {
        if (request.status === 401) {
            auth.logoutView(function () {
                APP.props.history.replaceState(null, '/login');
            });
        }
    });

};
