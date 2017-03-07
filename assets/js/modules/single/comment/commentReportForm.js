import { API } from '../../../modules/config';
import AutoForm from 'react-auto-form';

export default React.createClass({

    render () {
        return (
            <div className="report_modal_">
                <header className="head_">
                    <h2 className="title_">Denunciar Post</h2>
                    <span className="subtitle_">Qual a razão para denunciar este post?</span>
                </header>
                <AutoForm onSubmit={this.handleSubmit} trimOnSubmit>
                    <div className="row collapse">
                        <div className="columns">
                            <label>
                                <input type="radio" name="report" value="0" defaultChecked/>
                                <span className="radio_"></span>
                                Violando direitos autorais
                            </label>
                            <label>
                                <input type="radio" name="report" value="1"/>
                                <span className="radio_"></span>
                                Spam, propaganda
                            </label>
                            <label>
                                <input type="radio" name="report" value="2"/>
                                <span className="radio_"></span>
                                Material ofensivo/nudez
                            </label>
                        </div>
                    </div>
                    <input className="button" type="submit" value="Enviar"/>
                </AutoForm>
            </div>
        );
    },

    handleSubmit(e, submittedData) {
        e.preventDefault();
        $.ajax({
            url: `${ API }/posts/${ this.props.id }/report`,
            method: 'post',
            data: {
                type: submittedData.report
            },
            success: function () {
                this.setState({submittedData});
                this.props.handleClose();
                this.props.onSuccess();
            }.bind(this),
            error: function (error) {
                console.warn(error);
            }
        });
    }
    
});