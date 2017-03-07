import { API } from '../../modules/config';
import { FacebookButton, TwitterButton } from "../../services/socialShare";
import * as router from 'react-router';
import {TriggerModal} from '../../modules/modal.react';
import CommentReportForm from './comment/commentReportForm';


export default React.createClass({

    getInitialState () {
        return {
            report: this.props.can_viewer_report
        };
    },


    mixins: [router.History],

    render () {
        return (
            <footer className="foot_">

                <ul className="share_ button-group even-3">
                    <li>
                        <FacebookButton
                            className="button facebook_"
                            id={ this.props.id }
                            description={ this.props.description }
                            photo={ this.props._links.photo.href }
                        >
                            <i className="icon-facebook"></i>
                            <span>Compartilhar</span>
                        </FacebookButton>
                    </li>
                    <li>
                        <TwitterButton
                            className="button twitter_"
                            id={ this.props.id }
                            text={ this.props.description }
                        >
                            <i className="icon-twitter"></i>
                            <span>Twittar</span>
                        </TwitterButton>
                    </li>

                </ul>
                {
                    this.state.report && !this.props.can_viewer_delete &&
                    <TriggerModal
                        id="modal_report_post"
                        width={633}
                        className="report_post_"
                        onClick={this._onReport}
                    >
                                    Denunciar
                                    <CommentReportForm
                                        id={this.props.id}
                                        onSuccess={this._handleSubmitReport}
                                    />
                    </TriggerModal>
                }
                {
                    !auth.loggedIn() &&
                    <a href="#" className="report_post_" onClick={this._onClickLogged}>Denunciar</a>
                }

                {
                    this.props.can_viewer_delete &&
                    <a href="#"
                       className="right delete_post_"
                       onClick={this._handleDeletePost}>
                        <i className="icon-trash-empty"></i>
                        <span>Deletar</span>
                    </a>
                }

            </footer>
        );
    },

    _onClickLogged(e){
        e.preventDefault();
        APP.props.history.replaceState(null, '/login', {ref: APP.props.location.pathname});
    },

    _handleDeletePost (e) {
        e.preventDefault();

        $.ajax({
            url: `${ API }/posts/${this.props.id}`,
            method: 'delete',
            success: function (respond) {
                this.history.replaceState(null, `/`);
            }.bind(this),
            error: function (error) {
                console.warn(error);
            }
        });
    },

    _handleSubmitReport(){
        this.setState({
            report: false
        })
    },

    _onReport(){
        if (auth.loggedIn()) {
            return true;
        } else {
            APP.props.history.replaceState(null, '/login', {ref: APP.props.location.pathname});
            return false;
        }
    }
});


