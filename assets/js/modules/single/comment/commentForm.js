import { ROOT, API } from '../../../modules/config';
import Auth from '../../../services/auth'
import AutoForm from 'react-auto-form';
import * as router from 'react-router';
import validateFields from '../../../services/validateFields';


export default React.createClass({

    mixins: [router.History],

    getInitialState () {
        return {
            text: '',
            length: 0
        };
    },

    render () {
        return (
            <div className="comment_form_" onClick={this.handleClick}>
                <div className="av_"
                     style={{
                    backgroundImage: `url(${this.props.user._links && this.props.user._links.avatar.href || `${ ROOT }dist/images/meme-0.jpg`})`
                }}
                >
{
    /*
     <img
     src={ this.props.user._links && this.props.user._links.avatar.href || `${ ROOT }dist/images/meme-12.jpg` }
     alt={ this.props.user.username && this.props.user.username }/>
     */
}
                </div>
                <div className="body_">

                    <AutoForm className="commentForm" onSubmit={ this.handleSubmit }>
                            <textarea
                                placeholder="Escrever comantário"
                                value={ this.state.text }
                                onChange={ this.handleTextChange }
                            />

                        <footer className="foot_">
                            <input type="submit" value="Postar" className="right button"/>
                            <span>{ this.props.max - this.state.length }</span>
                        </footer>

                    </AutoForm>
                </div>
            </div>
        );
    },

    handleClick(){
        if (!Auth.loggedIn()) {
            this.history.replaceState({path: window.location.pathname}, '/login');
        }
    },

    handleTextChange (e) {
        let length = e.target.value.length;
        if (length > this.props.max) {
            return;
        }
        this.setState({text: e.target.value, length: length});
    },

    handleSubmit (e, data) {
        var text;
        e.preventDefault();

        text = this.state.text.trim();

        if (!text) {
            return;
        }

        this.props.onCommentSubmit({text: text});
        this.setState({text: '', length: 0});
    }

});
