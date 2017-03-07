import { API } from '../modules/config';
import Page from '../modules/page';
import AutoForm from 'react-auto-form';
import { showTotalAlertBox } from '../modules/alertBox.react';
import validateFields from '../services/validateFields';

export default React.createClass({

    render () {
        return (
            <div className="contact_us_ row">
                <div className="column">

                <h1 className="page_title_">Contato</h1>
                <hr/>
                <AutoForm
                    id="form_contact_"
                    data-abide="ajax"
                    className="contact_form_"
                    onSubmit={this._submit}
                >
                    <div className="row">
                        <div className="medium-6 columns" ref="first_name">
                            <input
                                type="text"
                                name="first_name"
                                placeholder="Nome"
                            />
                            <small className="error">Error</small>
                        </div>
                        <div className="medium-6 columns" ref="last_name">
                            <input
                                type="text"
                                name="last_name"
                                placeholder="Sobrenome"
                            />
                            <small className="error">Error</small>
                        </div>
                    </div>
                    <div className="row">
                        <div className="medium-6 columns" ref="email">
                            <input
                                type="email"
                                name="email"
                                placeholder="Email"
                            />
                            <small className="error">Error</small>
                        </div>
                        <div className="medium-6 columns" ref="subject">
                            <input
                                type="text"
                                name="subject"
                                placeholder="Assunto"
                            />
                            <small className="error">Error</small>
                        </div>
                    </div>
                    <div className="row">
                        <div className="columns" ref="message">
                            <textarea
                                name="message"
                                cols="30" rows="10"
                                placeholder="Escreva aqui sua mensagem…"
                            > </textarea>
                            <small className="error"> </small>
                        </div>
                    </div>
                    <input type="checkbox" hidden name="test_checkbox" defaultChecked={false}/>

                    <div className="row">
                        <div className="large-12 columns">
                            <button className="button" type="submit">Enviar</button>
                        </div>
                    </div>
                </AutoForm>
                </div>

            </div>
        );
    },

    _submit(e, data){

        e.preventDefault();
        if (data.test_checkbox) {
            return;
        }

        $.ajax({
            url: `${ API }/contact`,
            method: 'post',
            data: data,
            //beforeSend(){
            //    showTotalAlertBox('Salvar', 'info')
            //},
            success: function (respond) {

                if (Array.isArray(respond)) {
                    validateFields.call(this, respond);
                    //showTotalAlertBox(null);
                    $('body,html').animate({scrollTop: $($('.error .error').get(0).closest('div.error')).offset().top - 100}, 300);

                    return;
                }

                showTotalAlertBox('mensagem enviada', 'success');
                $('#form_contact_').find('input,textarea').val('');
                for (var key in this.refs) {
                    $(this.refs[key]).removeClass('error');
                }

            }.bind(this),
            error: function (error) {
                console.warn(error);
                showTotalAlertBox(error.responseText, 'alert')
            }
        });
    }

});
