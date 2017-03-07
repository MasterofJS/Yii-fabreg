import {API} from '../modules/config';
let logged = false;

export default {

    login(cb) {

        cb = arguments[arguments.length - 1];

        if (cb) cb(true);

        this.onChange(true);
    },

    setUser(user, ref = null, cb) {

        logged = true;

        APP.setState({
            user: null
        });

        APP.setState({
            user
        }, function () {

            auth.login(function () {

                if (ref !== null) {
                    APP.props.history.replaceState(null, ref);
                }
                if (typeof cb === 'function') cb();
            });
        });
    },

    logout(cb) {

        $.post(`${ API }/auth/logout`, function (r) {

            logged = false;
            switch (APP.props.location.pathname) {
                case '/settings':
                case '/settings/account':
                case '/settings/password':
                case '/settings/profile':
                case '/settings/delete':
                case '/notification':
                    APP.props.history.replaceState(null, '/');
                    break;
            }

            if (typeof cb === 'function') cb();

            APP.setState({user: null});
            APP.setState({user: 'guest'});

        });
    },

    logoutView(cb){
        logged = false;
        APP.setState({user: 'guest'}, function () {
            if (typeof cb === 'function') cb();
        })
    },

    loggedIn () {
        return logged
    },

    onChange () {
    },

    onEnter () {
    },

    onLogout () {
    },

    updateUser(cb) {
        return function () {
            $.ajax({
                url: `${ API }/users/me`,
                method: 'get',
                // data: {
                //    expand: 'settings,profile,avatar,cover'
                // },
                success: function (respond) {
                    if (Array.isArray(respond)) {
                        return;
                    }
                    auth.setUser(respond);

                },
                error: function (error) {
                    console.warn(error);
                }
            });

            cb(arguments);
        };
    }

}

