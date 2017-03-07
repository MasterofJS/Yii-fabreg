import React from 'react';
import Google from '../services/authGoogle';
import FacebookLogin from '../services/authFacebook';
import { API } from '../modules/config';
import * as router from 'react-router';
import {showTotalAlertBox} from '../modules/alertBox.react';

function login(data) {
    auth.setUser(data, APP.props.location.pathname === '/settings/profile' ? '/settings/profile' : ( APP.props.location.query.ref || '/'));
}


const FacebookButton = React.createClass({

    mixins: [router.History],

    render () {
        return (
            <FacebookLogin
                className={this.props.className}
                autoLoad={true}
                callback={this._submit}>
                {this.props.children}
            </FacebookLogin>
        );
    },

    _submit(respond){
        const that = this;

        $.ajax({
            type: 'POST',
            url: `${API}/oauth/facebook`,
            data: {
                access_token: respond.authResponse.accessToken,
                expires_in: respond.authResponse.expiresIn
            },
            success (respond) {
                login.call(that, respond);

                if (that.props.closeDropdown) that.props.closeDropdown();
            },
            error: function (error) {
                showTotalAlertBox(error.responseText, 'alert');
                if (error.status === 422) {
                    that.history.replaceState(null, that.props.responder || '/');
                    $('body,html').animate({scrollTop: 0}, 300);
                }
            }
        });
    }

});

const GoogleButton = React.createClass({

    mixins: [router.History],

    render () {
        return (
            <Google onSubmit={this._submit} className={this.props.className}>
                {this.props.children}
            </Google>
        );
    },

    _submit(respond){
        const that = this;
        $.ajax({
            type: 'POST',
            url: `${API}/oauth/google`,
            data: {
                access_token: respond.hg.access_token,
                expires_in: respond.hg.expires_in
            },
            success (respond) {
                login.call(that, respond);
                if (that.props.closeDropdown) that.props.closeDropdown();
            },
            error: function (error) {
                showTotalAlertBox(error.responseText, 'alert');
                if (error.status === 422) {
                    that.history.replaceState(null, that.props.responder || '/');
                    $('body,html').animate({scrollTop: 0}, 300);
                }
            }
        });
    }
});

export default {
    FacebookButton,
    GoogleButton
}