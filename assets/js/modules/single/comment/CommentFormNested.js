import moment from 'moment';
import { API } from '../../../modules/config';
import * as router from 'react-router';
require('es6-object-assign').polyfill();


export default React.createClass({

    mixins: [router.History],

    getInitialState () {
        return {
            text: '',
            length: 0
        };
    },


    componentDidMount(){
        $('body,html').animate({
            scrollTop: $(ReactDOM.findDOMNode(this)).offset().top - $(window).height() + 200
        }, 300);

        if (this.props.data) {
            this.setState({
                text: `@${this.props.data.author.username}`
            });
        }
    },

    componentWillReceiveProps(nextProps){
        if (nextProps.data.author.username !== this.props.data.author.username) {
            $('body,html').animate({
                scrollTop: $(ReactDOM.findDOMNode(this)).offset().top - $(window).height() + 200
            }, 300);
        }
        if (this.props.data) {
            this.setState({
                text: `@${nextProps.data.author.username}`
            });
        }
    },

    render () {
        return (
            <div className="comment_form_" onClick={this.handleClick}>

                <div
                    className="av_"
                    style={{
                        backgroundImage: `url(${this.props.user._links && this.props.user._links.avatar.href || `${ ROOT }dist/images/meme-0.jpg`})`
                    }}
                >
                    {/*img src={this.props.user._links && this.props.user._links.avatar.href || `${ ROOT }dist/images/meme-12.jpg`} alt="#"/*/}
                </div>

                <div className="body_">

                    <form className="commentForm" onSubmit={this.handleSubmit}>
                        <textarea
                            placeholder="Write comments..."
                            value={this.state.text}
                            onChange={this.handleTextChange}
                        ></textarea>
                        <footer className="foot_">
                            <input type="submit" value="Post" className="right button"/>
                            <span>{ this.props.max - this.state.length}</span>
                        </footer>
                    </form>
                </div>
            </div>
        );
    },


    handleTextChange (e) {
        let length = e.target.value.length;
        if (length > this.props.max) {
            return;
        }
        this.setState({text: e.target.value, length: length});
    },

    handleSubmit (e) {
        var newComment;
        var text;
        let isMedia = /^https?:\/\/(?:[a-z0-9\-]+\.)+[a-z]{2,6}(?:\/[^/#?]+)+\.(?:jpg|gif|png)$/ig;
        e.preventDefault();
        text = this.state.text.trim();
        if (!text) {
            return;
        }


        newComment = {
            post_id: this.props.post_id,
            content: text,
            reply_id: this.props.data.id
        };

        if (Array.isArray(text.match(isMedia))) {
            newComment.type = 1;
        }


        $.ajax({
            url: `${ API }/comments`,
            method: 'post',
            data: newComment,
            success: function (respond) {
                let newComments;
                respond = Object.assign(respond, {author: this.props.user});
                newComments = Array.prototype.concat(
                    (this.props.rootThat.state.answers || []),
                    respond
                );
                this.props.rootThat.setState({
                    answersCount: this.props.rootThat.state.answersCount + 1,
                    answers: newComments
                });
            }.bind(this),
            error: function (error) {
                console.warn(error);
            }
        });

        this.setState({text: '', length: 0});
        this.props.onCommentSubmit();
    },

    handleClick(){
        if (!auth.loggedIn()) {
            this.history.replaceState({path: window.location.pathname}, '/login');
        }
    }

});

