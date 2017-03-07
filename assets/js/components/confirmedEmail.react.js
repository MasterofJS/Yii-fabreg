
import {API} from '../modules/config';
import {showTotalAlertBox} from '../modules/alertBox.react';
export default React.createClass({

    propTypes: {
        user: React.PropTypes.any.isRequired
    },

    render() {
        if (this.props.user === 'guest' || (!this.props.user || this.props.user.has_confirmed_email)) {
            return null;
        }
        return (
            <div className="confirmation_email_ alert-box warning">
                E aí { this.props.user.username }, precisamos que você verifique seu email.
                Se você não recebeu um email nosso, podemos te <a href="#" onClick={this._onClick}>reenviar ou</a> você pode mudar o <Link
                to="/settings/account">email de recebimento</Link>
            </div>
        );
    },

    _onClick(e){
        e.preventDefault();

        $.ajax({
            url: `${ API }/users/me/confirm-email`,
            method: 'put',
            success: function () {
                showTotalAlertBox('Enviamos uma mensagem de confirmação.', 'info');
            }.bind(this),
            error: function (error) {
                showTotalAlertBox(error.responseText,'alert')
            }
        })
    }
});



