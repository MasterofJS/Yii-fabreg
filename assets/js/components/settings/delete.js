import AutoForm from 'react-auto-form';
import {ROOT, API} from '../../modules/config';
import validateFields from '../../services/validateFields';

export default React.createClass({

    getInitialState(){
        return {
            value: ''
        }
    },

    //componentDidMount () {
    //$('.delete_account_').foundation('abide', 'reflow');
    //},

    render () {
        return (
            <div className="delete_account_ text-center">
                <div className="row">
                    <div className="columns">
                        <img src={`${ ROOT }dist/images/sad_smiley.png`} alt="#"/>
                        <h1>Ficamos tristes de te ver partir!</h1>
                        <p>Unicorno é uma comunidade livre para se expressar e curtir posts. Quer nos contar a razão de nos deixar? Caso tenha vontade, deixe-nos uma mensagem no espaço abaixo.</p>
                        <AutoForm data-abide="ajax" onSubmit={this._submit}>
                            <div className="row">
                                <div ref="deletion_reason" className="columns small-centered ">
                                    <textarea
                                        name="reason"
                                        placeholder="Quer nos contar o motivo de deletar sua conta?"
                                        value={ this.state.value }
                                        onChange={ this._onChange}
                                    >
                                    </textarea>
                                    <small className="error">Error</small>
                                </div>
                            </div>
                            <div className="row">
                                <div className="columns text-center">
                                    <Link className="button" to="/settings/account">Cancelar</Link>
                                    <button type="submit" className="button">Deletar Conta</button>
                                </div>
                            </div>
                        </AutoForm>
                    </div>
                </div>
            </div>
        );
    },

    _onChange(e){

        this.setState({
            value: e.target.value
        })

    },

    _submit(e, submittedData){

        e.preventDefault();

        //if (submittedData.reason.length < 1) {
        //    return;
        //}


        $.ajax({
            url: `${ API }/users/me/delete`,
            method: 'delete',
            data: {
                deletion_reason: submittedData.reason
            },
            success: function (respond) {
                if (Array.isArray(respond)) {
                    validateFields.call(this, respond);

                    $('body,html').animate({ scrollTop: $($('.error .error').get(0).closest('div.error')).offset().top - 100}, 300);

                    return;
                }

                auth.logout();
            }.bind(this),
            error: function (error) {
                console.warn(error);
            }
        });
    }
});