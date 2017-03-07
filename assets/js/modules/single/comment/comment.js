import { API } from '../../../modules/config';
import moment from 'moment';
import * as router from 'react-router';
import paginationBuider from '../../../services/paginationBuilder';

import CommentNested from './commentNested';
import CommentFormNested from './CommentFormNested';
import CommentOpenNested from "./commentOpenNested";
import CommentMenu from './CommentMenu';
// import CommentVote from './CommentVote';

let commentWithForm = null;

const CommentListNested = React.createClass({

    getInitialState () {
        return {
            index: 0,
            loadMore: !!this.props.next,
            answersNext: this.props.next
        };
    },

    getDefaultProps() {
        return {
            handleChildReply: null
        };
    },

    render() {
        var commentNodes, self;
        self = this;
        commentNodes = this.props.data.map(function (comment) {
            return (
                <CommentNested

                    key={comment.id}
                    comment={comment}
                    handleReplyFormShow={self.props.handleReplyFormShow}
                    rootThat={ self.props.rootThat }
                    user={ self.props.user }

                >
                    {comment.content}
                </CommentNested>
            );
        });

        return (
            <div className="commentList">

                {commentNodes}

                {
                    !!this.state.answersNext &&
                    <CommentOpenNested onLoad={this._loadMoreCommets}/>
                }
            </div>
        );
    },

    _loadMoreCommets(e){
        let that = this;
        e.preventDefault();

        if (!this.state.answersNext) {
            return;
        }

        $.ajax({
            url: `${this.state.answersNext}`,
            data: {
                expand: 'author,avatar,permissions'
            },
            success: function (respond, t, request) {

                let newData, nextLink;

                nextLink = paginationBuider(request.getResponseHeader('Link'));
                newData = Array.prototype.concat(that.props.data, respond);

                that.props.rootThat.setState({
                    answers: newData
                });
                that.setState({
                    answersNext: nextLink
                });
            },
            error: function (er) {
                console.warn(er);
            }
        });
    }

});

export default React.createClass({

    mixins: [router.History],

    getInitialState(){
        return {
            replyForm: false,
            dataReply: null,
            answers: null,
            answersNext: null,
            likes: this.props.comment.likes,
            dislikes: this.props.comment.dislikes,
            liked: this.props.comment.liked,
            disliked: this.props.comment.disliked
        }
    },


    componentWillMount(){

        if (this.props.comment.comments > 0) {

            $.ajax({
                url: `${ API }/comments/${ this.props.comment.id }/comments`,
                data: {
                    'expand': 'author,avatar,permissions',
                    'pre-page': 1
                },
                success: function (respond, textStatus, request) {
                    let nextLink = paginationBuider(request.getResponseHeader('Link'));

                    this.setState({
                        answers: respond,
                        answersNext: nextLink
                    });

                }.bind(this),
                error: function (error) {
                    console.warn(error);
                }
            });

        }

    },

    render() {
        const comment = this.props.comment;
        return (
            <div className="comment_">

                <Link
                    to={`/user/${ comment.author.username }`}
                    className="av_"
                    style={{
                        backgroundImage: `url(${comment.author._links.avatar.href})`
                    }}
                >
                    {/*img src={comment.author._links.avatar.href} alt={comment.author.username}/*/}
                </Link>

                <div className="body_">

                    <Link
                        to={`/user/${ comment.author.username }`}
                        className="author_"
                    >
                        {comment.author.username}
                    </Link>

                    <span className="details_">
                        {this.state.likes}&nbsp;likes&nbsp;&middot;&nbsp;{this.state.dislikes}&nbsp;
                                                dislikes&nbsp;&middot;&nbsp;{moment.unix(/*-1 * (new Date()).getTimezoneOffset() * 60 + */comment.timestamp).format("LL")}
                    </span>


                    {
                        auth.loggedIn() &&
                        <CommentMenu {...comment} rootThat={ this.props.rootThat } user={ this.props.user }/>

                    }

                    {
                        (comment.type === 'media') ?
                            <div className="text_"><img src={comment.content} alt="#"/></div> :
                            <div className="text_">{comment.content}</div>
                    }

                    <a href="#"
                       className="reply_"
                       onClick={this.handleReplyFormShow.bind(this, comment)}>Responder</a>

                    <ul className="comment_vote_ no-bullet">
                        <li>
                            <a
                                href="#up"
                                className={classNames({
                                    'active': this.state.liked
                                })}
                                onClick={this.handleLike.bind(this, 'like')}>
                                <i className="icon-thumbs-up"></i>
                            </a>
                        </li>

                        <li>
                            <a
                                href="#down"
                                className={classNames({
                                    'active': this.state.disliked
                                })}
                                onClick={this.handleLike.bind(this, 'dislike')}>
                                <i className="icon-thumbs-down"></i>
                            </a>
                        </li>
                    </ul>


                </div>

                {
                    !!(this.state.answers) &&
                    <CommentListNested

                        data={ this.state.answers }
                        handleReplyFormShow={ this.handleReplyFormShow }
                        next={ this.state.answersNext }
                        rootThat={ this }
                        user={ this.props.user }
                    />
                }

                {
                    (this.state.replyForm) &&
                    <CommentFormNested
                        post_id={this.props.post_id}
                        data={this.state.dataReply}
                        user={this.props.user}
                        onCommentSubmit={this.onCommentSubmit}
                        rootThat={this}
                        max={500}
                    />
                }

            </div>

        );
    },

    onCommentSubmit(){
        this.setState({
            replyForm: false
        });
    },

    handleReplyFormShow(d, e = false){
        let that = this;
        if (e) {
            e.preventDefault();
        }

        if (!auth.loggedIn()) {
            that.history.replaceState({path: window.location.pathname}, '/login', {path: window.location.pathname});
            return;
        }

        this.setState({
            replyForm: true,
            dataReply: d
        });

        if (commentWithForm !== null && commentWithForm !== this) {
            commentWithForm.setState({
                replyForm: false
            });
        }

        commentWithForm = that;
    },

    handlePoints(newPoints){
        this.setState({
            points: newPoints
        });
    },

    handleLike(type, e){
        var oppositeType;
        e.preventDefault();

        if (!auth.loggedIn()) {
            APP.props.history.replaceState(null, '/login', {'ref': APP.props.location.pathname});
            return;
        }

        oppositeType = (type === 'like') ? 'dislike' : 'like';

        $.post(`${ API }/comments/${this.props.comment.id}/${type}`, function () {
            this.setState({
                [`${type}d`]: !this.state[`${type}d`],
                [`${type}s`]: this.state[`${type}d`] ? (this.state[`${type}s`] - 1) : (this.state[`${type}s`] + 1)
            });

            if (this.state[`${oppositeType}d`]) {

                this.setState({
                    [`${oppositeType}d`]: !this.state[`${oppositeType}d`],
                    [`${oppositeType}s`]: this.state[`${oppositeType}d`] ? (this.state[`${type}s`] - 1) : (this.state[`${type}s`] + 1)
                });
            }
        }.bind(this));
    }

});
