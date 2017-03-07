import AutoForm from 'react-auto-form';
import { API } from '../../modules/config';
import { showTotalAlertBox } from '../../modules/alertBox.react';
import validateFields from '../../services/validateFields';

export default React.createClass({

    getInitialState () {
        return {
            isChecked: !!+this.props.user.hide_upvotes
        };
    },

    //componentDidMount() {
    //$(document).foundation('abide', 'reflow');
    //},

    render () {
        return (
            <div className="account_">
                <h1 className="page_title_">Configurações de conta</h1>
                <hr/>
                <AutoForm data-abide="ajax" onSubmit={this._submit}>
                    <div className="row">
                        <div ref="username" className="medium-6 columns">
                            <input type="text"
                                   placeholder="Nome de usuário"
                                   name="username"
                                   defaultValue={this.props.user.username}/>
                            <small className="error">Erro</small>
                        </div>
                        <div ref="email" className="medium-6 columns">
                            <input type="email"
                                   placeholder="Email"
                                   name="email"
                                   defaultValue={this.props.user.email}/>
                            <small className="error">Erro</small>
                        </div>
                    </div>
                    <div className="row">
                        <div ref="show_nswf" className="large-12 columns">
                            <label>
                                Mostrar Posts NANT (Não abra no trabalho)
                                <select name="show_nswf" defaultValue={this.props.user.show_nswf}>
                                    <option value="0">Desligado</option>
                                    <option value="1">Ligado</option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <div className="row">
                        <div ref="hide_upvotes"
                             className="columns">
                            <label className="bl_">Curtidas</label>
                            <input id="checkbox_"
                                   type="checkbox"
                                   name="hide_upvotes"
                                   defaultChecked={this.state.isChecked}
                                   checked={this.state.isChecked}
                                   onChange={this._onChange}
                            />
                            <label htmlFor="checkbox_">
                                Não mostrar minhas curtidas no perfil
                            </label>
                        </div>
                    </div>
                    <div className="row">
                        <div className="columns">
                            <button type="submit" className="button">Salvar alterações</button>
                            {/*<Link className="del_account_" to={'/settings/delete'}>Deletar Conta</Link>*/}
                        </div>
                    </div>
                </AutoForm>
            </div>
        );
    },

    _submit(e, data) {
        e.preventDefault();

        $.ajax({
            url: `${ API }/users/me/settings`,
            type: 'put',
            data: {
                username: data.username,
                email: data.email,
                show_nswf: data.show_nswf,
                hide_upvotes: data.hide_upvotes && data.hide_upvotes === 'on' ? '1' : '0'
            },
            success: function (respond) {

                if (Array.isArray(respond)) {

                    validateFields.call(this, respond);
                    $('body,html').animate({ scrollTop: $($('.error .error').get(0).closest('div.error')).offset().top - 100}, 300);
                    showTotalAlertBox(null);

                    return;
                }

                showTotalAlertBox('Salvo', 'success');

                $('body,html').animate({
                    scrollTop: 0
                }, 300);

                auth.setUser(respond);

            }.bind(this),
            error: function (error) {
                console.warn(error);
                showTotalAlertBox(error.responseText, 'alert')
            }
        });
    },

    _onChange(){
        this.setState({isChecked: !this.state.isChecked});
    }
});


