import AutoForm from 'react-auto-form';
import { API } from '../../modules/config';
import { showTotalAlertBox } from '../../modules/alertBox.react';
import validateFields from '../../services/validateFields';

export default React.createClass({

    componentDidMount() {
        $(document).foundation('abide', 'reflow');
    },

    render () {
        return (
            <div>
                <h1 className="page_title_">Resetar Senha</h1>
                <hr/>
                <AutoForm data-abide="ajax" data-invalid onSubmit={this._submit}>
                    <div className="row">
                        <div className="medium-6 columns password-field" ref="old_password">
                            <input type="password"
                                   name="old_password"
                                   id="password_account_"
                                   placeholder="Senha antiga"
                            />
                            <small
                                className="error">Sua senha precisa ter letras e números, mínimo de 6 caracteres</small>
                        </div>

                    </div>
                    <div className="row">
                        <div className="medium-6 columns password-field" ref="new_password">
                            <input type="password"
                                   name="new_password"
                                   id="newpassword_account"
                                   required
                                   placeholder="Nova senha"/>
                            <small className="error">Sua nova senha precisa ter letras e números, mínimo de 6 caracteres, e não pode ser igual à senha antiga</small>
                        </div>
                        <div className="medium-6 columns password-confirmation-field">
                            <input type="password"
                                   data-equalto="newpassword_account"
                                   required
                                   placeholder="Re-digitar a nova senha"/>
                            <small className="error">As senhas devem ser iguais</small>
                        </div>
                    </div>
                    <div className="row">
                        <div className="large-12 columns">
                            <button className="button" type="submit">Salvar alterações</button>
                            {/*<Link className="del_account_" to={'/settings/delete'}>Deletar Conta</Link>*/}
                        </div>
                    </div>
                </AutoForm>
            </div>
        );
    },

    _submit(e, data) {
        e.preventDefault();

        if ($(e.target).is('[data-invalid]')) {
            return;
        }

        $.ajax({
            url: `${ API }/users/me/change-password`,
            type: 'put',
            data: {
                old_password: data.old_password,
                new_password: data.new_password
            },
            success: function (respond) {

                if (Array.isArray(respond)) {

                    validateFields.call(this, respond);
                    showTotalAlertBox(null);

                    $('body,html').animate({ scrollTop: $($('.error .error').get(0).closest('div.error')).offset().top - 100}, 300);

                    return;
                }

                showTotalAlertBox('Salvo', 'success');
                $('body,html').animate({
                    scrollTop: 0
                }, 300);

            }.bind(this),
            error: function (error) {
                console.warn(error);
                showTotalAlertBox(error.responseText, 'alert')
            }
        });
    }

});
