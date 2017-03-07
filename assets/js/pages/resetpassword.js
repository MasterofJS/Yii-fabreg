import AutoForm from 'react-auto-form';
import validateFields from '../services/validateFields';
import {API} from '../modules/config';
import * as router from 'react-router';
require('es6-object-assign').polyfill();
import {showTotalAlertBox} from '../modules/alertBox.react';


const ResetPassword = React.createClass({

    mixins: [router.History],

    getInitialState () {
        return {
            submittedData: null,
            password: null,
            passwordConfirm: null,
            validate: null
        };
    },

    render () {
        return (
            <div className="row">
                <div className="column small-centered large-6 reset_password_">
                    <h1 className="page_title_ text-center">Resetar Senha</h1>
                    <hr/>
                    {
                        this.state.validate ||
                        <AutoForm onSubmit={this._handlerSubmit} data-abide>

                            <div className={classNames({'error':this.state.password,'field-group_': true})}>
                                <input
                                    type="password"
                                    ref="newPassword"
                                    name="new_password"
                                    placeholder="New Nova Senha"/>
                                <small
                                    className="error">Sua senha precisa ter letras e números, mínimo de 6 caracteres</small>
                            </div>

                            <div className={classNames({'error': this.state.passwordConfirm,'field-group_': true})}>
                                <input
                                    type="password"
                                    ref="newPasswordConfirm"
                                    name="new_password_confirm"
                                    placeholder="Confirmar Nova Senha"/>
                                <small className="error">As senhas devem ser iguais</small>
                            </div>

                            <div className="text-center">
                                <input type="submit" value="Resetar Senha" className="button"/>
                            </div>

                        </AutoForm>
                    }
                </div>
            </div>
        );
    },

    _handlerSubmit(e, submittedData){

        e.preventDefault();

        if (this.refs.newPassword.value.length < 8) {
            this.setState({password: true});
            return;
        } else {
            this.setState({password: false});
        }
        if (this.refs.newPassword.value === this.refs.newPasswordConfirm.value) {
            this.setState({passwordConfirm: false});
        } else {
            this.setState({passwordConfirm: true});
            return;
        }

        $.ajax({
            url: `${ API }/recovery/password/reset?token=${ this.props.location.query.token }`,
            method: 'put',
            data: {
                password: submittedData.new_password
            },
            success: function () {

                this.props.history.replaceState(null, '/login');
            }.bind(this),
            error: function (error) {
                console.warn(error);
            }
        });
    }
});

const ForgotPassword = React.createClass({

    getInitialState(){
        return {
            success: false
        }
    },

    render () {
        return (
            <div className="row">
                <div className="column small-12 small-centered reset_password_"
                     style={{
                            minWidth:'100%'
                        }}
                >
                    {
                        
                        
                        !this.state.success ?
                            <h1 className="page_title_ text-center">Esqueci minha senha</h1> :
                            <h1 className="page_title_ text-center">Pronto, por favor cheque seu email</h1>
                    }
                    <hr/>
                </div>
                <div className="column small-centered large-6 reset_password_">

                    {
                        !this.state.success &&
                        <AutoForm onSubmit={this._handlerSubmit} noValidate>

                            <div ref="email" className={classNames({'field-group_': true})}>
                                <input
                                    type="email"
                                    name="email"
                                    placeholder="Email"
                                />
                                <small className="error"></small>
                            </div>
                            
                            <div className="text-center">
                                <input type="submit" value="Enviar nova senha" className="button"/>
                            </div>

                        </AutoForm>
                    }

                </div>
            </div>
        );
    },

    _handlerSubmit(e, submittedData){
        e.preventDefault();

        $.ajax({
            url: `${ API }/recovery/password/request`,
            method: 'put',
            data: {
                email: submittedData.email
            },
            success: function (respond) {
                if (Array.isArray(respond)) {

                    validateFields.call(this, respond);

                    return;
                }

                this.setState({
                    success: true
                });

            }.bind(this),
            error: function (error) {
                console.warn(error);
                showTotalAlertBox(error.responseText,'alert');
            }
        });
    }
});

const ConfirmEmail = React.createClass({

    componentDidMount(){
        $.ajax({
            url: `${API}/account/email/confirm?token=${ this.props.location.query.token }`,
            method: 'put',
            success: function (respond) {
                APP.setState({
                    user: Object.assign(APP.state.user, {has_confirmed_email: true})
                }, function () {
                    APP.props.history.replaceState(null, '/');
                });
            },
            error: function (error) {
                console.warn(error);
            }
        });
    },

    render () {
        return null;
    }

});

export default {
    ResetPassword,
    ForgotPassword,
    ConfirmEmail
};